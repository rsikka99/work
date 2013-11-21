<?php
/**
 * Class Memjetoptimization_Model_Device_Swap_Reason_Category
 */
class Memjetoptimization_Model_Device_Swap_Reason_Category extends My_Model_Abstract
{
    const FLAGGED               = 1;
    const HAS_REPLACEMENT       = 2;
    const FUNCTIONALITY_UPGRADE = 3;

    static $categoryNames = array(
        self::HAS_REPLACEMENT       => "Has replacement device",
        self::FLAGGED               => "Flagged devices",
        self::FUNCTIONALITY_UPGRADE => "Functionality upgrade",
    );
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $name;

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
        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "masterDeviceId" => $this->id,
            "name"           => $this->name,
        );
    }
}