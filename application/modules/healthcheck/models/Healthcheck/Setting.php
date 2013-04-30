<?php
/**
 * Class Healthcheck_Model_Healthcheck_Setting
 */
class Healthcheck_Model_Healthcheck_Setting extends My_Model_Abstract
{

    /**
     * The database id
     *
     * @var int
     */
    public $id;

    /**
     * The actual monochrome page coverage as a whole number
     *
     * @var int
     */
    public $pageCoverageMonochrome;

    /**
     * The actual color page coverage as a whole number
     *
     * @var int
     */
    public $pageCoverageColor;

    /**
     * The actual monochrome page coverage as a whole number
     *
     * @var int
     */
    public $actualPageCoverageMono;

    /**
     * The actual color page coverage as a whole number
     *
     * @var int
     */
    public $actualPageCoverageColor;

    /**
     * The labor cost per page
     *
     * @var int
     */
    public $laborCostPerPage;

    /**
     * The parts cost per page
     *
     * @var int
     */
    public $partsCostPerPage;

    /**
     * The admin cost per page
     *
     * @var int
     */
    public $adminCostPerPage;

    /**
     * The margin applied to the healthcheck
     *
     * @var int
     */
    public $healthcheckMargin;


    /**
     * The monthly lease payment for calculation with leased printers
     *
     * @var int
     */
    public $monthlyLeasePayment;

    /**
     * The default printer cost to use when a printer does not have a cost
     *
     * @var int
     */
    public $defaultPrinterCost;

    /**
     * The monochrome cost per page for a leased printer
     *
     * @var int
     */
    public $leasedBwCostPerPage;

    /**
     * The color cost per page for a leased printer
     *
     * @var int
     */
    public $leasedColorCostPerPage;

    /**
     * The MPS monochrome cost per page
     *
     * @var int
     */
    public $mpsBwCostPerPage;

    /**
     * The MPS color cost per page
     *
     * @var int
     */
    public $mpsColorCostPerPage;

    /**
     * The cost of electricity
     *
     * @var int
     */
    public $kilowattsPerHour;

    /**
     * The Average It Hourly Rate
     */
    public $averageItHourlyRate;

    /**
     *  Hours Spent On IT
     *
     * @var float
     */
    public $hoursSpentOnIt;

    /**
     * Cost Of labor
     *
     * @var float
     */
    public $costOfLabor;

    /**
     * The Cost to Execute Supplies Order
     */
    public $costToExecuteSuppliesOrder;

    /**
     * @var int
     *
     * @var float
     */
    public $numberOfSupplyOrdersPerMonth;

    /**
     * The id of the assessment pricing configuration
     *
     * @var int
     */
    public $healthcheckPricingConfigId;

    protected $_healthcheckPricingConfig;


    /**
     * Pricing config used for designated which tones to use for replacement devices
     *
     * @var Proposalgen_Model_PricingConfig
     */
    protected $_replacementPricingConfig;

