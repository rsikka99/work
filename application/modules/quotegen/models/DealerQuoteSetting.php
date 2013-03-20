<?php
class Quotegen_Model_DealerQuoteSetting extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $dealerId;
    /**
     * @var int
     */
    public $quoteSettingId;

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
        if (isset($params->quoteSettingId) && !is_null($params->quoteSettingId))
        {
            $this->quoteSettingId = $params->quoteSettingId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "dealerId" => $this->dealerId,
            "quoteSettingId" => $this->quoteSettingId,
        );
    }
}