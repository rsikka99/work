<?php

/**
 * Quotegen_Model_QuoteDeviceConfigurationOption
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_QuoteDeviceConfigurationOption extends My_Model_Abstract
{
    
    /**
     * The device option id
     *
     * @var int
     */
    protected $_quoteDeviceOptionId = 0;
    
    /**
     * The option id
     *
     * @var int
     */
    protected $_optionId = 0;
    
    /**
     * The option associated with this configuration
     *
     * @var Quotegen_Model_Option
     */
    protected $_option;
    
    /**
     * The quote device option associated with this configuration
     *
     * @var Quotegen_Model_QuoteDeviceOption
     */
    protected $_quoteDeviceOption;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->quoteDeviceOptionId) && ! is_null($params->quoteDeviceOptionId))
            $this->setQuoteDeviceOptionId($params->quoteDeviceOptionId);
        if (isset($params->optionId) && ! is_null($params->optionId))
            $this->setOptionId($params->optionId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'quoteDeviceOptionId' => $this->getQuoteDeviceOptionId(), 
                'optionId' => $this->getOptionId() 
        );
    }

    /**
     * Gets the quote device option id
     *
     * @return number The quote device option id
     */
    public function getQuoteDeviceOptionId ()
    {
        return $this->_quoteDeviceOptionId;
    }

    /**
     * Sets a new quote device option id
     *
     * @param number $_quoteDeviceOptionId
     *            The new id
     */
    public function setQuoteDeviceOptionId ($_quoteDeviceOptionId)
    {
        $this->_quoteDeviceOptionId = $_quoteDeviceOptionId;
        return $this;
    }

    /**
     * Gets the option id
     *
     * @return number The option id
     */
    public function getOptionId ()
    {
        return $this->_optionId;
    }

    /**
     * Sets a new option id
     *
     * @param number $_optionId
     *            The id
     */
    public function setOptionId ($_optionId)
    {
        $this->_optionId = $_optionId;
        return $this;
    }

    /**
     * Gets the option associated with the device configuration option
     *
     * @return Quotegen_Model_Option
     */
    public function getOption ()
    {
        if (! isset($this->_option))
        {
            $this->_option = Quotegen_Model_Mapper_Option::getInstance()->find($this->getOptionId());
        }
        return $this->_option;
    }

    /**
     * Sets the option associated with the device configuration option
     *
     * @param Quotegen_Model_Option $_option            
     */
    public function setOption ($_option)
    {
        $this->_option = $_option;
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
            $this->_quoteDeviceOption = Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->find($this->getQuoteDeviceOptionId());
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