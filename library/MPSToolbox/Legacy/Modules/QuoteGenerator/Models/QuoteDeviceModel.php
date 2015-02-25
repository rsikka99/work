<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use Exception;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerMasterDeviceAttributeMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DealerMasterDeviceAttributeModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceGroupDeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteMapper;
use My_Model_Abstract;
use Tangent\Accounting;
use Zend_Auth;

/**
 * Class QuoteDeviceModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class QuoteDeviceModel extends My_Model_Abstract
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
    public $costPerPageMonochrome;

    /**
     * @var int
     */
    public $costPerPageColor;

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
    public $buyoutValue;

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
     * @var QuoteDeviceConfigurationModel
     */
    protected $_device;

    /**
     * The quote device options that are stored separately from device configuration options
     *
     * @var QuoteDeviceOptionModel
     */
    protected $_quoteDeviceOptions;

    /**
     * The quote that this device is for
     *
     * @var QuoteModel
     */
    protected $_quote;

    /**
     * The devices attached to a group
     *
     * @var QuoteDeviceGroupDeviceModel[]
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

        if (isset($params->costPerPageMonochrome) && !is_null($params->costPerPageMonochrome))
        {
            $this->costPerPageMonochrome = $params->costPerPageMonochrome;
        }

        if (isset($params->costPerPageColor) && !is_null($params->costPerPageColor))
        {
            $this->costPerPageColor = $params->costPerPageColor;
        }

        if (isset($params->cost) && !is_null($params->cost))
        {
            $this->cost = $params->cost;
        }

        if (isset($params->buyoutValue) && !is_null($params->buyoutValue))
        {
            $this->buyoutValue = $params->buyoutValue;
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
        return [
            "id"                    => $this->id,
            "quoteId"               => $this->quoteId,
            "margin"                => $this->margin,
            "name"                  => $this->name,
            "oemSku"                => $this->oemSku,
            "dealerSku"             => $this->dealerSku,
            "costPerPageMonochrome" => $this->costPerPageMonochrome,
            "costPerPageColor"      => $this->costPerPageColor,
            "cost"                  => $this->cost,
            "buyoutValue"           => $this->buyoutValue,
            "packageCost"           => $this->packageCost,
            "packageMarkup"         => $this->packageMarkup,
            "tonerConfigId"         => $this->tonerConfigId,
        ];
    }

    /**
     * Gets the device associated with this quote device
     *
     * @return DeviceModel The device configuration
     */
    public function getDevice ()
    {
        if (!isset($this->_device))
        {
            $this->_device            = false;
            $quoteDeviceConfiguration = QuoteDeviceConfigurationMapper::getInstance()->findByQuoteDeviceId($this->id);
            if ($quoteDeviceConfiguration)
            {
                $this->_device = DeviceMapper::getInstance()->find([$quoteDeviceConfiguration->masterDeviceId, Zend_Auth::getInstance()->getIdentity()->dealerId]);
            }
        }

        return $this->_device;
    }

    /**
     * Sets the device configuration associated with this quote device
     *
     * @param DeviceModel $_device
     *            The new device configuration.
     *
     * @return $this
     */
    public function setDevice ($_device)
    {
        $this->_device = $_device;

        return $this;
    }

    /**
     * Gets the quote device options
     *
     * @return QuoteDeviceOptionModel[]
     */
    public function getQuoteDeviceOptions ()
    {
        if (!isset($this->_quoteDeviceOptions))
        {
            $this->_quoteDeviceOptions = QuoteDeviceOptionMapper::getInstance()->fetchAllOptionsForQuoteDevice($this->id);
        }

        return $this->_quoteDeviceOptions;
    }

    /**
     * Sets the quote device options
     *
     * @param QuoteDeviceOptionModel $_quoteDeviceOptions
     *            The new options for the device
     *
     * @return $this
     */
    public function setQuoteDeviceOptions ($_quoteDeviceOptions)
    {
        $this->_quoteDeviceOptions = $_quoteDeviceOptions;

        return $this;
    }

    /**
     * Gets the quote for this device
     *
     * @return QuoteModel The quote device group
     */
    public function getQuote ()
    {
        if (!isset($this->_quote))
        {
            $this->_quote = QuoteMapper::getInstance()->find($this->quoteId);
        }

        return $this->_quote;
    }

    /**
     * Sets the quote for this device
     *
     * @param QuoteModel $_quote
     *            The quote device group
     *
     * @return $this
     */
    public function setQuote ($_quote)
    {
        $this->_quote = $_quote;

        return $this;
    }

    /**
     * Gets the quote device group devices
     *
     * @return QuoteDeviceGroupDeviceModel[]
     */
    public function getQuoteDeviceGroupDevices ()
    {
        if (!isset($this->_quoteDeviceGroupDevices))
        {
            $this->_quoteDeviceGroupDevices = QuoteDeviceGroupDeviceMapper::getInstance()->fetchDevicesForQuoteDevice($this->id);
        }

        return $this->_quoteDeviceGroupDevices;
    }

    /**
     * Sets the quote device group devices
     *
     * @param QuoteDeviceGroupDeviceModel[] $_quoteDeviceGroupDevices An array of quote device group devices
     *
     * @return $this
     */
    public function setQuoteDeviceGroupDevices ($_quoteDeviceGroupDevices)
    {
        $this->_quoteDeviceGroupDevices = $_quoteDeviceGroupDevices;

        return $this;
    }

    /**
     * Returns the appropriate SKU, dealer SKU if filled out, OEM SKU if it is empty
     *
     * @return String the SKU to show on the reports
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
     * Returns whether or not the device is capable of printing in color based on it's comp and OEM CPP's (If they are 0
     * it is not a color device)
     *
     * @return bool
     */
    public function isColorCapable ()
    {
        return ((int)$this->tonerConfigId !== TonerConfigModel::BLACK_ONLY);
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

        return Accounting::applyMargin($cost, $marginPercent);
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

        return Accounting::reverseEngineerMargin($cost, $price);
    }

    /**
     * Calculates the total quantity of of this configuration over the entire quote.
     *
     * @return number The total quantity.
     */
    public function calculateTotalQuantity ()
    {
        $quantity = 0;
        /* @var $quoteDeviceGroupDevice QuoteDeviceGroupDeviceModel */
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
        return ($this->calculatePackagePrice() + $this->buyoutValue) * $this->calculateTotalQuantity();
    }

    /**
     * Calculates the monthly lease price for a single instance of this configuration
     *
     * @return number The monthly lease price
     */
    public function calculateMonthlyLeasePrice ()
    {
        $packagePrice = $this->calculatePackagePrice() + $this->buyoutValue;
        $leaseFactor  = $this->getQuote()->leaseRate;

        return $packagePrice * $leaseFactor;
    }

    /**
     * Calculates the monthly lease price for all instances of this configuration in the quote
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
        return $this->buyoutValue * $this->calculateTotalQuantity();
    }

    /**
     * Calculates the lease value for a single instance of this configuration
     *
     * @return number
     */
    public function calculateLeaseValue ()
    {
        $value       = $this->calculatePackagePrice();
        $buyoutValue = $this->buyoutValue;
        $leaseValue  = 0;

        /*
         * We need to be at or over 0 in all cases, otherwise we might as well return 0 since negative numbers make no
         * sense for this. Norm never wants a value to not show up on the hardware Financing page, so as long as both numbers
         * are greater than or equal to 0, then we will get the lease value.
         */
        if ($value >= 0 && $buyoutValue >= 0)
        {
            $leaseValue = $value + $buyoutValue;
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
     * The appropriate cost per page based on toner preference choice
     *
     * @throws Exception
     * @return number the cost per page for color
     */
    public function calculateColorCostPerPage ()
    {

        $costPerPageColor = $this->costPerPageColor;

        /*
         * Only add service and admin if we have a CPP > 0. This way if CPP is 0 for some reason the end user will see
         * the problem instead of it being masked by service and admin CPP.
         */
        if ($costPerPageColor > 0)
        {
            $deviceAttributes = DealerMasterDeviceAttributeMapper::getInstance()->find([$this->getDevice()->masterDeviceId, $this->getDevice()->dealerId]);
            if ($deviceAttributes instanceof DealerMasterDeviceAttributeModel)
            {
                $costPerPageColor += $this->getQuote()->adminCostPerPage + $deviceAttributes->laborCostPerPage + $deviceAttributes->partsCostPerPage;
            }
            else
            {
                throw new Exception("Cannot calculate color cost per page because device attributes were not found.");
            }
        }

        return (float)$costPerPageColor;
    }

    /**
     * The appropriate cost per page based on toner preference choice
     *
     * @throws Exception
     * @return number the cost per page for monochrome
     */
    public function calculateMonochromeCostPerPage ()
    {
        $costPerPageMonochrome = $this->costPerPageMonochrome;
        /*
         * Only add service and admin if we have a CPP > 0. This way if CPP is 0 for some reason the end user will see
         * the problem instead of it being masked by service and admin CPP.
         */
        if ($costPerPageMonochrome > 0)
        {
            $deviceAttributes = DealerMasterDeviceAttributeMapper::getInstance()->find([$this->getDevice()->masterDeviceId, $this->getDevice()->dealerId]);
            if ($deviceAttributes instanceof DealerMasterDeviceAttributeModel)
            {
                $costPerPageMonochrome += $this->getQuote()->adminCostPerPage + $deviceAttributes->laborCostPerPage + $deviceAttributes->partsCostPerPage;
            }
            else
            {
                throw new Exception("Cannot calculate mono cost per page because device attributes were not found.");
            }
        }

        return (float)$costPerPageMonochrome;
    }
}