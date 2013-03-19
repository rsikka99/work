<?php
class Proposalgen_Model_Dealer_Report_Setting extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $dealerId;
    /**
     * @var int
     */
    public $reportSettingId;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }
        if (isset($params->reportSettingId) && !is_null($params->reportSettingId))
        {
            $this->reportSettingId = $params->reportSettingId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "dealerId"        => $this->dealerId,
            "reportSettingId" => $this->reportSettingId
        );
    }
}