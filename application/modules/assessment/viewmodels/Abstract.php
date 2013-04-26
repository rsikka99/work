<?php
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
            $this->_devices = new Assessment_ViewModel_Devices($this->assessment);
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
            $assessmentSettings                                           = $this->assessment->getAssessmentSettings();
            $this->_costPerPageSettingForCustomer->adminCostPerPage       = $assessmentSettings->adminCostPerPage;
            $this->_costPerPageSettingForCustomer->pageCoverageColor      = $this->getPageCoverageColor();
            $this->_costPerPageSettingForCustomer->pageCoverageMonochrome = $this->getPageCoverageBlackAndWhite();
            $this->_costPerPageSettingForCustomer->pricingConfiguration   = $assessmentSettings->getAssessmentPricingConfig();
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
            $this->_costPerPageSettingForDealer->pageCoverageColor      = $assessmentSettings->actualPageCoverageColor;
            $this->_costPerPageSettingForDealer->pageCoverageMonochrome = $assessmentSettings->actualPageCoverageMono;
            $this->_costPerPageSettingForDealer->pricingConfiguration   = $assessmentSettings->getGrossMarginPricingConfig();
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

            $assessmentSettings                                               = $this->assessment->getAssessmentSettings();
            $this->_costPerPageSettingForReplacements->adminCostPerPage       = $assessmentSettings->adminCostPerPage;
            $this->_costPerPageSettingForReplacements->pageCoverageColor      = $assessmentSettings->actualPageCoverageColor;
            $this->_costPerPageSettingForReplacements->pageCoverageMonochrome = $assessmentSettings->actualPageCoverageMono;
            $this->_costPerPageSettingForReplacements->pricingConfiguration   = $assessmentSettings->getReplacementPricingConfig();
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