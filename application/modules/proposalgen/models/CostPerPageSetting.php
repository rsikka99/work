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
     * The default service cost per page
     *
     * @var float
     */
    public $laborCostPerPage = 0;

    /**
     * The default service cost per page
     *
     * @var float
     */
    public $partsCostPerPage = 0;
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
            "partsCostPerPage"     => $this->partsCostPerPage,
            "laborCostPerPage"     => $this->laborCostPerPage,
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
        return "{$this->adminCostPerPage}_{$this->partsCostPerPage}_{$this->laborCostPerPage}_{$this->pageCoverageMonochrome}_{$this->pageCoverageColor}_{$this->pricingConfiguration->pricingConfigId}";
    }
}