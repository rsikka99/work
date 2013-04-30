<?php
/**
 * Class Quotegen_Model_GlobalDeviceConfiguration
 */
class Quotegen_Model_GlobalDeviceConfiguration extends My_Model_Abstract
{

    /**
     * @var int
     */
    public $deviceConfigurationId = 0;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->deviceConfigurationId) && !is_null($params->deviceConfigurationId))
        {
            $this->deviceConfigurationId = $params->deviceConfigurationId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "deviceConfigurationId" => $this->deviceConfigurationId,
        );
    }
}
