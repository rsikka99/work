<?php
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentStepsModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\HardwareOptimizationMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationStepsModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\ViewModels\OptimizationViewModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;

/**
 * Class Hardwareoptimization_Library_Controller_Action
 */
class Hardwareoptimization_Library_Controller_Action extends My_Controller_Report
{

    /**
     * @var HardwareOptimizationModel
     */
    protected $_hardwareOptimization;

    /**
     * @var OptimizationViewModel
     */
    protected $_optimizationViewModel;

    /**
     * The navigation steps
     *
     * @var HardwareOptimizationStepsModel
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
    protected $_firstStepName = HardwareOptimizationStepsModel::STEP_OPTIMIZE;

    /**
     * Called from the constructor as the final step of initialization
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::init()
     */
    public function init ()
    {
        parent::init();

        $this->_navigation = HardwareOptimizationStepsModel::getInstance();
        $this->_dealerId   = Zend_Auth::getInstance()->getIdentity()->dealerId;

        if (!My_Feature::canAccess(My_Feature::HARDWARE_OPTIMIZATION))
        {
            $this->_flashMessenger->addMessage([
                "error" => "You do not have permission to access this."
            ]);

            $this->redirectToRoute('hardwareoptimization');
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

//        MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel::setESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE(6 / 100);
//        MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel::setESTIMATED_PAGE_COVERAGE_COLOR(24 / 100);
//         Gross Margin Report Page Coverage
//        MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel::setACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE(6 / 100);
//        MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel::setACTUAL_PAGE_COVERAGE_COLOR(24 / 100);

        $this->view->ReportAbsoluteCachePath = $this->_fullCachePath;
        $this->view->ReportCachePath         = $this->_relativeCachePath;
    }

    public function initReportList ()
    {
        // This is a list of reports that we can view.
        $this->view->availableReports = [
            "Reports"              => [
                "pagetitle" => "Select a report...",
                "active"    => false,
                "url"       => $this->view->baseUrl('/hardwareoptimization/report-index')
            ],
            "CustomerOptimization" => [
                "pagetitle" => "Customer Optimization",
                "active"    => false,
                "url"       => $this->view->baseUrl('/hardwareoptimization/report-customer-optimization')
            ],
            "DealerOptimization"   => [
                "pagetitle" => "Dealer Optimization",
                "active"    => false,
                "url"       => $this->view->baseUrl('/hardwareoptimization/report-dealer-optimization')
            ],
        ];
    }

    /**
     * Init function for HTML reports
     *
     * @throws Exception
     */
    public function initHtmlReport ()
    {
        $this->view->headScript()->appendFile($this->view->baseUrl('/js/app/legacy/HtmlReport.js'));

        if ($this->getHardwareOptimization()->id < 1)
        {
            $this->_flashMessenger->addMessage(["error" => "Please select a report first."]);

            // Send user to the index
            $this->redirectToRoute('hardwareoptimization');
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
        $stage = ($this->getHardwareOptimization()->stepName) ?: $this->_firstStepName;
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
     * Gets the hardware optimization we're working on
     *
     * @return HardwareOptimizationModel
     */
    public function getHardwareOptimization ()
    {
        if (!isset($this->_hardwareOptimization))
        {
            if (isset($this->getMpsSession()->hardwareOptimizationId) && $this->getMpsSession()->hardwareOptimizationId > 0)
            {
                $this->_hardwareOptimization = HardwareOptimizationMapper::getInstance()->find($this->getMpsSession()->hardwareOptimizationId);
            }
            else
            {
                $this->_hardwareOptimization               = new HardwareOptimizationModel();
                $this->_hardwareOptimization->dateCreated  = date('Y-m-d H:i:s');
                $this->_hardwareOptimization->lastModified = date('Y-m-d H:i:s');
                $this->_hardwareOptimization->name         = "Hardware Optimization " . date('Y/m/d');
                $this->_hardwareOptimization->dealerId     = $this->getIdentity()->dealerId;
                $this->_hardwareOptimization->clientId     = $this->getSelectedClient()->id;
                $this->_hardwareOptimization->rmsUploadId  = $this->getSelectedUpload()->id;

                HardwareOptimizationMapper::getInstance()->insert($this->_hardwareOptimization);
                $this->getMpsSession()->hardwareOptimizationId = $this->_hardwareOptimization->id;
            }
        }

        return $this->_hardwareOptimization;
    }

    /**
     * Gets the optimization view model
     *
     * @return \MPSToolbox\Legacy\Modules\HardwareOptimization\ViewModels\OptimizationViewModel
     */
    public function getOptimizationViewModel ()
    {
        if (!isset($this->_optimizationViewModel))
        {
            $this->_optimizationViewModel = new OptimizationViewModel($this->_hardwareOptimization);
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
            HardwareOptimizationMapper::getInstance()->save($this->_hardwareOptimization);
        }
        else
        {
            HardwareOptimizationMapper::getInstance()->insert($this->_hardwareOptimization);
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
