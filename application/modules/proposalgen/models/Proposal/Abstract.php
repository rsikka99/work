<?php
class Proposalgen_Model_Proposal_Abstract
{
    /**
     * @var Proposalgen_Model_Proposal_Devices
     */
    protected $_devices;

    /**
     * @var Proposalgen_Model_Report
     */
    public $report;

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
    protected $PageCoverageBlackAndWhite;
    protected $PageCoverageColor;
    protected $ReportQuestions;


    /**
     * Constructor
     *
     * @param int|Proposalgen_Model_Report $report The report model, or the id of our report
     */
    public function __construct ($report)
    {
        if ($report instanceof Proposalgen_Model_Report)
        {
            $this->report = $report;
        }
        else
        {
            $this->report = Proposalgen_Model_Mapper_Report::getInstance()->find($report);
        }
    }

    /**
     * Gets the devices object for the report.
     *
     * @return Proposalgen_Model_Proposal_Devices
     */
    public function getDevices ()
    {
        if (!isset($this->_devices))
        {
            $this->_devices = new Proposalgen_Model_Proposal_Devices($this->report);
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
            $this->_costPerPageSettingForCustomer                         = new Proposalgen_Model_CostPerPageSetting();
            $reportSettings                                               = $this->report->getReportSettings();
            $this->_costPerPageSettingForCustomer->adminCostPerPage       = $reportSettings->adminCostPerPage;
            $this->_costPerPageSettingForCustomer->pageCoverageColor      = $this->getPageCoverageColor();
            $this->_costPerPageSettingForCustomer->pageCoverageMonochrome = $this->getPageCoverageBlackAndWhite();
            $this->_costPerPageSettingForCustomer->pricingConfiguration   = $reportSettings->getAssessmentPricingConfig();
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

            $reportSettings                                             = $this->report->getReportSettings();
            $this->_costPerPageSettingForDealer->adminCostPerPage       = $reportSettings->adminCostPerPage;
            $this->_costPerPageSettingForDealer->pageCoverageColor      = $reportSettings->actualPageCoverageColor;
            $this->_costPerPageSettingForDealer->pageCoverageMonochrome = $reportSettings->actualPageCoverageMono;
            $this->_costPerPageSettingForDealer->pricingConfiguration   = $reportSettings->getGrossMarginPricingConfig();
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
            $this->PageCoverageBlackAndWhite = $this->report->getSurvey()->pageCoverageMonochrome;
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
            $this->PageCoverageColor = $this->report->getSurvey()->pageCoverageColor;
        }

        return $this->PageCoverageColor;
    }
}