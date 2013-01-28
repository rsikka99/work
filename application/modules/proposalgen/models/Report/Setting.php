<?php
class Proposalgen_Model_Report_Setting extends My_Model_Abstract
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
     * The service cost per page
     *
     * @var int
     */
    public $serviceCostPerPage;

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

    /**
     * The id of the gross margin pricing configuration
     *
     * @var int
     */
    public $grossMarginPricingConfigId;

    /**
     * Cost delta is used withing optimization report for a minimum savings
     *
     * @var int
     */
    public $costThreshold;

    /**
     * Target Monochrome is the desired cost per page looking to obtain for the fleet
     *
     * @var int
     */
    public $targetMonochrome;

    /**
     *  Target Color is the desired cost per page looking to obtain for the fleet
     *
     * @var int
     */
    public $targetColor;
    /**
     * The assessment pricing configuration
     *
     * @var Proposalgen_Model_PricingConfig
     */
    protected $_assessmentPricingConfig;
    /**
     * The gross margin pricing configuration
     *
     * @var Proposalgen_Model_PricingConfig
     */
    protected $_grossMarginPricingConfig;

    /**
     * Overrides all the settings.
     * Null values will be excluded.
     *
     * @param Proposalgen_Model_Report_Setting $settings
     *            These can be either a Proposalgen_Model_Report_Setting or an array of settings
     */
    public function ApplyOverride ($settings)
    {
        if ($settings instanceof Proposalgen_Model_Report_Setting)
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

        if (isset($params->serviceCostPerPage) && !is_null($params->serviceCostPerPage))
        {
            $this->serviceCostPerPage = $params->serviceCostPerPage;
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

        if (isset($params->costThreshold) && !is_null($params->costThreshold))
        {
            $this->costThreshold = $params->costThreshold;
        }

        if (isset($params->targetMonochrome) && !is_null($params->targetMonochrome))
        {
            $this->targetMonochrome = $params->targetMonochrome;
        }

        if (isset($params->targetColor) && !is_null($params->targetColor))
        {
            $this->targetColor = $params->targetColor;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                         => $this->id,
            "actualPageCoverageMono"     => $this->actualPageCoverageMono,
            "actualPageCoverageColor"    => $this->actualPageCoverageColor,
            "serviceCostPerPage"         => $this->serviceCostPerPage,
            "adminCostPerPage"           => $this->adminCostPerPage,
            "assessmentReportMargin"     => $this->assessmentReportMargin,
            "grossMarginReportMargin"    => $this->grossMarginReportMargin,
            "monthlyLeasePayment"        => $this->monthlyLeasePayment,
            "defaultPrinterCost"         => $this->defaultPrinterCost,
            "leasedBwCostPerPage"        => $this->leasedBwCostPerPage,
            "leasedColorCostPerPage"     => $this->leasedColorCostPerPage,
            "mpsBwCostPerPage"           => $this->mpsBwCostPerPage,
            "mpsColorCostPerPage"        => $this->mpsColorCostPerPage,
            "kilowattsPerHour"           => $this->kilowattsPerHour,
            "assessmentPricingConfigId"  => $this->assessmentPricingConfigId,
            "grossMarginPricingConfigId" => $this->grossMarginPricingConfigId,
            "costThreshold"              => $this->costThreshold,
            "targetMonochrome"           => $this->targetMonochrome,
            "targetColor"                => $this->targetColor,
        );
    }

    /**
     * Gets the assessment pricing configuration object
     *
     * @return Proposalgen_Model_PricingConfig
     */
    public
    function getAssessmentPricingConfig ()
    {
        if (!isset($this->_assessmentPricingConfig))
        {
            $this->_assessmentPricingConfig = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($this->assessmentPricingConfigId);
        }

        return $this->_assessmentPricingConfig;
    }

    /**
     * Sets the assessment pricing configuration object
     *
     * @param $AssessmentPricingConfig Proposalgen_Model_PricingConfig
     *                                 The pricing configuration to set
     *
     * @return \Proposalgen_Model_Report_Setting
     */
    public
    function setAssessmentPricingConfig ($AssessmentPricingConfig)
    {
        $this->_assessmentPricingConfig = $AssessmentPricingConfig;

        return $this;
    }

    /**
     * Gets the gross margin pricing configuration object
     *
     * @return Proposalgen_Model_PricingConfig
     */
    public
    function getGrossMarginPricingConfig ()
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
     * @return \Proposalgen_Model_Report_Setting
     */
    public
    function setGrossMarginPricingConfig ($GrossMarginPricingConfig)
    {
        $this->_grossMarginPricingConfig = $GrossMarginPricingConfig;

        return $this;
    }
}