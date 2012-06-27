<?php

/**
 * Quotegen_Model_QuoteDevicePage
 *
 * @author Shawn Wilder
 *        
 */
class Quotegen_Model_QuoteDevicePage extends My_Model_Abstract
{
    const PAGEBILLINGPREFERENCE_PERPAGE = 'Per Page';
    const PAGEBILLINGPREFERENCE_MONTHLY = 'Monthly';
    
    /**
     * Id that relates to quoteDevice table
     *
     * @var int
     */
    protected $_quoteDeviceId;
    
    /**
     * Cost for monochrome coverage per page
     *
     * @var double
     */
    protected $_costPerPageMonochrome;
    
    /**
     * Cost for color coverage per page
     *
     * @var double
     */
    protected $_costPerPageColor;
    
    /**
     * Enum for billing preference
     *
     * @var string
     */
    protected $_pageBillingPreference;
    
    /**
     * Margin for device pages
     *
     * @var double
     */
    protected $_margin;
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
            $this->setQuoteDeviceId($params->quoteDeviceId);
        if (isset($params->costPerPageMonochrome) && ! is_null($params->costPerPageMonochrome))
            $this->setCostPerPageMonochrome($params->costPerPageMonochrome);
        if (isset($params->costPerPageColor) && ! is_null($params->costPerPageColor))
            $this->setCostPerPageColor($params->costPerPageColor);
        if (isset($params->pageBillingPreference) && ! is_null($params->pageBillingPreference))
            $this->setPageBillingPreference($params->pageBillingPreference);
        if (isset($params->margin) && ! is_null($params->margin))
            $this->setMargin($params->margin);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'quoteDeviceId' => $this->getQuoteDeviceId(), 
                'costPerPageMonochrome' => $this->getCostPerPageMonochrome(), 
                'costPerPageColor' => $this->getCostPerPageColor(), 
                'pagesBillingPreference' => $this->getPageBillingPreference(), 
                'margin' => $this->getMargin() 
        );
    }

    /**
     * Gets the objects related quoteDeviceId
     *
     * @return the $_quoteDeviceId
     */
    public function getQuoteDeviceId ()
    {
        return $this->_quoteDeviceId;
    }

    /**
     * Set a new relate quoteDeviceId
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
     * Gets the current costPerPageMonochrome
     *
     * @return the $_costPerPageMonochrome
     */
    public function getCostPerPageMonochrome ()
    {
        return $this->_costPerPageMonochrome;
    }

    /**
     * Sets a new costPerPageMonochrome
     *
     * @param number $_costPerPageMonocrome
     *            the new costPerPageMonochrome
     */
    public function setCostPerPageMonochrome ($_costPerPageMonochrome)
    {
        $this->_costPerPageMonochrome = $_costPerPageMonochrome;
        return $this;
    }

    /**
     * Gets the current costPerPageColor
     *
     * @return the $_costPerPageColor
     */
    public function getCostPerPageColor ()
    {
        return $this->_costPerPageColor;
    }

    /**
     * Sets a new costPerPageColor
     *
     * @param number $_costPerPageColor
     *            the new costPerPageColor
     */
    public function setCostPerPageColor ($_costPerPageColor)
    {
        $this->_costPerPageColor = $_costPerPageColor;
        return $this;
    }

    /**
     * Gets the current billing preference
     *
     * @return the $_pageBillingPreference
     */
    public function getPageBillingPreference ()
    {
        return $this->_pageBillingPreference;
    }

    /**
     * Sets a new billing preference
     *
     * @param string $_pageBillingPreference
     *            the new pageBillingPrefernce
     */
    public function setPageBillingPreference ($_pageBillingPreference)
    {
        $this->_pageBillingPreference = $_pageBillingPreference;
        return $this;
    }

    /**
     * Gets the current margin for the device pages
     *
     * @return the $_margin
     */
    public function getMargin ()
    {
        return $this->_margin;
    }

    /**
     * Sets the current margin for the device pages
     *
     * @param number $_margin
     *            the new margin
     */
    public function setMargin ($_margin)
    {
        $this->_margin = $_margin;
        return $this;
    }
}
