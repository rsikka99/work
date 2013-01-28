<?php
class Proposalgen_Model_CostPerPageSetting extends My_Model_Abstract
{
    /**
     * The admin cost per page
     *
     * @var float
     */
    public $adminCostPerPage = 0;

    /**
     * The service billing preference
     *
     * @var int
     */
    public $billingPreference = Proposalgen_Model_Report_Setting::SERVICE_BILLING_PREFERENCE_PER_PAGE;

    /**
     * The default labor cost
     *
     * @var float
     */
    public $defaultLaborCost = 0;

    /**
     * The default parts cost
     *
     * @var float
     */
    public $defaultPartsCost = 0;

    /**
     * The monochrome page coverage
     *
     * @var float
     */
    public $pageCoverageMonochrome = 5;

    /**
     * The color page coverage
     *
     * @var float
     */
    public $pageCoverageColor = 20;

    /**
     * The pricing configuration (determines which toners are used)
     *
     * @var Proposalgen_Model_PricingConfig
     */
    public $pricingConfiguration;

    /**
     * @param null|array $options
     */
    public function __construct ($options = null)
    {
        parent::__construct($options);

        // If we haven't passed in a pricing configuration through the constructor, lets set it to OEM by default
        if (!isset($this->pricingConfiguration))
        {
            $this->pricingConfiguration = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find(Proposalgen_Model_PricingConfig::OEM);
        }
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

        if (isset($params->adminCostPerPage) && !is_null($params->adminCostPerPage))
        {
            $this->adminCostPerPage = $params->adminCostPerPage;
        }

        if (isset($params->billingPreference) && !is_null($params->billingPreference))
        {
            $this->billingPreference = $params->billingPreference;
        }

        if (isset($params->defaultLaborCost) && !is_null($params->defaultLaborCost))
        {
            $this->defaultLaborCost = $params->defaultLaborCost;
        }

        if (isset($params->defaultPartsCost) && !is_null($params->defaultPartsCost))
        {
            $this->defaultPartsCost = $params->defaultPartsCost;
        }

        if (isset($params->pageCoverageMonochrome) && !is_null($params->pageCoverageMonochrome))
        {
            $this->pageCoverageMonochrome = $params->pageCoverageMonochrome;
        }

        if (isset($params->pageCoverageColor) && !is_null($params->pageCoverageColor))
        {
            $this->pageCoverageColor = $params->pageCoverageColor;
        }

        if (isset($params->pricingConfiguration) && !is_null($params->pricingConfiguration))
        {
            $this->pricingConfiguration = $params->pricingConfiguration;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "adminCostPerPage"       => $this->adminCostPerPage,
            "billingPreference"      => $this->billingPreference,
            "defaultLaborCost"       => $this->defaultLaborCost,
            "defaultPartsCost"       => $this->defaultPartsCost,
            "pageCoverageMonochrome" => $this->pageCoverageMonochrome,
            "pageCoverageColor"      => $this->pageCoverageColor,
            "pricingConfiguration"   => $this->pricingConfiguration,
        );
    }

    /**
     * Used to cache this object
     *
     * @return string
     */
    public function createCacheKey ()
    {
        return "{$this->adminCostPerPage}_{$this->billingPreference}_{$this->defaultLaborCost}_{$this->defaultPartsCost}_{$this->pageCoverageMonochrome}_{$this->pageCoverageColor}_{$this->pricingConfiguration}";
    }
}