<?php

/**
 * Class Proposalgen_Model_Dealer_Toner_Attribute
 */
class Proposalgen_Model_Dealer_Toner_Attribute extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $tonerId;

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var Float
     */
    public $cost;

    /**
     * @var string
     */
    public $dealerSku;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->tonerId) && !is_null($params->tonerId))
        {
            $this->tonerId = $params->tonerId;
        }

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->cost) && !is_null($params->cost))
        {
            $this->cost = $params->cost;
        }

        if (isset($params->dealerSku) && !is_null($params->dealerSku))
        {
            $this->dealerSku = $params->dealerSku;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "tonerId"   => $this->tonerId,
            "dealerId"  => $this->dealerId,
            "cost"      => $this->cost,
            "dealerSku" => $this->dealerSku,
        );
    }
}