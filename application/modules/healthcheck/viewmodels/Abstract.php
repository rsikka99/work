<?php
use MPSToolbox\Legacy\Modules\HealthCheck\Mappers\HealthCheckMapper;
use MPSToolbox\Legacy\Modules\HealthCheck\Models\HealthCheckModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;

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
     * @var HealthCheckModel
     */
    public $healthcheck;

    /**
     * The cost page setting when displaying numbers to a customer
     *
     * @var CostPerPageSettingModel
     */
    protected $_costPerPageSettingForCustomer;

    /**
     * The cost page setting when selecting replacement devices
     *
     * @var CostPerPageSettingModel
     */
    protected $_costPerPageSettingForReplacements;

    protected $PageCoverageBlackAndWhite;
    protected $PageCoverageColor;
    protected $ReportQuestions;


    /**
     * Constructor
     *
     * @param int|HealthCheckModel $report The report model, or the id of our report
     */
    public function __construct ($report)
    {
        if ($report instanceof HealthCheckModel)
        {
            $this->healthcheck = $report;
        }
        else
        {
            $this->healthcheck = HealthCheckMapper::getInstance()->find($report);
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
            $this->_devices = new Healthcheck_ViewModel_Devices(
                $this->healthcheck->rmsUploadId,
                $this->healthcheck->getClient()->getClientSettings()->currentFleetSettings->defaultMonochromeLaborCostPerPage,
                $this->healthcheck->getClient()->getClientSettings()->currentFleetSettings->defaultMonochromePartsCostPerPage,
                $this->healthcheck->getClient()->getClientSettings()->currentFleetSettings->adminCostPerPage
            );
        }

        return $this->_devices;
    }

    /**
     * Gets the cost per page settings for the customers point of view
     *
     * @return CostPerPageSettingModel
     */
    public function getCostPerPageSettingForCustomer ()
    {
        if (!isset($this->_costPerPageSettingForCustomer))
        {
            $this->_costPerPageSettingForCustomer = new CostPerPageSettingModel();

            $clientSettings = $this->healthcheck->getClient()->getClientSettings();

            $this->_costPerPageSettingForCustomer->adminCostPerPage       = $clientSettings->currentFleetSettings->adminCostPerPage;
            $this->_costPerPageSettingForCustomer->pageCoverageMonochrome = $clientSettings->currentFleetSettings->defaultMonochromeCoverage;
            $this->_costPerPageSettingForCustomer->pageCoverageColor      = $clientSettings->currentFleetSettings->defaultColorCoverage;

            $this->_costPerPageSettingForCustomer->monochromeTonerRankSet        = $clientSettings->currentFleetSettings->getMonochromeRankSet();
            $this->_costPerPageSettingForCustomer->colorTonerRankSet             = $clientSettings->currentFleetSettings->getColorRankSet();
            $this->_costPerPageSettingForCustomer->useDevicePageCoverages        = $clientSettings->currentFleetSettings->useDevicePageCoverages;
            $this->_costPerPageSettingForCustomer->customerMonochromeCostPerPage = $clientSettings->genericSettings->mpsMonochromeCostPerPage;
            $this->_costPerPageSettingForCustomer->customerColorCostPerPage      = $clientSettings->genericSettings->mpsColorCostPerPage;
            $this->_costPerPageSettingForCustomer->pricingMargin                 = $clientSettings->genericSettings->tonerPricingMargin;

            $this->_costPerPageSettingForCustomer->useCustomerCostPerPageForManagedDevices = true;
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
            $this->PageCoverageBlackAndWhite = $this->healthcheck->getClient()->getClientSettings()->currentFleetSettings->defaultMonochromeCoverage;
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
            $this->PageCoverageColor = $this->healthcheck->getClient()->getClientSettings()->currentFleetSettings->defaultColorCoverage;
        }

        return $this->PageCoverageColor;
    }
}