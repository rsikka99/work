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
    protected $_quoteDeviceId;
    
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
     * The cost of the option
     *
     * @var double
     */
    protected $_cost;
    
    /**
     * The quantity of the option wanted
     *
     * @var int
     */
    protected $_quantity;
    
    /**
     * The quanity of the item that is included
     *
     * @var int
     */
    protected $_includedQuantity;
    
    /**
     * The option associated
     *
     * @var Quotegen_Model_Option
     */
    protected $_option;
    
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
            $this->setQuoteDeviceId($params->quoteDeviceId);
        if (isset($params->sku) && ! is_null($params->sku))
            $this->setSku($params->sku);
        if (isset($params->name) && ! is_null($params->name))
            $this->setName($params->name);
        if (isset($params->description) && ! is_null($params->description))
            $this->setDescription($params->description);
        if (isset($params->cost) && ! is_null($params->cost))
            $this->setCost($params->cost);
        if (isset($params->quantity) && ! is_null($params->quantity))
            $this->setQuantity($params->quantity);
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
                'quoteDeviceId' => $this->getQuoteDeviceId(), 
                'sku' => $this->getSku(), 
                'name' => $this->getName(), 
                'description' => $this->getDescription(), 
                'cost' => $this->getCost(), 
                'quantity' => $this->getQuantity(), 
                'includedQuantity' => $this->getIncludedQuantity() 
        );
    }

    /**
     * Gets the id of the option
     *
     * @return number The id of the option
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     * Sets the id of the option
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
     * Gets the current quoteDeviceId of the option
     *
     * @return int
     */
    public function getQuoteDeviceId ()
    {
        return $this->_quoteDeviceId;
    }

    /**
     * Set a new quoteDeviceId
     *
     * @param number $_quoteDeviceId
     *            the new quoteDeviceId
     */
    public function setQuoteDeviceId ($_quoteDeviceId)
    {
        $this->_quoteDeviceId = $_quoteDeviceId;
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
     * Gets the options current description
     *
     * @return the $_description
     */
    public function getDescription ()
    {
        return $this->_description;
    }

    /**
     * Set a new description for the option
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
     * Gets the current cost of the option
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
     *            The new cost
     */
    public function setCost ($_cost)
    {
        $this->_cost = $_cost;
        return $this;
    }

    /**
     * Gets the current quantity of the option
     *
     * @return the $_quantity
     */
    public function getQuantity ()
    {
        return $this->_quantity;
    }

    /**
     * Sets a quantity for the option
     *
     * @param number $_quanity
     *            the new quantity
     */
    public function setQuantity ($_quanity)
    {
        $this->_quantity = $_quanity;
        return $this;
    }

    /**
     * Gets the included quantity of the option
     *
     * @return the $_includedQuantity the new quantity
     */
    public function getIncludedQuantity ()
    {
        return $this->_includedQuantity;
    }

    /**
     * Sets the included quantity
     *
     * @param number $_includedQuantity
     *            the new included quantity
     */
    public function setIncludedQuantity ($_includedQuantity)
    {
        $this->_includedQuantity = $_includedQuantity;
        return $this;
    }

    /**
     * Gets the associated option, if any.
     *
     * @return Quotegen_Model_Option The option, or false if no link exists
     */
    public function getOption ()
    {
        if (! isset($this->_option))
        {
            $this->_option = false;
            $quoteDeviceConfigurationOption = Quotegen_Model_Mapper_QuoteDeviceConfigurationOption::getInstance()->findByQuoteDeviceOptionId($this->getId());
            if ($quoteDeviceConfigurationOption)
            {
                $this->_option = $quoteDeviceConfigurationOption->getOption();
            }
        }
        return $this->_option;
    }

    /**
     * Sets the associated option.
     *
     * @param Quotegen_Model_Option $_option            
     */
    public function setOption ($_option)
    {
        $this->_option = $_option;
        return $this;
    }

    /**
     * Gets the total quantity (quantity + included quantity)
     *
     * @return number The total quantity
     */
    public function getTotalQuantity ()
    {
        return (int)$this->getQuantity() + (int)$this->getIncludedQuantity();
    }
    
    /**
     * Gets the total cost (Cost * Quantity)
     *
     * @return number The total cost for the option
     */
    public function getSubTotal ()
    {
        $subtotal = 0;
        $cost = (float)$this->getCost();
        $quantity = (int)$this->getQuantity();
        if ($cost > 0 && $quantity > 0)
        {
            $subtotal = $cost * $quantity;
        }
        return $subtotal;
    }
}
