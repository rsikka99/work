<?php

/**
 * Quotegen_Model_QuoteSetting
 *
 * @author John Sadler
 *        
 */
class Quotegen_Model_QuoteSetting extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    /**
     * The default black & white page coverage value
     *
     * @var double
     */
    protected $_pageCoverageMonochrome = 0;
    /**
     * The default color page coverage value
     *
     * @var double
     */
    protected $_pageCoverageColor = 0;
    
    /**
     * The default device margin value
     *
     * @var double
     */
    protected $_deviceMargin = 0;
    
    /**
     * The default page margin value
     *
     * @var double
     */
    protected $_pageMargin = 0;
    
    /**
     * The default pricing config preference
     *
     * @var int
     */
    protected $_pricingConfigId = 0;
    
    /**
     * A pricing config object
     *
     * @var Proposalgen_Model_PricingConfig
     */
    protected $_pricingConfig;
    
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
        if (isset($params->pageCoverageMonochrome) && ! is_null($params->pageCoverageMonochrome))
            $this->setPageCoverageMonochrome($params->pageCoverageMonochrome);
        if (isset($params->pageCoverageColor) && ! is_null($params->pageCoverageColor))
            $this->setPageCoverageColor($params->pageCoverageColor);
        if (isset($params->deviceMargin) && ! is_null($params->deviceMargin))
            $this->setDeviceMargin($params->deviceMargin);
        if (isset($params->pageMargin) && ! is_null($params->pageMargin))
            $this->setPageMargin($params->pageMargin);
        if (isset($params->pricingConfigId) && ! is_null($params->pricingConfigId))
            $this->setPricingConfigId($params->pricingConfigId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'pageCoverageMonochrome' => $this->getPageCoverageMonochrome(), 
                'pageCoverageColor' => $this->getPageCoverageColor(), 
                'deviceMargin' => $this->getDeviceMargin(), 
                'pageMargin' => $this->getPageMargin(), 
                'pricingConfigId' => $this->getPricingConfigId() 
        );
    }

    /**
     *
     * @return the $_id
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     *
     * @param number $_id            
     */
    public function setId ($_id)
    {
        $this->_id = $_id;
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
     * @param number $_pageCoverageMonochrome            
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
     * @param number $_pageCoverageColor            
     */
    public function setPageCoverageColor ($_pageCoverageColor)
    {
        $this->_pageCoverageColor = $_pageCoverageColor;
        return $this;
    }

    /**
     *
     * @return the $_deviceMargin
     */
    public function getDeviceMargin ()
    {
        return $this->_deviceMargin;
    }

    /**
     *
     * @param number $_deviceMargin            
     */
    public function setDeviceMargin ($_deviceMargin)
    {
        $this->_deviceMargin = $_deviceMargin;
        return $this;
    }

    /**
     *
     * @return the $_pageMargin
     */
    public function getPageMargin ()
    {
        return $this->_pageMargin;
    }

    /**
     *
     * @param number $_pageMargin            
     */
    public function setPageMargin ($_pageMargin)
    {
        $this->_pageMargin = $_pageMargin;
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
     * @param number $_pricingConfigId            
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
     * @param Proposalgen_Model_PricingConfig $_pricingConfig
     *            The new princing config.
     */
    public function setPricingConfig ($_pricingConfig)
    {
        $this->_pricingConfig = $_pricingConfig;
        return $this;
    }
}
