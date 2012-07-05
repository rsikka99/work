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
     * The price of the toner
     *
     * @var double
     */
    protected $_price;
    
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
     * The part type name for the toner
     *
     * @var array
     */
    protected $_partType;
    
    /**
     * The manufacturer id for the toner
     *
     * @var int
     */
    protected $_manufacturerId;

    /**
     * The manufacturer name
     *
     * @var array
     */    
    protected $_manufacturer;
    
    /**
     * The toner color id for the toenr
     *
     * @var int
     */
    protected $_tonerColorId;

    /**
     * The toner color name
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
        if (isset($params->price) && ! is_null($params->price))
            $this->setPrice($params->price);
        if (isset($params->yield) && ! is_null($params->yield))
            $this->setYield($params->yield);
        if (isset($params->partTypeId) && ! is_null($params->partTypeId))
            $this->setPartTypeId($params->partTypeId);
        if (isset($params->manufacturerId) && ! is_null($params->manufacturerId))
            $this->setManufacturerId($params->manufacturerId);
        if (isset($params->tonerColorId) && ! is_null($params->tonerColorId))
            $this->setTonerColorId($params->tonerColorId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(),
                'sku' => $this->getSku(),
                'price' => $this->getPrice(),
                'yield' => $this->getYield(),
                'part_type_id' => $this->getPartTypeId(),
                'manufacturer_id' => $this->getManufacturerId(),
                'toner_color_id' => $this->getTonerColorId()
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
     * @return the $_sku
     */
    public function getSku ()
    {
        return $this->_sku;
    }

	/**
     * @param string $_sku
     */
    public function setSku ($_sku)
    {
        $this->_sku = $_sku;
        return $this;
    }

	/**
     * @return the $_price
     */
    public function getPrice ()
    {
        return $this->_price;
    }

	/**
     * @param number $_price
     */
    public function setPrice ($_price)
    {
        $this->_price = $_price;
        return $this;
    }

	/**
     * @return the $_yield
     */
    public function getYield ()
    {
        return $this->_yield;
    }

	/**
     * @param number $_yield
     */
    public function setYield ($_yield)
    {
        $this->_yield = $_yield;
        return $this;
    }

	/**
     * @return the $_partTypeId
     */
    public function getPartTypeId ()
    {
        return $this->_partTypeId;
    }

	/**
     * @param number $_partTypeId
     */
    public function setPartTypeId ($_partTypeId)
    {
        $this->_partTypeId = $_partTypeId;
        return $this;
    }

	/**
     * @return the $_manufacturerId
     */
    public function getManufacturerId ()
    {
        return $this->_manufacturerId;
    }

	/**
     * @param number $_manufacturerId
     */
    public function setManufacturerId ($_manufacturerId)
    {
        $this->_manufacturerId = $_manufacturerId;
        return $this;
    }

	/**
     * @return the $_tonerColorId
     */
    public function getTonerColorId ()
    {
        return $this->_tonerColorId;
    }

	/**
     * @param number $_tonerColorId
     */
    public function setTonerColorId ($_tonerColorId)
    {
        $this->_tonerColorId = $_tonerColorId;
        return $this;
    }
    
	/**
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
     * @param string $_partType
     */
    public function setPartType ($_partType)
    {
        $this->_partType = $_partType;
        return $this;
    }

	/**
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
     * @param string $_manufacturer
     */
    public function setManufacturer ($_manufacturer)
    {
        $this->_manufacturer = $_manufacturer;
        return $this;
    }

	/**
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
     * @param string $_tonerColor
     */
    public function setTonerColor ($_tonerColor)
    {
        $this->_tonerColor = $_tonerColor;
        return $this;
    }


}
