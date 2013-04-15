<?php
class Hardwareoptimization_Model_Setting extends My_Model_Abstract
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
    public $targetMonochromeCostPerPage;

    /**
     * @var int
     */
    public $targetColorCostPerPage;

    /**
     * @var int
     */
    public $dealerMargin;

    /**
     * @var int
     */
    public $replacementPricingConfigId;

    /**
     * @var int
     */
    public $dealerPricingConfigId;

    /**
     * @var int
     */
    public $customerPricingConfigId;


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

        if (isset($params->targetMonochromeCostPerPage) && !is_null($params->targetMonochromeCostPerPage))
        {
            $this->targetMonochromeCostPerPage = $params->targetMonochromeCostPerPage;
        }

        if (isset($params->targetColorCostPerPage) && !is_null($params->targetColorCostPerPage))
        {
            $this->targetColorCostPerPage = $params->targetColorCostPerPage;
        }

        if (isset($params->dealerMargin) && !is_null($params->dealerMargin))
        {
            $this->dealerMargin = $params->dealerMargin;
        }

        if (isset($params->replacementPricingConfigId) && !is_null($params->replacementPricingConfigId))
        {
            $this->replacementPricingConfigId = $params->replacementPricingConfigId;
        }

        if (isset($params->dealerPricingConfigId) && !is_null($params->dealerPricingConfigId))
        {
            $this->dealerPricingConfigId = $params->dealerPricingConfigId;
        }

        if (isset($params->customerPricingConfigId) && !is_null($params->customerPricingConfigId))
        {
            $this->customerPricingConfigId = $params->customerPricingConfigId;
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
            "targetMonochromeCostPerPage" => $this->targetMonochromeCostPerPage,
            "targetColorCostPerPage"      => $this->targetColorCostPerPage,
            "dealerMargin"                => $this->dealerMargin,
            "replacementPricingConfigId"  => $this->replacementPricingConfigId,
            "dealerPricingConfigId"       => $this->dealerPricingConfigId,
            "customerPricingConfigId"     => $this->customerPricingConfigId,
        );
    }
}