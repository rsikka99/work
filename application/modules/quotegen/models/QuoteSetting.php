<?php

/**
 * Quotegen_Model_QuoteSetting
 *
 * @author John Sadler
 *        
 */
class Quotegen_Model_QuoteSetting extends My_Model_Abstract
{
    const SYSTEM_ROW_ID = 1;
    
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
    protected $_pageCoverageMonochrome;
    /**
     * The default color page coverage value
     *
     * @var double
     */
    protected $_pageCoverageColor;
    
    /**
     * The default device margin value
     *
     * @var double
     */
    protected $_deviceMargin;
    
    /**
     * The default page margin value
     *
     * @var double
     */
    protected $_pageMargin;
    
    /**
     * The default pricing config preference
     *
     * @var int
     */
    protected $_pricingConfigId;
    
    /**
     * Service cost per page
     *
     * @var float
     */
    protected $_serviceCostPerPage;
    
    /**
     * Admin cost per page
     *
     * @var float
     */
    protected $_adminCostPerPage;
    
    /**
     * Represents the overage rate for monochrome pages
     *
     * @var float
     */
    protected $_monochromeOverageRatePerPage;
    
    /**
     * Represents the overage rate for color pages
     *
     * @var float
     */
    protected $_colorOverageRatePerPage;
    
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
        if (isset($params->adminCostPerPage) && ! is_null($params->adminCostPerPage))
            $this->setAdminCostPerPage($params->adminCostPerPage);
        if (isset($params->serviceCostPerPage) && ! is_null($params->serviceCostPerPage))
            $this->setServiceCostPerPage($params->serviceCostPerPage);
        if (isset($params->monochromeOverageRatePerPage) && ! is_null($params->monochromeOverageRatePerPage))
            $this->setMonochromeOverageRatePerPage($params->monochromeOverageRatePerPage);
        if (isset($params->colorOverageRatePerPage) && ! is_null($params->colorOverageRatePerPage))
            $this->setColorOverageRatePerPage($params->colorOverageRatePerPage);
    }

    /**
     * Takes an array or a quote setting object and applies overrides
     *
     * @param
     *            <array, Quotegen_Model_QuoteSetting> $settings
     */
    public function applyOverride ($settings)
    {
        // Turn an object into an array
        if ($settings instanceof Quotegen_Model_QuoteSetting)
        {
            $settings = $settings->toArray();
        }
        
        // Turn an array into an ArrayObject
        if (is_array($settings))
        {
            $settings = new ArrayObject($settings, ArrayObject::ARRAY_AS_PROPS);
        }
        
        // Do the same logic as populate, except never set the id.
        if (isset($settings->pageCoverageMonochrome) && ! is_null($settings->pageCoverageMonochrome))
            $this->setPageCoverageMonochrome($settings->pageCoverageMonochrome);
        if (isset($settings->pageCoverageColor) && ! is_null($settings->pageCoverageColor))
            $this->setPageCoverageColor($settings->pageCoverageColor);
        if (isset($settings->deviceMargin) && ! is_null($settings->deviceMargin))
            $this->setDeviceMargin($settings->deviceMargin);
        if (isset($settings->pageMargin) && ! is_null($settings->pageMargin))
            $this->setPageMargin($settings->pageMargin);
        if (isset($settings->serviceCostPerPage) && ! is_null($settings->serviceCostPerPage))
            $this->setServiceCostPerPage($settings->serviceCostPerPage);
        if (isset($settings->adminCostPerPage) && ! is_null($settings->adminCostPerPage))
            $this->setAdminCostPerPage($settings->adminCostPerPage);
        if (isset($settings->monochromeOverageRatePerPage) && ! is_null($settings->monochromeOverageRatePerPage))
            $this->setMonochromeOverageRatePerPage($params->monochromeOverageRatePerPage);
        if (isset($settings->colorOverageRatePerPage) && ! is_null($settings->colorOverageRatePerPage))
            $this->setColorOverageRatePerPage($params->colorOverageRatePerPage);
        
        if (isset($settings->pricingConfigId) && ! is_null($settings->pricingConfigId))
        {
            if ($settings->pricingConfigId !== Proposalgen_Model_PricingConfig::NONE)
            {
                $this->setPricingConfigId($settings->pricingConfigId);
            }
        }
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
                'pricingConfigId' => $this->getPricingConfigId(), 
                'serviceCostPerPage' => $this->getServiceCostPerPage(), 
                'adminCostPerPage' => $this->getAdminCostPerPage(), 
                'monochromeOverageRatePerPage' => $this->getMonochromeOverageRatePerPage(), 
                'colorOverageRatePerPage' => $this->getColorOverageRatePerPage() 
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

    /**
     * Gets the service cost per page
     *
     * @return number
     */
    public function getServiceCostPerPage ()
    {
        return $this->_serviceCostPerPage;
    }

    /**
     * Sets the service cost per page
     *
     * @param number $_serviceCostPerPage            
     */
    public function setServiceCostPerPage ($_serviceCostPerPage)
    {
        $this->_serviceCostPerPage = $_serviceCostPerPage;
        return $this;
    }

    /**
     * Gets the admin cost per page
     *
     * @return number
     */
    public function getAdminCostPerPage ()
    {
        return $this->_adminCostPerPage;
    }

    /**
     * Sets the admin cost per page
     *
     * @param number $_adminCostPerPage            
     */
    public function setAdminCostPerPage ($_adminCostPerPage)
    {
        $this->_adminCostPerPage = $_adminCostPerPage;
        return $this;
    }

    /**
     * Gets the monochrome overage rate
     *
     * @return the $_monochromeOverageRatePerPage
     */
    public function getMonochromeOverageRatePerPage ()
    {
        return $this->_monochromeOverageRatePerPage;
    }

    /**
     * Sets the monochrome overage rate
     *
     * @param float $_monochromeOverageRatePerPage            
     */
    public function setMonochromeOverageRatePerPage ($_monochromeOverageRatePerPage)
    {
        $this->_monochromeOverageRatePerPage = $_monochromeOverageRatePerPage;
        return $this;
    }

    /**
     * Gets the color overage rate per page
     *
     * @return the $_colorOverageRatePerPage
     */
    public function getColorOverageRatePerPage ()
    {
        return $this->_colorOverageRatePerPage;
    }

    /**
     * Sets the color overage rate per page
     *
     * @param number $_colorOverageRatePerPage            
     */
    public function setColorOverageRatePerPage ($_colorOverageRatePerPage)
    {
        $this->_colorOverageRatePerPage = $_colorOverageRatePerPage;
        return $this;
    }
}
