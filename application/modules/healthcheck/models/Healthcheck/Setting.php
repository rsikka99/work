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
     * @var bool
     */
    public $useDevicePageCoverages = 0;

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
     * The margin applied to the Healthcheck
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
     * The Average IT Hourly Rate
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
     * The MPS monochrome cost per page
     *
     * @var float
     */
    public $customerMonochromeCostPerPage;

    /**
     * The MPS color cost per page
     *
     * @var float
     */
    public $customerColorCostPerPage;

    /**
     * @var int
     */
    public $customerMonochromeRankSetId;

    /**
     * @var int
     */
    public $customerColorRankSetId;

    /**
     * @var Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    protected $_customerMonochromeRankSet;

    /**
     * @var Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    protected $_customerColorRankSet;

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
        if (isset($params->useDevicePageCoverages) && !is_null($params->useDevicePageCoverages))
        {
            $this->useDevicePageCoverages = $params->useDevicePageCoverages;
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
        if (isset($params->customerMonochromeCostPerPage) && !is_null($params->customerMonochromeCostPerPage))
        {
            $this->customerMonochromeCostPerPage = $params->customerMonochromeCostPerPage;
        }
        if (isset($params->customerColorCostPerPage) && !is_null($params->customerColorCostPerPage))
        {
            $this->customerColorCostPerPage = $params->customerColorCostPerPage;
        }
        if (isset($params->kilowattsPerHour) && !is_null($params->kilowattsPerHour))
        {
            $this->kilowattsPerHour = $params->kilowattsPerHour;
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
        if (isset($params->customerMonochromeRankSetId) && !is_null($params->customerMonochromeRankSetId))
        {
            $this->customerMonochromeRankSetId = $params->customerMonochromeRankSetId;
        }
        if (isset($params->customerColorRankSetId) && !is_null($params->customerColorRankSetId))
        {
            $this->customerColorRankSetId = $params->customerColorRankSetId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                            => $this->id,
            "pageCoverageMonochrome"        => $this->pageCoverageMonochrome,
            "pageCoverageColor"             => $this->pageCoverageColor,
            "useDevicePageCoverages"        => $this->useDevicePageCoverages,
            "laborCostPerPage"              => $this->laborCostPerPage,
            "partsCostPerPage"              => $this->partsCostPerPage,
            "adminCostPerPage"              => $this->adminCostPerPage,
            "healthcheckMargin"             => $this->healthcheckMargin,
            "monthlyLeasePayment"           => $this->monthlyLeasePayment,
            "defaultPrinterCost"            => $this->defaultPrinterCost,
            "leasedBwCostPerPage"           => $this->leasedBwCostPerPage,
            "leasedColorCostPerPage"        => $this->leasedColorCostPerPage,
            "mpsBwCostPerPage"              => $this->mpsBwCostPerPage,
            "mpsColorCostPerPage"           => $this->mpsColorCostPerPage,
            "customerMonochromeCostPerPage" => $this->customerMonochromeCostPerPage,
            "customerColorCostPerPage"      => $this->customerColorCostPerPage,
            "kilowattsPerHour"              => $this->kilowattsPerHour,
            "averageItHourlyRate"           => $this->averageItHourlyRate,
            "hoursSpentOnIt"                => $this->hoursSpentOnIt,
            "costOfLabor"                   => $this->costOfLabor,
            "costToExecuteSuppliesOrder"    => $this->costToExecuteSuppliesOrder,
            "numberOfSupplyOrdersPerMonth"  => $this->numberOfSupplyOrdersPerMonth,
            "customerMonochromeRankSetId"   => $this->customerMonochromeRankSetId,
            "customerColorRankSetId"        => $this->customerColorRankSetId,
        );
    }

    /**
     * @return array
     */
    public function getTonerRankSets ()
    {
        return array(
            "customerColorRankSetArray"      => $this->getCustomerColorRankSet()->getRanksAsArray(),
            "customerMonochromeRankSetArray" => $this->getCustomerMonochromeRankSet()->getRanksAsArray(),
        );
    }

    /**
     * @return Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    public function getCustomerColorRankSet ()
    {
        if (!isset($this->_customerColorRankSet))
        {
            if ($this->customerColorRankSetId > 0)
            {
                $this->_customerColorRankSet = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance()->find($this->customerColorRankSetId);
            }
            else
            {
                $this->_customerColorRankSet  = new Proposalgen_Model_Toner_Vendor_Ranking_Set();
                $this->customerColorRankSetId = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance()->insert($this->_customerColorRankSet);

                // Update ourselves
                Healthcheck_Model_Mapper_Healthcheck_Setting::getInstance()->save($this);
            }
        }

        return $this->_customerColorRankSet;
    }

    /**
     * @return Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    public function getCustomerMonochromeRankSet ()
    {
        if (!isset($this->_customerMonochromeRankSet))
        {
            if ($this->customerMonochromeRankSetId > 0)
            {
                $this->_customerMonochromeRankSet = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance()->find($this->customerMonochromeRankSetId);
            }
            else
            {
                $this->_customerMonochromeRankSet  = new Proposalgen_Model_Toner_Vendor_Ranking_Set();
                $this->customerMonochromeRankSetId = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance()->insert($this->_customerMonochromeRankSet);

                // Update ourselves
                Healthcheck_Model_Mapper_Healthcheck_Setting::getInstance()->save($this);
            }
        }

        return $this->_customerMonochromeRankSet;
    }
}