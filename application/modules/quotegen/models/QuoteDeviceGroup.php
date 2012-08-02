<?php

/**
 * Quotegen_Model_QuoteDeviceGroup
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_QuoteDeviceGroup extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id;
    
    /**
     * The quote id of the quote that the device group belongs to
     *
     * @var int
     */
    protected $_quoteId;
    
    /**
     * The cost per monochrome page
     *
     * @var number
     */
    protected $_pageMargin;
    
    /**
     * The quote
     *
     * @var Quotegen_Model_Quote
     */
    protected $_quote;
    
    /**
     * The quote devices
     *
     * @var array
     */
    protected $_quoteDevices;
    
    /**
     * Pages associated with the device group
     *
     * @var array
     */
    protected $_pages;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->id) && ! is_null($params->id))
            $this->setId($params->id);
        
        if (isset($params->quoteId) && ! is_null($params->quoteId))
            $this->setQuoteId($params->quoteId);
        
        if (isset($params->pageMargin) && ! is_null($params->pageMargin))
            $this->setPageMargin($params->pageMargin);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'quoteId' => $this->getQuoteId(), 
                'pageMargin' => $this->getPageMargin() 
        );
    }

    /**
     * Gets the id of the object
     *
     * @return number The id of the object
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     * Sets the id of the object
     *
     * @param $_id number
     *            the new id
     */
    public function setId ($_id)
    {
        $this->_id = $_id;
    }

    /**
     * Gets the quote id
     *
     * @return number
     */
    public function getQuoteId ()
    {
        return $this->_quoteId;
    }

    /**
     * Sets the quote id
     *
     * @param $_quoteId number            
     */
    public function setQuoteId ($_quoteId)
    {
        $this->_quoteId = $_quoteId;
        return $this;
    }

    /**
     * Gets the page margin
     *
     * @return number
     */
    public function getPageMargin ()
    {
        return $this->_pageMargin;
    }

    /**
     * Sets the page margin
     *
     * @param $_pageMargin number            
     */
    public function setPageMargin ($_pageMargin)
    {
        $this->_pageMargin = $_pageMargin;
        return $this;
    }

    /**
     * Gets the quote
     *
     * @return Quotegen_Model_Quote
     */
    public function getQuote ()
    {
        if (! isset($this->_quote))
        {
            $this->_quote = Quotegen_Model_Mapper_Quote::getInstance()->find($this->getQuoteId());
        }
        return $this->_quote;
    }

    /**
     * Sets the quote
     *
     * @param $_quote Quotegen_Model_Quote            
     */
    public function setQuote ($_quote)
    {
        $this->_quote = $_quote;
        return $this;
    }

    /**
     * Gets the quote devices
     *
     * @return multitype:Quotegen_Model_QuoteDevice
     */
    public function getQuoteDevices ()
    {
        if (! isset($this->_quoteDevices))
        {
            $this->_quoteDevices = Quotegen_Model_Mapper_QuoteDevice::getInstance()->fetchDevicesForQuoteDeviceGroup($this->getId());
        }
        return $this->_quoteDevices;
    }

    /**
     * Sets the quote devices
     *
     * @param $_quoteDevices multitype:Quotegen_Model_QuoteDevice
     *            The quote devices
     */
    public function setQuoteDevices ($_quoteDevices)
    {
        $this->_quoteDevices = $_quoteDevices;
        return $this;
    }

    /**
     * Calculates the sub total for the group's devices.
     * This is the number used for the purchase total and the number that will be used to choose a leasing factor.
     *
     * @return number The sub total
     */
    public function calculateGroupSubtotal ()
    {
        $subtotal = 0;
        
        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
        foreach ( $this->getQuoteDevices() as $quoteDevice )
        {
            $subtotal += $quoteDevice->calculateSubtotal();
        }
        return $subtotal;
    }

    /**
     * Calculates the lease sub total for the quote's devices.
     *
     * @return number The sub total
     */
    public function calculateGroupLeaseSubtotal ()
    {
        $subtotal = 0;
        
        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
        foreach ( $this->getQuoteDevices() as $quoteDevice )
        {
            $subtotal += $quoteDevice->calculateLeaseSubtotal();
        }
        
        $subtotal += $this->calculateTotalPagePrice();
        
        return $subtotal;
    }
    
    public function calculateTotalPagePrice()
    {
        $pagePrice = 0;
        $leaseTerm = (int)$this->getQuote()->getLeaseTerm();
        
        /* @var $quoteDeviceGroupPage Quotegen_Model_QuoteDeviceGroupPage */
        foreach ( $this->getPages() as $quoteDeviceGroupPage )
        {
            if ($quoteDeviceGroupPage->getIncludedPrice() > 0 && $leaseTerm > 0)
            {
                $pagePrice += $quoteDeviceGroupPage->getIncludedPrice() * $leaseTerm;
            }
        }
        return $pagePrice;
    }

    /**
     * Calculates the lease sub total for the quote's devices.
     *
     * @return number The sub total
     */
    public function calculateGroupSubtotalWithResidualsApplied ()
    {
        $subtotal = 0;
        
        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
        foreach ( $this->getQuoteDevices() as $quoteDevice )
        {
            $subtotal += $quoteDevice->calculateSubtotalWithResidual();
        }
        
        $subtotal += $this->calculateTotalPagePrice();
        
        return $subtotal;
    }

    /**
     * Calculates the total residual for the group
     *
     * @return number
     */
    public function calculateTotalResidual ()
    {
        $totalResidual = 0;
        
        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
        foreach ( $this->getQuoteDevices() as $quoteDevice )
        {
            $totalResidual += $quoteDevice->getResidual();
        }
        return $totalResidual;
    }

    /**
     * Calculates the total cost of the quote
     *
     * @return number The total cost.
     */
    public function calculateTotalCost ()
    {
        $totalCost = 0;
        
        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
        foreach ( $this->getQuoteDevices() as $quoteDevice )
        {
            if ($quoteDevice->getQuantity() > 0)
            {
                $totalCost += $quoteDevice->calculatePackageCost() * $quoteDevice->getQuantity();
            }
        }
        return $totalCost;
    }

    /**
     * Gets pages associated with the group
     *
     * @return multitype: Quotegen_Model_QuoteDeviceGroupPages
     */
    public function getPages ()
    {
        if (! isset($this->_pages))
        {
            $this->_pages = Quotegen_Model_Mapper_QuoteDeviceGroupPage::getInstance()->fetchAllPagesForQuoteDeviceGroup($this->getId());
        }
        return $this->_pages;
    }

    /**
     *
     * @param multitype: $_pages            
     */
    public function setPages ($_pages)
    {
        $this->_pages = $_pages;
        return $this;
    }
}
