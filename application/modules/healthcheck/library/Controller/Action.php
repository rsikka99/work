<?php
use MPSToolbox\Legacy\Modules\HealthCheck\Mappers\HealthCheckMapper;
use MPSToolbox\Legacy\Modules\HealthCheck\Models\HealthCheckModel;
use MPSToolbox\Legacy\Modules\HealthCheck\Models\HealthCheckStepsModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;

/**
 * Class Healthcheck_Library_Controller_Action
 */
class Healthcheck_Library_Controller_Action extends My_Controller_Report
{
    /**
     * @var HealthCheckModel
     */
    protected $_healthcheck;

    /**
     * The navigation steps
     *
     * @var HealthCheckStepsModel
     */
    protected $_navigation;

    /**
     * An object containing various word styles
     *
     * @var stdClass
     */
    protected $_wordStyles;

    /**
     * Report name is the title behind the reports that are being generated.
     *
     * @var string
     */
    public $reportName;

    /**
     * The current proposal
     *
     * @var Healthcheck_ViewModel_Healthcheck
     */
    protected $_healthcheckViewModel;

    /**
     * @var string
     */
    protected $_firstStepName = HealthCheckStepsModel::STEP_FINISHED;

    /**
     * Called from the constructor as the final step of initialization
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::init()
     */
    public function init ()
    {
        parent::init();

        $this->_navigation = HealthCheckStepsModel::getInstance();

        if (!My_Feature::canAccess(My_Feature::HEALTHCHECK))
        {
            $this->_flashMessenger->addMessage([
                "error" => "You do not have permission to access this."
            ]);

            $this->redirectToRoute('app.dashboard');
        }

        if (!$this->getSelectedClient() instanceof \MPSToolbox\Legacy\Entities\ClientEntity)
        {
            $this->_flashMessenger->addMessage([
                "danger" => "A client is not selected."
            ]);

            $this->redirectToRoute('app.dashboard');
        }

        $this->_navigation->clientName = $this->getSelectedClient()->companyName;

        if (!$this->getSelectedUpload() instanceof \MPSToolbox\Legacy\Entities\RmsUploadEntity)
        {
            $this->_flashMessenger->addMessage([
                "danger" => "An RMS upload is not selected."
            ]);

            $this->redirectToRoute('app.dashboard');
        }

        $this->_fullCachePath     = PUBLIC_PATH . "/cache/reports/healthcheck/" . $this->getHealthcheck()->id;
        $this->_relativeCachePath = "/cache/reports/healthcheck/" . $this->_healthcheck->id;

        // Make the directory if it doesn't exist
        if (!is_dir($this->_fullCachePath))
        {
            if (!mkdir($this->_fullCachePath, 0777, true))
            {
                throw new Exception("Could not open cache folder! PATH:" . $this->_fullCachePath, 0);
            }
        }

        $this->view->ReportAbsoluteCachePath = $this->_fullCachePath;
        $this->view->ReportCachePath         = $this->_relativeCachePath;
    }

    public function initReportList ()
    {
        // This is a list of reports that we can view.
        $availableReports            = [];
        $availableReports["Reports"] = [
            "pagetitle" => "Select a report...",
            "active"    => false,
            "url"       => $this->view->url([], 'healthcheck')
        ];

        if (My_Feature::canAccess(My_Feature::HEALTHCHECK_PRINTIQ))
        {
            $availableReports['Printiq_Healthcheck'] = [
                "pagetitle" => "Health Check",
                "active"    => false,
                "url"       => $this->view->url([], 'healthcheck.report-printiq')
            ];
        }
        else
        {
            $availableReports['Healthcheck'] = [
                "pagetitle" => "Health Check",
                "active"    => false,
                "url"       => $this->view->url([], 'healthcheck.report')
            ];
        }
        $this->view->availableReports = $availableReports;
    }

    /**
     * Init function for HTML reports
     *
     * @throws Exception
     */
    public function initHtmlReport ()
    {
        $this->view->headScript()->appendFile($this->view->baseUrl('/js/app/legacy/HtmlReport.js'));

        if ($this->getHealthcheck()->id < 1)
        {
            $this->_flashMessenger->addMessage(["error" => "Please select a report first."]);

            // Send user to the index
            $this->redirectToRoute('app.dashboard');
        }

        $this->view->dealerLogoFile = $this->getDealerLogoFile();
    }

    /**
     * Prepares the view (for HTML reports) with the variables needed.
     *
     * @param $filename
     */
    public function initReportVariables ($filename)
    {
        $this->view->publicFileName       = $this->_relativeCachePath . "/" . $filename;
        $this->view->savePath             = $this->_fullCachePath . "/" . $filename;
        $this->view->dealerLogoFile       = $this->getDealerLogoFile();
        $this->view->healthcheckViewModel = $this->getHealthcheckViewModel();
    }

