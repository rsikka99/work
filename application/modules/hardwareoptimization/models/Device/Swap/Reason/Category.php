<?php
/**
 * Class Hardwareoptimization_Model_Device_Swap_Reason_Category
 */
class Hardwareoptimization_Model_Device_Swap_Reason_Category extends My_Model_Abstract
{
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