<?php
class Proposalgen_Model_Proposal_Abstract
{
    /**
     * @var Proposalgen_Model_Proposal_Devices
     */
    protected $_devices;

    /**
     * @var Proposalgen_Model_Assessment
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
     * @param int|Proposalgen_Model_Assessment $report The report model, or the id of our report
     */
    public function __construct ($report)
    {
        if ($report instanceof Proposalgen_Model_Assessment)
        {
            $this->report = $report;
        }
        else
        {
            $this->report = Proposalgen_Model_Mapper_Assessment::getInstance()->find($report);
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
     * Gets the cost per page settings for replacement devices
     *
     * @return Proposalgen_Model_CostPerPageSetting
     */
    public function getCostPerPageSettingForReplacements ()
    {

        if (!isset($this->_costPerPageSettingForReplacements))
        {
            $this->_costPerPageSettingForReplacements = new Proposalgen_Model_CostPerPageSetting();

            $reportSettings                                                   = $this->report->getReportSettings();
            $this->_costPerPageSettingForReplacements->adminCostPerPage       = $reportSettings->adminCostPerPage;
            $this->_costPerPageSettingForReplacements->pageCoverageColor      = $reportSettings->actualPageCoverageColor;
            $this->_costPerPageSettingForReplacements->pageCoverageMonochrome = $reportSettings->actualPageCoverageMono;
            $this->_costPerPageSettingForReplacements->pricingConfiguration   = $reportSettings->getReplacementPricingConfig();
        }

        return $this->_costPerPageSettingForReplacements;
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