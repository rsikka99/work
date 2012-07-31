<?php

/**
 * Quotegen_Model_QuoteDevice
 *
 * @author Shawn Wilder
 *        
 */
class Quotegen_Model_QuoteDevice extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The id of a quote
     *
     * @var int
     */
    protected $_quoteDeviceGroupId;
    
    /**
     * A number representing margins
     *
     * @var double
     */
    protected $_margin;
    
    /**
     * A string representing a name of a device
     *
     * @var string
     */
    protected $_name;
    
    /**
     * String of the sku
     *
     * @var string
     */
    protected $_sku;
    
    /**
     * Number of oem cost per page monochrome
     *
     * @var double
     */
    protected $_oemCostPerPageMonochrome;
    
    /**
     * Number of oem cost per page color
     *
     * @var double
     */
    protected $_oemCostPerPageColor;
    
    /**
     * Number of comp cost per page monochrome
     *
     * @var double
     */
    protected $_compCostPerPageMonochrome;
    
    /**
     * Number of comp cost per page color
     *
     * @var double
     */
    protected $_compCostPerPageColor;
    
    /**
     * Number of the cost of the device
     *
     * @var double
     */
    protected $_cost;
    
    /**
     * Number of devices
     *
     * @var int
     */
    protected $_quantity;
    
    /**
     * The price of the entire package
     *
     * @var int
     */
    protected $_packagePrice;
    
    /**
     * The residual to leave on the device
     *
     * @var int
     */
    protected $_residual;
    
    /**
     * The device configuration that this quote is attached to
     *
     * @var Quotegen_Model_QuoteDeviceConfiguration
     */
    protected $_device;
    
    /**
     * The quote device options that are stored separately from device configuration options
     *
     * @var Quotegen_Model_QuoteDeviceOption
     */
    protected $_quoteDeviceOptions;
    
    /**
     * The quote that this device is for
     *
     * @var Quotegen_Model_Quote
     */
    protected $_quoteDeviceGroup;
    
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
        if (isset($params->quoteDeviceGroupId) && ! is_null($params->quoteDeviceGroupId))
            $this->setQuoteDeviceGroupId($params->quoteDeviceGroupId);
        if (isset($params->margin) && ! is_null($params->margin))
            $this->setMargin($params->margin);
        if (isset($params->name) && ! is_null($params->name))
            $this->setName($params->name);
        if (isset($params->sku) && ! is_null($params->sku))
            $this->setSku($params->sku);
        if (isset($params->oemCostPerPageMonochrome) && ! is_null($params->oemCostPerPageMonochrome))
            $this->setOemCostPerPageMonochrome($params->oemCostPerPageMonochrome);
        if (isset($params->omeCostPerPageColor) && ! is_null($params->omeCostPerPageColor))
            $this->setOemCostPerPageColor($params->omeCostPerPageColor);
        if (isset($params->compCostPerPageMonochrome) && ! is_null($params->compCostPerPageMonochrome))
            $this->setCompCostPerPageMonochrome($params->compCostPerPageMonochrome);
        if (isset($params->compCostPerPageColor) && ! is_null($params->compCostPerPageColor))
            $this->setCompCostPerPageColor($params->compCostPerPageColor);
        if (isset($params->cost) && ! is_null($params->cost))
            $this->setCost($params->cost);
        if (isset($params->quantity) && ! is_null($params->quantity))
            $this->setQuantity($params->quantity);
        if (isset($params->packagePrice) && ! is_null($params->packagePrice))
            $this->setPackagePrice($params->packagePrice);
        if (isset($params->residual) && ! is_null($params->residual))
            $this->setResidual($params->residual);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'quoteDeviceGroupId' => $this->getQuoteDeviceGroupId(), 
                'margin' => $this->getMargin(), 
                'name' => $this->getName(), 
                'sku' => $this->getSku(), 
                'oemCostPerPageMonochrome' => $this->getOemCostPerPageMonochrome(), 
                'oemCostPerPageColor' => $this->getOemCostPerPageColor(), 
                'compCostPerPageMonochrome' => $this->getCompCostPerPageMonochrome(), 
                'compCostPerPageColor' => $this->getCompCostPerPageColor(), 
                'cost' => $this->getCost(), 
                'quantity' => $this->getQuantity(), 
                'packagePrice' => $this->getPackagePrice(), 
                'residual' => $this->getResidual() 
        );
    }

    /**
     * Gets the id of the device
     *
     * @return number The id of the device
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     * Sets the id of the device
     *
     * @param number $_id
     *            the new id
     */
    public function setId ($_id)
    {
        $this->_id = $_id;
        return $this;
    }

    /**
     * Gets the quote id
     *
     * @return number The quote device group id
     */
    public function getQuoteDeviceGroupId ()
    {
        return $this->_quoteDeviceGroupId;
    }

    /**
     * Sets a quote id
     *
     * @param number $_quoteDeviceGroupId
     *            The new quote device group id
     */
    public function setQuoteDeviceGroupId ($_quoteDeviceGroupId)
    {
        $this->_quoteDeviceGroupId = $_quoteDeviceGroupId;
        return $this;
    }

    /**
     * Gets the devices margin
     *
     * @return number The margin in whole number format (20 = 20%)
     */
    public function getMargin ()
    {
        return $this->_margin;
    }

    /**
     * Sets the margin
     *
     * @param number $_margin
     *            The new margin
     */
    public function setMargin ($_margin)
    {
        $this->_margin = $_margin;
        return $this;
    }

    /**
     * Gets the name of the quote device
     *
     * @return string The name of the quote device
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * Sets the name of the quote device
     *
     * @param string $_name
     *            The new name
     */
    public function setName ($_name)
    {
        $this->_name = $_name;
        return $this;
    }

    /**
     * Gets the sku of the quote device
     *
     * @return the $_sku
     */
    public function getSku ()
    {
        return $this->_sku;
    }

    /**
     * Sets a new sku for the quote device
     *
     * @param string $_sku
     *            The new sku
     */
    public function setSku ($_sku)
    {
        $this->_sku = $_sku;
        return $this;
    }

    /**
     * Gets the OEM cost per page for monochrome pages
     *
     * @return number The cost per page
     */
    public function getOemCostPerPageMonochrome ()
    {
        return $this->_oemCostPerPageMonochrome;
    }

    /**
     * Sets the OEM cost per page for monochrome pages
     *
     * @param number $_oemCostPerPageMonochrome
     *            The new cost per page
     */
    public function setOemCostPerPageMonochrome ($_oemCostPerPageMonochrome)
    {
        $this->_oemCostPerPageMonochrome = $_oemCostPerPageMonochrome;
        return $this;
    }

    /**
     * Gets the OEM cost per page for color pages
     *
     * @return number The cost per page
     */
    public function getOemCostPerPageColor ()
    {
        return $this->_oemCostPerPageColor;
    }

    /**
     * Sets the OEM cost per page for color pages
     *
     * @param number $_oemCostPerPageColor
     *            The new cost per page
     */
    public function setOemCostPerPageColor ($_oemCostPerPageColor)
    {
        $this->_oemCostPerPageColor = $_oemCostPerPageColor;
        return $this;
    }

    /**
     * Gets the compCostPerPageMonochrome
     *
     * @return the $_compCostPerPageMonochrome
     */
    public function getCompCostPerPageMonochrome ()
    {
        return $this->_compCostPerPageMonochrome;
    }

    /**
     * Sets the compCostPerPageMonochrome
     *
     * @param number $_compCostPerPageMonochrome
     *            the new compCostPerPageMonochrome
     */
    public function setCompCostPerPageMonochrome ($_compCostPerPageMonochrome)
    {
        $this->_compCostPerPageMonochrome = $_compCostPerPageMonochrome;
        return $this;
    }

    /**
     * Gets the compCostPerPageColor
     *
     * @return the $_compCostPerPageColor
     */
    public function getCompCostPerPageColor ()
    {
        return $this->_compCostPerPageColor;
    }

    /**
     * Sets the devices compCostPerPageColor
     *
     * @param number $_compCostPerPageColor
     *            the new compCostPerPageColor
     */
    public function setCompCostPerPageColor ($_compCostPerPageColor)
    {
        $this->_compCostPerPageColor = $_compCostPerPageColor;
        return $this;
    }

    /**
     * Gets the devices cost
     *
     * @return number The cost of the device
     */
    public function getCost ()
    {
        return $this->_cost;
    }

    /**
     * Sets the devices new cost
     *
     * @param number $_cost
     *            The new cost
     */
    public function setCost ($_cost)
    {
        $this->_cost = $_cost;
        return $this;
    }

    /**
     * Gets the device currrent quatity
     *
     * @return the $_quantity
     */
    public function getQuantity ()
    {
        return $this->_quantity;
    }

    /**
     * Sets the devices new quantity
     *
     * @param number $_quantity
     *            the new quanitty
     */
    public function setQuantity ($_quantity)
    {
        $this->_quantity = $_quantity;
        return $this;
    }

    /**
     * Gets the package price
     *
     * @return number The package price
     */
    public function getPackagePrice ()
    {
        return $this->_packagePrice;
    }

    /**
     * Sets the new package price
     *
     * @param number $_packagePrice            
     */
    public function setPackagePrice ($_packagePrice)
    {
        $this->_packagePrice = $_packagePrice;
        return $this;
    }

    /**
     * Gets the residual on the device
     *
     * @return number The residual
     */
    public function getResidual ()
    {
        return $this->_residual;
    }

    /**
     * Sets the residual on the device
     *
     * @param number $_residual
     *            The new residual
     */
    public function setResidual ($_residual)
    {
        $this->_residual = $_residual;
        return $this;
    }

    /**
     * Gets the device associated with this quote device
     *
     * @return Quotegen_Model_Device The device configuration
     */
    public function getDevice ()
    {
        if (! isset($this->_device))
        {
            $this->_device = false;
            $quoteDeviceConfiguration = Quotegen_Model_Mapper_QuoteDeviceConfiguration::getInstance()->findByQuoteDeviceId($this->getId());
            if ($quoteDeviceConfiguration)
            {
                $this->_device = Quotegen_Model_Mapper_Device::getInstance()->find($quoteDeviceConfiguration->getMasterDeviceId());
            }
        }
        return $this->_device;
    }

    /**
     * Sets the device configuration associated with this quote device
     *
     * @param Quotegen_Model_Device $_device
     *            The new device configuration.
     */
    public function setDevice ($_device)
    {
        $this->_device = $_device;
        return $this;
    }

    /**
     * Gets the quote device options
     *
     * @return Quotegen_Model_QuoteDeviceOption The quote device options
     */
    public function getQuoteDeviceOptions ()
    {
        if (! isset($this->_quoteDeviceOptions))
        {
            $this->_quoteDeviceOptions = Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->fetchAllOptionsForQuoteDevice($this->getId());
        }
        return $this->_quoteDeviceOptions;
    }

    /**
     * Sets the quote device options
     *
     * @param Quotegen_Model_QuoteDeviceOption $_quoteDeviceOptions
     *            The new options for the device
     */
    public function setQuoteDeviceOptions ($_quoteDeviceOptions)
    {
        $this->_quoteDeviceOptions = $_quoteDeviceOptions;
        return $this;
    }

    /**
     * Calculates the cost of a device's options
     *
     * @return number The cost of the options
     */
    public function calculateOptionsCost ()
    {
        $optionsCost = 0;
        
        // Add all the devices together
        /* @var $quoteDeviceOption Quotegen_Model_QuoteDeviceOption */
        foreach ( $this->getQuoteDeviceOptions() as $quoteDeviceOption )
        {
            $optionsCost += (float)$quoteDeviceOption->getCost() * (int)$quoteDeviceOption->getQuantity();
        }
        
        return $optionsCost;
    }

    /**
     * Calculates the cost of a single device and all of its options
     *
     * @return number The cost
     */
    public function calculatePackageCost ()
    {
        // Get the device price
        $price = (float)$this->getCost();
        
        // Tack on the option prices
        /* @var $quoteDeviceOption Quotegen_Model_QuoteDeviceOption */
        foreach ( $this->getQuoteDeviceOptions() as $quoteDeviceOption )
        {
            $price += $quoteDeviceOption->getSubTotal();
        }
        
        return $price;
    }

    /**
     * Reverse engineers the margin based on the price and cost of the package.
     *
     * @return number The margin
     */
    public function calculateMargin ()
    {
        $margin = 0;
        $cost = $this->calculatePackageCost();
        $price = $this->getPackagePrice();
        
        // Only calculate if we have real numbers to return
        if ($cost > 0 && $price > 0)
        {
            if ($price > $cost)
            {
                // Price is greater than cost. Positive Margin time
                // Margin % = (price - cost) / price * 100
                $margin = (($price - $cost) / $price) * 100;
            }
            else if ($price < $cost)
            {
                // Price is less than cost. Negative margin time.
                // Margin % = (price - cost) / cost * 100
                $margin = (($price - $cost) / $cost) * 100;
            }
            else
            {
                // If the prices are identical, we make 0 margin.
                $margin = 0;
            }
        }
        return round($margin, 2);
    }

    /**
     * Calculates the cost of a single device and all of its options with margin
     *
     * @return number The price
     */
    public function calculatePackagePrice ()
    {
        // Get the device price
        $cost = (float)$this->calculatePackageCost();
        $price = 0;
        
        // Tack on the margin
        if ($cost > 0)
        {
            $margin = (float)$this->getMargin();
            if ($margin > 0 && $margin < 100)
            {
                // When we have a positive margin, we apply it to the cost
                $margin = 1 - (abs($margin) / 100);
                $price = round($cost / $margin, 2);
            }
            else if ($margin < 0 && $margin > - 100)
            {
                // When we have a negative margin, we remove it from the cost
                $margin = 1 - (abs($margin) / 100);
                $price = round($cost * $margin, 2);
            }
            else
            {
                $price = $cost;
            }
        }
        
        return $price;
    }

    /**
     * Calculates the sub total (package price * quantity)
     *
     * @return number The sub total
     */
    public function calculateSubtotal ()
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
     * Calculates the sub total with a residual ((package price - residual) * quantity)
     *
     * @return number The sub total with a residual
     */
    public function calculateSubtotalWithResidual ()
    {
        $subTotal = 0;
        $packagePrice = (float)$this->getPackagePrice();
        $quantity = $this->getQuantity();
        $residual = (float)$this->getResidual();
        
        // Make sure both the price and quantity are greater than 0
        if ($packagePrice > 0 && $quantity > 0)
        {
            $subTotal = ($packagePrice - $residual) * $quantity;
        }
        
        return $subTotal;
    }

    /**
     * Calculates the lease price for the device on a monthly basis
     *
     * @return number The lease price for a single device
     */
    public function calculateLeasePrice ()
    {
        $leasePrice = 0;
        $leaseRate = $this->getQuoteDeviceGroup()->getQuote()->getLeaseRate();
        $packagePrice = (float)$this->getPackagePrice();
        $residual = (float)$this->getResidual();
        
        if ($leaseRate > 0 && $packagePrice > 0)
        {
            $leasePrice = $leaseRate * ($packagePrice - $residual);
        }
        
        return round($leasePrice, 2);
    }

    /**
     * Calculates the lease sub total (leasePrice * quantity)
     *
     * @return number The lease sub total
     */
    public function calculateLeaseSubtotal ()
    {
        $subTotal = 0;
        $leasePrice = (float)$this->calculateLeasePrice();
        $quantity = $this->getQuantity();
        
        // Make sure both the price and quantity are greater than 0
        if ($leasePrice > 0 && $quantity > 0)
        {
            $subTotal = $leasePrice * $quantity;
        }
        
        return round($subTotal, 0);
    }

    /**
     * Gets the quote for this device
     *
     * @return Quotegen_Model_QuoteDeviceGroup The quote device group
     */
    public function getQuoteDeviceGroup ()
    {
        if (! isset($this->_quoteDeviceGroup))
        {
            $this->_quoteDeviceGroup = Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->find($this->getQuoteDeviceGroupId());
        }
        return $this->_quoteDeviceGroup;
    }

    /**
     * Sets the quote for this device
     *
     * @param Quotegen_Model_Mapper_QuoteDeviceGroup $_quoteDeviceGroup
     *            The quote device group
     */
    public function setQuoteDeviceGroup ($_quoteDeviceGroup)
    {
        $this->_quoteDeviceGroup = $_quoteDeviceGroup;
        return $this;
    }
}
