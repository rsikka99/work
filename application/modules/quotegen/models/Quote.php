<?php

/**
 * Quotegen_Model_Quote
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_Quote extends My_Model_Abstract
{
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
     * The default pricing config preference
     *
     * @var int
     */
    protected $_pricingConfigId;
    
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
        if (isset($params->pageCoverageColor) && ! is_null($params->pageCoverageColor))
            $this->setPageCoverageColor($params->pageCoverageColor);
        if (isset($params->pageCoverageMonochrome) && ! is_null($params->pageCoverageMonochrome))
            $this->setPageCoverageMonochrome($params->pageCoverageMonochrome);
        if (isset($params->pricingConfigId) && ! is_null($params->pricingConfigId))
            $this->setPricingConfigId($params->pricingConfigId);
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
                'pageCoverageColor' => $this->getPageCoverageColor(), 
                'pageCoverageMonochrome' => $this->getPageCoverageMonochrome(), 
                'pricingConfigId' => $this->getPricingConfigId(), 
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
     *            The date in MySQL format
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
     * @param $_quoteDevices multitype:Quotegen_Model_QuoteDevice
     *            The quote devices.
     */
    public function setQuoteDevices ($_quoteDevices)
    {
        $this->_quoteDeviceGroups = $_quoteDevices;
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
     * Calculates the sub total for the quote's devices.
     * This is the number used for the purchase total and the number that will be used to choose a leasing factor.
     *
     * @return number The sub total
     */
    public function calculateQuoteSubtotal ()
    {
        $subtotal = 0;
        
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
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
    public function calculateQuoteLeaseSubtotal ()
    {
        $subtotal = 0;
        
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $this->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            $subtotal += $quoteDeviceGroup->calculateGroupLeaseSubtotal();
        }
        return $subtotal;
    }

    /**
     * Calculates the lease sub total for the quote's devices.
     *
     * @return number The sub total
     */
    public function calculateQuoteSubtotalWithResidualsApplied ()
    {
        $subtotal = 0;
        
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $this->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            $subtotal += $quoteDeviceGroup->calculateGroupSubtotalWithResidualsApplied();
        }
        return $subtotal;
    }

    /**
     * Calculates the total residual for the quote
     *
     * @return number
     */
    public function calculateTotalResidual ()
    {
        $totalResidual = 0;
        
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $this->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            $totalResidual += $quoteDeviceGroup->calculateTotalResidual();
        }
        return $totalResidual;
    }

    /**
     * Gives a count of the number of devices attached to the quote.
     *
     * @return number The number of devices
     */
    public function countDevices ()
    {
        $count = 0;
        
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $this->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            $count += count($quoteDeviceGroup->getQuoteDevices());
        }
        return $count;
    }

    /**
     * Calculates the total cost of the quote
     *
     * @return number The total cost.
     */
    public function calculateTotalCost ()
    {
        $totalCost = 0;
        
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $this->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            $totalCost += $quoteDeviceGroup->calculateTotalCost();
        }
        return $totalCost;
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
}
