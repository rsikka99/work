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
    const QUOTE_TYPE_LEASED = 'leased';
    const QUOTE_TYPE_PURCHASED = 'purchased';
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The client id that the quote was made for
     *
     * @var number
     */
    protected $_clientId;
    
    /**
     * The date the quote was created
     *
     * @var string
     */
    protected $_dateCreated;
    
    /**
     * The date the quote was last modified
     *
     * @var string
     */
    protected $_dateModified;
    
    /**
     * The date the quote was made for
     *
     * @var string
     */
    protected $_quoteDate;
    
    /**
     * The user who created the quote/owns the quote?
     *
     * @var number
     */
    protected $_userId;
    
    /**
     * The name that will be shown on the report
     *
     * @var string
     */
    protected $_clientDisplayName;
    
    /**
     * The length of the lease in months
     *
     * @var number
     */
    protected $_leaseTerm;
    
    /**
     * The lease percentage
     *
     * @var number
     */
    protected $_leaseRate;
    
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
     * The default black & white page coverage value
     *
     * @var double
     */
    protected $_pageCoverageMonochrome;
    
    /**
     * The default color page coverage value
     *
     * @var double
     */
    protected $_pageCoverageColor;
    
    /**
     * The page margin for the quote
     *
     * @var float
     */
    protected $_monochromePageMargin;
    
    /**
     * The color page margin for the quote
     *
     * @var float
     */
    protected $_colorPageMargin;
    
    /**
     * Admin cost per page to be applied to the quote
     *
     * @var float
     */
    protected $_adminCostPerPage;
    
    /**
     * Service cost per page to be applied to the quote
     *
     * @var float
     */
    protected $_serviceCostPerPage;
    
    /**
     * This is the monochrome overage per page rate for the quote
     *
     * @var float
     */
    protected $_monochromeOverageRatePerPage;
    
    /**
     * This is the color overager rate per page for the quote
     *
     * @var float
     */
    protected $_colorOverageRatePerPage;
    
    /**
     * The default pricing config preference
     *
     * @var int
     */
    protected $_pricingConfigId;
    
    /**
     * The quote type
     *
     * @var string
     */
    protected $_quoteType;
    
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
     * @var multitype:Quotegen_Model_QuoteDevice
     */
    protected $_quoteDevices;

    /**
     * (non-PHPdoc)
     *
     * @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->id) && ! is_null($params->id))
            $this->setId($params->id);
        if (isset($params->clientId) && ! is_null($params->clientId))
            $this->setClientId($params->clientId);
        if (isset($params->dateCreated) && ! is_null($params->dateCreated))
            $this->setDateCreated($params->dateCreated);
        if (isset($params->dateModified) && ! is_null($params->dateModified))
            $this->setDateModified($params->dateModified);
        if (isset($params->quoteDate) && ! is_null($params->quoteDate))
            $this->setQuoteDate($params->quoteDate);
        if (isset($params->userId) && ! is_null($params->userId))
            $this->setUserId($params->userId);
        if (isset($params->clientDisplayName) && ! is_null($params->clientDisplayName))
            $this->setClientDisplayName($params->clientDisplayName);
        if (isset($params->adminCostPerPage) && ! is_null($params->adminCostPerPage))
            $this->setAdminCostPerPage($params->adminCostPerPage);
        if (isset($params->serviceCostPerPage) && ! is_null($params->serviceCostPerPage))
            $this->setServiceCostPerPage($params->serviceCostPerPage);
        if (isset($params->monochromePageMargin) && ! is_null($params->monochromePageMargin))
            $this->setMonochromePageMargin($params->monochromePageMargin);
        if (isset($params->colorPageMargin) && ! is_null($params->colorPageMargin))
            $this->setColorPageMargin($params->colorPageMargin);
        if (isset($params->pageCoverageColor) && ! is_null($params->pageCoverageColor))
            $this->setPageCoverageColor($params->pageCoverageColor);
        if (isset($params->pageCoverageMonochrome) && ! is_null($params->pageCoverageMonochrome))
            $this->setPageCoverageMonochrome($params->pageCoverageMonochrome);
        if (isset($params->monochromeOverageRatePerPage) && ! is_null($params->monochromeOverageRatePerPage))
            $this->setMonochromeOverageRatePerPage($params->monochromeOverageRatePerPage);
        if (isset($params->colorOverageRatePerPage) && ! is_null($params->colorOverageRatePerPage))
            $this->setColorOverageRatePerPage($params->colorOverageRatePerPage);
        if (isset($params->pricingConfigId) && ! is_null($params->pricingConfigId))
            $this->setPricingConfigId($params->pricingConfigId);
        if (isset($params->quoteType) && ! is_null($params->quoteType))
            $this->setQuoteType($params->quoteType);
        if (isset($params->leaseRate) && ! is_null($params->leaseRate))
            $this->setLeaseRate($params->leaseRate);
        if (isset($params->leaseTerm) && ! is_null($params->leaseTerm))
            $this->setLeaseTerm($params->leaseTerm);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'clientId' => $this->getClientId(), 
                'dateCreated' => $this->getDateCreated(), 
                'dateModified' => $this->getDateModified(), 
                'quoteDate' => $this->getQuoteDate(), 
                'userId' => $this->getUserId(), 
                'clientDisplayName' => $this->getClientDisplayName(), 
                'adminCostPerPage' => $this->getAdminCostPerPage(), 
                'serviceCostPerPage' => $this->getServiceCostPerPage(), 
                'pageCoverageColor' => $this->getPageCoverageColor(), 
                'monochromeOverageRatePerPage' => $this->getMonochromeOverageRatePerPage(), 
                'colorOverageRatePerPage' => $this->getColorOverageRatePerPage(), 
                'monochromePageMargin' => $this->getMonochromePageMargin(), 
                'colorPageMargin' => $this->getColorPageMargin(), 
                'pageCoverageMonochrome' => $this->getPageCoverageMonochrome(), 
                'pricingConfigId' => $this->getPricingConfigId(), 
                'quoteType' => $this->getQuoteType(), 
                'leaseTerm' => $this->getLeaseTerm(), 
                'leaseRate' => $this->getLeaseRate() 
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
     * Gets the client id of the quote
     *
     * @return number
     */
    public function getClientId ()
    {
        return $this->_clientId;
    }

    /**
     * Sets the client id of the quote
     *
     * @param $_clientId number
     *            The new client id
     */
    public function setClientId ($_clientId)
    {
        $this->_clientId = $_clientId;
        return $this;
    }

    /**
     * Gets the date the quote was created
     *
     * @return string The date created in MySQL format.
     */
    public function getDateCreated ()
    {
        return $this->_dateCreated;
    }

    /**
     * Sets the date the quote was created
     *
     * @param $_dateCreated string
     *            The date modified in MySQL format
     */
    public function setDateCreated ($_dateCreated)
    {
        $this->_dateCreated = $_dateCreated;
        return $this;
    }

    /**
     * Gets the date that the quote was last modified on
     *
     * @return string The date modified in MySQL format
     */
    public function getDateModified ()
    {
        return $this->_dateModified;
    }

    /**
     * Sets the date that the quote was last modified on
     *
     * @param $_dateModified string
     *            The date modified in MySQL format
     */
    public function setDateModified ($_dateModified)
    {
        $this->_dateModified = $_dateModified;
        return $this;
    }

    /**
     * Gets the date the quote was made for
     *
     * @return string The date in MySQL format
     */
    public function getQuoteDate ()
    {
        return $this->_quoteDate;
    }

    /**
     * Sets the date the quote was made for
     *
     * @param $_quoteDate string
     *            The date in MySQL formato
     */
    public function setQuoteDate ($_quoteDate)
    {
        $this->_quoteDate = $_quoteDate;
        return $this;
    }

    /**
     * Gets the user id
     *
     * @return number The user id
     */
    public function getUserId ()
    {
        return $this->_userId;
    }

    /**
     * Sets the user id
     *
     * @param $_userId number
     *            The user id
     */
    public function setUserId ($_userId)
    {
        $this->_userId = $_userId;
        return $this;
    }

    /**
     * Gets the client display name
     *
     * @return string The client name
     */
    public function getClientDisplayName ()
    {
        return $this->_clientDisplayName;
    }

    /**
     * Sets the client's display name
     *
     * @param $_clientDisplayName string            
     */
    public function setClientDisplayName ($_clientDisplayName)
    {
        $this->_clientDisplayName = $_clientDisplayName;
        return $this;
    }

    /**
     * Gets the lease length in months
     *
     * @return number The term in months
     */
    public function getLeaseTerm ()
    {
        return $this->_leaseTerm;
    }

    /**
     * Sets the new lease length
     *
     * @param $_leaseTerm number
     *            The new term in months
     */
    public function setLeaseTerm ($_leaseTerm)
    {
        $this->_leaseTerm = $_leaseTerm;
        return $this;
    }

    /**
     * Gets the rate percentage
     *
     * @return number The rate percentage.
     */
    public function getLeaseRate ()
    {
        return $this->_leaseRate;
    }

    /**
     * Sets a new rate percentage
     *
     * @param $_leaseRate number
     *            The new lease rate percentage
     */
    public function setLeaseRate ($_leaseRate)
    {
        $this->_leaseRate = $_leaseRate;
        return $this;
    }

    /**
     * Gets the client for the report
     *
     * @return Quotegen_Model_Client
     */
    public function getClient ()
    {
        if (! isset($this->_client) && isset($this->_clientId))
        {
            $this->_client = Quotegen_Model_Mapper_Client::getInstance()->find($this->getClientId());
        }
        return $this->_client;
    }

    /**
     * Sets the client for the report (Also sets the client id of the report if the client has one.
     *
     * @param $_client Quotegen_Model_Client
     *            The new client
     */
    public function setClient (Quotegen_Model_Client $_client)
    {
        $this->_client = $_client;
        if ($_client->getId() !== null)
        {
            $this->setClientId($_client->getId());
        }
        return $this;
    }

    /**
     * Gets the quote devices for the quote
     *
     * @return multitype:Quotegen_Model_QuoteDevice The quote devices.
     */
    public function getQuoteDeviceGroups ()
    {
        if (! isset($this->_quoteDeviceGroups))
        {
            $this->_quoteDeviceGroups = Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->fetchDeviceGroupsForQuote($this->getId());
        }
        return $this->_quoteDeviceGroups;
    }

    /**
     * Sets the quote devices for the quote
     *
     * @param $_quoteDeviceGroups multitype:Quotegen_Model_QuoteDeviceGroup
     *            The quote devices.
     */
    public function setQuoteDeviceGroups ($_quoteDeviceGroups)
    {
        $this->_quoteDeviceGroups = $_quoteDeviceGroups;
        return $this;
    }

    /**
     *
     * @return the $_pageCoverageMonochrome
     */
    public function getPageCoverageMonochrome ()
    {
        return $this->_pageCoverageMonochrome;
    }

    /**
     *
     * @param $_pageCoverageMonochrome number            
     */
    public function setPageCoverageMonochrome ($_pageCoverageMonochrome)
    {
        $this->_pageCoverageMonochrome = $_pageCoverageMonochrome;
        return $this;
    }

    /**
     *
     * @return the $_pageCoverageColor
     */
    public function getPageCoverageColor ()
    {
        return $this->_pageCoverageColor;
    }

    /**
     *
     * @param $_pageCoverageColor number            
     */
    public function setPageCoverageColor ($_pageCoverageColor)
    {
        $this->_pageCoverageColor = $_pageCoverageColor;
        return $this;
    }

    /**
     *
     * @return the $_pricingConfigId
     */
    public function getPricingConfigId ()
    {
        return $this->_pricingConfigId;
    }

    /**
     *
     * @param $_pricingConfigId number            
     */
    public function setPricingConfigId ($_pricingConfigId)
    {
        $this->_pricingConfigId = $_pricingConfigId;
        return $this;
    }

    /**
     * Gets the quote type
     *
     * @return string
     */
    public function getQuoteType ()
    {
        return $this->_quoteType;
    }

    /**
     * Sets the quote type
     *
     * @param string $_quoteType            
     */
    public function setQuoteType ($_quoteType)
    {
        $this->_quoteType = $_quoteType;
        return $this;
    }

    /**
     * Gets the pricing config object
     *
     * @return Proposalgen_Model_PricingConfig The pricing config object.
     */
    public function getPricingConfig ()
    {
        if (! isset($this->_pricingConfig))
        {
            $this->_pricingConfig = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($this->getPricingConfigId());
        }
        return $this->_pricingConfig;
    }

    /**
     * Sets the pricing config object
     *
     * @param $_pricingConfig Proposalgen_Model_PricingConfig
     *            The new princing config.
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
        if (! isset($this->_leasingSchemaTerm))
        {
            $quoteLeaseTerm = Quotegen_Model_Mapper_QuoteLeaseTerm::getInstance()->find($this->getId());
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
     */
    public function setLeasingSchemaTerm ($_leasingSchemaTerm)
    {
        $this->_leasingSchemaTerm = $_leasingSchemaTerm;
        return $this;
    }

    /**
     * Gets all the quote device configurations for a quote
     *
     * @return multitype:Quotegen_Model_QuoteDevice
     */
    public function getQuoteDevices ()
    {
        if (! isset($this->_quoteDevices))
        {
            $this->_quoteDevices = Quotegen_Model_Mapper_QuoteDevice::getInstance()->fetchDevicesForQuote($this->getId());
        }
        return $this->_quoteDevices;
    }

    /**
     * Sets all the quote device configurations for a quote
     *
     * @param multitype:Quotegen_Model_QuoteDevice $_quoteDevices            
     */
    public function setQuoteDevices ($_quoteDevices)
    {
        $this->_quoteDevices = $_quoteDevices;
        return $this;
    }

    /**
     * Gets the color page margin for the quote
     *
     * @return the $_colorPageMargin
     */
    public function getColorPageMargin ()
    {
        return $this->_colorPageMargin;
    }

    /**
     * Sets the page margin for the quote
     *
     * @param number $_colorPageMargin            
     */
    public function setColorPageMargin ($_colorPageMargin)
    {
        $this->_colorPageMargin = $_colorPageMargin;
        return $this;
    }

    /**
     * Gets the monochrome page margin for the quote
     *
     * @return the $_monochromePageMargin
     */
    public function getMonochromePageMargin ()
    {
        return $this->_monochromePageMargin;
    }

    /**
     * Sets the monochrome page margin for the quote
     *
     * @param number $_monochromePageMargin            
     */
    public function setMonochromePageMargin ($_monochromePageMargin)
    {
        $this->_monochromePageMargin = $_monochromePageMargin;
    }

    /**
     * Gets the admin cost per page for the whole quote
     *
     * @return the $_adminCostPerPage
     */
    public function getAdminCostPerPage ()
    {
        return $this->_adminCostPerPage;
    }

    /**
     * Sets the admin cost per page for the whole quote
     *
     * @param number $_adminCostPerPage            
     */
    public function setAdminCostPerPage ($_adminCostPerPage)
    {
        $this->_adminCostPerPage = $_adminCostPerPage;
    }

    /**
     * Gets the service cost per page for the whole quote
     *
     * @return the $_serviceCostPerPage
     */
    public function getServiceCostPerPage ()
    {
        return $this->_serviceCostPerPage;
    }

    /**
     * Gets the service cost per page
     *
     * @param number $_serviceCostPerPage            
     */
    public function setServiceCostPerPage ($_serviceCostPerPage)
    {
        $this->_serviceCostPerPage = $_serviceCostPerPage;
    }

    /**
     * Gets the monohchrome overage rate per page for the quote
     *
     * @return the $_monochromeOverageRatePerPage
     */
    public function getMonochromeOverageRatePerPage ()
    {
        return $this->_monochromeOverageRatePerPage;
    }

    /**
     * Sets the monochrome overage rate per page for the quote
     *
     * @param number $_monochromeOverageRatePerPage            
     */
    public function setMonochromeOverageRatePerPage ($_monochromeOverageRatePerPage)
    {
        $this->_monochromeOverageRatePerPage = $_monochromeOverageRatePerPage;
        return $this;
    }

    /**
     * Gets the color overage rate per page for the quote
     *
     * @return the $_colorOverageRatePerPage
     */
    public function getColorOverageRatePerPage ()
    {
        return $this->_colorOverageRatePerPage;
    }

    /**
     * Sets the color overage rate per page for the quote
     *
     * @param number $_colorOverageRatePerPage            
     */
    public function setColorOverageRatePerPage ($_colorOverageRatePerPage)
    {
        $this->_colorOverageRatePerPage = $_colorOverageRatePerPage;
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
        return ($this->getQuoteType() === Quotegen_Model_Quote::QUOTE_TYPE_LEASED);
    }

    /**
     * Calculates the total lease value for the quote
     *
     * @return number The total lease value
     */
    public function calculateTotalLeaseValue ()
    {
        $leaseValue = 0;
        
        foreach ( $this->getQuoteDevices() as $quoteDevice )
        {
            $leaseValue += $quoteDevice->calculateTotalLeaseValue();
        }
        
        // TODO: Add Pages to this calculation
        

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
        $margin = 0;
        $deviceCount = count($this->getQuoteDevices);
        
        foreach ( $this->getQuoteDevices() as $quoteDevice )
        {
            $margin += $quoteDevice->getMargin();
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
        $leaseValue = $this->calculateTotalLeaseValue();
        $monthlyPayment = 0;
        $leaseFactor = $this->getLeaseRate();
        
        if (! empty($leaseFactor) && ! empty($leaseValue))
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
        
        foreach ( $this->getQuoteDevices() as $quoteDevice )
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
        
        foreach ( $this->getQuoteDevices() as $quoteDevice )
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
        
        foreach ( $this->getQuoteDevices() as $quoteDevice )
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
        
        foreach ( $this->getQuoteDevices() as $quoteDevice )
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
        
        foreach ( $this->getQuoteDeviceGroups() as $quoteDeviceGroup )
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
        
        foreach ( $this->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            $subtotal += $quoteDeviceGroup->calculateGroupMonthlyLeasePrice();
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
        
        foreach ( $this->getQuoteDevices() as $quoteDevice )
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
        
        foreach ( $this->getQuoteDeviceGroups() as $quoteDeviceGroup )
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
        $cost = $this->calculateTotalCost();
        $price = $this->calculateQuoteSubtotal();
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
        
        foreach ( $this->getQuoteDeviceGroups() as $quoteDeviceGroup )
            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
                $quantity += $quoteDeviceGroupDevice->getMonochromePagesQuantity(); /* * $quoteDeviceGroupDevice->getQuantity(); */
        
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
        
        foreach ( $this->getQuoteDeviceGroups() as $quoteDeviceGroup )
            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
                $quantity += $quoteDeviceGroupDevice->getColorPagesQuantity(); /* * $quoteDeviceGroupDevice->getQuantity(); */
        
        return $quantity;
    }

    /**
     * Gets the cost per page for monochrome pages for the whole quote
     *
     * @var int the calcuated quote monochrome cpp
     */
    public function calculateQuoteMonochromeCostPerPage ()
    {
        // Represents quote total page weigth
        $monochromePageQuantity = 0;
        $monochromeCostPerPage = 0;
        $monochromeTotal = 0;
        
        // Represents quote total costs for pages
        $quoteDeviceGroupDeviceCost = 0;
        
        foreach ( $this->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            
            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
            {
                // Weight for each device
                $monochromePageQuantity = $quoteDeviceGroupDevice->getMonochromePagesQuantity();
                
                // Total weight
                $monochromeTotal += $monochromePageQuantity;
                
                // Total Cost for pages
                $quoteDeviceGroupDeviceCost += $monochromePageQuantity * $quoteDeviceGroupDevice->getQuoteDevice()->calculateMonochromeCostPerPage();
            }
        }
        
        if ($monochromeTotal != 0)
        {
            $monochromeCostPerPage = $quoteDeviceGroupDeviceCost / $monochromeTotal;
        }
        
        return $monochromeCostPerPage;
    }

    /**
     * Gets the cost per page for color pages for the whole quote
     *
     * @var int the calcuated quote color cpp
     */
    public function calculateQuoteColorCostPerPage ()
    {
        // Quantity of pages for each grouped device (pages * deviceQuantity)
        $colorPageQuantity = 0;
        // The calculated quote CPP for color pages
        $colorCostPerPage = 0;
        // The quantity of color pages that have been assigned in this quote
        $colorTotal = 0;
        // The accumication of cost for coolor pages per device
        $colorPageCostTotal = 0;
        
        foreach ( $this->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            
            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
            {
                $colorPageQuantity = $quoteDeviceGroupDevice->getColorPagesQuantity();
                $colorTotal += $colorPageQuantity;
                $colorPageCostTotal += $colorPageQuantity * $quoteDeviceGroupDevice->getQuoteDevice()->calculateColorCostPerPage();
            }
        }
        
        if ($colorTotal != 0)
            $colorCostPerPage = $colorPageCostTotal / $colorTotal;
        
        return (float)$colorCostPerPage;
    }

    /**
     * Gets the total cost of monochrome pages
     *
     * @return int the total cost of monochrome pages
     */
    public function calculateQuoteMonochromePageCost ()
    {
        // The total cost of monochrome pages (deviceQuantity * pages * cpp)
        $totalMonochromePageCost = 0;
        
        foreach ( $this->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            
            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
            {
                $totalMonochromePageCost += $quoteDeviceGroupDevice->getMonochromePagesQuantity() * $quoteDeviceGroupDevice->getQuoteDevice()->calculateMonochromeCostPerPage();
            }
        }
        
        return $totalMonochromePageCost;
    }

    /**
     * Calcuates the total color page cost for the qoute
     *
     * @return float the total color page cost for the qoute
     */
    public function calculateQuoteColorPageCost ()
    {
        // The total cost of color pages (deviceQuantity * pages * cpp)
        $totalColorPageCost = 0;
        foreach ( $this->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
            {
                $totalColorPageCost += $quoteDeviceGroupDevice->getColorPagesQuantity() * $quoteDeviceGroupDevice->getQuoteDevice()->calculateColorCostPerPage();
            }
        }
        return $totalColorPageCost;
    }

    /**
     * Calcuates the revenue for monocchrome pages
     *
     * @return float the calculated price per page
     */
    public function calculateQuoteMonochromePricePerPage ()
    {
        return Tangent_Accounting::applyMargin($this->calculateQuoteMonochromeCostPerPage(), $this->getMonochromePageMargin());
    }

    /**
     * Calcuates the revenue for color pages
     *
     * @return float the calculated price per page
     */
    public function calculateQuoteColorPricePerPage ()
    {
        return Tangent_Accounting::applyMargin($this->calculateQuoteColorCostPerPage(), $this->getColorPageMargin());
    }

    /**
     * Calcuates the revenue for monocchrome pages
     *
     * @return float the calculated price per page
     */
    public function calculateQuoteMonochromeRevenue ()
    {
        return Tangent_Accounting::applyMargin($this->calculateQuoteMonochromePageCost(), $this->getMonochromePageMargin());
    }

    /**
     * Calcuates the revenue for color pages
     *
     * @return float the calculated price per page
     */
    public function calculateQuoteColorRevenue ()
    {
        return Tangent_Accounting::applyMargin($this->calculateQuoteColorPageCost(), $this->getColorPageMargin());
    }

    /**
     * Gets the profit for monochrome pages for the quote
     *
     * @return number the total quote profit for monochrome
     */
    public function calculateQuoteMonochromeProfit ()
    {
        return $this->calculateQuoteMonochromeRevenue() - $this->calculateQuoteMonochromePageCost();
    }

    /**
     * Gets the profit for color pages for the quote
     *
     * @return float the total quote profit for color
     */
    public function calculateQuoteColorProfit ()
    {
        return $this->calculateQuoteColorRevenue() - $this->calculateQuoteColorPageCost();
    }

    /**
     * Gets the total of monochrome and color pages for the quote
     *
     * @return int
     */
    public function calculateQuoteTotalQuantity ()
    {
        return $this->calculateTotalMonochromePages() + $this->calculateTotalColorPages();
    }

    /**
     * Gets the cost per page for monochrome and color pages for the whole quote
     *
     * @return float the quote cost per page
     */
    public function calculateQuoteTotalCostPerPage ()
    {
        return $this->calculateQuoteMonochromeCostPerPage() + $this->calculateQuoteColorCostPerPage();
    }

    /**
     * Gets the price per page for monochrome and color pages for the whole quote
     *
     * @return float the quote total price per page
     */
    public function calculateQuoteTotalPricePerPage ()
    {
        return $this->calculateQuoteMonochromePricePerPage() + $this->calculateQuoteColorPricePerPage();
    }

    /**
     * Gets the quote total page cost
     *
     * @return float the quote cost for pagews
     */
    public function calculateQuoteTotalCost ()
    {
        return $this->calculateQuoteMonochromePageCost() + $this->calculateQuoteColorPageCost();
    }

    /**
     * Gets the total revenue made by pages in the quote
     *
     * @return float the total revenue for the quote
     */
    public function calculateQuoteTotalRevenue ()
    {
        return $this->calculateQuoteMonochromeRevenue() + $this->calculateQuoteColorRevenue();
    }

    /**
     * Gets the total quote profit
     *
     * @return float the total profit for pages in the quote
     */
    public function calculateQuoteTotalProfit ()
    {
        return $this->calculateQuoteMonochromeProfit() + $this->calculateQuoteColorProfit();
    }
}
