<?php
use MPSToolbox\Legacy\Modules\Assessment\Mappers\AssessmentMapper;
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentStepsModel;
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;

/**
 * Class Assessment_Library_Controller_Action
 */
class Assessment_Library_Controller_Action extends My_Controller_Report
{
    /**
     * @var AssessmentModel
     */
    protected $_assessment;

    /**
     * The navigation steps
     *
     * @var AssessmentStepsModel
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
     * The current assessment view model
     *
     * @var Assessment_ViewModel_Assessment
     */
    protected $_assessmentViewModel;

    /**
     * @var string
     */
    protected $_firstStepName = AssessmentStepsModel::STEP_SURVEY;

    /**
     * Called from the constructor as the final step of initialization
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::init()
     */
    public function init ()
    {
        parent::init();

        $this->_navigation = AssessmentStepsModel::getInstance();

        if (!My_Feature::canAccess(My_Feature::ASSESSMENT))
        {
            $this->_flashMessenger->addMessage([
                "error" => "You do not have permission to access this."
            ]);

            $this->redirectToRoute('assessment');
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


        $this->_fullCachePath     = PUBLIC_PATH . "/cache/reports/assessment/" . $this->getAssessment()->id;
        $this->_relativeCachePath = "/cache/reports/assessment/" . $this->getAssessment()->id;

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
        $availableReportsArray            = [];
        $availableReportsArray["Reports"] = [
            "pagetitle" => "Select a report...",
            "active"    => false,
            "url"       => $this->view->url([], 'assessment.report-index')
        ];

        if (My_Feature::canAccess(My_Feature::ASSESSMENT))
        {
            $availableReportsArray["Assessment"] = [
                "pagetitle" => "Assessment",
                "active"    => false,
                "url"       => $this->view->url([], 'assessment.report-assessment')
            ];
        }

        if (My_Feature::canAccess(My_Feature::ASSESSMENT_CUSTOMER_COST_ANALYSIS))
        {
            $availableReportsArray["CustomerCostAnalysis"] = [
                "pagetitle" => "Customer Cost Analysis",
                "active"    => false,
                "url"       => $this->view->url([], 'assessment.report-cost-analysis')
            ];
        }

        if (My_Feature::canAccess(My_Feature::ASSESSMENT_GROSS_MARGIN))
        {
            $availableReportsArray["GrossMargin"] = [
                "pagetitle" => "Gross Margin",
                "active"    => false,
                "url"       => $this->view->url([], 'assessment.report-gross-margin')
            ];
        }

        if (My_Feature::canAccess(My_Feature::ASSESSMENT_TONER_VENDOR_GROSS_MARGIN))
        {
            $availableReportsArray["TonerVendorGrossMargin"] = [
                "pagetitle" => "Toner Vendor Gross Margin",
                "active"    => false,
                "url"       => $this->view->url([], 'assessment.report-toner-vendor-gross-margin')
            ];
        }

        if (My_Feature::canAccess(My_Feature::ASSESSMENT_JIT_SUPPLY_AND_TONER_SKU_REPORT))
        {
            $availableReportsArray["JITSupplyAndTonerSku"] = [
                "pagetitle" => My_Brand::$jit . " Supply and Toner SKU Report",
                "active"    => false,
                "url"       => $this->view->url([], 'assessment.report-jit-supply-and-toner-sku')
            ];
        }

        if (My_Feature::canAccess(My_Feature::ASSESSMENT_OLD_DEVICE_LIST))
        {
            $availableReportsArray["OldDeviceList"] = [
                "pagetitle" => "Old Device List",
                "active"    => false,
                "url"       => $this->view->url([], 'assessment.report-old-device-list')
            ];
        }

        if (My_Feature::canAccess(My_Feature::ASSESSMENT_PRINTING_DEVICE_LIST))
        {
            $availableReportsArray["PrintingDeviceList"] = [
                "pagetitle" => "Printing Device List",
                "active"    => false,
                "url"       => $this->view->url([], 'assessment.report-printing-device-list')
            ];
        }

        if (My_Feature::canAccess(My_Feature::ASSESSMENT_SOLUTION))
        {
            $availableReportsArray["Solution"] = [
                "pagetitle" => "Solution",
                "active"    => false,
                "url"       => $this->view->url([], 'assessment.report-solution')
            ];
        }

        if (My_Feature::canAccess(My_Feature::ASSESSMENT_LEASE_BUYBACK))
        {
            $availableReportsArray["LeaseBuyback"] = [
                "pagetitle" => "Lease Buyback",
                "active"    => false,
                "url"       => $this->view->url([], 'assessment.report-lease-buy-back')
            ];
        }

        if (My_Feature::canAccess(My_Feature::ASSESSMENT_FLEET_ATTRIBUTES))
        {
            $availableReportsArray["FleetAttributes"] = [
                "pagetitle" => "Fleet Attributes",
                "active"    => false,
                "url"       => $this->view->url([], 'assessment.report-fleet-attributes')
            ];
        }

        if (My_Feature::canAccess(My_Feature::ASSESSMENT_UTILIZATION))
        {
            $availableReportsArray["Utilization"] = [
                "pagetitle" => "Utilization",
                "active"    => false,
                "url"       => $this->view->url([], 'assessment.report-utilization')
            ];
        }

        $this->view->availableReports = $availableReportsArray;
    }

    /**
     * Init function for HTML reports
     *
     * @throws Exception
     */
    public function initHtmlReport ()
    {
        $this->view->headScript()->appendFile($this->view->baseUrl('/js/app/legacy/HtmlReport.js'));
        if ($this->getAssessment()->id < 1)
        {
            $this->_flashMessenger->addMessage(["error" => "Please select a report first."]);

            // Send user to the index
            $this->redirectToRoute([], 'assessment');
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
        $this->view->publicFileName      = $this->_relativeCachePath . "/" . $filename;
        $this->view->savePath            = $this->_fullCachePath . "/" . $filename;
        $this->view->dealerLogoFile      = $this->getDealerLogoFile();
        $this->view->assessmentViewModel = $this->getAssessmentViewModel();
    }

    /**
     * Gets the proposal object for reports to use
     *
     * @throws Zend_Exception
     * @return Assessment_ViewModel_Assessment
     */
    public function getAssessmentViewModel ()
    {
        if (!$this->_assessmentViewModel)
        {
            $this->_assessmentViewModel = false;
            $hasError                   = false;
            try
            {
                $this->_assessmentViewModel = new Assessment_ViewModel_Assessment($this->getAssessment());

                if ($this->_assessmentViewModel->getDevices()->allIncludedDeviceInstances->getCount() < 1)
                {
                    $this->view->ErrorMessages = ["All uploaded printers were excluded from your report. Reports can not be generated until at least 1 printer is added."];
                    $hasError                  = true;
                }
            }
            catch (Exception $e)
            {
                $this->view->ErrorMessages = ["There was an error getting the reports."];
                throw new Zend_Exception("Error Getting Assessment View Model.", 0, $e);
            }

            if ($hasError)
            {
                $this->_assessmentViewModel = false;
            }
        }

        return $this->_assessmentViewModel;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::postDispatch()
     */
    public function postDispatch ()
    {
        $stage = ($this->getAssessment()->stepName) ?: AssessmentStepsModel::STEP_SURVEY;
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
     * Gets the assessment we're working on
     *
     * @return AssessmentModel
     */
    public function getAssessment ()
    {
        if (!isset($this->_assessment))
        {
            if (isset($this->_mpsSession->assessmentId) && $this->_mpsSession->assessmentId > 0)
            {
                $this->_assessment = AssessmentMapper::getInstance()->find($this->_mpsSession->assessmentId);
            }
            else
            {
                $this->_assessment               = new AssessmentModel();
                $this->_assessment->dateCreated  = date('Y-m-d H:i:s');
                $this->_assessment->lastModified = date('Y-m-d H:i:s');
                $this->_assessment->reportDate   = date('Y-m-d H:i:s');
                $this->_assessment->dealerId     = $this->_identity->dealerId;
                $this->_assessment->rmsUploadId  = $this->getSelectedUpload()->id;
                $this->_assessment->name         = "Assessment " . date('Y/m/d');
                $this->_assessment->clientId     = $this->_mpsSession->selectedClientId;
                AssessmentMapper::getInstance()->insert($this->_assessment);
                $this->_mpsSession->assessmentId = $this->_assessment->id;
            }
        }

        return $this->_assessment;
    }

    /**
     * Saves an assessment
     */
    public function saveAssessment ()
    {
        if (isset($this->_mpsSession->assessmentId) && $this->_mpsSession->assessmentId > 0)
        {
            // Update the last modified date
            $this->_assessment->lastModified = date('Y-m-d H:i:s');
            AssessmentMapper::getInstance()->save($this->_assessment);
        }
        else
        {
            $this->_mpsSession->assessmentId = $this->_assessment->id;
        }
    }


    /**
     * Updates an assessment to be at the next available step
     *
     * @param bool $force Whether or not to force the update
     */
    public function updateAssessmentStepName ($force = false)
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
                    $this->getAssessment()->stepName = $this->_navigation->activeStep->nextStep->enumValue;
                }
            }
        }
    }
}
