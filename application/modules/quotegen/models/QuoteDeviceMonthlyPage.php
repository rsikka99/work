<?php

/**
 * Quotegen_Model_Template
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_Template extends My_Model_Abstract
{
    /**
     * id from quoteDeivce
     *
     * @var int
     */
    protected $_quoteDeviceId;
    
    /**
     * Monochrome pages
     *
     * @var int
     */
    protected $_monochrome;
    
    /**
     * Color pages
     *
     * @var int
     */
    protected $_color;
    
    /**
     * Price of option
     *
     * @var double
     */
    protected $_price;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->quoteDeviceId) && ! is_null($params->quoteDeviceId))
            $this->setId($params->quoteDeviceId);
        if (isset($params->monochrome) && ! is_null($params->monochrome))
            $this->setId($params->monochrome);
        if (isset($params->color) && ! is_null($params->color))
            $this->setId($params->color);
        if (isset($params->price) && ! is_null($params->price))
            $this->setId($params->price);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'quoteDeviceId' => $this->getQuoteDeviceId(), 
                'monochrome' => $this->getMonochrome(), 
                'color' => $this->getColor(), 
                'price' => $this->getPrice() 
        );
    }

    /**
     * Gets the quoteDeviceId
     *
     * @return the $_quoteDeviceId
     */
    public function getQuoteDeviceId ()
    {
        return $this->_quoteDeviceId;
    }

    /**
     * Sets a new quoteDeviceId
     *
     * @param number $_quoteDeviceId
     *            the new quoteDeviceId
     */
    public function setQuoteDeviceId ($_quoteDeviceId)
    {
        $this->_quoteDeviceId = $_quoteDeviceId;
        return $this;
    }

    /**
     * Gets the current objects monochrome page amount
     *
     * @return the $_monochrome
     */
    public function getMonochrome ()
    {
        return $this->_monochrome;
    }

    /**
     * Sets the current objects monochrome page amount
     *
     * @param number $_monochrome
     *            the new monochrome page amount
     */
    public function setMonochrome ($_monochrome)
    {
        $this->_monochrome = $_monochrome;
        return $this;
    }

    /**
     * Gets the current objects color page amount
     *
     * @return the $_color
     */
    public function getColor ()
    {
        return $this->_color;
    }

    /**
     * Sets the current objects monochrome page amount
     *
     * @param number $_color
     *            the new color page amount
     */
    public function setColor ($_color)
    {
        $this->_color = $_color;
        return $this;
    }

    /**
     * Gets the current pirce of the deviceMonthlyPage
     *
     * @return the $_price
     */
    public function getPrice ()
    {
        return $this->_price;
    }

    /**
     * Sets the current pirce of the deviceMonthlyPage
     *
     * @param number $_price
     *            the new price for deviceMonthlyPage
     */
    public function setPrice ($_price)
    {
        $this->_price = $_price;
        return $this;
    }
}
