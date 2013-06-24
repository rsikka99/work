<?php

/**
 * Quotegen_Model_Quote
 *
 * @author Lee Robert
 *
 */
/* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
/* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
/* @var $quoteDevice Quotegen_Model_QuoteDevice */

class Quotegen_Model_Quote extends My_Model_Abstract
{
    const QUOTE_TYPE_LEASED    = 'leased';
    const QUOTE_TYPE_PURCHASED = 'purchased';

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $clientId;

    /**
     * @var string
     */
    public $dateCreated;

    /**
     * @var string
     */
    public $dateModified;

    /**
     * @var string
     */
    public $quoteDate;

    /**
     * @var string
     */
    public $clientDisplayName;

    /**
     * @var int
     */
    public $leaseTerm;

    /**
     * @var int
     */
    public $leaseRate;

    /**
     * The client associated with the quote
     *
     * @var Quotegen_Model_Client
     */
    protected $_client;

    /**
     * The quote devices attached to the quote
     *
     * @var array
     */
    protected $_quoteDeviceGroups;

    /**
     * @var float
     */
    public $pageCoverageMonochrome;

    /**
     * @var float
     */
    public $pageCoverageColor;

    /**
     * @var float
     */
    public $monochromePageMargin;

    /**
     * @var float
     */
    public $colorPageMargin;

    /**
     * @var float
     */
    public $monochromeOverageMargin;

    /**
     * @var float
     */
    public $colorOverageMargin;

    /**
     * @var float
     */
    public $adminCostPerPage;

    /**
     * @var int
     */
    public $pricingConfigId;

    /**
     * @var string
     */
    public $quoteType;

    /**
     * A pricing config object
     *
     * @var Proposalgen_Model_PricingConfig
     */
    protected $_pricingConfig;

    /**
     * The leasing schema term for the quote
     *
     * @var Quotegen_Model_LeasingSchemaTerm
     */
    protected $_leasingSchemaTerm;

    /**
     * The quote device configurations in this quote
     *
     * @var Quotegen_Model_QuoteDevice[]
     */
    protected $_quoteDevices;


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

        if (isset($params->clientId) && !is_null($params->clientId))
        {
            $this->clientId = $params->clientId;
        }

        if (isset($params->dateCreated) && !is_null($params->dateCreated))
        {
            $this->dateCreated = $params->dateCreated;
        }

        if (isset($params->dateModified) && !is_null($params->dateModified))
        {
            $this->dateModified = $params->dateModified;
        }

        if (isset($params->quoteDate) && !is_null($params->quoteDate))
        {
            $this->quoteDate = $params->quoteDate;
        }

        if (isset($params->clientDisplayName) && !is_null($params->clientDisplayName))
        {
            $this->clientDisplayName = $params->clientDisplayName;
        }

        if (isset($params->leaseTerm) && !is_null($params->leaseTerm))
        {
            $this->leaseTerm = $params->leaseTerm;
        }

        if (isset($params->leaseRate) && !is_null($params->leaseRate))
        {
            $this->leaseRate = $params->leaseRate;
        }

        if (isset($params->pageCoverageMonochrome) && !is_null($params->pageCoverageMonochrome))
        {
            $this->pageCoverageMonochrome = $params->pageCoverageMonochrome;
        }

        if (isset($params->pageCoverageColor) && !is_null($params->pageCoverageColor))
        {
            $this->pageCoverageColor = $params->pageCoverageColor;
        }

        if (isset($params->monochromePageMargin) && !is_null($params->monochromePageMargin))
        {
            $this->monochromePageMargin = $params->monochromePageMargin;
        }

        if (isset($params->colorPageMargin) && !is_null($params->colorPageMargin))
        {
            $this->colorPageMargin = $params->colorPageMargin;
        }

        if (isset($params->monochromeOverageMargin) && !is_null($params->monochromeOverageMargin))
        {
            $this->monochromeOverageMargin = $params->monochromeOverageMargin;
        }

        if (isset($params->colorOverageMargin) && !is_null($params->colorOverageMargin))
        {
            $this->colorOverageMargin = $params->colorOverageMargin;
        }

        if (isset($params->adminCostPerPage) && !is_null($params->adminCostPerPage))
        {
            $this->adminCostPerPage = $params->adminCostPerPage;
        }

