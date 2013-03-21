<?php
class Proposalgen_Model_DealerMasterDeviceAttributes extends My_Model_Abstract
{
    /**
 * @var int
 */
    public $masterDeviceId;

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var double
     */
    public $cost;

    /**
     * @var double
     */
    public $partsCostPerPage;

    /**
     * @var double
     */
    public $laborCostPerPage;

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

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->cost) && !is_null($params->cost))
        {
            $this->cost = $params->cost;
        }

        if (isset($params->partsCostPerPage) && !is_null($params->partsCostPerPage))
        {
            $this->partsCostPerPage = $params->partsCostPerPage;
        }

        if (isset($params->laborCostPerPage) && !is_null($params->laborCostPerPage))
        {
            $this->laborCostPerPage = $params->laborCostPerPage;
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
            "masterDeviceId" => $this->masterDeviceId,
            "dealerId" => $this->dealerId,
            "cost" => $this->cost,
            "partsCostPerPage" => $this->partsCostPerPage,
            "laborCostPerPage" => $this->laborCostPerPage,
            "dealerSku" => $this->dealerSku,
        );
    }
}