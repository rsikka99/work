<?php
/**
 * Class Quotegen_Model_QuoteDeviceOption
 */
class Quotegen_Model_QuoteDeviceOption extends My_Model_Abstract
{

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $quoteDeviceId;

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
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var float
     */
    public $cost;

    /**
     * @var int
     */
    public $quantity;

    /**
     * @var int
     */
    public $includedQuantity;

    /**
     * The option associated
     *
     * @var Quotegen_Model_Option
     */
    protected $_deviceOption;

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

        if (isset($params->quoteDeviceId) && !is_null($params->quoteDeviceId))
        {
            $this->quoteDeviceId = $params->quoteDeviceId;
        }

        if (isset($params->oemSku) && !is_null($params->oemSku))
        {
            $this->oemSku = $params->oemSku;
        }

        if (isset($params->dealerSku) && !is_null($params->dealerSku))
        {
            $this->dealerSku = $params->dealerSku;
        }

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }

        if (isset($params->description) && !is_null($params->description))
        {
            $this->description = $params->description;
        }

        if (isset($params->cost) && !is_null($params->cost))
        {
            $this->cost = $params->cost;
        }

        if (isset($params->quantity) && !is_null($params->quantity))
        {
            $this->quantity = $params->quantity;
        }

        if (isset($params->includedQuantity) && !is_null($params->includedQuantity))
        {
            $this->includedQuantity = $params->includedQuantity;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"               => $this->id,
            "quoteDeviceId"    => $this->quoteDeviceId,
            "oemSku"           => $this->oemSku,
            "dealerSku"        => $this->dealerSku,
            "name"             => $this->name,
            "description"      => $this->description,
            "cost"             => $this->cost,
            "quantity"         => $this->quantity,
            "includedQuantity" => $this->includedQuantity,
        );
    }

    /**
     * Gets the associated option, if any.
     *
     * @return Quotegen_Model_DeviceOption The option, or false if no link exists
     */
    public function getDeviceOption ()
    {
        if (!isset($this->_deviceOption))
        {
            $this->_deviceOption            = false;
            $quoteDeviceConfigurationOption = Quotegen_Model_Mapper_QuoteDeviceConfigurationOption::getInstance()->findByQuoteDeviceOptionId($this->id);
            if ($quoteDeviceConfigurationOption)
            {
                $this->_deviceOption = $quoteDeviceConfigurationOption->getDeviceOption();
            }
        }

        return $this->_deviceOption;
    }

    /**
     * Sets the associated option.
     *
     * @param Quotegen_Model_DeviceOption $_option
     *
     * @return $this
     */
    public function setOption ($_option)
    {
        $this->_deviceOption = $_option;

        return $this;
    }

    /**
     * Gets the total quantity (quantity + included quantity)
     *
     * @return number The total quantity
     */
    public function getTotalQuantity ()
    {
        return (int)$this->quantity + (int)$this->includedQuantity;
    }

    /**
     * Gets the total cost (Cost * Quantity)
     *
     * @return number The total cost for the option
     */
    public function getTotalCost ()
    {
        $subtotal = 0;
        $cost     = (float)$this->cost;
        $quantity = (int)$this->quantity;
        if ($cost > 0 && $quantity > 0)
        {
            $subtotal = $cost * $quantity;
        }

        return $subtotal;
    }

    /**
     * Returns the appropriate SKU, dealer SKU if filled out, OEM SKU if it is empty
     *
     * @return String the SKU to show on the reports
     */
    public function getReportSku ()
    {
        return ($this->dealerSku === null ? $this->oemSku : $this->dealerSku);
    }
}