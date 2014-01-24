<?php

/**
 * Class Quotegen_Model_QuoteDeviceGroupDevice
 */
class Quotegen_Model_QuoteDeviceGroupDevice extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $quoteDeviceId;

    /**
     * @var int
     */
    public $quoteDeviceGroupId;

    /**
     * @var int
     */
    public $quantity;

    /**
     * @var int
     */
    public $monochromePagesQuantity;

    /**
     * @var int
     */
    public $colorPagesQuantity;

    /**
     * The quote device
     *
     * @var Quotegen_Model_QuoteDevice
     */
    protected $_quoteDevice;

    /**
     * The quote device group
     *
     * @var Quotegen_Model_QuoteDeviceGroup
     */
    protected $_quoteDeviceGroup;


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

        if (isset($params->quoteDeviceGroupId) && !is_null($params->quoteDeviceGroupId))
        {
            $this->quoteDeviceGroupId = $params->quoteDeviceGroupId;
        }

        if (isset($params->quantity) && !is_null($params->quantity))
        {
            $this->quantity = $params->quantity;
        }

        if (isset($params->monochromePagesQuantity) && !is_null($params->monochromePagesQuantity))
        {
            $this->monochromePagesQuantity = $params->monochromePagesQuantity;
        }

        if (isset($params->colorPagesQuantity) && !is_null($params->colorPagesQuantity))
        {
            $this->colorPagesQuantity = $params->colorPagesQuantity;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "quoteDeviceId"           => $this->quoteDeviceId,
            "quoteDeviceGroupId"      => $this->quoteDeviceGroupId,
            "quantity"                => $this->quantity,
            "monochromePagesQuantity" => $this->monochromePagesQuantity,
            "colorPagesQuantity"      => $this->colorPagesQuantity,
        );
    }

    /**
     * Gets the quote device
     *
     * @return Quotegen_Model_QuoteDevice
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
     * Sets the quote device
     *
     * @param Quotegen_Model_QuoteDevice $_quoteDevice
     *
     * @return $this
     */
    public function setQuoteDevice ($_quoteDevice)
    {
        $this->_quoteDevice = $_quoteDevice;

        return $this;
    }

    /**
     * Gets the quote device group
     *
     * @return Quotegen_Model_QuoteDeviceGroup
     */
    public function getQuoteDeviceGroup ()
    {
        if (!isset($this->_quoteDeviceGroup))
        {
            $this->_quoteDeviceGroup = Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->find($this->quoteDeviceGroupId);
        }

        return $this->_quoteDeviceGroup;
    }

    /**
     * Sets the quote device group
     *
     * @param Quotegen_Model_QuoteDeviceGroup $_quoteDeviceGroup
     *
     * @return $this
     */
    public function setQuoteDeviceGroup ($_quoteDeviceGroup)
    {
        $this->_quoteDeviceGroup = $_quoteDeviceGroup;

        return $this;
    }

    /**
     * Calculates the sub total for this device (quantity * package price)
     *
     * @return number
     */
    public function calculateSubtotal ()
    {
        return $this->quantity * $this->getQuoteDevice()->calculatePackagePrice();
    }
}