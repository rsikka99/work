<?php

/**
 * Class Assessment_Model_Assessment_Setting
 */
class Assessment_Model_Assessment_Setting extends My_Model_Abstract
{
    const SERVICE_BILLING_PREFERENCE_NOT_SET  = null;
    const SERVICE_BILLING_PREFERENCE_PER_PAGE = 1;
    const SERVICE_BILLING_PREFERENCE_MONTHLY  = 2;
    const MIN_VALID_SERVICE_BILLING_VALUE     = 1;
    const MAX_VALID_SERVICE_BILLING_VALUE     = 2;

    static $ServiceBillingPreferenceOptions = array(
        self::SERVICE_BILLING_PREFERENCE_NOT_SET  => "",
        self::SERVICE_BILLING_PREFERENCE_PER_PAGE => "Per Page",
        self::SERVICE_BILLING_PREFERENCE_MONTHLY  => "Monthly"
    );

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
     * The margin applied to the assessment
     *
     * @var int
     */
    public $assessmentReportMargin;

    /**
     * The margin applied to the gross margin
     *
     * @var int
     */
    public $grossMarginReportMargin;

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
     * @var int
     */
    public $dealerMonochromeRankSetId;

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
    protected $_customerColorRankSet;

    /**
     * @var Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    protected $_customerMonochromeRankSet;

    /**
     * @var Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    protected $_dealerColorRankSet;

    /**
     * @var Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    protected $_dealerMonochromeRankSet;

    /**
     * @var int
     */
    public $dealerColorRankSetId;

    /**
     * @var float
     */
    public $costThreshold;

    /**
     * The id of the gross margin pricing configuration
     *
     * @var int
     */
    public $grossMarginPricingConfigId;

    /**
     * @var float
     */
    public $targetMonochromeCostPerPage;

    /**
     * @var float
     */
    public $targetColorCostPerPage;

    /**
     * @var bool
     */
    public $useDevicePageCoverages = 0;

