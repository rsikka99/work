<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceMapper;
use My_Model_Abstract;

/**
 * Class QuoteDeviceConfigurationModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class QuoteDeviceConfigurationModel extends My_Model_Abstract
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
     * @var DeviceModel
     */
    protected $_device;

    /**
     * The quote device
     *
     * @var QuoteDeviceModel
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
        return [
            "quoteDeviceId"  => $this->quoteDeviceId,
            "masterDeviceId" => $this->masterDeviceId,
        ];
    }

    /**
     * Gets the associated device
     *
     * @return DeviceModel The device.
     */
    public function getDevice ()
    {
        if (!isset($this->_device))
        {
            $this->_device = DeviceMapper::getInstance()->find($this->masterDeviceId);
        }

        return $this->_device;
    }

    /**
     * Sets the associated device
     *
     * @param DeviceModel $_device
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
     * @return QuoteDeviceModel The quote device.
     */
    public function getQuoteDevice ()
    {
        if (!isset($this->_quoteDevice))
        {
            $this->_quoteDevice = QuoteDeviceMapper::getInstance()->find($this->quoteDeviceId);
        }

        return $this->_quoteDevice;
    }

    /**
     * Sets the associated quote device
     *
     * @param QuoteDeviceModel $_quoteDevice
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