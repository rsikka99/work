<?php

/**
 * Class Hardwareoptimization_Model_Device_Swap
 */
class Hardwareoptimization_Model_Device_Swap_Reason extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $dealerId;
    /**
     * @var string
     */
    public $reason;

    /**
     * @var Hardwareoptimization_Model_Device_Swap_Reason_Category
     */
    protected $_category;

    /**
     * @var int
     */
    public $deviceSwapReasonCategoryId;

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

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->reason) && !is_null($params->reason))
        {
            $this->reason = $params->reason;
        }

        if (isset($params->deviceSwapReasonCategoryId) && !is_null($params->deviceSwapReasonCategoryId))
        {
            $this->deviceSwapReasonCategoryId = $params->deviceSwapReasonCategoryId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                         => $this->id,
            "dealerId"                   => $this->dealerId,
            "reason"                     => $this->reason,
            "deviceSwapReasonCategoryId" => $this->deviceSwapReasonCategoryId,
        );
    }

    /**
     * Getter for _category
     *
     * @return \Hardwareoptimization_Model_Device_Swap_Reason_Category
     */
    public function getCategory ()
    {
        if (!isset($this->_category))
        {
            $this->_category = Hardwareoptimization_Model_Mapper_Device_Swap_Reason_Category::getInstance()->find($this->deviceSwapReasonCategoryId);
        }

        return $this->_category;
    }
}