    /**
     * Gets the proposal object for reports to use
     *
     * @throws Zend_Exception
     * @return Healthcheck_ViewModel_Healthcheck
     */
    public function getHealthcheckViewModel ()
    {
        if (!$this->_healthcheckViewModel)
        {
            $this->_healthcheckViewModel = false;
            $hasError                    = false;
            try
            {
                $this->_healthcheckViewModel = new Healthcheck_ViewModel_Healthcheck($this->getHealthcheck());

                if ($this->_healthcheckViewModel->getDevices()->allIncludedDeviceInstances->getCount() < 1)
                {
                    $this->view->ErrorMessages [] = "All uploaded printers were excluded from your report. Reports can not be generated until at least 1 printer is added.";
                    $hasError                     = true;
                }
            }
            catch (Exception $e)
            {
                $this->view->ErrorMessages [] = "There was an error getting the reports.";
                throw new Zend_Exception("Error Getting Healthcheck View Model.", 0, $e);
            }

            if ($hasError)
            {
                $this->_healthcheckViewModel = false;
            }
        }

        return $this->_healthcheckViewModel;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::postDispatch()
     */
    public function postDispatch ()
    {
        $stage = ($this->getHealthcheck()->stepName) ?: HealthCheckStepsModel::STEP_FINISHED;
        $this->_navigation->updateAccessibleSteps($stage);

        $this->view->placeholder('ProgressionNav')->set($this->view->NavigationMenu($this->_navigation));

        parent::postDispatch();
    }

    /**
     * @return stdClass
     */
    public function getWordStyles ()
    {
        if (!isset($this->_wordStyles))
        {
            // Get the for a dealer styles table
            $this->_wordStyles                                         = new stdClass();
            $this->_wordStyles->default->sectionHeaderFontColor        = "0096D6";
            $this->_wordStyles->default->sectionHeaderBorderColor      = "000000";
            $this->_wordStyles->default->subSectionBackgroundColor     = "0096D6";
            $this->_wordStyles->default->subSectionFontColor           = "FFFFFF";
            $this->_wordStyles->default->tableHeaderBackgroundColor    = "B8CCE3";
            $this->_wordStyles->default->tableSubHeaderBackgroundColor = "EAF0F7";
            $this->_wordStyles->default->tableHeaderFontColor          = "FFFFFF";
        }

        return $this->_wordStyles;
    }


    /**
     * Gets the Healthcheck we're working on
     *
     * @return HealthCheckModel
     */
    public function getHealthcheck ()
    {
        if (!isset($this->_healthcheck))
        {
            if (isset($this->_mpsSession->healthcheckId) && $this->_mpsSession->healthcheckId > 0)
            {
                $this->_healthcheck = HealthCheckMapper::getInstance()->find($this->_mpsSession->healthcheckId);
            }
            else
            {
                $this->_healthcheck               = new HealthCheckModel();
                $this->_healthcheck->dateCreated  = date('Y-m-d H:i:s');
                $this->_healthcheck->lastModified = date('Y-m-d H:i:s');
                $this->_healthcheck->reportDate   = date('Y-m-d H:i:s');
                $this->_healthcheck->dealerId     = $this->getIdentity()->dealerId;
                $this->_healthcheck->name         = "Health Check " . date('Y/m/d');
                $this->_healthcheck->clientId     = $this->getSelectedClient()->id;
                $this->_healthcheck->rmsUploadId  = $this->getSelectedUpload()->id;
                $this->_healthcheck->stepName     = $this->_firstStepName;

                $this->_mpsSession->healthcheckId = $this->_healthcheck = HealthCheckMapper::getInstance()->insert($this->_healthcheck);
            }
        }

        return $this->_healthcheck;
    }

    /**
     * Saves the current report.
     * This keeps the updated modification date in the same location at all times.
     */
    protected function saveHealthcheck ()
    {
        if (isset($this->_mpsSession->healthcheckId) && $this->_mpsSession->healthcheckId > 0)
        {
            // Update the last modified date
            $this->_healthcheck->lastModified = date('Y-m-d H:i:s');
            HealthCheckMapper::getInstance()->save($this->_healthcheck);
        }
        else
        {
            $this->_healthcheck->lastModified = date('Y-m-d H:i:s');
            HealthCheckMapper::getInstance()->insert($this->_healthcheck);
            $this->_mpsSession->healthcheckId = $this->_healthcheck->id;
        }
    }

    /**
     * Updates a Healthcheck to be at the next available step
     *
     * @param bool $force Whether or not to force the update
     */
    public function updateHealthcheckStepName ($force = false)
    {
        // We can only do this when we have an active step
        if ($this->_navigation->activeStep instanceof My_Navigation_Step)
        {
            // That step also needs a next step for this to work
            if ($this->_navigation->activeStep->nextStep instanceof My_Navigation_Step)
            {
                $update = true;
                // We only want to update
                if ($force)
                {
                    $update = true;
                }
                else
                {
                    $newStepName = $this->_navigation->activeStep->nextStep->enumValue;

                    foreach ($this->_navigation->steps as $step)
                    {
                        // No need to update the step if we were going back in time.
                        if ($step->enumValue == $newStepName && $step->canAccess)
                        {
                            $update = false;
                            break;
                        }
                    }
                }

                if ($update)
                {
                    $this->getHealthcheck()->stepName = $this->_navigation->activeStep->nextStep->enumValue;
                }
            }
        }
    }
}
