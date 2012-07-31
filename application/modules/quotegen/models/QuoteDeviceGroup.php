<?php

/**
 * Quotegen_Model_QuoteDeviceGroup
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_QuoteDeviceGroup extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The quote id of the quote that the device group belongs to
     *
     * @var int
     */
    protected $_quoteId;
    
    /**
     * The cost per monochrome page
     *
     * @var number
     */
    protected $_pageMargin;
    
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
            $this->setQuoteId($params->quoteId);
        
        if (isset($params->pageMargin) && ! is_null($params->pageMargin))
            $this->setPageMargin($params->pageMargin);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'quoteId' => $this->getQuoteId(), 
                'pageMargin' => $this->getPageMargin() 
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
     * Gets the quote id
     *
     * @return number
     */
    public function getQuoteId ()
    {
        return $this->_quoteId;
    }

    /**
     * Sets the quote id
     *
     * @param number $_quoteId            
     */
    public function setQuoteId ($_quoteId)
    {
        $this->_quoteId = $_quoteId;
        return $this;
    }

    /**
     * Gets the page margin
     *
     * @return number
     */
    public function getPageMargin ()
    {
        return $this->_pageMargin;
    }

    /**
     * Sets the page margin
     *
     * @param number $_pageMargin            
     */
    public function setPageMargin ($_pageMargin)
    {
        $this->_pageMargin = $_pageMargin;
        return $this;
    }
}