    /**
     * Overrides all the settings.
     * Null values will be excluded.
     *
     * @param array|Healthcheck_Model_Healthcheck_Setting $settings These can be either a Proposalgen_Model_Healthcheck_Setting or an array of settings
     */
    public function ApplyOverride ($settings)
    {
        if ($settings instanceof Healthcheck_Model_Healthcheck_Setting)
        {
            $settings = $settings->toArray();
        }

        $this->populate($settings);
    }

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->id) && !is_null($params->id))
        {
            $this->id = $params->id;
        }
        if (isset($params->pageCoverageMonochrome) && !is_null($params->pageCoverageMonochrome))
        {
            $this->pageCoverageMonochrome = $params->pageCoverageMonochrome;
        }
        if (isset($params->pageCoverageColor) && !is_null($params->pageCoverageColor))
        {
            $this->pageCoverageColor = $params->pageCoverageColor;
        }
        if (isset($params->actualPageCoverageMono) && !is_null($params->actualPageCoverageMono))
        {
            $this->actualPageCoverageMono = $params->actualPageCoverageMono;
        }
        if (isset($params->actualPageCoverageColor) && !is_null($params->actualPageCoverageColor))
        {
            $this->actualPageCoverageColor = $params->actualPageCoverageColor;
        }
        if (isset($params->laborCostPerPage) && !is_null($params->laborCostPerPage))
        {
            $this->laborCostPerPage = $params->laborCostPerPage;
        }
        if (isset($params->partsCostPerPage) && !is_null($params->partsCostPerPage))
        {
            $this->partsCostPerPage = $params->partsCostPerPage;
        }
        if (isset($params->adminCostPerPage) && !is_null($params->adminCostPerPage))
        {
            $this->adminCostPerPage = $params->adminCostPerPage;
        }
        if (isset($params->healthcheckMargin) && !is_null($params->healthcheckMargin))
        {
            $this->healthcheckMargin = $params->healthcheckMargin;
        }
        if (isset($params->monthlyLeasePayment) && !is_null($params->monthlyLeasePayment))
        {
            $this->monthlyLeasePayment = $params->monthlyLeasePayment;
        }
        if (isset($params->defaultPrinterCost) && !is_null($params->defaultPrinterCost))
        {
            $this->defaultPrinterCost = $params->defaultPrinterCost;
        }
        if (isset($params->leasedBwCostPerPage) && !is_null($params->leasedBwCostPerPage))
        {
            $this->leasedBwCostPerPage = $params->leasedBwCostPerPage;
        }
        if (isset($params->leasedColorCostPerPage) && !is_null($params->leasedColorCostPerPage))
        {
            $this->leasedColorCostPerPage = $params->leasedColorCostPerPage;
        }
        if (isset($params->mpsBwCostPerPage) && !is_null($params->mpsBwCostPerPage))
        {
            $this->mpsBwCostPerPage = $params->mpsBwCostPerPage;
        }
        if (isset($params->mpsColorCostPerPage) && !is_null($params->mpsColorCostPerPage))
        {
            $this->mpsColorCostPerPage = $params->mpsColorCostPerPage;
        }
        if (isset($params->kilowattsPerHour) && !is_null($params->kilowattsPerHour))
        {
            $this->kilowattsPerHour = $params->kilowattsPerHour;
        }
        if (isset($params->healthcheckPricingConfigId) && !is_null($params->healthcheckPricingConfigId))
        {
            $this->healthcheckPricingConfigId = $params->healthcheckPricingConfigId;
        }
        if (isset($params->averageItHourlyRate) && !is_null($params->averageItHourlyRate))
        {
            $this->averageItHourlyRate = $params->averageItHourlyRate;
        }
        if (isset($params->hoursSpentOnIt) && !is_null($params->hoursSpentOnIt))
        {
            $this->hoursSpentOnIt = $params->hoursSpentOnIt;
        }
        if (isset($params->costOfLabor) && !is_null($params->costOfLabor))
        {
            $this->costOfLabor = $params->costOfLabor;
        }
        if (isset($params->costToExecuteSuppliesOrder) && !is_null($params->costToExecuteSuppliesOrder))
        {
            $this->costToExecuteSuppliesOrder = $params->costToExecuteSuppliesOrder;
        }
        if (isset($params->numberOfSupplyOrdersPerMonth) && !is_null($params->numberOfSupplyOrdersPerMonth))
        {
            $this->numberOfSupplyOrdersPerMonth = $params->numberOfSupplyOrdersPerMonth;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                           => $this->id,
            "pageCoverageMonochrome"       => $this->pageCoverageMonochrome,
            "pageCoverageColor"            => $this->pageCoverageColor,
            "actualPageCoverageMono"       => $this->actualPageCoverageMono,
            "actualPageCoverageColor"      => $this->actualPageCoverageColor,
            "laborCostPerPage"             => $this->laborCostPerPage,
            "partsCostPerPage"             => $this->partsCostPerPage,
            "adminCostPerPage"             => $this->adminCostPerPage,
            "healthcheckMargin"            => $this->healthcheckMargin,
            "monthlyLeasePayment"          => $this->monthlyLeasePayment,
            "defaultPrinterCost"           => $this->defaultPrinterCost,
            "leasedBwCostPerPage"          => $this->leasedBwCostPerPage,
            "leasedColorCostPerPage"       => $this->leasedColorCostPerPage,
            "mpsBwCostPerPage"             => $this->mpsBwCostPerPage,
            "mpsColorCostPerPage"          => $this->mpsColorCostPerPage,
            "kilowattsPerHour"             => $this->kilowattsPerHour,
            "healthcheckPricingConfigId"   => $this->healthcheckPricingConfigId,
            "averageItHourlyRate"          => $this->averageItHourlyRate,
            "hoursSpentOnIt"               => $this->hoursSpentOnIt,
            "costOfLabor"                  => $this->costOfLabor,
            "costToExecuteSuppliesOrder"   => $this->costToExecuteSuppliesOrder,
            "numberOfSupplyOrdersPerMonth" => $this->numberOfSupplyOrdersPerMonth,
        );
    }

    /**
     * Gets the healthcheck pricing configuration object
     *
     * @return Proposalgen_Model_PricingConfig
     */
    public function getHealthcheckPricingConfig ()
    {
        if (!isset($this->_healthcheckPricingConfig))
        {
            $this->_healthcheckPricingConfig = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($this->healthcheckPricingConfigId);
        }

        return $this->_healthcheckPricingConfig;
    }


    /**
     * Sets the healthcheck pricing configuration object
     *
     * @param $HealthcheckPricingConfig Proposalgen_Model_PricingConfig
     *                                  The pricing configuration to set
     *
     * @return $this
     */
    public function setHealthcheckPricingConfig ($HealthcheckPricingConfig)
    {
        $this->_healthcheckPricingConfig = $HealthcheckPricingConfig;

        return $this;
    }
}