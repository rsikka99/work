<?php

/**
 * Admin_Model_Toner
 *
 * @author John Sadler
 *        
 */
class Admin_Model_Toner extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The sku of the toner
     *
     * @var string
     */
    protected $_sku;
    
    /**
     * The cost of the toner
     *
     * @var float
     */
    protected $_cost;
    
    /**
     * The yield of the toner
     *
     * @var int
     */
    protected $_yield;
    
    /**
     * The part type id for the toner
     *
     * @var int
     */
    protected $_partTypeId;
    
    /**
     * The manufacturer id for the toner
     *
     * @var int
     */
    protected $_manufacturerId;
    
    /**
     * The toner color id for the toenr
     *
     * @var int
     */
    protected $_tonerColorId;
    
    /**
     * The part type object
     *
     * @var array
     */
    protected $_partType;
    
    /**
     * The manufacturer object
     *
     * @var array
     */
    protected $_manufacturer;
    
    /**
     * The toner color object
     *
     * @var array
     */
    protected $_tonerColor;
    
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
        
        if (isset($params->sku) && ! is_null($params->sku))
            $this->setSku($params->sku);
        
        if (isset($params->cost) && ! is_null($params->cost))
            $this->setCost($params->cost);
        
        if (isset($params->yield) && ! is_null($params->yield))
            $this->setYield($params->yield);
        
        if (isset($params->part_type_id) && ! is_null($params->part_type_id))
            $this->setPartTypeId($params->part_type_id);
        
        if (isset($params->manufacturer_id) && ! is_null($params->manufacturer_id))
            $this->setManufacturerId($params->manufacturer_id);
        
        if (isset($params->toner_color_id) && ! is_null($params->toner_color_id))
            $this->setTonerColorId($params->toner_color_id);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'sku' => $this->getSku(), 
                'cost' => $this->getCost(), 
                'yield' => $this->getYield(), 
                'part_type_id' => (int)$this->getPartTypeId(), 
                'manufacturer_id' => (int)$this->getManufacturerId(), 
                'toner_color_id' => (int)$this->getTonerColorId() 
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
        $this->_id = (int)$_id;
    }

    /**
     *
     * @return the $_sku
     */
    public function getSku ()
    {
        return $this->_sku;
    }

    /**
     *
     * @param string $_sku            
     */
    public function setSku ($_sku)
    {
        $this->_sku = (string)$_sku;
        return $this;
    }

    /**
     *
     * @return the $_cost
     */
    public function getCost ()
    {
        return $this->_cost;
    }

    /**
     *
     * @param number $_cost            
     */
    public function setCost ($_cost)
    {
        $this->_cost = (float)$_cost;
        return $this;
    }

    /**
     *
     * @return the $_yield
     */
    public function getYield ()
    {
        return $this->_yield;
    }

    /**
     *
     * @param number $_yield            
     */
    public function setYield ($_yield)
    {
        $this->_yield = (int)$_yield;
        return $this;
    }

    /**
     *
     * @return the $_partTypeId
     */
    public function getPartTypeId ()
    {
        return $this->_partTypeId;
    }

    /**
     *
     * @param number $_partTypeId            
     */
    public function setPartTypeId ($_partTypeId)
    {
        $this->_partTypeId = (int)$_partTypeId;
        return $this;
    }

    /**
     *
     * @return the $_manufacturerId
     */
    public function getManufacturerId ()
    {
        return $this->_manufacturerId;
    }

    /**
     *
     * @param number $_manufacturerId            
     */
    public function setManufacturerId ($_manufacturerId)
    {
        $this->_manufacturerId = (int)$_manufacturerId;
        return $this;
    }

    /**
     *
     * @return the $_tonerColorId
     */
    public function getTonerColorId ()
    {
        return $this->_tonerColorId;
    }

    /**
     *
     * @param number $_tonerColorId            
     */
    public function setTonerColorId ($_tonerColorId)
    {
        $this->_tonerColorId = (int)$_tonerColorId;
        return $this;
    }

    /**
     *
     * @return the $_partType
     */
    public function getPartType ()
    {
        if (! isset($this->_partType))
        {
            $this->_partType = Proposalgen_Model_Mapper_PartType::getInstance()->find($this->getPartTypeId());
        }
        return $this->_partType;
    }

    /**
     *
     * @param string $_partType            
     */
    public function setPartType ($_partType)
    {
        $this->_partType = $_partType;
        return $this;
    }

    /**
     *
     * @return the $_manufacturer
     */
    public function getManufacturer ()
    {
        if (! isset($this->_manufacturer))
        {
            $this->_manufacturer = Proposalgen_Model_Mapper_Manufacturer::getInstance()->find($this->getManufacturerId());
        }
        return $this->_manufacturer;
    }

    /**
     *
     * @param string $_manufacturer            
     */
    public function setManufacturer ($_manufacturer)
    {
        $this->_manufacturer = $_manufacturer;
        return $this;
    }

    /**
     *
     * @return the $_tonerColor
     */
    public function getTonerColor ()
    {
        if (! isset($this->_tonerColor))
        {
            $this->_tonerColor = Proposalgen_Model_Mapper_TonerColor::getInstance()->find($this->getTonerColorId());
        }
        return $this->_tonerColor;
    }

    /**
     *
     * @param string $_tonerColor            
     */
    public function setTonerColor ($_tonerColor)
    {
        $this->_tonerColor = $_tonerColor;
        return $this;
    }
}
