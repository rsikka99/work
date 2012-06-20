<?php

/**
 * Quotegen_Model_LeasedQuote
 *
 * @author Shawn Wilder
 *        
 */
class Quotegen_Model_LeasedQuote extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_quoteId = 0;
    
    /**
     * The percentage for the rate of the lease
     *  
     * @var double
     */
    protected $_rate;
    
    /**
     * The length of term in months
     * 
     * @var int
     */
    protected $_term;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->quoteId) && ! is_null($params->quoteId))
            $this->setId($params->quoteId);
        if (isset($params->rate) && ! is_null($params->rate))
            $this->setId($params->rate);
        if (isset($params->term) && ! is_null($params->term))
            $this->setId($params->term);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'quoteId' => $this->getId(),
                'rate' => $this->getRate(),
                'term' => $this->getTerm(),
        );
    }
	/**
     * @return the $_quoteId
     */
    public function getQuoteId ()
    {
        return $this->_quoteId;
    }

	/**
     * @param number $_quoteId
     */
    public function setQuoteId ($_quoteId)
    {
        $this->_quoteId = $_quoteId;
    }

	/**
     * @return the $_rate
     */
    public function getRate ()
    {
        return $this->_rate;
    }

	/**
     * @param number $_rate
     */
    public function setRate ($_rate)
    {
        $this->_rate = $_rate;
    }

	/**
     * @return the $_term
     */
    public function getTerm ()
    {
        return $this->_term;
    }

	/**
     * @param number $_term
     */
    public function setTerm ($_term)
    {
        $this->_term = $_term;
    }

}
