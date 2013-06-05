<?php
/**
 * Class Hardwareoptimization_Model_Hardware_Optimization_Setting
 */
class Hardwareoptimization_Model_Hardware_Optimization_Setting extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $costThreshold;

    /**
     * @var int
     */
    public $customerPricingConfigId;

    /**
     * @var int
     */
    public $dealerPricingConfigId;

    /**
     * @var int
     */
    public $targetColorCostPerPage;

    /**
     * @var int
     */
    public $targetMonochromeCostPerPage;

    /**
     * @var int
     */
    public $replacementPricingConfigId;

    /**
     * @var float
     */
    public $adminCostPerPage;

    /**
     * @var float
     */
    public $laborCostPerPage;

    /**
     * @var float
     */
    public $partsCostPerPage;

    /**
     * @var float
     */
    public $pageCoverageMonochrome;

    /**
     * @var float
     */
    public $pageCoverageColor;


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

        if (isset($params->costThreshold) && !is_null($params->costThreshold))
        {
            $this->costThreshold = $params->costThreshold;
        }

        if (isset($params->customerPricingConfigId) && !is_null($params->customerPricingConfigId))
        {
            $this->customerPricingConfigId = $params->customerPricingConfigId;
        }

        if (isset($params->dealerPricingConfigId) && !is_null($params->dealerPricingConfigId))
        {
            $this->dealerPricingConfigId = $params->dealerPricingConfigId;
        }

        if (isset($params->targetColorCostPerPage) && !is_null($params->targetColorCostPerPage))
        {
            $this->targetColorCostPerPage = $params->targetColorCostPerPage;
        }

        if (isset($params->targetMonochromeCostPerPage) && !is_null($params->targetMonochromeCostPerPage))
        {
            $this->targetMonochromeCostPerPage = $params->targetMonochromeCostPerPage;
        }

        if (isset($params->replacementPricingConfigId) && !is_null($params->replacementPricingConfigId))
        {
            $this->replacementPricingConfigId = $params->replacementPricingConfigId;
        }

        if (isset($params->adminCostPerPage) && !is_null($params->adminCostPerPage))
        {
            $this->adminCostPerPage = $params->adminCostPerPage;
        }

        if (isset($params->laborCostPerPage) && !is_null($params->laborCostPerPage))
        {
            $this->laborCostPerPage = $params->laborCostPerPage;
        }

        if (isset($params->partsCostPerPage) && !is_null($params->partsCostPerPage))
        {
            $this->partsCostPerPage = $params->partsCostPerPage;
        }

        if (isset($params->pageCoverageMonochrome) && !is_null($params->pageCoverageMonochrome))
        {
            $this->pageCoverageMonochrome = $params->pageCoverageMonochrome;
        }

        if (isset($params->pageCoverageColor) && !is_null($params->pageCoverageColor))
        {
            $this->pageCoverageColor = $params->pageCoverageColor;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                          => $this->id,
            "costThreshold"               => $this->costThreshold,
            "customerPricingConfigId"     => $this->customerPricingConfigId,
            "dealerPricingConfigId"       => $this->dealerPricingConfigId,
            "targetColorCostPerPage"      => $this->targetColorCostPerPage,
            "targetMonochromeCostPerPage" => $this->targetMonochromeCostPerPage,
            "replacementPricingConfigId"  => $this->replacementPricingConfigId,
            "adminCostPerPage"            => $this->adminCostPerPage,
            "laborCostPerPage"            => $this->laborCostPerPage,
            "partsCostPerPage"            => $this->partsCostPerPage,
            "pageCoverageMonochrome"      => $this->pageCoverageMonochrome,
            "pageCoverageColor"           => $this->pageCoverageColor,
        );
    }

    /**
     *
     * @param $pricingConfigId
     *
     * @return \Proposalgen_Model_DeviceToner
     */
    public function getPricingConfig ($pricingConfigId)
    {
        $pricingConfig = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($pricingConfigId);

        return $pricingConfig;
    }
}