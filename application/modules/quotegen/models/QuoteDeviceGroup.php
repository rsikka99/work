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
     * Gets the name
     *
     * @return string
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * Sets the new name
     *
     * @param string $_name
     *            The new name
     */
    public function setName ($_name)
    {
        $this->_name = $_name;
    }

    /**
     * Gets the isDefault flag
     *
     * @return boolean
     */
    public function getIsDefault ()
    {
        return $this->_isDefault;
    }

    /**
     * Sets the isDefault flag
     *
     * @param boolean $_isDefault            
     */
    public function setIsDefault ($_isDefault)
    {
        $this->_isDefault = $_isDefault;
    }

    /**
     * Gets the group pages flag
     *
     * @return number
     */
    public function getGroupPages ()
    {
        return $this->_groupPages;
    }

    /**
     * Sets the group pages flag
     *
     * @param number $_groupPages            
     */
    public function setGroupPages ($_groupPages)
    {
        $this->_groupPages = $_groupPages;
        return $this;
    }

    /**
     * ****************************************************************************************************************************************
     * AUTO FETCH GETTERS AND SETTERS FOR RELATED MODELS
     * ****************************************************************************************************************************************
     */
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
     * ****************************************************************************************************************************************
     * HARDWARE CALCULATIONS
     * ****************************************************************************************************************************************
     */
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
    public function calculateMonthlyLeasePrice ()
    {
        $subtotal = 0;
        
        /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
        foreach ( $this->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
        {
            $quantity = $quoteDeviceGroupDevice->getQuantity();
            if ($quantity > 0)
            {
                $subtotal += $quoteDeviceGroupDevice->getQuoteDevice()->calculateMonthlyLeasePrice() * $quantity;
            }
        }
        
        // Add Pages
        $subtotal += $this->calculateTotalPageRevenue() * $this->getQuote()->getLeaseRate();
        
        return $subtotal;
    }

    /**
     * Calculates the lease sub total for the quote's devices.
     *
     * @return number The sub total
     */
    public function calculateHardwareLeaseValue ()
    {
        $subtotal = 0;
        
        /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
        foreach ( $this->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
        {
            $subtotal += $quoteDeviceGroupDevice->getQuoteDevice()->calculateLeaseValue() * $quoteDeviceGroupDevice->getQuantity();
        }
        return $subtotal;
    }
    
    /**
     * Calculates the lease sub total for the quote's devices.
     *
     * @return number The sub total
     */
    public function calculateLeaseValue ()
    {
        $subtotal = 0;
    
        $subtotal += $this->calculateHardwareLeaseValue();
    
        // Add Pages
        $subtotal += $this->calculateTotalPageRevenue();
    
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

    /**
     * ****************************************************************************************************************************************
     * PAGE CALCULATIONS
     * ****************************************************************************************************************************************
     */
    /**
     * Gets the quantity of monochrome pages for the group
     *
     * @return the quantity of monochrome pages
     */
    public function calculateTotalMonochromePages ()
    {
        $pagesMonochrome = 0;
        
        /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
        foreach ( $this->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
        {
            $pagesMonochrome += $quoteDeviceGroupDevice->getMonochromePagesQuantity();
        }
        
        return $pagesMonochrome;
    }

    /**
     * Calcuates the total monochrome page cost for the group
     *
     * @return float The total cost
     */
    public function calculateMonochromePageCost ()
    {
        $cost = 0;
        foreach ( $this->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
        {
            $cost += $quoteDeviceGroupDevice->getMonochromePagesQuantity() * $quoteDeviceGroupDevice->getQuoteDevice()->calculateMonochromeCostPerPage();
        }
        
        return $cost;
    }

    /**
     * Calcuates the revenue for monochrome pages
     *
     * @return float The total revenue for monochrome pages
     */
    public function calculateMonochromePageRevenue ()
    {
        return Tangent_Accounting::applyMargin($this->calculateMonochromePageCost(), $this->getQuote()->getMonochromePageMargin());
    }

    /**
     * Gets the profit for monochrome pages for the quote
     *
     * @return number the total quote profit for monochrome
     */
    public function calculateMonochromePageProfit ()
    {
        return $this->calculateMonochromePageRevenue() - $this->calculateMonochromePageCost();
    }

    /**
     * Gets the quantity of color pages for the group
     *
     * @return the quantity of color pages
     */
    public function calculateTotalColorPages ()
    {
        $pagesColor = 0;
        
        /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
        foreach ( $this->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
        {
            $pagesColor += $quoteDeviceGroupDevice->getColorPagesQuantity();
        }
        
        return $pagesColor;
    }

    /**
     * Calcuates the total color page cost for the group
     *
     * @return float The total cost
     */
    public function calculateColorPageCost ()
    {
        $cost = 0;
        foreach ( $this->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
        {
            $cost += $quoteDeviceGroupDevice->getColorPagesQuantity() * $quoteDeviceGroupDevice->getQuoteDevice()->calculateColorCostPerPage();
        }
        
        return $cost;
    }

    /**
     * Calcuates the revenue for color pages
     *
     * @return float The total revenue for color pages
     */
    public function calculateColorPageRevenue ()
    {
        return Tangent_Accounting::applyMargin($this->calculateColorPageCost(), $this->getQuote()->getColorPageMargin());
    }

    /**
     * Gets the profit for color pages for the quote
     *
     * @return float the total quote profit for color
     */
    public function calculateColorPageProfit ()
    {
        return $this->calculateColorPageRevenue() - $this->calculateColorPageCost();
    }

    /**
     * Calculates the total page cost
     *
     * @return number
     */
    public function calculateTotalPageCost ()
    {
        return $this->calculateMonochromePageCost() + $this->calculateColorPageCost();
    }

    /**
     * Calculates the total revenue for pages
     *
     * @return number
     */
    public function calculateTotalPageRevenue ()
    {
        return $this->calculateMonochromePageRevenue() + $this->calculateColorPageRevenue();
    }

    /**
     * Calculates the total profit for pages
     *
     * @return number
     */
    public function calculateTotalPageProfit ()
    {
        return $this->calculateMonochromePageProfit() + $this->calculateColorPageProfit();
    }
}
