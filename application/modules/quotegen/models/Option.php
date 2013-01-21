<?php

/**
 * Quotegen_Model_Option
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_Option extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The name of the option
     *
     * @var string
     */
    protected $_name;
    
    /**
     * The description of the option
     *
     * @var string
     */
    protected $_description;
    
    /**
     * The cost of the option
     *
     * @var number
     */
    protected $_cost;
    
    /**
     * The sku of the device
     *
     * @var string
     */
    protected $_oemSku;
    
    /**
     * the dealer sku for the object
     *
     * @var string
     */
    protected $_dealerSku;
    
    /**
     * An array of Quotegen_Model_Category associated with the object
     *
     * @var array
     */
    protected $_categories;
    
    /**
     * Returns this options included quantity for a device
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
        
        if (isset($params->name) && ! is_null($params->name))
            $this->setName($params->name);
        
        if (isset($params->description) && ! is_null($params->description))
            $this->setDescription($params->description);
        
        if (isset($params->cost) && ! is_null($params->cost))
            $this->setCost($params->cost);
        
        if (isset($params->oemSku) && ! is_null($params->oemSku))
            $this->setOemSku($params->oemSku);
        
        if (isset($params->dealerSku) && ! is_null($params->dealerSku))
            $this->setDealerSku($params->dealerSku);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'name' => $this->getName(), 
                'description' => $this->getDescription(), 
                'cost' => $this->getCost(), 
                'dealerSku' => $this->getDealerSku(), 
                'oemSku' => $this->getOemSku() 
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
     * Gets the name of the option
     *
     * @return string The name of the option
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * Sets the name of the option
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
     * Gets the description of the option
     *
     * @return string The description of the option
     */
    public function getDescription ()
    {
        return $this->_description;
    }

    /**
     * Sets the description of the option
     *
     * @param string $_description
     *            The new description
     */
    public function setDescription ($_description)
    {
        $this->_description = $_description;
        return $this;
    }

    /**
     * Gets the cost of the option
     *
     * @return number The cost of the option
     */
    public function getCost ()
    {
        return $this->_cost;
    }

    /**
     * Sets a new cost for the option
     *
     * @param number $_cost
     *            The new cost to set
     */
    public function setCost ($_cost)
    {
        $this->_cost = $_cost;
        return $this;
    }

    /**
     * Gets the sku of the option
     *
     * @return string The sku
     */
    public function getOemSku ()
    {
        return $this->_oemSku;
    }

    /**
     * Sets a new sku for the option
     *
     * @param string $_sku
     *            The new sku
     */
    public function setOemSku ($_sku)
    {
        $this->_oemSku = $_sku;
        return $this;
    }
    
    /**
     * Gets the current sku of the item
     *
     * @return string
     */
    public function getDealerSku ()
    {
        return $this->_dealerSku;
    }
    
    /**
     * Sets a new sku
     *
     * @param string $_dealerSku
     *            The new value
     */
    public function setDealerSku ($_dealerSku)
    {
        $this->_dealerSku = $_dealerSku;
        return $this;
    }

    /**
     * Gets all categories associated with this objects id
     *
     * @return Quotegen_Model_Category $_categories
     */
    public function getCategories ()
    {
        if (! isset($this->_categories))
        {
            $this->_categories = Quotegen_Model_Mapper_OptionCategory::getInstance()->fetchAllCategoriesForOption($this->getId());
        }
        return $this->_categories;
    }

    /**
     * Set new categories for this object
     *
     * @param multitype: $_categories
     *            The new array of categories
     */
    public function setCategories ($_categories)
    {
        $this->_categories = $_categories;
        return $this;
    }

    /**
     * Gets the included quantity for a device option
     *
     * @return int $includedQuantity
     */
    public function getIncludedQuantity ($deviceId)
    {
        if (! isset($this->_includedQuantity))
        {
            $includedQuantity = Quotegen_Model_Mapper_DeviceOption::getInstance()->fetch("masterDeviceId = {$deviceId} AND optionid = {$this->getId()}");
            if ($includedQuantity)
            {
                $this->_includedQuantity = $includedQuantity->includedQuantity;
            }
            else
            {
                $this->_includedQuantity = 0;
            }
        }
        return $this->_includedQuantity;
    }
}
