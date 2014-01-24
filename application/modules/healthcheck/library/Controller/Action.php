<?php

/**
 * Class Healthcheck_Library_Controller_Action
 */
class Healthcheck_Library_Controller_Action extends My_Controller_Report
{
    /**
     * @var Healthcheck_Model_Healthcheck
     */
    protected $_healthcheck;

    /**
     * The navigation steps
     *
     * @var Healthcheck_Model_Healthcheck_Steps
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
    protected $_firstStepName = Healthcheck_Model_Healthcheck_Steps::STEP_SELECT_UPLOAD;

    /**
     * Called from the constructor as the final step of initialization
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::init()
     */
    public function init ()
    {
        parent::init();

        $this->_navigation = Healthcheck_Model_Healthcheck_Steps::getInstance();

        if (!My_Feature::canAccess(My_Feature::HEALTHCHECK))
        {
            $this->_flashMessenger->addMessage(array(
                "error" => "You do not have permission to access this."
            ));

            $this->redirector('index', 'index', 'index');
        }

        if (isset($this->_mpsSession->selectedClientId))
        {
            $client = Quotegen_Model_Mapper_Client::getInstance()->find($this->_mpsSession->selectedClientId);
            if (!$client instanceof Quotegen_Model_Client)
            {
                $this->_flashMessenger->addMessage(array(
                    "error" => "A client is not selected."
                ));

                $this->redirector('index', 'index', 'index');
            }
            else
            {
                $this->_navigation->clientName = $client->companyName;
            }
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
        $availableReports            = array();
        $availableReports["Reports"] = array(
            "pagetitle" => "Select a report...",
            "active"    => false,
            "url"       => $this->view->baseUrl('/healthcheck/report_index/index')
        );

        if (My_Feature::canAccess(My_Feature::HEALTHCHECK_PRINTIQ))
        {
            $availableReports['Printiq_Healthcheck'] = array(
                "pagetitle" => "Health Check",
                "active"    => false,
                "url"       => $this->view->baseUrl('/healthcheck/report_printiq_healthcheck/index')
            );
        }
        else
        {
            $availableReports['Healthcheck'] = array(
                "pagetitle" => "Health Check",
                "active"    => false,
                "url"       => $this->view->baseUrl('/healthcheck/report_healthcheck/index')
            );
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
        $this->view->headScript()->appendFile($this->view->baseUrl('/js/htmlReport.js'));

        if ($this->getHealthcheck()->id < 1)
        {
            $this->_flashMessenger->addMessage(array("error" => "Please select a report first."));

            // Send user to the index
            $this->redirector('index', 'index', 'index');
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
        $stage = ($this->getHealthcheck()->stepName) ? : Healthcheck_Model_Healthcheck_Steps::STEP_SELECT_UPLOAD;
        $this->_navigation->updateAccessibleSteps($stage);

        $this->view->placeholder('ProgressionNav')->set($this->view->NavigationMenu($this->_navigation));
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
     * @return Healthcheck_Model_Healthcheck
     */
    public function getHealthcheck ()
    {
        if (!isset($this->_healthcheck))
        {
            if (isset($this->_mpsSession->healthcheckId) && $this->_mpsSession->healthcheckId > 0)
            {
                $this->_healthcheck = Healthcheck_Model_Mapper_Healthcheck::getInstance()->find($this->_mpsSession->healthcheckId);
            }
            else
            {
                $this->_healthcheck               = new Healthcheck_Model_Healthcheck();
                $this->_healthcheck->dateCreated  = date('Y-m-d H:i:s');
                $this->_healthcheck->lastModified = date('Y-m-d H:i:s');
                $this->_healthcheck->reportDate   = date('Y-m-d H:i:s');
                $this->_healthcheck->dealerId     = $this->_identity->dealerId;
                $this->_healthcheck->name         = "Health Check " . date('Y/m/d');
                $this->_healthcheck->clientId     = $this->_mpsSession->selectedClientId;
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
            Healthcheck_Model_Mapper_Healthcheck::getInstance()->save($this->_healthcheck);
        }
        else
        {
            $this->_healthcheck->healthcheckSettingId = $this->_healthcheck->getHealthcheckSettings()->id;
            $this->_healthcheck->lastModified         = date('Y-m-d H:i:s');
            Healthcheck_Model_Mapper_Healthcheck::getInstance()->insert($this->_healthcheck);
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
