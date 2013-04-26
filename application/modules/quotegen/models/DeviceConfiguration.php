<?php
class Quotegen_Model_DeviceConfiguration extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var int
     */
    public $masterDeviceId = 0;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * The quote device associated with this configuration
     *
     * @var Quotegen_Model_Device
     */
    protected $_device;

    /**
     * The options added to the configuraiton
     *
     * @var multitype: Quotegen_Model_DeviceOption
     */
    protected $_options;

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

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }

        if (isset($params->description) && !is_null($params->description))
        {
            $this->description = $params->description;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"             => $this->id,
            "dealerId"       => $this->dealerId,
            "masterDeviceId" => $this->masterDeviceId,
            "name"           => $this->name,
            "description"    => $this->description,
        );
    }

    /**
     * Gets the quote device associated with this configuration
     *
     * @return Quotegen_Model_Device
     */
    public function getDevice ()
    {
        if (!isset($this->_device))
        {
            $this->_device = Quotegen_Model_Mapper_Device::getInstance()->find(array($this->masterDeviceId, $this->dealerId));
        }

        return $this->_device;
    }

    /**
     * Sets the quote device associated with this configuration
     *
     * @param Quotegen_Model_Device $_device
     *
     * @return Quotegen_Model_DeviceConfiguration
     */
    public function setDevice ($_device)
    {
        $this->_device = $_device;

        return $this;
    }

    /**
     * Get the array of options for the device
     *
     * @return Quotegen_Model_DeviceOption[]
     */
    public function getOptions ()
    {
        if (!isset($this->_options))
        {
            $this->_options = Quotegen_Model_Mapper_Option::getInstance()->fetchAllOptionsForDeviceConfiguration($this->id);
        }

        return $this->_options;
    }

    /**
     * Set a new array of options for the device
     *
     * @param Quotegen_Model_DeviceOption[] $_options
     *
     * @return Quotegen_Model_DeviceConfiguration
     */
    public function setOptions ($_options)
    {
        $this->_options = $_options;

        return $this;
    }

}