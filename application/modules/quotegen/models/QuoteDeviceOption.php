<?php

/**
 * Quotegen_Model_QuoteDeviceOption
 *
 * @author Shawn Wilder
 *        
 */
class Quotegen_Model_QuoteDeviceOption extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The related quoteDeviceId
     *
     * @var int
     */
    protected $_quoteDeiviceId;
    
    /**
     * The sku of the device option
     *
     * @var string
     */
    protected $_sku;
    
    /**
     * The name of the option
     *
     * @var string
     */
    protected $_name;
    
    /**
     * The description of the device option
     *
     * @var string
     */
    protected $_description;
    
    /**
     * The price of the option
     *
     * @var double
     */
    protected $_price;
    
    /**
     * The quantity of the option wanted
     *
     * @var int
     */
    protected $_quanity;
    
    /**
     * The quanity of the item that is included
     *
     * @var int
     */
    protected $_includedQuanitity;
    
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
        if (isset($params->quoteDeviceId) && ! is_null($params->quoteDeviceId))
            $this->setId($params->quoteDeviceId);
        if (isset($params->sku) && ! is_null($params->sku))
            $this->setId($params->sku);
        if (isset($params->name) && ! is_null($params->name))
            $this->setId($params->name);
        if (isset($params->description) && ! is_null($params->description))
            $this->setId($params->description);
        if (isset($params->price) && ! is_null($params->price))
            $this->setId($params->price);
        if (isset($params->quantity) && ! is_null($params->quantity))
            $this->setId($params->quantity);
        if (isset($params->includedQuantity) && ! is_null($params->includedQuantity))
            $this->setId($params->includedQuantity);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'quoteDeviceId' => $this->getQuoteDeiviceId(), 
                'sku' => $this->getSku(), 
                'name' => $this->getName(), 
                'description' => $this->getDescription(), 
                'price' => $this->getPrice(), 
                'quantity' => $this->getQuanity(), 
                'includedQuantity' => $this->getIncludedQuanitity() 
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
        return $this;
    }

    /**
     * Gets the current quoteDeviceId of the object
     *
     * @return the $_quoteDeiviceId
     */
    public function getQuoteDeiviceId ()
    {
        return $this->_quoteDeiviceId;
    }

    /**
     * Set a new quoteDeviceId
     *
     * @param number $_quoteDeiviceId
     *            the new quoteDeviceId
     */
    public function setQuoteDeiviceId ($_quoteDeiviceId)
    {
        $this->_quoteDeiviceId = $_quoteDeiviceId;
        return $this;
    }

    /**
     * Gets the current sku of the item
     *
     * @return the $_sku
     */
    public function getSku ()
    {
        return $this->_sku;
    }

    /**
     * Sets a new sku
     *
     * @param string $_sky
     *            The new Sku
     */
    public function setSku ($_sku)
    {
        $this->_sku = $_sku;
        return $this;
    }

    /**
     * Gets the name of the deviceOption
     *
     * @return the $_name
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * Sets a new name for the device option
     *
     * @param string $_name
     *            the new name (string)
     */
    public function setName ($_name)
    {
        $this->_name = $_name;
        return $this;
    }

    /**
     * Gets the objects current description
     *
     * @return the $_description
     */
    public function getDescription ()
    {
        return $this->_description;
    }

    /**
     * Set a new description for the object
     *
     * @param string $_description
     *            the new description
     */
    public function setDescription ($_description)
    {
        $this->_description = $_description;
        return $this;
    }

    /**
     * Gets the current price of the object
     *
     * @return the $_price
     */
    public function getPrice ()
    {
        return $this->_price;
    }

    /**
     * Sets a new price for the object
     *
     * @param number $_price
     *            the new price
     */
    public function setPrice ($_price)
    {
        $this->_price = $_price;
        return $this;
    }

    /**
     * Gets the current quantity of the object
     *
     * @return the $_quanity
     */
    public function getQuanity ()
    {
        return $this->_quanity;
    }

    /**
     * Sets a quantity for the object
     *
     * @param number $_quanity
     *            the new quantity
     */
    public function setQuanity ($_quanity)
    {
        $this->_quanity = $_quanity;
        return $this;
    }

    /**
     * Gets the included quantity of the object
     *
     * @return the $_includedQuanitity the new quantity
     */
    public function getIncludedQuanitity ()
    {
        return $this->_includedQuanitity;
    }

    /**
     * Sets the included quantity
     *
     * @param number $_includedQuanitity
     *            the new included quantity
     */
    public function setIncludedQuanitity ($_includedQuanitity)
    {
        $this->_includedQuanitity = $_includedQuanitity;
        return $this;
    }
}
