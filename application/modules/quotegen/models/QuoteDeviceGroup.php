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
     * Group Name
     *
     * @var string
     */
    protected $_name;
    
    /**
     * Flag for default group
     *
     * @var int
     */
    protected $_isDefault;
    
    /**
     * Are the pages going to be grouped
     *
     * @var int
     */
    protected $_groupPages;
    
    /**
     * The quote
     *
     * @var Quotegen_Model_Quote
     */
    protected $_quote;
    
    /**
     * The quote device group devices
     *
     * @var array
     */
    protected $_quoteDeviceGroupDevices;
    
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
        
        if (isset($params->name) && ! is_null($params->name))
            $this->setName($params->name);
        
        if (isset($params->isDefault) && ! is_null($params->isDefault))
            $this->setIsDefault($params->isDefault);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'quoteId' => $this->getQuoteId(), 
                "name" => $this->getName(), 
                "isDefault" => $this->getIsDefault() 
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
     * @return multitype:Quotegen_Model_QuoteDeviceGroupDevice
     */
    public function getQuoteDeviceGroupDevices ()
    {
        if (! isset($this->_quoteDeviceGroupDevices))
        {
            $this->_quoteDeviceGroupDevices = Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance()->fetchDevicesForQuoteDeviceGroup($this->getId());
        }
        return $this->_quoteDeviceGroupDevices;
    }

    /**
     * Sets the quote devices
     *
     * @param $_quoteDeviceGroupDevices multitype:Quotegen_Model_QuoteDeviceGroupDevice
     *            The quote devices
     */
    public function setQuoteDeviceGroupDevices ($_quoteDeviceGroupDevices)
    {
        $this->_quoteDeviceGroupDevices = $_quoteDeviceGroupDevices;
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
        
        /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
        foreach ( $this->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
        {
            $quantity = $quoteDeviceGroupDevice->getQuantity();
            if ($quantity > 0)
            {
                $subtotal += $quoteDeviceGroupDevice->getQuoteDevice()->calculatePackagePrice() * $quantity;
            }
        }
        return $subtotal;
    }

    /**
     * Calculates the lease sub total for the quote's devices.
     *
     * @return number The sub total
     */
    public function calculateGroupMonthlyLeasePrice ()
    {
        $subtotal = 0;
        
        /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
        foreach ( $this->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
        {
            $quantity = $quoteDeviceGroupDevice->getQuantity();
            if ($quantity > 0)
            {
                $subtotal += $quoteDeviceGroupDevice->getQuoteDevice()->calculatePackageMonthlyLeasePrice() * $quantity;
            }
        }
        
        $subtotal += $this->calculateMonthlyPagePrice();
        
        return $subtotal;
    }

    public function calculateMonthlyPagePrice ()
    {
        $pagePrice = 0;
        
        /* @var $quoteDeviceGroupPage Quotegen_Model_QuoteDeviceGroupPage */
        foreach ( $this->getPages() as $quoteDeviceGroupPage )
        {
            $pagePrice += $quoteDeviceGroupPage->getIncludedPrice();
        }
        return $pagePrice;
    }

    /**
     * Calculates the lease sub total for the quote's devices.
     *
     * @return number The sub total
     */
    public function calculateGroupLeaseValue ()
    {
        $subtotal = 0;
        
        /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
        foreach ( $this->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
        {
            $subtotal += $quoteDeviceGroupDevice->getQuoteDevice()->calculateLeaseValue();
        }
        
        $leaseTerm = (int)$this->getQuote()->getLeaseTerm();
        if ($leaseTerm > 0)
        {
            $subtotal += $this->calculateMonthlyPagePrice() * $leaseTerm;
        }
        
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
        foreach ( $this->getQuoteDeviceGroupDevices() as $quoteDevice )
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
        foreach ( $this->getQuoteDeviceGroupDevices() as $quoteDevice )
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

    /**
     *
     * @return the $_name
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     *
     * @param string $_name            
     */
    public function setName ($_name)
    {
        $this->_name = $_name;
    }

    /**
     *
     * @return the $_isDefault
     */
    public function getIsDefault ()
    {
        return $this->_isDefault;
    }

    /**
     *
     * @param number $_isDefault            
     */
    public function setIsDefault ($_isDefault)
    {
        $this->_isDefault = $_isDefault;
    }

    /**
     *
     * @return the $_groupPages
     */
    public function getGroupPages ()
    {
        return $this->_groupPages;
    }

    /**
     *
     * @param number $_groupPages            
     */
    public function setGroupPages ($_groupPages)
    {
        $this->_groupPages = $_groupPages;
    }

    /**
     * ********************************************************
     * Calculations for the quote device group
     *
     * Calculations here use the quote and quote devices related
     * to this group.
     *
     * *********************************************************
     */
    
    /**
     * Calculates the sub total (package price * quantity)
     *
     * @return number The sub total
     */
    public function calculatePurchaseSubtotal ()
    {
        $subTotal = 0;
        $packagePrice = (float)$this->getPackagePrice();
        
        $quantity = $this->getQuantity();
        
        // Make sure both the price and quantity are greater than 0
        if ($packagePrice > 0 && $quantity > 0)
        {
            $subTotal = $packagePrice * $quantity;
        }
        
        return $subTotal;
    }
}
