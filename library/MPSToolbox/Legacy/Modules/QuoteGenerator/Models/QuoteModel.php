<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\HardwareOptimizationQuoteMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorRankingSetMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingSetModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\LeasingSchemaTermMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceGroupMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteLeaseTermMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteMapper;
use My_Model_Abstract;
use Tangent\Accounting;

/**
 * Class QuoteModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class QuoteModel extends My_Model_Abstract
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
     * @var ClientModel
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
     * @var string
     */
    public $quoteType;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $stepName;

    /**
     * The leasing schema term for the quote
     *
     * @var LeasingSchemaTermModel
     */
    protected $_leasingSchemaTerm;

    /**
     * The quote device configurations in this quote
     *
     * @var QuoteDeviceModel[]
     */
    protected $_quoteDevices;

    /**
     * @var int
     */
    public $dealerMonochromeRankSetId;

    /**
     * @var int
     */
    public $dealerColorRankSetId;

    /**
     * @var TonerVendorRankingSetModel
     */
    protected $_dealerMonochromeRankSet;

    /**
     * @var TonerVendorRankingSetModel
     */
    protected $_dealerColorRankSet;

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

        if (isset($params->quoteType) && !is_null($params->quoteType))
        {
            $this->quoteType = $params->quoteType;
        }

        if (isset($params->dealerMonochromeRankSetId) && !is_null($params->dealerMonochromeRankSetId))
        {
            $this->dealerMonochromeRankSetId = $params->dealerMonochromeRankSetId;
        }

        if (isset($params->dealerColorRankSetId) && !is_null($params->dealerColorRankSetId))
        {
            $this->dealerColorRankSetId = $params->dealerColorRankSetId;
        }

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }

        if (isset($params->stepName) && !is_null($params->stepName))
        {
            $this->stepName = $params->stepName;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "id"                        => $this->id,
            "clientId"                  => $this->clientId,
            "dateCreated"               => $this->dateCreated,
            "dateModified"              => $this->dateModified,
            "quoteDate"                 => $this->quoteDate,
            "clientDisplayName"         => $this->clientDisplayName,
            "leaseTerm"                 => $this->leaseTerm,
            "leaseRate"                 => $this->leaseRate,
            "pageCoverageMonochrome"    => $this->pageCoverageMonochrome,
            "pageCoverageColor"         => $this->pageCoverageColor,
            "monochromePageMargin"      => $this->monochromePageMargin,
            "colorPageMargin"           => $this->colorPageMargin,
            "monochromeOverageMargin"   => $this->monochromeOverageMargin,
            "colorOverageMargin"        => $this->colorOverageMargin,
            "adminCostPerPage"          => $this->adminCostPerPage,
            "quoteType"                 => $this->quoteType,
            "dealerMonochromeRankSetId" => $this->dealerMonochromeRankSetId,
            "dealerColorRankSetId"      => $this->dealerColorRankSetId,
            "name"                      => $this->name,
            "stepName"                  => $this->stepName,
        ];
    }

    /**
     * @return array
     */
    public function getTonerRankSets ()
    {
        return [
            "dealerMonochromeRankSetArray" => $this->getDealerMonochromeRankSet()->getRanksAsArray(),
            "dealerColorRankSetArray"      => $this->getDealerColorRankSet()->getRanksAsArray(),
        ];
    }


    /**
     * @return TonerVendorRankingSetModel
     */
    public function getDealerMonochromeRankSet ()
    {
        if (!isset($this->_dealerMonochromeRankSet))
        {
            if ($this->dealerMonochromeRankSetId > 0)
            {
                $this->_dealerMonochromeRankSet = TonerVendorRankingSetMapper::getInstance()->find($this->dealerMonochromeRankSetId);
            }
            else
            {
                $this->_dealerMonochromeRankSet  = new TonerVendorRankingSetModel();
                $this->dealerMonochromeRankSetId = TonerVendorRankingSetMapper::getInstance()->insert($this->_dealerMonochromeRankSet);
                // Update ourselves
                QuoteMapper::getInstance()->save($this);
            }
        }

        return $this->_dealerMonochromeRankSet;
    }

    /**
     * @return TonerVendorRankingSetModel
     */
    public function getDealerColorRankSet ()
    {
        if (!isset($this->_dealerColorRankSet))
        {
            if ($this->dealerColorRankSetId > 0)
            {
                $this->_dealerColorRankSet = TonerVendorRankingSetMapper::getInstance()->find($this->dealerColorRankSetId);
            }
            else
            {
                $this->_dealerColorRankSet  = new TonerVendorRankingSetModel();
                $this->dealerColorRankSetId = TonerVendorRankingSetMapper::getInstance()->insert($this->_dealerColorRankSet);
                // Update ourselves
                QuoteMapper::getInstance()->save($this);
            }
        }

        return $this->_dealerColorRankSet;
    }


    /**
     * Gets the client for the report
     *
     * @return ClientModel
     */
    public function getClient ()
    {
        if (!isset($this->_client) && isset($this->clientId))
        {
            $this->_client = ClientMapper::getInstance()->find($this->clientId);
        }

        return $this->_client;
    }

    /**
     * Sets the client for the report (Also sets the client id of the report if the client has one.
     *
     * @param $_client ClientModel
     *                 The new client
     *
     * @return $this
     */
    public function setClient (ClientModel $_client)
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
     * @return QuoteDeviceGroupModel[].
     *
     */
    public function getQuoteDeviceGroups ()
    {
        if (!isset($this->_quoteDeviceGroups))
        {
            $this->_quoteDeviceGroups = QuoteDeviceGroupMapper::getInstance()->fetchDeviceGroupsForQuote($this->id);
        }

        return $this->_quoteDeviceGroups;
    }

    /**
     * Sets the quote devices for the quote
     *
     * @param  QuoteDeviceGroupModel[] $_quoteDeviceGroups The quote devices.
     *
     * @return $this
     */
    public function setQuoteDeviceGroups ($_quoteDeviceGroups)
    {
        $this->_quoteDeviceGroups = $_quoteDeviceGroups;

        return $this;
    }

    /**
     * Gets the leasing schema term
     *
     * @return LeasingSchemaTermModel
     */
    public function getLeasingSchemaTerm ()
    {
        if (!isset($this->_leasingSchemaTerm))
        {
            $quoteLeaseTerm = QuoteLeaseTermMapper::getInstance()->find($this->id);
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
     * @param $_leasingSchemaTerm LeasingSchemaTermModel
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
     * @return QuoteDeviceModel[]
     */
    public function getQuoteDevices ()
    {
        if (!isset($this->_quoteDevices))
        {
            $this->_quoteDevices = QuoteDeviceMapper::getInstance()->fetchDevicesForQuote($this->id);
        }

        return $this->_quoteDevices;
    }

    /**
     * Sets all the quote device configurations for a quote
     *
     * @param QuoteDeviceModel[] $_quoteDevices
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
        $this->quoteType               = $quoteType;
        $this->clientId                = $clientId;
        $this->dateCreated             = date('Y-m-d H:i:s');
        $this->dateModified            = date('Y-m-d H:i:s');
        $this->quoteDate               = date('Y-m-d H:i:s');
        $this->name                    = ucwords($quoteType) . " Quote " . date('Y/m/d');
        $this->userId                  = $userId;
        $this->colorPageMargin         = $this->getClient()->getClientSettings()->quoteSettings->defaultPageMargin;
        $this->monochromePageMargin    = $this->getClient()->getClientSettings()->quoteSettings->defaultPageMargin;
        $this->colorOverageMargin      = $this->getClient()->getClientSettings()->quoteSettings->defaultPageMargin;
        $this->monochromeOverageMargin = $this->getClient()->getClientSettings()->quoteSettings->defaultPageMargin;
        $this->pageCoverageColor       = $this->getClient()->getClientSettings()->proposedFleetSettings->defaultColorCoverage;
        $this->pageCoverageMonochrome  = $this->getClient()->getClientSettings()->proposedFleetSettings->defaultMonochromeCoverage;
        $this->adminCostPerPage        = $this->getClient()->getClientSettings()->proposedFleetSettings->adminCostPerPage;
        $this->stepName                = QuoteStepsModel::STEP_ADD_HARDWARE;

        $this->id = QuoteMapper::getInstance()->insert($this);

        // Add a default group
        $quoteDeviceGroup            = new QuoteDeviceGroupModel();
        $quoteDeviceGroup->name      = 'Default Group (Ungrouped)';
        $quoteDeviceGroup->isDefault = 1;
        $quoteDeviceGroup->setGroupPages(0);
        $quoteDeviceGroup->quoteId = $this->id;
        QuoteDeviceGroupMapper::getInstance()->insert($quoteDeviceGroup);

        // If this is a leased quote, select the first leasing schema term
        if ($this->isLeased())
        {
            // FIXME: Use quote settings?
            $leasingSchemaTerms = LeasingSchemaTermMapper::getInstance()->fetchAll();
            if (count($leasingSchemaTerms) > 0)
            {

                $quoteLeaseTerm                      = new QuoteLeaseTermModel();
                $quoteLeaseTerm->quoteId             = $this->id;
                $quoteLeaseTerm->leasingSchemaTermId = $leasingSchemaTerms [0]->id;
                QuoteLeaseTermMapper::getInstance()->insert($quoteLeaseTerm);
            }
        }

        return $this;
    }

    /**
     * @return int
     */
    public function isHardwareOptimizationExport ()
    {
        return count(HardwareOptimizationQuoteMapper::getInstance()->fetchByQuoteId($this->id));
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
        return ($this->quoteType === QuoteModel::QUOTE_TYPE_LEASED);
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
            $leaseValue += $quoteDeviceGroup->calculateHardwareLeaseValue();
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
        // The total CPP for all quote devices, used for calculation with no pages
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
                $deviceObj = $quoteDeviceGroupDevice->getQuoteDevice()->getDevice();
                if (!$deviceObj) continue;

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
        // The total CPP for all quote devices, used for calculation with no pages
        $totalCpp = 0;
        // Total device count, used for calculation with no pages in quote
        $totalDevices = 0;
        // Flag to see if pages exist
        $quoteHasPages = false;

        foreach ($this->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {
            /* @var $quoteDeviceGroupDevice QuoteDeviceGroupDeviceModel */
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
        return Accounting::applyMargin($this->calculateMonochromeCostPerPage(), $this->monochromePageMargin);
    }

    /**
     * Calculates the overage monochrome price per page based on color overage margin
     *
     * @return float the calculated overage monochrome price per page
     */
    public function calculateMonochromeOverageRatePerPage ()
    {
        return Accounting::applyMargin($this->calculateMonochromeCostPerPage(), $this->monochromeOverageMargin);
    }

    /**
     * Calculates the revenue for color pages
     *
     * @return float the calculated price per page
     */
    public function calculateColorPricePerPage ()
    {
        return Accounting::applyMargin($this->calculateColorCostPerPage(), $this->colorPageMargin);
    }

    /**
     * Calculates the overage color price per page based on color overage margin
     *
     * @return float the calculated overage color price per page
     */
    public function calculateColorOverageRatePerPage ()
    {
        return Accounting::applyMargin($this->calculateColorCostPerPage(), $this->colorOverageMargin);
    }

    /**
     * Calculates the revenue for monochrome pages
     *
     * @return float the calculated price per page
     */
    public function calculateMonochromePageRevenue ()
    {
        return Accounting::applyMargin($this->calculateMonochromePageCost(), $this->monochromePageMargin);
    }

    /**
     * Calculates the revenue for color pages
     *
     * @return float the calculated price per page
     */
    public function calculateColorPageRevenue ()
    {
        return Accounting::applyMargin($this->calculateColorPageCost(), $this->colorPageMargin);
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
     * @return float the quote cost for pages
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
