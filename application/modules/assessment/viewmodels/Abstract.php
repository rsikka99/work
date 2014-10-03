<?php

/**
 * Class Assessment_ViewModel_Abstract
 */
class Assessment_ViewModel_Abstract
{
    /**
     * @var Assessment_ViewModel_Devices
     */
    protected $_devices;

    /**
     * @var Assessment_Model_Assessment
     */
    public $assessment;

    /**
     * The cost page setting when displaying numbers to a customer
     *
     * @var Proposalgen_Model_CostPerPageSetting
     */
    protected $_costPerPageSettingForCustomer;
    /**
     * The cost page setting when displaying numbers to a dealer
     *
     * @var Proposalgen_Model_CostPerPageSetting
     */
    protected $_costPerPageSettingForDealer;
    /**
     * The cost page setting when selecting replacement devices
     *
     * @var Proposalgen_Model_CostPerPageSetting
     */
    protected $_costPerPageSettingForReplacements;

    protected $PageCoverageBlackAndWhite;
    protected $PageCoverageColor;
    protected $ReportQuestions;


    /**
     * Constructor
     *
     * @param int|Assessment_Model_Assessment $report The report model, or the id of our report
     */
    public function __construct ($report)
    {
        if ($report instanceof Assessment_Model_Assessment)
        {
            $this->assessment = $report;
        }
        else
        {
            $this->assessment = Assessment_Model_Mapper_Assessment::getInstance()->find($report);
        }
    }

    /**
     * Gets the devices object for the report.
     *
     * @return Assessment_ViewModel_Devices
     */
    public function getDevices ()
    {
        if (!isset($this->_devices))
        {
            $this->_devices = new Assessment_ViewModel_Devices($this->assessment->rmsUploadId, $this->assessment->getAssessmentSettings()->laborCostPerPage, $this->assessment->getAssessmentSettings()->partsCostPerPage, $this->assessment->getAssessmentSettings()->adminCostPerPage);
        }

        return $this->_devices;
    }

    /**
     * Gets the cost per page settings for the customers point of view
     *
     * @return Proposalgen_Model_CostPerPageSetting
     */
    public function getCostPerPageSettingForCustomer ()
    {
        if (!isset($this->_costPerPageSettingForCustomer))
        {
            $this->_costPerPageSettingForCustomer                                          = new Proposalgen_Model_CostPerPageSetting();
            $assessmentSettings                                                            = $this->assessment->getAssessmentSettings();
            $this->_costPerPageSettingForCustomer->adminCostPerPage                        = $assessmentSettings->adminCostPerPage;
            $this->_costPerPageSettingForCustomer->pageCoverageColor                       = $this->getPageCoverageColor();
            $this->_costPerPageSettingForCustomer->pageCoverageMonochrome                  = $this->getPageCoverageBlackAndWhite();
            $this->_costPerPageSettingForCustomer->monochromeTonerRankSet                  = $assessmentSettings->getCustomerMonochromeRankSet();
            $this->_costPerPageSettingForCustomer->colorTonerRankSet                       = $assessmentSettings->getCustomerColorRankSet();
            $this->_costPerPageSettingForCustomer->useDevicePageCoverages                  = $assessmentSettings->useDevicePageCoverages;
            $this->_costPerPageSettingForCustomer->pricingMargin                           = $assessmentSettings->assessmentReportMargin;
            $this->_costPerPageSettingForCustomer->useCustomerCostPerPageForManagedDevices = true;
        }

        return $this->_costPerPageSettingForCustomer;
    }

    /**
     * Gets the cost per page settings for the dealers point of view
     *
     * @return Proposalgen_Model_CostPerPageSetting
     */
    public function getCostPerPageSettingForDealer ()
    {
        if (!isset($this->_costPerPageSettingForDealer))
        {
            $this->_costPerPageSettingForDealer = new Proposalgen_Model_CostPerPageSetting();

            $assessmentSettings                                         = $this->assessment->getAssessmentSettings();
            $this->_costPerPageSettingForDealer->adminCostPerPage       = $assessmentSettings->adminCostPerPage;
            $this->_costPerPageSettingForDealer->partsCostPerPage       = $assessmentSettings->partsCostPerPage;
            $this->_costPerPageSettingForDealer->laborCostPerPage       = $assessmentSettings->laborCostPerPage;
            $this->_costPerPageSettingForDealer->pageCoverageColor      = $assessmentSettings->actualPageCoverageColor;
            $this->_costPerPageSettingForDealer->pageCoverageMonochrome = $assessmentSettings->actualPageCoverageMono;
            $this->_costPerPageSettingForDealer->monochromeTonerRankSet = $assessmentSettings->getDealerMonochromeRankSet();
            $this->_costPerPageSettingForDealer->colorTonerRankSet      = $assessmentSettings->getDealerColorRankSet();
            $this->_costPerPageSettingForDealer->useDevicePageCoverages = $assessmentSettings->useDevicePageCoverages;
        }

        return $this->_costPerPageSettingForDealer;
    }

    /**
     * @return float
     */
    public function getPageCoverageBlackAndWhite ()
    {
        if (!isset($this->PageCoverageBlackAndWhite))
        {
            $this->PageCoverageBlackAndWhite = $this->assessment->getSurvey()->pageCoverageMonochrome;
        }

        return $this->PageCoverageBlackAndWhite;
    }

    /**
     * @return float
     */
    public function getPageCoverageColor ()
    {
        if (!isset($this->PageCoverageColor))
        {
            $this->PageCoverageColor = $this->assessment->getSurvey()->pageCoverageColor;
        }

        return $this->PageCoverageColor;
    }
}