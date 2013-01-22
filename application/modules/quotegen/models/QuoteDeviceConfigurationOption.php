<?php
class Quotegen_Model_QuoteDeviceConfigurationOption extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $quoteDeviceOptionId = 0;

    /**
     * @var int
     */
    public $optionId = 0;

    /**
     * @var int
     */
    public $masterDeviceId;
    
    /**
     * The option associated with this configuration
     *
     * @var Quotegen_Model_Option
     */
    protected $_deviceOption;
    
    /**
     * The quote device option associated with this configuration
     *
     * @var Quotegen_Model_QuoteDeviceOption
     */
    protected $_quoteDeviceOption;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->quoteDeviceOptionId) && ! is_null($params->quoteDeviceOptionId))
            $this->quoteDeviceOptionId = $params->quoteDeviceOptionId;

        if (isset($params->optionId) && ! is_null($params->optionId))
            $this->optionId = $params->optionId;

        if (isset($params->masterDeviceId) && ! is_null($params->masterDeviceId))
            $this->masterDeviceId = $params->masterDeviceId;

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array (
            "quoteDeviceOptionId" => $this->quoteDeviceOptionId,
            "optionId" => $this->optionId,
            "masterDeviceId" => $this->masterDeviceId,
        );
    }

    /**
     * Gets the option associated with the device configuration option
     *
     * @return Quotegen_Model_DeviceOption
     */
    public function getDeviceOption ()
    {
        if (! isset($this->_deviceOption))
        {
            $this->_deviceOption = Quotegen_Model_Mapper_DeviceOption::getInstance()->find(array (
                    $this->masterDeviceId,
                    $this->optionId
            ));
        }
        return $this->_deviceOption;
    }

    /**
     * Sets the option associated with the device configuration option
     *
     * @param Quotegen_Model_DeviceOption $_deviceOption            
     */
    public function setDeviceOption ($_deviceOption)
    {
        $this->_deviceOption = $_deviceOption;
        return $this;
    }

    /**
     * Gets the quote device option
     *
     * @return Quotegen_Model_QuoteDeviceOption
     */
    public function getQuoteDeviceOption ()
    {
        if (! isset($this->_quoteDeviceOption))
        {
            $this->_quoteDeviceOption = Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->find($this->quoteDeviceOptionId);
        }
        return $this->_quoteDeviceOption;
    }

    /**
     * Sets the quote device option
     *
     * @param Quotegen_Model_QuoteDeviceOption $_quoteDeviceOption
     *            The new quote device option
     */
    public function setQuoteDeviceOption ($_quoteDeviceOption)
    {
        $this->_quoteDeviceOption = $_quoteDeviceOption;
        return $this;
    }
}