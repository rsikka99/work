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
    protected $_id;
    
    /**
     * The id of a quote
     *
     * @var int
     */
    protected $_quoteId;
    
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
     * The residual to leave on the device
     *
     * @var int
     */
    protected $_residual;
    
    /**
     * The cost of the package (Can be recalculated)
     *
     * @var number
     */
    protected $_packageCost;
    
    /**
     * The package markup.
     * This is normally the same as the package cost, but the user can edit this to be what they wish.
     *
     * @var number
     */
    protected $_packageMarkup;
    
    /**
     * The toner configuration associated with this quote.
     *
     * @var int
     */
    protected $_tonerConfigId;
    
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
    protected $_quote;
    
    /**
     * The devices attached to a group
     *
     * @var Quotegen_Model_QuoteDeviceGroupDevices
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
            $this->setQuoteId(($params->quoteId));
        if (isset($params->margin) && ! is_null($params->margin))
            $this->setMargin($params->margin);
        if (isset($params->name) && ! is_null($params->name))
            $this->setName($params->name);
        if (isset($params->sku) && ! is_null($params->sku))
            $this->setSku($params->sku);
        if (isset($params->oemCostPerPageMonochrome) && ! is_null($params->oemCostPerPageMonochrome))
            $this->setOemCostPerPageMonochrome($params->oemCostPerPageMonochrome);
        if (isset($params->oemCostPerPageColor) && ! is_null($params->oemCostPerPageColor))
            $this->setOemCostPerPageColor($params->oemCostPerPageColor);
        if (isset($params->compCostPerPageMonochrome) && ! is_null($params->compCostPerPageMonochrome))
            $this->setCompCostPerPageMonochrome($params->compCostPerPageMonochrome);
        if (isset($params->compCostPerPageColor) && ! is_null($params->compCostPerPageColor))
            $this->setCompCostPerPageColor($params->compCostPerPageColor);
        if (isset($params->cost) && ! is_null($params->cost))
            $this->setCost($params->cost);
        if (isset($params->residual) && ! is_null($params->residual))
            $this->setResidual($params->residual);
        if (isset($params->packageCost) && ! is_null($params->packageCost))
            $this->setPackageCost($params->packageCost);
        if (isset($params->packageMarkup) && ! is_null($params->packageMarkup))
            $this->setPackageMarkup($params->packageMarkup);
        if (isset($params->tonerConfigId) && ! is_null($params->tonerConfigId))
            $this->setTonerConfigId($params->tonerConfigId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'quoteId' => $this->getQuoteId(), 
                'margin' => $this->getMargin(), 
                'name' => $this->getName(), 
                'sku' => $this->getSku(), 
                'oemCostPerPageMonochrome' => $this->getOemCostPerPageMonochrome(), 
                'oemCostPerPageColor' => $this->getOemCostPerPageColor(), 
                'compCostPerPageMonochrome' => $this->getCompCostPerPageMonochrome(), 
                'compCostPerPageColor' => $this->getCompCostPerPageColor(), 
                'cost' => $this->getCost(), 
                'residual' => $this->getResidual(), 
                "packageCost" => $this->getPackageCost(), 
                "packageMarkup" => $this->getPackageMarkup(), 
                "tonerConfigId" => $this->getTonerConfigId() 
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
    public function getQuoteId ()
    {
        return $this->_quoteId;
    }

    /**
     * Sets a quote id
     *
     * @param number $_quoteDeviceGroupId
     *            The new quote device group id
     */
    public function setQuoteId ($_quoteId)
    {
        $this->_quoteId = $_quoteId;
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
     * Gets the package cost
     *
     * @return number
     */
    public function getPackageCost ()
    {
        return $this->_packageCost;
    }

    /**
     * Sets the package cost
     *
     * @param number $_packageCost            
     */
    public function setPackageCost ($_packageCost)
    {
        $this->_packageCost = $_packageCost;
        return $this;
    }

    /**
     * Gets the package markup (user cost)
     *
     * @return number
     */
    public function getPackageMarkup ()
    {
        return $this->_packageMarkup;
    }

    /**
     * Sets the package markup (user cost)
     *
     * @param number $_packageMarkup            
     */
    public function setPackageMarkup ($_packageMarkup)
    {
        $this->_packageMarkup = $_packageMarkup;
        return $this;
    }

    /**
     * Gets the toner configuration id
     *
     * @return number
     */
    public function getTonerConfigId ()
    {
        return $this->_tonerConfigId;
    }

    /**
     * Sets the toner configuration id
     *
     * @param number $_tonerConfigId            
     */
    public function setTonerConfigId ($_tonerConfigId)
    {
        $this->_tonerConfigId = $_tonerConfigId;
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
     * Gets the quote for this device
     *
     * @return Quotegen_Model_Quote The quote device group
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
     * Sets the quote for this device
     *
     * @param Quotegen_Model_Quote $_quote
     *            The quote device group
     */
    public function setQuote ($_quote)
    {
        $this->_quote = $_quote;
        return $this;
    }

    /**
     * Gets the quote device group devices
     *
     * @return multitype:Quotegen_Model_QuoteDeviceGroupDevice An array of quote device group devices
     */
    public function getQuoteDeviceGroupDevices ()
    {
        if (! isset($this->_quoteDeviceGroupDevices))
        {
            $this->_quoteDeviceGroupDevices = Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance()->fetchDevicesForQuoteDevice($this->_id);
        }
        return $this->_quoteDeviceGroupDevices;
    }

    /**
     * Sets the quote device group devices
     *
     * @param array $_quoteDeviceGroupDevices
     *            An array of qutoe device group devices
     * @return Quotegen_Model_QuoteDevice
     */
    public function setQuoteDeviceGroupDevices ($_quoteDeviceGroupDevices)
    {
        $this->_quoteDeviceGroupDevices = $_quoteDeviceGroupDevices;
        return this;
    }

    /**
     * ****************************************************************************************************************************************
     * DEVICE PROPERTIES
     * ****************************************************************************************************************************************
     */
    /**
     * Returns whether or not the device is capable of printing in color based on it's comp and oem cpp's (If they are 0
     * it is not a color device)
     *
     * @return bool
     */
    public function isColorCapable ()
    {
        return ((int)$this->getTonerConfigId() !== Proposalgen_Model_TonerConfig::BLACK_ONLY);
    }

    /**
     * ****************************************************************************************************************************************
     * DEVICE CALCULATIONS
     * ****************************************************************************************************************************************
     */
    
    /**
     * Calculates the cost of options for this configuration
     *
     * @return number The cost of the options for this configuration
     */
    public function calculateOptionCost ()
    {
        $cost = 0;
        foreach ( $this->getQuoteDeviceOptions() as $quoteDeviceOption )
        {
            $cost += $quoteDeviceOption->getTotalCost();
        }
        return $cost;
    }

    /**
     * Calculates the cost of the package plus it's options
     */
    public function calculatePackageCost ()
    {
        return $this->getCost() + $this->calculateOptionCost();
    }

    /**
     * Calculates the price of the package.
     *
     * @return number The cost of the package with the margin added onto it.
     */
    public function calculatePackagePrice ()
    {
        $marginPercent = (float)$this->getMargin();
        $cost = (float)$this->calculateFinalPackageCost();
        
        return Tangent_Accounting::applyMargin($cost, $marginPercent);
    }

    /**
     * Calculates the final cost of the package.
     * (Package Cost + Markup)
     * (cost + markup)
     *
     * @return number The package cost.
     */
    public function calculateFinalPackageCost ()
    {
        return $this->getPackageCost() + $this->getPackageMarkup();
    }

    /**
     * Reverse engineers the margin based on the price and cost of the package.
     *
     * @return number The margin
     */
    public function calculateMargin ()
    {
        $cost = (float)$this->calculateFinalPackageCost();
        $price = (float)$this->calculatePackagePrice();
        
        return Tangent_Accounting::reverseEngineerMargin($cost, $price);
    }

    /**
     * Calculates the total quantity of of this configuration over the entire quote.
     *
     * @return number The total quantity.
     */
    public function calculateTotalQuantity ()
    {
        $quantity = 0;
        /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
        foreach ( $this->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
        {
            $quantity += $quoteDeviceGroupDevice->getQuantity();
        }
        return $quantity;
    }

    /**
     * Calculates the total cost of all configurations in this quote.
     * (Quantity * Package cost)
     *
     * @return number The total cost.
     */
    public function calculateTotalCost ()
    {
        return $this->calculateFinalPackageCost() * $this->calculateTotalQuantity();
    }

    /**
     * Calculates the total price of all configurations in this quote.
     * (Quantity * Package Price)
     *
     * @return number The total price.
     */
    public function calculateTotalPrice ()
    {
        return ($this->calculatePackagePrice() + $this->getResidual()) * $this->calculateTotalQuantity();
    }

    /**
     * Calculates the monthly lease price for a single instance of this configuration
     *
     * @return number The monthly lease price
     */
    public function calculateMonthlyLeasePrice ()
    {
        $packagePrice = $this->calculatePackagePrice() + $this->getResidual();
        $leaseFactor = $this->getQuote()->getLeaseRate();
        
        return $packagePrice * $leaseFactor;
    }

    /**
     * Calcualtes the monthly lease price for all instances of this configuration in the quote
     *
     * @return number
     */
    public function calculateTotalMonthlyLeasePrice ()
    {
        return $this->calculateMonthlyLeasePrice() * $this->calculateTotalQuantity();
    }

    /**
     * Calculates the total residual for all instances of this configuration in the quote.
     *
     * @return number
     */
    public function calculateTotalResidual ()
    {
        return $this->getResidual() * $this->calculateTotalQuantity();
    }

    /**
     * Calcualtes the lease value for a single instance of this configuration
     *
     * @return number
     */
    public function calculateLeaseValue ()
    {
        $value = $this->calculatePackagePrice();
        $residual = $this->getResidual();
        $leaseValue = 0;
        
        /*
         * We need to be at or over 0 in all cases, otherwise we might as well return 0 since negative numbers make no
         * sense for this.
         */
        if ($value > 0 && $residual >= 0 && ($value - $residual) >= 0)
        {
            $leaseValue = $value + $residual;
        }
        
        return $leaseValue;
    }

    /**
     * Calculates the total lease value for all instances of this configuration.
     *
     * @return number
     */
    public function calculateTotalLeaseValue ()
    {
        return $this->calculateLeaseValue() * $this->calculateTotalQuantity();
    }

    /**
     * ****************************************************************************************************************************************
     * PAGE CALCULATIONS
     * ****************************************************************************************************************************************
     */
    /**
     * The appropirate cost per page based on toner preference choice
     *
     * @return number the cost per page for color
     */
    public function calculateColorCostPerPage ()
    {
        $getCompCostPerPage = false;
        $costPerPageColor = 0;
        
        // Get the pricing config
        switch ($this->getQuote()
            ->getPricingConfig()
            ->getConfigName())
        {
            case Proposalgen_Model_PricingConfig::COMP :
            case Proposalgen_Model_PricingConfig::OEMMONO_COMPCOLOR :
                $getCompCostPerPage = true;
                break;
        }
        
        // If we want to get comp prices then get them
        if ($getCompCostPerPage)
        {
            $costPerPageColor = $this->getCompCostPerPageColor();
        }
        
        // If not they are set to oem
        if ($costPerPageColor <= 0)
        {
            $costPerPageColor = $this->getOemCostPerPageColor();
        }
        
        /*
         * Adjust for coverage (Disabled since it's not working)
         */
        //$costPerPageColor = Tangent_PrinterMath::adjustDeviceCostPerPage($costPerPageColor, $this->getQuote()->getPageCoverageColor(), $this->getTonerConfigId());
        

        /*
         * Only add service and admin if we have a cpp > 0. This way if cpp is 0 for some reason the end user will see
         * the problem instead of it being masked by service and admin cpp.
         */
        if ($costPerPageColor > 0)
        {
            $costPerPageColor += $this->getQuote()->getAdminCostPerPage() + $this->getQuote()->getServiceCostPerPage();
        }
        return (float)$costPerPageColor;
    }

    /**
     * The appropirate cost per page based on toner preference choice
     *
     * @return number the cost per page for monochrome
     */
    public function calculateMonochromeCostPerPage ()
    {
        $getCompCostPerPage = false;
        $costPerPageMonochrome = 0;
        
        // Figure out which pricing configuration the quote is set for
        switch ($this->getQuote()
            ->getPricingConfig()
            ->getConfigName())
        {
            case Proposalgen_Model_PricingConfig::COMP :
            case Proposalgen_Model_PricingConfig::COMPMONO_OEMCOLOR :
                $getCompCostPerPage = true;
                break;
        }
        
        // If we want to get comp prices then get them
        if ($getCompCostPerPage)
        {
            $costPerPageMonochrome = $this->getCompCostPerPageMonochrome();
        }
        
        // If not they are set to oem
        if ($costPerPageMonochrome <= 0)
        {
            $costPerPageMonochrome = $this->getOemCostPerPageMonochrome();
        }
        
        /*
         * Adjust for coverage (Disabled since it's not working)
         */
        //$costPerPageMonochrome = Tangent_PrinterMath::adjustDeviceCostPerPage($costPerPageMonochrome, $this->getQuote()->getPageCoverageMonochrome(), $this->getTonerConfigId());
        

        /*
         * Only add service and admin if we have a cpp > 0. This way if cpp is 0 for some reason the end user will see
         * the problem instead of it being masked by service and admin cpp.
         */
        if ($costPerPageMonochrome > 0)
        {
            $costPerPageMonochrome += $this->getQuote()->getAdminCostPerPage() + $this->getQuote()->getServiceCostPerPage();
        }
        
        return $costPerPageMonochrome;
    }
}
