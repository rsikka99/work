<?php

/**
 * Quotegen_Model_QuoteDevice
 *
 * @author Shawn Wilder
 *        
 */
class Quotegen_Model_QuoteDevice extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The id of a quote
     *
     * @var int
     */
    protected $_quoteId;
    
    /**
     * A number representing margins
     *
     * @var double
     */
    protected $_margin;
    
    /**
     * A string representing a name of a device
     *
     * @var string
     */
    protected $_name;
    
    /**
     * String of the sku
     *
     * @var string
     */
    protected $_sku;
    
    /**
     * Number of oem cost per page monochrome
     *
     * @var double
     */
    protected $_oemCostPerPageMonochrome;
    
    /**
     * Number of oem cost per page color
     *
     * @var double
     */
    protected $_oemCostPerPageColor;
    
    /**
     * Number of comp cost per page monochrome
     *
     * @var double
     */
    protected $_compCostPerPageMonochrome;
    
    /**
     * Number of comp cost per page color
     *
     * @var double
     */
    protected $_compCostPerPageColor;
    
    /**
     * Number of the price of the device
     *
     * @var double
     */
    protected $_price;
    
    /**
     * Number of devices
     *
     * @var int
     */
    protected $_quantity;
    
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
        if (isset($params->quoteId) && ! is_null($params->quoteId))
            $this->setId($params->quoteId);
        if (isset($params->margin) && ! is_null($params->margin))
            $this->setId($params->margin);
        if (isset($params->sku) && ! is_null($params->sku))
            $this->setId($params->sku);
        if (isset($params->oemCostPerPageMonochrome) && ! is_null($params->oemCostPerPageMonochrome))
            $this->setId($params->oemCostPerPageMonochrome);
        if (isset($params->omeCostPerPageColor) && ! is_null($params->omeCostPerPageColor))
            $this->setId($params->omeCostPerPageColor);
        if (isset($params->compCostPerPageMonochrome) && ! is_null($params->compCostPerPageMonochrome))
            $this->setId($params->compCostPerPageMonochrome);
        if (isset($params->compCostPerPageColor) && ! is_null($params->compCostPerPageColor))
            $this->setId($params->compCostPerPageColor);
        if (isset($params->price) && ! is_null($params->price))
            $this->setId($params->price);
        if (isset($params->quantity) && ! is_null($params->quantity))
            $this->setId($params->quantity);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'quoteId' => $this->getQuoteId(), 
                'margin' => $this->getMargin(), 
                'name' => $this->getName(), 
                'sku' => $this->getSku(), 
                'oemCostPerPageMonochrome' => $this->getOmeCostPerPageMonochrome(), 
                'oemCostPerPageColor' => $this->getOemCostPerPageColor(), 
                'compCostPerPageMonochrome' => $this->getCompCostPerPageMonochrome(), 
                'compCostPerPageColor' => $this->getCompCostPerPageColor(), 
                'price' => $this->getPrice(), 
                'quantity' => $this->getQuantity() 
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
     * Gets the quote id
     *
     * @return the $_quoteId
     */
    public function getQuoteId ()
    {
        return $this->_quoteId;
    }

    /**
     * Sets a quote id
     *
     * @param number $_quoteId
     *            the new quoteId
     */
    public function setQuoteId ($_quoteId)
    {
        $this->_quoteId = $_quoteId;
        return $this;
    }

    /**
     * Gets the objects margin
     *
     * @return the $_margin
     */
    public function getMargin ()
    {
        return $this->_margin;
    }

    /**
     * Sets the margin
     *
     * @param number $_margin
     *            the new margin
     */
    public function setMargin ($_margin)
    {
        $this->_margin = $_margin;
        return $this;
    }

    /**
     * Gets the quote name
     *
     * @return the $_name
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * Sets the name
     *
     * @param string $_name
     *            the new name
     */
    public function setName ($_name)
    {
        $this->_name = $_name;
        return $this;
    }

    /**
     * Gets the sku of the object
     *
     * @return the $_sku
     */
    public function getSku ()
    {
        return $this->_sku;
    }

    /**
     * Sets a new sku for the objet
     *
     * @param string $_sku
     *            the new sku
     */
    public function setSku ($_sku)
    {
        $this->_sku = $_sku;
        return $this;
    }

    /**
     * Gets the oemCostPerPageMonochrome
     *
     * @return the $_oemCostPerPageMonochrome
     */
    public function getOmeCostPerPageMonochrome ()
    {
        return $this->_oemCostPerPageMonochrome;
    }

    /**
     * Sets the oemCostPerPageMonochrome
     *
     * @param number $_oemCostPerPageMonochrome
     *            the new oemCostPerPageMonochrome
     */
    public function setOmeCostPerPageMonochrome ($_oemCostPerPageMonochrome)
    {
        $this->_oemCostPerPageMonochrome = $_oemCostPerPageMonochrome;
        return $this;
    }

    /**
     * Gets the oemCostPerPageColor
     *
     * @return the $_oemCostPerPageColor
     */
    public function getOemCostPerPageColor ()
    {
        return $this->_oemCostPerPageColor;
    }

    /**
     * Sets the oemCostPerPageColor
     *
     * @param number $_oemCostPerPageColor
     *            the new oemCostPerPageColor
     */
    public function setOemCostPerPageColor ($_oemCostPerPageColor)
    {
        $this->_oemCostPerPageColor = $_oemCostPerPageColor;
        return $this;
    }

    /**
     * Gets the compCostPerPageMonochrome
     *
     * @return the $_compCostPerPageMonochrome
     */
    public function getCompCostPerPageMonochrome ()
    {
        return $this->_compCostPerPageMonochrome;
    }

    /**
     * Sets the compCostPerPageMonochrome
     *
     * @param number $_compCostPerPageMonochrome
     *            the new compCostPerPageMonochrome
     */
    public function setCompCostPerPageMonochrome ($_compCostPerPageMonochrome)
    {
        $this->_compCostPerPageMonochrome = $_compCostPerPageMonochrome;
        return $this;
    }

    /**
     * Gets the compCostPerPageColor
     *
     * @return the $_compCostPerPageColor
     */
    public function getCompCostPerPageColor ()
    {
        return $this->_compCostPerPageColor;
    }

    /**
     * Sets the objects compCostPerPageColor
     *
     * @param number $_compCostPerPageColor
     *            the new compCostPerPageColor
     */
    public function setCompCostPerPageColor ($_compCostPerPageColor)
    {
        $this->_compCostPerPageColor = $_compCostPerPageColor;
        return $this;
    }

    /**
     * Gets the objects price
     *
     * @return the $_price
     */
    public function getPrice ()
    {
        return $this->_price;
    }

    /**
     * Sets the objects new price
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
     * Gets the object currrent quatity
     *
     * @return the $_quantity
     */
    public function getQuantity ()
    {
        return $this->_quantity;
    }

    /**
     * Sets the objects new quantity
     *
     * @param number $_quantity
     *            the new quanitty
     */
    public function setQuantity ($_quantity)
    {
        $this->_quantity = $_quantity;
        return $this;
    }
}
