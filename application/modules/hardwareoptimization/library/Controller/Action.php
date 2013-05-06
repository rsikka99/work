<?php
/**
 * Class Hardwareoptimization_Library_Controller_Action
 */
class Hardwareoptimization_Library_Controller_Action extends My_Controller_Report
{

    /**
     * @var Hardwareoptimization_Model_Hardware_Optimization
     */
    protected $_hardwareOptimization;

    /**
     * @var Hardwareoptimization_ViewModel_Optimization
     */
    protected $_optimizationViewModel;

    /**
     * The navigation steps
     *
     * @var Hardwareoptimization_Model_Hardware_Optimization_Steps
     */
    protected $_navigation;

    /**
     * An object containing various word styles
     *
     * @var stdClass
     */
    protected $_wordStyles;

    /**
     * Number representing the dealer id
     *
     * @var int
     */
    protected $_dealerId;

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

        $this->_navigation = Hardwareoptimization_Model_Hardware_Optimization_Steps::getInstance();
        $this->_dealerId   = Zend_Auth::getInstance()->getIdentity()->dealerId;


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

        $this->_fullCachePath     = PUBLIC_PATH . "/cache/reports/hardwareoptimization/" . $this->getHardwareOptimization()->id;
        $this->_relativeCachePath = "/cache/reports/hardwareoptimization/" . $this->_hardwareOptimization->id;

        // Make the directory if it doesn't exist
        if (!is_dir($this->_fullCachePath))
        {
            if (!mkdir($this->_fullCachePath, 0777, true))
            {
                throw new Exception("Could not open cache folder! PATH:" . $this->_fullCachePath, 0);
            }
        }

//        Proposalgen_Model_Toner::setESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE(6 / 100);
//        Proposalgen_Model_Toner::setESTIMATED_PAGE_COVERAGE_COLOR(24 / 100);
//         Gross Margin Report Page Coverage
//        Proposalgen_Model_Toner::setACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE(6 / 100);
//        Proposalgen_Model_Toner::setACTUAL_PAGE_COVERAGE_COLOR(24 / 100);

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
                "url"       => $this->view->baseUrl('/hardwareoptimization/report_index/index')
            ),
            "CustomerOptimization" => array(
                "pagetitle" => "Customer Optimization",
                "active"    => false,
                "url"       => $this->view->baseUrl('/hardwareoptimization/report_customer_optimization/index')
            ),
            "DealerOptimization"   => array(
                "pagetitle" => "Dealer Optimization",
                "active"    => false,
                "url"       => $this->view->baseUrl('/hardwareoptimization/report_dealer_optimization/index')
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

        if ($this->getHardwareOptimization()->id < 1)
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
        $this->view->publicFileName       = $this->_relativeCachePath . "/" . $filename;
        $this->view->savePath             = $this->_fullCachePath . "/" . $filename;
        $this->view->dealerLogoFile       = $this->getDealerLogoFile();
        $this->view->optimization         = $this->getOptimizationViewModel();
        $this->view->hardwareOptimization = $this->_hardwareOptimization;
    }


    /**
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::postDispatch()
     */
    public function postDispatch ()
    {
        $stage = ($this->getHardwareOptimization()->stepName) ? : $this->_firstStepName;
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
     * Gets the hardware optimization we're working on
     *
     * @return Hardwareoptimization_Model_Hardware_Optimization
     */
    public function getHardwareOptimization ()
    {
        if (!isset($this->_hardwareOptimization))
        {
            if (isset($this->_mpsSession->hardwareOptimizationId) && $this->_mpsSession->hardwareOptimizationId > 0)
            {
                $this->_hardwareOptimization = Hardwareoptimization_Model_Mapper_Hardware_Optimization::getInstance()->find($this->_mpsSession->hardwareOptimizationId);
            }
            else
            {
                $this->_hardwareOptimization               = new Hardwareoptimization_Model_Hardware_Optimization();
                $this->_hardwareOptimization->dateCreated  = date('Y-m-d H:i:s');
                $this->_hardwareOptimization->lastModified = date('Y-m-d H:i:s');
                $this->_hardwareOptimization->dealerId     = $this->_identity->dealerId;
                $this->_hardwareOptimization->clientId     = $this->_mpsSession->selectedClientId;
            }
        }

        return $this->_hardwareOptimization;
    }

    /**
     * Gets the optimization view model
     *
     * @return \Hardwareoptimization_ViewModel_Optimization
     */
    public function getOptimizationViewModel ()
    {
        if (!isset($this->_optimizationViewModel))
        {
            $this->_optimizationViewModel = new Hardwareoptimization_ViewModel_Optimization($this->_hardwareOptimization);
        }

        return $this->_optimizationViewModel;
    }


    /**
     * Saves a hardware optimization
     */
    public function saveHardwareOptimization ()
    {
        if (isset($this->_mpsSession->hardwareOptimizationId) && $this->_mpsSession->hardwareOptimizationId > 0)
        {
            // Update the last modified date
            $this->_hardwareOptimization->lastModified = date('Y-m-d H:i:s');
            Hardwareoptimization_Model_Mapper_Hardware_Optimization::getInstance()->save($this->_hardwareOptimization);
        }
        else
        {
            $this->_hardwareOptimization->hardwareOptimizationSettingId = Hardwareoptimization_Model_Mapper_Hardware_Optimization_Setting::getInstance()->insert(new Hardwareoptimization_Model_Hardware_Optimization_Setting());
            Hardwareoptimization_Model_Mapper_Hardware_Optimization::getInstance()->insert($this->_hardwareOptimization);
            $this->_mpsSession->hardwareOptimizationId = $this->_hardwareOptimization->id;
        }
    }

    /**
     * Updates an hardware optimization to be at the next available step
     *
     * @param bool $force Whether or not to force the update
     */
    public function updateStepName ($force = false)
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
                    $this->getHardwareOptimization()->stepName = $this->_navigation->activeStep->nextStep->enumValue;
                }
            }
        }
    }
}