        if (isset($params->pricingConfigId) && !is_null($params->pricingConfigId))
        {
            $this->pricingConfigId = $params->pricingConfigId;
        }

        if (isset($params->quoteType) && !is_null($params->quoteType))
        {
            $this->quoteType = $params->quoteType;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                      => $this->id,
            "clientId"                => $this->clientId,
            "dateCreated"             => $this->dateCreated,
            "dateModified"            => $this->dateModified,
            "quoteDate"               => $this->quoteDate,
            "clientDisplayName"       => $this->clientDisplayName,
            "leaseTerm"               => $this->leaseTerm,
            "leaseRate"               => $this->leaseRate,
            "pageCoverageMonochrome"  => $this->pageCoverageMonochrome,
            "pageCoverageColor"       => $this->pageCoverageColor,
            "monochromePageMargin"    => $this->monochromePageMargin,
            "colorPageMargin"         => $this->colorPageMargin,
            "monochromeOverageMargin" => $this->monochromeOverageMargin,
            "colorOverageMargin"      => $this->colorOverageMargin,
            "adminCostPerPage"        => $this->adminCostPerPage,
            "pricingConfigId"         => $this->pricingConfigId,
            "quoteType"               => $this->quoteType,
        );
    }

    /**
     * Gets the client for the report
     *
     * @return Quotegen_Model_Client
     */
    public function getClient ()
    {
        if (!isset($this->_client) && isset($this->clientId))
        {
            $this->_client = Quotegen_Model_Mapper_Client::getInstance()->find($this->clientId);
        }

        return $this->_client;
    }

    /**
     * Sets the client for the report (Also sets the client id of the report if the client has one.
     *
     * @param $_client Quotegen_Model_Client
     *                 The new client
     *
     * @return \Quotegen_Model_Quote
     */
    public function setClient (Quotegen_Model_Client $_client)
    {
        $this->_client = $_client;
        if ($_client->id !== null)
        {
            $this->clientId = $_client->id;
        }

        return $this;
    }

    /**
     * Gets the quote devices for the quote
     *
     * @return Quotegen_Model_QuoteDeviceGroup[].
     *
     */
    public function getQuoteDeviceGroups ()
    {
        if (!isset($this->_quoteDeviceGroups))
        {
            $this->_quoteDeviceGroups = Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->fetchDeviceGroupsForQuote($this->id);
        }

        return $this->_quoteDeviceGroups;
    }

    /**
     * Sets the quote devices for the quote
     *
     * @param  Quotegen_Model_QuoteDeviceGroup[] $_quoteDeviceGroups The quote devices.
     *
     * @return \Quotegen_Model_Quote
     */
    public function setQuoteDeviceGroups ($_quoteDeviceGroups)
    {
        $this->_quoteDeviceGroups = $_quoteDeviceGroups;

        return $this;
    }

    /**
     * Gets the pricing config object
     *
     * @return Proposalgen_Model_PricingConfig The pricing config object.
     */
    public function getPricingConfig ()
    {
        if (!isset($this->_pricingConfig))
        {
            $this->_pricingConfig = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($this->pricingConfigId);
        }

        return $this->_pricingConfig;
    }

    /**
     * Sets the pricing config object
     *
     * @param $_pricingConfig Proposalgen_Model_PricingConfig
     *                        The new pricing config.
     *
     * @return $this
     */
    public function setPricingConfig ($_pricingConfig)
    {
        $this->_pricingConfig = $_pricingConfig;

        return $this;
    }

    /**
     * Gets the leasing schema term
     *
     * @return Quotegen_Model_LeasingSchemaTerm
     */
    public function getLeasingSchemaTerm ()
    {
        if (!isset($this->_leasingSchemaTerm))
        {
            $quoteLeaseTerm = Quotegen_Model_Mapper_QuoteLeaseTerm::getInstance()->find($this->id);
            if ($quoteLeaseTerm)
            {
                $this->_leasingSchemaTerm = $quoteLeaseTerm->getLeaseTerm();
            }
        }

        return $this->_leasingSchemaTerm;
    }

    /**
     * Sets the leasing schema term
     *
     * @param $_leasingSchemaTerm Quotegen_Model_LeasingSchemaTerm
     *
     * @return $this
     */
    public function setLeasingSchemaTerm ($_leasingSchemaTerm)
    {
        $this->_leasingSchemaTerm = $_leasingSchemaTerm;

        return $this;
    }

    /**
     * Gets all the quote device configurations for a quote
     *
     * @return Quotegen_Model_QuoteDevice[]
     */
    public function getQuoteDevices ()
    {
        if (!isset($this->_quoteDevices))
        {
            $this->_quoteDevices = Quotegen_Model_Mapper_QuoteDevice::getInstance()->fetchDevicesForQuote($this->id);
        }

        return $this->_quoteDevices;
    }

    /**
     * Sets all the quote device configurations for a quote
     *
     * @param Quotegen_Model_QuoteDevice[] $_quoteDevices
     *
     * @return $this
     */
    public function setQuoteDevices ($_quoteDevices)
    {
        $this->_quoteDevices = $_quoteDevices;

        return $this;
    }

    /**
     * Creates everything needed for a quote
     *
     * @param $quoteType
     * @param $clientId
     * @param $userId
     *
     * @return $this
     */
    public function createNewQuote ($quoteType, $clientId, $userId)
    {
        // Get the system and user defaults and apply overrides for user settings
        $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
        $user         = Application_Model_Mapper_User::getInstance()->find($userId);
        $quoteSetting->applyOverride($user->getUserSettings()->getQuoteSettings());

        // Update current quote object and save new quote items to database
        $this->populate($quoteSetting->toArray());
        $this->quoteType               = $quoteType;
        $this->clientId                = $clientId;
        $this->dateCreated             = date('Y-m-d H:i:s');
        $this->dateModified            = date('Y-m-d H:i:s');
        $this->quoteDate               = date('Y-m-d H:i:s');
        $this->userId                  = $userId;
        $this->colorPageMargin         = $quoteSetting->pageMargin;
        $this->monochromePageMargin    = $quoteSetting->pageMargin;
        $this->colorOverageMargin      = $quoteSetting->pageMargin;
        $this->monochromeOverageMargin = $quoteSetting->pageMargin;
        $this->id                      = Quotegen_Model_Mapper_Quote::getInstance()->insert($this);

        // Add a default group
        $quoteDeviceGroup            = new Quotegen_Model_QuoteDeviceGroup();
        $quoteDeviceGroup->name      = 'Default Group (Ungrouped)';
        $quoteDeviceGroup->isDefault = 1;
        $quoteDeviceGroup->setGroupPages(0);
        $quoteDeviceGroup->quoteId = $this->id;
        Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->insert($quoteDeviceGroup);

        // If this is a leased quote, select the first leasing schema term
        if ($this->isLeased())
        {
            // FIXME: Use quote settings?
            $leasingSchemaTerms = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance()->fetchAll();
            if (count($leasingSchemaTerms) > 0)
            {

                $quoteLeaseTerm                      = new Quotegen_Model_QuoteLeaseTerm();
                $quoteLeaseTerm->quoteId             = $this->id;
                $quoteLeaseTerm->leasingSchemaTermId = $leasingSchemaTerms [0]->id;
                Quotegen_Model_Mapper_QuoteLeaseTerm::getInstance()->insert($quoteLeaseTerm);
            }
        }

        return $this;
    }

    /**
     * ****************************************************************************************************************************************
     * QUOTE CALCULATIONS
     * ****************************************************************************************************************************************
     */

    /**
     * Returns if a quote is being leased or not.
     *
     * @return bool True if the quote is leased
     */
    public function isLeased ()
    {
        return ($this->quoteType === Quotegen_Model_Quote::QUOTE_TYPE_LEASED);
    }

    /**
     * Calculates the total lease value for the quote
     *
     * @return number The total lease value
     */
    public function calculateTotalLeaseValue ()
    {
        $leaseValue = 0;

        foreach ($this->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {
            $leaseValue += $quoteDeviceGroup->calculateLeaseValue();
        }

        return $leaseValue;
    }

    /**
     * Calculates the total lease value for the quote's hardware (no pages included here)
     *
     * @return number The total lease value
     */
    public function calculateTotalHardwareLeaseValue ()
    {
        $leaseValue = 0;

        foreach ($this->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {
            $leaseValue += $quoteDeviceGroup->calculateLeaseValue();
        }

        return $leaseValue;
    }

    /**
     * ****************************************************************************************************************************************
     * DEVICE CALCULATIONS
     * ****************************************************************************************************************************************
     */

    /**
     * Calculates the average margin for the devices
     *
     * @return number The average margin
     */
    public function calculateAverageDeviceMargin ()
    {
        $margin      = 0;
        $deviceCount = count($this->getQuoteDevices);

        foreach ($this->getQuoteDevices() as $quoteDevice)
        {
            $margin += $quoteDevice->margin;
        }

        if ($deviceCount > 1)
        {
            $margin = $margin / $deviceCount;
        }

        return $margin;
    }

    /**
     * Calculates what the monthly lease price of the quote is.
     *
     * @return number The monthly lease payment
     */
    public function calculateTotalMonthlyLeasePrice ()
    {
        $leaseValue     = $this->calculateTotalHardwareLeaseValue();
        $monthlyPayment = 0;
        $leaseFactor    = $this->leaseRate;

        if (!empty($leaseFactor) && !empty($leaseValue))
        {
            $monthlyPayment = $leaseFactor * $leaseValue;
        }

        return $monthlyPayment;
    }

    /**
     * Calculates the total cost of the quote (uses the markup value)
     *
     * @return number The total cost
     */
    public function calculateTotalCost ()
    {
        $totalCost = 0;

        foreach ($this->getQuoteDevices() as $quoteDevice)
        {
            $totalCost += $quoteDevice->calculateTotalCost();
        }

        return $totalCost;
    }

    /**
     * Calculates the total price of the quote
     *
     * @return number The total price
     */
    public function calculateTotalPrice ()
    {
        $totalPrice = 0;

        foreach ($this->getQuoteDevices() as $quoteDevice)
        {
            $totalPrice += $quoteDevice->calculateTotalPrice();
        }

        return $totalPrice;
    }

    /**
     * Calculates the total number of devices in the quote
     *
     * @return number The total residual for the quote
     */
    public function calculateTotalQuantity ()
    {
        $deviceCount = 0;

        foreach ($this->getQuoteDevices() as $quoteDevice)
        {
            $deviceCount += $quoteDevice->calculateTotalQuantity();
        }

        return $deviceCount;
    }

    /**
     * Calculates the total residual
     *
     * @return number The total residual for the quote
     */
    public function calculateTotalResidual ()
    {
        $totalResidual = 0;

        foreach ($this->getQuoteDevices() as $quoteDevice)
        {
            $totalResidual += $quoteDevice->calculateTotalResidual();
        }

        return $totalResidual;
    }

    /**
     * Calculates the sub total for the quote's devices.
     * This is the number used for the purchase total.
     *
     * @return number The sub total
     */
    public function calculateQuoteSubtotal ()
    {
        $subtotal = 0;

        foreach ($this->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {
            $subtotal += $quoteDeviceGroup->calculateGroupSubtotal();
        }

        return $subtotal;
    }

    /**
     * Calculates the lease sub total for the quote's devices.
     *
     * @return number The sub total
     */
    public function calculateQuoteMonthlyLeaseSubtotal ()
    {
        $subtotal = 0;

        foreach ($this->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {
            $subtotal += $quoteDeviceGroup->calculateMonthlyLeasePrice();
        }

        return $subtotal;
    }

    /**
     * Calculates the lease sub total for the quote's devices.
     *
     * @return number The sub total
     */
    public function calculateQuoteLeaseValue ()
    {
        $subtotal = 0;

        foreach ($this->getQuoteDevices() as $quoteDevice)
        {
            $subtotal += $quoteDevice->calculateTotalLeaseValue();
        }

        return $subtotal;
    }

    /**
     * Gives a count of the number of devices attached to the quote.
     *
     * @return number The number of devices
     */
    public function countDevices ()
    {
        $count = 0;

        foreach ($this->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {
            $count += count($quoteDeviceGroup->getQuoteDeviceGroupDevices());
        }

        return $count;
    }

    /**
     * Calculates the average margin across all devices
     *
     * @return number The average margin
     */
    public function calculateTotalMargin ()
    {
        $cost   = $this->calculateTotalCost();
        $price  = $this->calculateQuoteSubtotal();
        $margin = 0;

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

        return $margin;
    }

    /**
     * ****************************************************************************************************************************************
     * PAGE CALCULATIONS
     * ****************************************************************************************************************************************
     */

    /**
     * Get the number of monochrome pages attached to quote
     *
     * @return int The number of monochrome pages that is attached to this quote
     */
    public function calculateTotalMonochromePages ()
    {
        $quantity = 0;

        foreach ($this->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {
            $quantity += $quoteDeviceGroup->calculateTotalMonochromePages();
        }

        return $quantity;
    }

    /**
     * Get the number of color pages attached to quote
     *
     * @return int The number of color pages that is attached to this quote
     */
    public function calculateTotalColorPages ()
    {
        $quantity = 0;

        foreach ($this->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {
            $quantity += $quoteDeviceGroup->calculateTotalColorPages();
        }

        return $quantity;
    }

    /**
     * Gets the cost per page for monochrome pages for the whole quote
     *
     * @return float
     */
    public function calculateMonochromeCostPerPage ()
    {
        // Represents quote total page weight
        $monochromeTotal = 0;
        // The total cpp for all quote devices, used for calculation with no pages
        $totalCpp = 0;
        // Total device count, used for calculation with no pages in quote
        $totalDevices = 0;
        // Flag to see if pages exist
        $quoteHasPages = false;

        // Represents quote total costs for pages
        $quoteDeviceGroupDeviceCost = 0;

        foreach ($this->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {
            foreach ($quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice)
            {
                // Total weight
                $monochromeTotal += $quoteDeviceGroupDevice->monochromePagesQuantity * $quoteDeviceGroupDevice->quantity;

                // Total Cost for pages
                if ($quoteDeviceGroupDevice->monochromePagesQuantity > 0)
                {
                    $quoteDeviceGroupDeviceCost += $quoteDeviceGroupDevice->monochromePagesQuantity * $quoteDeviceGroupDevice->getQuoteDevice()->calculateMonochromeCostPerPage() * $quoteDeviceGroupDevice->quantity;
                    $quoteHasPages = true;
                }
                $totalCpp = $quoteDeviceGroupDevice->getQuoteDevice()->calculateMonochromeCostPerPage();
                $totalDevices++;
            }
        }
        $monochromeCostPerPage = 0;
        if ($quoteHasPages)
        {
            $monochromeCostPerPage = $quoteDeviceGroupDeviceCost / $monochromeTotal;
        }
        else
        {
            if ($totalCpp != 0 || $totalDevices != 0)
            {
                $monochromeCostPerPage = $totalCpp / $totalDevices;
            }
        }

        return $monochromeCostPerPage;
    }

    /**
     * Gets the cost per page for color pages for the whole quote
     *
     * @return float
     */
    public function calculateColorCostPerPage ()
    {
        // The quantity of color pages that have been assigned in this quote
        $colorTotal = 0;
        // The accumulation of cost for color pages per device
        $colorPageCostTotal = 0;
        // The total cpp for all quote devices, used for calculation with no pages
        $totalCpp = 0;
        // Total device count, used for calculation with no pages in quote
        $totalDevices = 0;
        // Flag to see if pages exist
        $quoteHasPages = false;

        foreach ($this->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {
            /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
            foreach ($quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice)
            {
                $colorTotal += $quoteDeviceGroupDevice->colorPagesQuantity * $quoteDeviceGroupDevice->quantity;
                if ($quoteDeviceGroupDevice->colorPagesQuantity > 0)
                {
                    $colorPageCostTotal += $quoteDeviceGroupDevice->colorPagesQuantity * $quoteDeviceGroupDevice->getQuoteDevice()->calculateColorCostPerPage() * $quoteDeviceGroupDevice->quantity;
                    $quoteHasPages = true;
                }
                $totalCpp = $quoteDeviceGroupDevice->getQuoteDevice()->calculateColorCostPerPage();
                $totalDevices++;
            }
        }
        $colorCostPerPage = 0;
        if ($quoteHasPages)
        {
            $colorCostPerPage = $colorPageCostTotal / $colorTotal;
        }
        else
        {
            if ($totalCpp != 0 || $totalDevices != 0)
            {
                $colorCostPerPage = $totalCpp / $totalDevices;
            }
        }

        return (float)$colorCostPerPage;
    }

    /**
     * Gets the total cost of monochrome pages
     *
     * @return int the total cost of monochrome pages
     */
    public function calculateMonochromePageCost ()
    {
        $totalMonochromePageCost = 0;

        foreach ($this->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {
            $totalMonochromePageCost += $quoteDeviceGroup->calculateMonochromePageCost();
        }

        return $totalMonochromePageCost;
    }

    /**
     * Calculates the total color page cost for the quote
     *
     * @return float the total color page cost for the quote
     */
    public function calculateColorPageCost ()
    {
        $totalColorPageCost = 0;
        foreach ($this->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {
            $totalColorPageCost += $quoteDeviceGroup->calculateColorPageCost();
        }

        return $totalColorPageCost;
    }

    /**
     * Calculates the revenue for monochrome pages
     *
     * @return float the calculated price per page
     */
    public function calculateMonochromePricePerPage ()
    {
        return Tangent_Accounting::applyMargin($this->calculateMonochromeCostPerPage(), $this->monochromePageMargin);
    }

    /**
     * Calculates the overage monochrome price per page based on color overage margin
     *
     * @return float the calculated overage monochrome price per page
     */
    public function calculateMonochromeOverageRatePerPage ()
    {
        return Tangent_Accounting::applyMargin($this->calculateMonochromeCostPerPage(), $this->monochromeOverageMargin);
    }

    /**
     * Calculates the revenue for color pages
     *
     * @return float the calculated price per page
     */
    public function calculateColorPricePerPage ()
    {
        return Tangent_Accounting::applyMargin($this->calculateColorCostPerPage(), $this->colorPageMargin);
    }

    /**
     * Calculates the overage color price per page based on color overage margin
     *
     * @return float the calculated overage color price per page
     */
    public function calculateColorOverageRatePerPage ()
    {
        return Tangent_Accounting::applyMargin($this->calculateColorCostPerPage(), $this->colorOverageMargin);
    }

    /**
     * Calculates the revenue for monochrome pages
     *
     * @return float the calculated price per page
     */
    public function calculateMonochromePageRevenue ()
    {
        return Tangent_Accounting::applyMargin($this->calculateMonochromePageCost(), $this->monochromePageMargin);
    }

    /**
     * Calculates the revenue for color pages
     *
     * @return float the calculated price per page
     */
    public function calculateColorPageRevenue ()
    {
        return Tangent_Accounting::applyMargin($this->calculateColorPageCost(), $this->colorPageMargin);
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
     * Gets the profit for color pages for the quote
     *
     * @return float the total quote profit for color
     */
    public function calculateColorPageProfit ()
    {
        return $this->calculateColorPageRevenue() - $this->calculateColorPageCost();
    }

    /**
     * Gets the total of monochrome and color pages for the quote
     *
     * @return int
     */
    public function calculateTotalPagesQuantity ()
    {
        return $this->calculateTotalMonochromePages() + $this->calculateTotalColorPages();
    }

    /**
     * Gets the cost per page for monochrome and color pages for the whole quote
     *
     * @return float the quote cost per page
     */
    public function calculateTotalCostPerPage ()
    {
        return $this->calculateMonochromeCostPerPage() + $this->calculateColorCostPerPage();
    }

    /**
     * Gets the price per page for monochrome and color pages for the whole quote
     *
     * @return float the quote total price per page
     */
    public function calculateTotalPricePerPage ()
    {
        return $this->calculateMonochromePricePerPage() + $this->calculateColorPricePerPage();
    }

    /**
     * Gets the quote total page cost
     *
     * @return float the quote cost for pagews
     */
    public function calculateTotalPageCost ()
    {
        return $this->calculateMonochromePageCost() + $this->calculateColorPageCost();
    }

    /**
     * Gets the total revenue made by pages in the quote
     *
     * @return float the total revenue for the quote
     */
    public function calculateTotalPageRevenue ()
    {
        return $this->calculateMonochromePageRevenue() + $this->calculateColorPageRevenue();
    }

    /**
     * Gets the total quote profit
     *
     * @return float the total profit for pages in the quote
     */
    public function calculateTotalPageProfit ()
    {
        return $this->calculateMonochromePageProfit() + $this->calculateColorPageProfit();
    }
}