    /**
     * Overrides all the settings.
     * Null values will be excluded.
     *
     * @param array|Assessment_Model_Assessment_Setting $settings These can be either a Assessment_Model_Report_Setting or an array of settings
     */
    public function ApplyOverride ($settings)
    {
        if ($settings instanceof Assessment_Model_Assessment_Setting)
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
        if (isset($params->assessmentReportMargin) && !is_null($params->assessmentReportMargin))
        {
            $this->assessmentReportMargin = $params->assessmentReportMargin;
        }
        if (isset($params->grossMarginReportMargin) && !is_null($params->grossMarginReportMargin))
        {
            $this->grossMarginReportMargin = $params->grossMarginReportMargin;
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
        if (isset($params->targetMonochromeCostPerPage) && !is_null($params->targetMonochromeCostPerPage))
        {
            $this->targetMonochromeCostPerPage = $params->targetMonochromeCostPerPage;
        }
        if (isset($params->targetColorCostPerPage) && !is_null($params->targetColorCostPerPage))
        {
            $this->targetColorCostPerPage = $params->targetColorCostPerPage;
        }
        if (isset($params->costThreshold) && !is_null($params->costThreshold))
        {
            $this->costThreshold = $params->costThreshold;
        }
        if (isset($params->customerColorRankSetId) && !is_null($params->customerColorRankSetId))
        {
            $this->customerColorRankSetId = $params->customerColorRankSetId;
        }
        if (isset($params->customerMonochromeRankSetId) && !is_null($params->customerMonochromeRankSetId))
        {
            $this->customerMonochromeRankSetId = $params->customerMonochromeRankSetId;
        }
        if (isset($params->dealerColorRankSetId) && !is_null($params->dealerColorRankSetId))
        {
            $this->dealerColorRankSetId = $params->dealerColorRankSetId;
        }
        if (isset($params->dealerMonochromeRankSetId) && !is_null($params->dealerMonochromeRankSetId))
        {
            $this->dealerMonochromeRankSetId = $params->dealerMonochromeRankSetId;
        }
        if (isset($params->useDevicePageCoverages) && !is_null($params->useDevicePageCoverages))
        {
            $this->useDevicePageCoverages = $params->useDevicePageCoverages;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                          => $this->id,
            "actualPageCoverageMono"      => $this->actualPageCoverageMono,
            "actualPageCoverageColor"     => $this->actualPageCoverageColor,
            "laborCostPerPage"            => $this->laborCostPerPage,
            "partsCostPerPage"            => $this->partsCostPerPage,
            "adminCostPerPage"            => $this->adminCostPerPage,
            "assessmentReportMargin"      => $this->assessmentReportMargin,
            "grossMarginReportMargin"     => $this->grossMarginReportMargin,
            "monthlyLeasePayment"         => $this->monthlyLeasePayment,
            "defaultPrinterCost"          => $this->defaultPrinterCost,
            "leasedBwCostPerPage"         => $this->leasedBwCostPerPage,
            "leasedColorCostPerPage"      => $this->leasedColorCostPerPage,
            "mpsBwCostPerPage"            => $this->mpsBwCostPerPage,
            "mpsColorCostPerPage"         => $this->mpsColorCostPerPage,
            "kilowattsPerHour"            => $this->kilowattsPerHour,
            "costThreshold"               => $this->costThreshold,
            "targetMonochromeCostPerPage" => $this->targetMonochromeCostPerPage,
            "targetColorCostPerPage"      => $this->targetColorCostPerPage,
            "customerColorRankSetId"      => $this->customerColorRankSetId,
            "customerMonochromeRankSetId" => $this->customerMonochromeRankSetId,
            "dealerColorRankSetId"        => $this->dealerColorRankSetId,
            "dealerMonochromeRankSetId"   => $this->dealerMonochromeRankSetId,
            "useDevicePageCoverages"      => $this->useDevicePageCoverages,
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
            "dealerMonochromeRankSetArray"   => $this->getDealerMonochromeRankSet()->getRanksAsArray(),
            "dealerColorRankSetArray"        => $this->getDealerColorRankSet()->getRanksAsArray(),
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
                Assessment_Model_Mapper_Assessment_Setting::getInstance()->save($this);
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
                Assessment_Model_Mapper_Assessment_Setting::getInstance()->save($this);
            }
        }

        return $this->_customerMonochromeRankSet;
    }


    /**
     * @return Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    public function getDealerMonochromeRankSet ()
    {
        if (!isset($this->_dealerMonochromeRankSet))
        {
            if ($this->dealerMonochromeRankSetId > 0)
            {
                $this->_dealerMonochromeRankSet = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance()->find($this->dealerMonochromeRankSetId);
            }
            else
            {
                $this->_dealerMonochromeRankSet  = new Proposalgen_Model_Toner_Vendor_Ranking_Set();
                $this->dealerMonochromeRankSetId = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance()->insert($this->_dealerMonochromeRankSet);
                Assessment_Model_Mapper_Assessment_Setting::getInstance()->save($this);
            }
        }

        return $this->_dealerMonochromeRankSet;
    }

    /**
     * @return Proposalgen_Model_Toner_Vendor_Ranking_Set
     */
    public function getDealerColorRankSet ()
    {
        if (!isset($this->_dealerColorRankSet))
        {
            if ($this->dealerColorRankSetId > 0)
            {
                $this->_dealerColorRankSet = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance()->find($this->dealerColorRankSetId);
            }
            else
            {
                $this->_dealerColorRankSet  = new Proposalgen_Model_Toner_Vendor_Ranking_Set();
                $this->dealerColorRankSetId = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance()->insert($this->_dealerColorRankSet);
                Assessment_Model_Mapper_Assessment_Setting::getInstance()->save($this);
            }
        }

        return $this->_dealerColorRankSet;
    }
}