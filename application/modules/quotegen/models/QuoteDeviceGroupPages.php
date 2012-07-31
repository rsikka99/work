<?php

/**
 * Quotegen_Model_QuoteDeviceGroupPages
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_QuoteDeviceGroupPages extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id;
    
    /**
     * The quote device group id
     *
     * @var int
     */
    protected $_quoteDeviceGroupId;
    /**
     * The name of the item
     *
     * @var string
     */
    protected $_name;
    /**
     * The sku of the item
     *
     * @var string
     */
    protected $_sku;
    /**
     * The cost per page of the item
     *
     * @var float
     */
    protected $_costPerPage;
    /**
     * The price of the included pages
     *
     * @var float
     */
    protected $_includedPrice;
    /**
     * The quantity of included pages
     *
     * @var int
     */
    protected $_includedQuantity;
    
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
        
        if (isset($params->name) && ! is_null($params->name))
            $this->setName($params->name);
        
        if (isset($params->sku) && ! is_null($params->sku))
            $this->setSku($params->sku);
        
        if (isset($params->costPerPage) && ! is_null($params->costPerPage))
            $this->setCostPerPage($params->costPerPage);
        
        if (isset($params->includedPrice) && ! is_null($params->includedPrice))
            $this->setIncludedPrice($params->includedPrice);
        
        if (isset($params->includedQuantity) && ! is_null($params->includedQuantity))
            $this->setIncludedQuantity($params->includedQuantity);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                "quoteDeviceGroupId" => $this->getQuoteDeviceGroupId(), 
                "name" => $this->getName(), 
                "sku" => $this->getSku(), 
                "costPerPage" => $this->getCostPerPage(), 
                "includedPrice" => $this->getIncludedPrice(), 
                "includedQuantity" => $this->getIncludedQuantity() 
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
     * @param number $_id
     *            the new id
     */
    public function setId ($_id)
    {
        $this->_id = $_id;
    }

    /**
     * Gets the quote device group id
     *
     * @return number
     */
    public function getQuoteDeviceGroupId ()
    {
        return $this->_quoteDeviceGroupId;
    }

    /**
     * Sets the quote device group id
     *
     * @param number $_quoteDeviceGroupId            
     */
    public function setQuoteDeviceGroupId ($_quoteDeviceGroupId)
    {
        $this->_quoteDeviceGroupId = $_quoteDeviceGroupId;
        return $this;
    }

    /**
     * Gets the name
     *
     * @return string
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * Sets the name
     *
     * @param string $_name            
     */
    public function setName ($_name)
    {
        $this->_name = $_name;
        return $this;
    }

    /**
     * Gets the sku
     *
     * @return string
     */
    public function getSku ()
    {
        return $this->_sku;
    }

    /**
     * Sets the sku
     *
     * @param string $_sku            
     */
    public function setSku ($_sku)
    {
        $this->_sku = $_sku;
        return $this;
    }

    /**
     * Gets the cost per page
     *
     * @return number
     */
    public function getCostPerPage ()
    {
        return $this->_costPerPage;
    }

    /**
     * Sets the cost per page
     *
     * @param number $_costPerPage            
     */
    public function setCostPerPage ($_costPerPage)
    {
        $this->_costPerPage = $_costPerPage;
        return $this;
    }

    /**
     * Gets the included price
     *
     * @return number
     */
    public function getIncludedPrice ()
    {
        return $this->_includedPrice;
    }

    /**
     * Sets the included price
     *
     * @param number $_includedPrice            
     */
    public function setIncludedPrice ($_includedPrice)
    {
        $this->_includedPrice = $_includedPrice;
        return $this;
    }

    /**
     * Gets the included quantity
     *
     * @return number
     */
    public function getIncludedQuantity ()
    {
        return $this->_includedQuantity;
    }

    /**
     * Sets the included quantity
     *
     * @param number $_includedQuantity            
     */
    public function setIncludedQuantity ($_includedQuantity)
    {
        $this->_includedQuantity = $_includedQuantity;
        return $this;
    }
}
