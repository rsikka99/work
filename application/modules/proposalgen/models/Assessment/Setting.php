<?php
class Proposalgen_Model_Assessment_Setting extends My_Model_Abstract
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
     * The id of the assessment pricing configuration
     *
     * @var int
     */
    public $assessmentPricingConfigId;

    public $replacementPricingConfigId;

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

    public $targetMonochromeCostPerPage;
    public $targetColorCostPerPage;
    protected $_assessmentPricingConfig;

    /**
     * The gross margin pricing configuration
     *
     * @var Proposalgen_Model_PricingConfig
     */
    protected $_grossMarginPricingConfig;

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
     * @param array|Proposalgen_Model_Assessment_Setting $settings These can be either a Proposalgen_Model_Report_Setting or an array of settings
     */
    public function ApplyOverride ($settings)
    {
        if ($settings instanceof Proposalgen_Model_Assessment_Setting)
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
        if (isset($params->assessmentPricingConfigId) && !is_null($params->assessmentPricingConfigId))
        {
            $this->assessmentPricingConfigId = $params->assessmentPricingConfigId;
        }
        if (isset($params->grossMarginPricingConfigId) && !is_null($params->grossMarginPricingConfigId))
        {
            $this->grossMarginPricingConfigId = $params->grossMarginPricingConfigId;
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
        if (isset($params->replacementPricingConfigId) && !is_null($params->replacementPricingConfigId))
        {
            $this->replacementPricingConfigId = $params->replacementPricingConfigId;
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
            "assessmentPricingConfigId"   => $this->assessmentPricingConfigId,
            "grossMarginPricingConfigId"  => $this->grossMarginPricingConfigId,
            "costThreshold"               => $this->costThreshold,
            "targetMonochromeCostPerPage" => $this->targetMonochromeCostPerPage,
            "targetColorCostPerPage"      => $this->targetColorCostPerPage,
            "replacementPricingConfigId"  => $this->replacementPricingConfigId,
        );
    }

    /**
     * Gets the assessment pricing configuration object
     *
     * @return Proposalgen_Model_PricingConfig
     */
    public function getAssessmentPricingConfig ()
    {
        if (!isset($this->_assessmentPricingConfig))
        {
            $this->_assessmentPricingConfig = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($this->assessmentPricingConfigId);
        }

        return $this->_assessmentPricingConfig;
    }

    public function getReplacementPricingConfig ()
    {
        if (!isset($this->_replacementPricingConfig))
        {
            $this->_replacementPricingConfig = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($this->replacementPricingConfigId);
        }

        return $this->_replacementPricingConfig;
    }

    /**
     * Sets the assessment pricing configuration object
     *
     * @param $AssessmentPricingConfig Proposalgen_Model_PricingConfig
     *                                 The pricing configuration to set
     *
     * @return \Proposalgen_Model_Assessment_Setting
     */
    public function setAssessmentPricingConfig ($AssessmentPricingConfig)
    {
        $this->_assessmentPricingConfig = $AssessmentPricingConfig;

        return $this;
    }

    /**
     * Gets the gross margin pricing configuration object
     *
     * @return Proposalgen_Model_PricingConfig
     */
    public function getGrossMarginPricingConfig ()
    {
        if (!isset($this->_grossMarginPricingConfig))
        {
            $this->_grossMarginPricingConfig = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($this->grossMarginPricingConfigId);
        }

        return $this->_grossMarginPricingConfig;
    }

    /**
     * Sets the gross margin pricing configuration object
     *
     * @param $GrossMarginPricingConfig Proposalgen_Model_PricingConfig
     *                                  The pricing configuration to set
     *
     * @return \Proposalgen_Model_Assessment_Setting
     */
    public function setGrossMarginPricingConfig ($GrossMarginPricingConfig)
    {
        $this->_grossMarginPricingConfig = $GrossMarginPricingConfig;

        return $this;
    }
}