<?php
class Quotegen_Model_QuoteDevice extends My_Model_Abstract
{

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $quoteId;

    /**
     * @var int
     */
    public $margin;

    /**
     * @var int
     */
    public $name;

    /**
     * @var int
     */
    public $oemSku;

    /**
     * @var int
     */
    public $dealerSku;

    /**
     * @var int
     */
    public $oemCostPerPageMonochrome;

    /**
     * @var int
     */
    public $oemCostPerPageColor;

    /**
     * @var int
     */
    public $compCostPerPageMonochrome;

    /**
     * @var int
     */
    public $compCostPerPageColor;

    /**
     * @var int
     */
    public $cost;

    /**
     * @var int
     */
    public $residual;

    /**
     * @var int
     */
    public $packageCost;

    /**
     * @var int
     */
    public $packageMarkup;

    /**
     * @var int
     */
    public $tonerConfigId;

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

        if (isset($params->quoteId) && !is_null($params->quoteId))
        {
            $this->quoteId = $params->quoteId;
        }

        if (isset($params->margin) && !is_null($params->margin))
        {
            $this->margin = $params->margin;
        }

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }

        if (isset($params->oemSku) && !is_null($params->oemSku))
        {
            $this->oemSku = $params->oemSku;
        }

        if (isset($params->dealerSku) && !is_null($params->dealerSku))
        {
            $this->dealerSku = $params->dealerSku;
        }

        if (isset($params->oemCostPerPageMonochrome) && !is_null($params->oemCostPerPageMonochrome))
        {
            $this->oemCostPerPageMonochrome = $params->oemCostPerPageMonochrome;
        }

        if (isset($params->oemCostPerPageColor) && !is_null($params->oemCostPerPageColor))
        {
            $this->oemCostPerPageColor = $params->oemCostPerPageColor;
        }

        if (isset($params->compCostPerPageMonochrome) && !is_null($params->compCostPerPageMonochrome))
        {
            $this->compCostPerPageMonochrome = $params->compCostPerPageMonochrome;
        }

        if (isset($params->compCostPerPageColor) && !is_null($params->compCostPerPageColor))
        {
            $this->compCostPerPageColor = $params->compCostPerPageColor;
        }

        if (isset($params->cost) && !is_null($params->cost))
        {
            $this->cost = $params->cost;
        }

        if (isset($params->residual) && !is_null($params->residual))
        {
            $this->residual = $params->residual;
        }

        if (isset($params->packageCost) && !is_null($params->packageCost))
        {
            $this->packageCost = $params->packageCost;
        }

        if (isset($params->packageMarkup) && !is_null($params->packageMarkup))
        {
            $this->packageMarkup = $params->packageMarkup;
        }

        if (isset($params->tonerConfigId) && !is_null($params->tonerConfigId))
        {
            $this->tonerConfigId = $params->tonerConfigId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                        => $this->id,
            "quoteId"                   => $this->quoteId,
            "margin"                    => $this->margin,
            "name"                      => $this->name,
            "oemSku"                    => $this->oemSku,
            "dealerSku"                 => $this->dealerSku,
            "oemCostPerPageMonochrome"  => $this->oemCostPerPageMonochrome,
            "oemCostPerPageColor"       => $this->oemCostPerPageColor,
            "compCostPerPageMonochrome" => $this->compCostPerPageMonochrome,
            "compCostPerPageColor"      => $this->compCostPerPageColor,
            "cost"                      => $this->cost,
            "residual"                  => $this->residual,
            "packageCost"               => $this->packageCost,
            "packageMarkup"             => $this->packageMarkup,
            "tonerConfigId"             => $this->tonerConfigId,
        );
    }

    /**
     * Gets the device associated with this quote device
     *
     * @return Quotegen_Model_Device The device configuration
     */
    public function getDevice ()
    {
        if (!isset($this->_device))
        {
            $this->_device            = false;
            $quoteDeviceConfiguration = Quotegen_Model_Mapper_QuoteDeviceConfiguration::getInstance()->findByQuoteDeviceId($this->id);
            if ($quoteDeviceConfiguration)
            {
                $this->_device = Quotegen_Model_Mapper_Device::getInstance()->find($quoteDeviceConfiguration->masterDeviceId);
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
        if (!isset($this->_quoteDeviceOptions))
        {
            $this->_quoteDeviceOptions = Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->fetchAllOptionsForQuoteDevice($this->id);
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
        if (!isset($this->_quote))
        {
            $this->_quote = Quotegen_Model_Mapper_Quote::getInstance()->find($this->quoteId);
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
        if (!isset($this->_quoteDeviceGroupDevices))
        {
            $this->_quoteDeviceGroupDevices = Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance()->fetchDevicesForQuoteDevice($this->id);
        }

        return $this->_quoteDeviceGroupDevices;
    }

    /**
     * Sets the quote device group devices
     *
     * @param array $_quoteDeviceGroupDevices
     *            An array of qutoe device group devices
     *
     * @return Quotegen_Model_QuoteDevice
     */
    public function setQuoteDeviceGroupDevices ($_quoteDeviceGroupDevices)
    {
        $this->_quoteDeviceGroupDevices = $_quoteDeviceGroupDevices;

        return this;
    }

    /**
     * Returns the appropriate sku, dealer sku if filled out, oem sku if it is empty
     *
     * @return String the sku to show on the reports
     */
    public function getReportSku ()
    {
        return ($this->dealerSku === null ? $this->oemSku : $this->dealerSku);
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
        return ((int)$this->tonerConfigId !== Proposalgen_Model_TonerConfig::BLACK_ONLY);
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
        foreach ($this->getQuoteDeviceOptions() as $quoteDeviceOption)
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
        return $this->cost + $this->calculateOptionCost();
    }

    /**
     * Calculates the price of the package.
     *
     * @return number The cost of the package with the margin added onto it.
     */
    public function calculatePackagePrice ()
    {
        $marginPercent = (float)$this->margin;
        $cost          = (float)$this->calculateFinalPackageCost();

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
        return $this->packageCost + $this->packageMarkup;
    }

    /**
     * Reverse engineers the margin based on the price and cost of the package.
     *
     * @return number The margin
     */
    public function calculateMargin ()
    {
        $cost  = (float)$this->calculateFinalPackageCost();
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
        foreach ($this->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice)
        {
            $quantity += $quoteDeviceGroupDevice->quantity;
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
        return ($this->calculatePackagePrice() + $this->residual) * $this->calculateTotalQuantity();
    }

    /**
     * Calculates the monthly lease price for a single instance of this configuration
     *
     * @return number The monthly lease price
     */
    public function calculateMonthlyLeasePrice ()
    {
        $packagePrice = $this->calculatePackagePrice() + $this->residual;
        $leaseFactor  = $this->getQuote()->leaseRate;

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
        return $this->residual * $this->calculateTotalQuantity();
    }

    /**
     * Calcualtes the lease value for a single instance of this configuration
     *
     * @return number
     */
    public function calculateLeaseValue ()
    {
        $value      = $this->calculatePackagePrice();
        $residual   = $this->residual;
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
        $costPerPageColor   = 0;

        // Get the pricing config
        switch ($this->getQuote()->pricingConfigId)
        {
            case Proposalgen_Model_PricingConfig::COMP :
            case Proposalgen_Model_PricingConfig::OEMMONO_COMPCOLOR :
                $getCompCostPerPage = true;
                break;
        }

        // If we want to get comp prices then get them
        if ($getCompCostPerPage)
        {
            $costPerPageColor = $this->compCostPerPageColor;
        }

        // If not they are set to oem
        if ($costPerPageColor <= 0.0)
        {
            $costPerPageColor = $this->oemCostPerPageColor;
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
            $costPerPageColor += $this->getQuote()->adminCostPerPage + $this->getQuote()->laborCostPerPage + $this->getQuote()->partsCostPerPage;
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
        $getCompCostPerPage    = false;
        $costPerPageMonochrome = 0;

        // Figure out which pricing configuration the quote is set for
        switch ($this->getQuote()->pricingConfigId)
        {
            case Proposalgen_Model_PricingConfig::COMP :
            case Proposalgen_Model_PricingConfig::COMPMONO_OEMCOLOR :
                $getCompCostPerPage = true;
                break;
        }

        // If we want to get comp prices then get them
        if ($getCompCostPerPage)
        {
            $costPerPageMonochrome = $this->compCostPerPageMonochrome;
        }

        // If not they are set to oem
        if ($costPerPageMonochrome <= 0.0)
        {
            $costPerPageMonochrome = $this->oemCostPerPageMonochrome;
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
            $costPerPageMonochrome += $this->getQuote()->adminCostPerPage + $this->getQuote()->partsCostPerPage + $this->getQuote()->laborCostPerPage;
        }

        return $costPerPageMonochrome;
    }
}