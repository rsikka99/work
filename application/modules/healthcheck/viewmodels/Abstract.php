<?php

/**
 * Class Healthcheck_ViewModel_Abstract
 */
class Healthcheck_ViewModel_Abstract
{
    /**
     * @var Healthcheck_ViewModel_Devices
     */
    protected $_devices;

    /**
     * @var Healthcheck_Model_Healthcheck
     */
    public $healthcheck;

    /**
     * The cost page setting when displaying numbers to a customer
     *
     * @var Proposalgen_Model_CostPerPageSetting
     */
    protected $_costPerPageSettingForCustomer;

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
     * @param int|Healthcheck_Model_Healthcheck $report The report model, or the id of our report
     */
    public function __construct ($report)
    {
        if ($report instanceof Healthcheck_Model_Healthcheck)
        {
            $this->healthcheck = $report;
        }
        else
        {
            $this->healthcheck = Healthcheck_Model_Mapper_Healthcheck::getInstance()->find($report);
        }
    }

    /**
     * Gets the devices object for the report.
     *
     * @return Healthcheck_ViewModel_Devices
     */
    public function getDevices ()
    {
        if (!isset($this->_devices))
        {
            $this->_devices = new Healthcheck_ViewModel_Devices($this->healthcheck);
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
            $this->_costPerPageSettingForCustomer                                = new Proposalgen_Model_CostPerPageSetting();
            $healthcheckSettings                                                 = $this->healthcheck->getHealthcheckSettings();
            $this->_costPerPageSettingForCustomer->adminCostPerPage              = $healthcheckSettings->adminCostPerPage;
            $this->_costPerPageSettingForCustomer->pageCoverageColor             = $this->getPageCoverageColor();
            $this->_costPerPageSettingForCustomer->pageCoverageMonochrome        = $this->getPageCoverageBlackAndWhite();
            $this->_costPerPageSettingForCustomer->monochromeTonerRankSet        = $healthcheckSettings->getCustomerMonochromeRankSet();
            $this->_costPerPageSettingForCustomer->colorTonerRankSet             = $healthcheckSettings->getCustomerColorRankSet();
            $this->_costPerPageSettingForCustomer->useDevicePageCoverages        = $healthcheckSettings->useDevicePageCoverages;
            $this->_costPerPageSettingForCustomer->customerMonochromeCostPerPage = $healthcheckSettings->customerMonochromeCostPerPage;
            $this->_costPerPageSettingForCustomer->customerColorCostPerPage      = $healthcheckSettings->customerColorCostPerPage;
        }

        return $this->_costPerPageSettingForCustomer;
    }


    /**
     * @return float
     */
    public function getPageCoverageBlackAndWhite ()
    {
        if (!isset($this->PageCoverageBlackAndWhite))
        {
            $this->PageCoverageBlackAndWhite = $this->healthcheck->getHealthcheckSettings()->pageCoverageMonochrome;
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
            $this->PageCoverageColor = $this->healthcheck->getHealthcheckSettings()->pageCoverageColor;
        }

        return $this->PageCoverageColor;
    }
}