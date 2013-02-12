<?php
class Quotegen_Model_Device extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $masterDeviceId;

    /**
     * @var string
     */
    public $oemSku;

    /**
     * @var string
     */
    public $dealerSku;

    /**
     * @var string
     */
    public $description;

    /**
     * @var Proposalgen_Model_MasterDevice
     */
    protected $masterDevice;

    /**
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

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->oemSku) && !is_null($params->oemSku))
        {
            $this->oemSku = $params->oemSku;
        }

        if (isset($params->dealerSku) && !is_null($params->dealerSku))
        {
            $this->dealerSku = $params->dealerSku;
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
            "masterDeviceId" => $this->masterDeviceId,
            "oemSku"         => $this->oemSku,
            "dealerSku"      => $this->dealerSku,
            "description"    => $this->description
        );
    }

    /**
     * Gets the master device object associated with this device
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function getMasterDevice ()
    {
        if (!isset($this->_masterDevice))
        {
            $this->_masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($this->masterDeviceId);
        }

        return $this->_masterDevice;
    }

    /**
     * Sets a new master device id for the object
     *
     * @param number $_masterDeviceId
     *            The new master device id to set
     */
    public function setMasterDeviceId ($_masterDeviceId)
    {
        $this->_masterDeviceId = $_masterDeviceId;

        return $this;
    }

    /**
     * Get the array of options for the device
     *
     * @return multitype:Quotegen_Model_DeviceOption The array of options
     */
    public function getDeviceOptions ()
    {
        if (!isset($this->_options))
        {
            $this->_options = Quotegen_Model_Mapper_Option::getInstance()->fetchAllDeviceOptionsForDevice($this->masterDeviceId);
        }

        return $this->_options;
    }

    /**
     * Set a new array of options for the device
     *
     * @param multitype: $_options
     */
    public function setOptions ($_options)
    {
        $this->_options = $_options;

        return $this;
    }
}