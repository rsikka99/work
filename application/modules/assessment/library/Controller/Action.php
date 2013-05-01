<?php
/**
 * Class Assessment_Library_Controller_Action
 */
class Assessment_Library_Controller_Action extends My_Controller_Report
{
    /**
     * @var Assessment_Model_Assessment
     */
    protected $_assessment;

    /**
     * The navigation steps
     *
     * @var Assessment_Model_Assessment_Steps
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
    protected $_firstStepName = Assessment_Model_Assessment_Steps::STEP_FLEET_UPLOAD;

    /**
     * Called from the constructor as the final step of initialization
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::init()
     */
    public function init ()
    {
        parent::init();

        $this->_navigation = Assessment_Model_Assessment_Steps::getInstance();

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
        $this->view->availableReports = array(
            "Reports"              => array(
                "pagetitle" => "Select a report...",
                "active"    => false,
                "url"       => $this->view->baseUrl('/assessment/report_index/index')
            ),
            "Assessment"           => array(
                "pagetitle" => "Assessment",
                "active"    => false,
                "url"       => $this->view->baseUrl('/assessment/report_assessment/index')
            ),
            "CustomerCostAnalysis" => array(
                "pagetitle" => "Customer Cost Analysis",
                "active"    => false,
                "url"       => $this->view->baseUrl('/assessment/report_costanalysis/index')
            ),
            "GrossMargin"          => array(
                "pagetitle" => "Gross Margin",
                "active"    => false,
                "url"       => $this->view->baseUrl('/assessment/report_grossmargin/index')
            ),
            "JITSupplyAndTonerSku" => array(
                "pagetitle" => "JIT Supply and Toner SKU Report",
                "active"    => false,
                "url"       => $this->view->baseUrl('/assessment/report_toners/index')
            ),
            "OldDeviceList"        => array(
                "pagetitle" => "Old Device List",
                "active"    => false,
                "url"       => $this->view->baseUrl('/assessment/report_olddevicelist/index')
            ),
            "PrintingDeviceList"   => array(
                "pagetitle" => "Printing Device List",
                "active"    => false,
                "url"       => $this->view->baseUrl('/assessment/report_printingdevicelist/index')
            ),
            "Solution"             => array(
                "pagetitle" => "Solution",
                "active"    => false,
                "url"       => $this->view->baseUrl('/assessment/report_solution/index')
            ),
        );
    }

    /**
     * Init function for html reports
     *
     * @throws Exception
     */
    public function initHtmlReport ()
    {
        $this->view->headScript()->appendFile($this->view->baseUrl('/js/htmlReport.js'));
        if ($this->getAssessment()->id < 1)
        {
            $this->_flashMessenger->addMessage(array("error" => "Please select a report first."));

            // Send user to the index
            $this->redirector('index', 'index', 'index');
        }

        $this->view->dealerLogoFile = $this->getDealerLogoFile();
    }

    /**
     * Prepares the view (for html reports) with the variables needed.
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
                    $this->view->ErrorMessages [] = "All uploaded printers were excluded from your report. Reports can not be generated until at least 1 printer is added.";
                    $hasError                     = true;
                }
            }
            catch (Exception $e)
            {
                $this->view->ErrorMessages [] = "There was an error getting the reports.";
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
        $stage = ($this->getAssessment()->stepName) ? : Assessment_Model_Assessment_Steps::STEP_FLEET_UPLOAD;
        $this->_navigation->updateAccessibleSteps($stage);

        $this->view->placeholder('ProgressionNav')->set($this->view->NavigationMenu($this->_navigation->steps));
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
     * @return Assessment_Model_Assessment
     */
    public function getAssessment ()
    {
        if (!isset($this->_assessment))
        {
            if (isset($this->_mpsSession->assessmentId) && $this->_mpsSession->assessmentId > 0)
            {
                $this->_assessment = Assessment_Model_Mapper_Assessment::getInstance()->find($this->_mpsSession->assessmentId);
            }
            else
            {
                $this->_assessment               = new Assessment_Model_Assessment();
                $this->_assessment->dateCreated  = date('Y-m-d H:i:s');
                $this->_assessment->lastModified = date('Y-m-d H:i:s');
                $this->_assessment->reportDate   = date('Y-m-d H:i:s');
                $this->_assessment->dealerId     = $this->_identity->dealerId;
                $this->_assessment->clientId     = $this->_mpsSession->selectedClientId;
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
            Assessment_Model_Mapper_Assessment::getInstance()->save($this->_assessment);
        }
        else
        {
            $this->_assessment->assessmentSettingId = $this->_assessment->getAssessmentSettings()->id;
            Assessment_Model_Mapper_Assessment::getInstance()->insert($this->_assessment);
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
