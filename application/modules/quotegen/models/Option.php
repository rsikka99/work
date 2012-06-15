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
     * The price of the option
     *
     * @var number
     */
    protected $_price;
    
    /**
     * The sku of the device
     *
     * @var string
     */
    protected $_sku;
    
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
        
        if (isset($params->price) && ! is_null($params->price))
            $this->setPrice($params->price);
        
        if (isset($params->sku) && ! is_null($params->sku))
            $this->setSku($params->sku);
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
                'price' => $this->getPrice(), 
                'sku' => $this->getSku() 
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
     * Gets the price of the option
     * 
     * @return number The price of the option
     */
    public function getPrice ()
    {
        return $this->_price;
    }

    /**
     * Sets a new price for the option
     * 
     * @param number $_price
     *            The new price to set
     */
    public function setPrice ($_price)
    {
        $this->_price = $_price;
        return $this;
    }

    /**
     * Gets the sku of the option
     * 
     * @return string The sku
     */
    public function getSku ()
    {
        return $this->_sku;
    }

    /**
     * Sets a new sku for the option
     * 
     * @param string $_sku
     *            The new sku
     */
    public function setSku ($_sku)
    {
        $this->_sku = $_sku;
        return $this;
    }
}
