<?php

/**
 * Class Quotegen_Model_QuoteDeviceConfiguration
 */
class Quotegen_Model_QuoteDeviceConfiguration extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $quoteDeviceId;

    /**
     * @var int
     */
    public $masterDeviceId;

    /**
     * The device
     *
     * @var Quotegen_Model_Device
     */
    protected $_device;

    /**
     * The quote device
     *
     * @var Quotegen_Model_QuoteDevice
     */
    protected $_quoteDevice;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->quoteDeviceId) && !is_null($params->quoteDeviceId))
        {
            $this->quoteDeviceId = $params->quoteDeviceId;
        }

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "quoteDeviceId"  => $this->quoteDeviceId,
            "masterDeviceId" => $this->masterDeviceId,
        );
    }

    /**
     * Gets the associated device
     *
     * @return Quotegen_Model_Device The device.
     */
    public function getDevice ()
    {
        if (!isset($this->_device))
        {
            $this->_device = Quotegen_Model_Mapper_Device::getInstance()->find($this->masterDeviceId);
        }

        return $this->_device;
    }

    /**
     * Sets the associated device
     *
     * @param Quotegen_Model_Device $_device
     *            The device.
     *
     * @return $this
     */
    public function setDevice ($_device)
    {
        $this->_device = $_device;

        return $this;
    }

    /**
     * Gets the associated quote device
     *
     * @return Quotegen_Model_QuoteDevice The quote device.
     */
    public function getQuoteDevice ()
    {
        if (!isset($this->_quoteDevice))
        {
            $this->_quoteDevice = Quotegen_Model_Mapper_QuoteDevice::getInstance()->find($this->quoteDeviceId);
        }

        return $this->_quoteDevice;
    }

    /**
     * Sets the associated quote device
     *
     * @param Quotegen_Model_QuoteDevice $_quoteDevice
     *            The quote device.
     *
     * @return $this
     */
    public function setQuoteDevice ($_quoteDevice)
    {
        $this->_quoteDevice = $_quoteDevice;

        return $this;
    }
}