<?php

/**
 * Class Admin_Model_Memjet_Device_Swap_Page_Threshold
 */
class Admin_Model_Memjet_Device_Swap_Page_Threshold extends My_Model_Abstract
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
     * @var int
     */
    public $minimumPageCount;

    /**
     * @var int
     */
    public $maximumPageCount;

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

        if (isset($params->minimumPageCount) && !is_null($params->minimumPageCount))
        {
            $this->minimumPageCount = $params->minimumPageCount;
        }

        if (isset($params->maximumPageCount) && !is_null($params->maximumPageCount))
        {
            $this->maximumPageCount = $params->maximumPageCount;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "masterDeviceId"   => $this->masterDeviceId,
            "dealerId"         => $this->dealerId,
            "minimumPageCount" => $this->minimumPageCount,
            "maximumPageCount" => $this->maximumPageCount,
        );
    }
}