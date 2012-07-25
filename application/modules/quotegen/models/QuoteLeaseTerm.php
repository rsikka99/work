<?php

/**
 * Quotegen_Model_QuoteLeaseTerm
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_QuoteLeaseTerm extends My_Model_Abstract
{
    
    /**
     * The quote id
     *
     * @var int
     */
    protected $_quoteId;
    
    /**
     * The lease term id
     *
     * @var int
     */
    protected $_leasingSchemaTermId;
    
    /**
     * The quote
     *
     * @var Quotegen_Model_Quote
     */
    protected $_quote;
    
    /**
     * The leasing term object
     *
     * @var Quotegen_Model_LeasingSchemaTerm
     */
    protected $_leaseTerm;
    
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
            $this->setQuoteId($params->quoteId);
        if (isset($params->leasingSchemaTermId) && ! is_null($params->leasingSchemaTermId))
            $this->setLeasingSchemaTermId($params->leasingSchemaTermId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'quoteId' => $this->getQuoteId(), 
                'leasingSchemaTermId' => $this->getLeasingSchemaTermId() 
        );
    }

    /**
     * Gets the quote id
     *
     * @return number The quote id
     */
    public function getQuoteId ()
    {
        return $this->_quoteId;
    }

    /**
     * Sets a new quote id
     *
     * @param number $_quoteId
     *            The new id
     */
    public function setQuoteId ($_quoteId)
    {
        $this->_quoteId = $_quoteId;
        return $this;
    }

    /**
     * Gets the lease term id
     *
     * @return number The lease term id
     */
    public function getLeasingSchemaTermId ()
    {
        return $this->_leasingSchemaTermId;
    }

    /**
     * Sets a lease term id
     *
     * @param number $_leasingSchemaTermId
     *            The id
     */
    public function setLeasingSchemaTermId ($_leasingSchemaTermId)
    {
        $this->_leasingSchemaTermId = $_leasingSchemaTermId;
        return $this;
    }

    /**
     * Gets the quote object
     *
     * @return Quotegen_Model_Quote
     */
    public function getQuote ()
    {
        if (! isset($this->_quote))
        {
            $this->_quote = Quotegen_Model_Mapper_Quote::getInstance()->find($this->getQuoteId());
        }
        return $this->_quote;
    }

    /**
     * Sets the quote object
     *
     * @param Quotegen_Model_Quote $_quote            
     */
    public function setQuote ($_quote)
    {
        $this->_quote = $_quote;
        return $this;
    }

    /**
     * Gets the lease term object
     *
     * @return Quotegen_Model_LeasingSchemaTerm
     */
    public function getLeaseTerm ()
    {
        if (! isset($this->_quote))
        {
            $this->_leaseTerm = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance()->find($this->getLeasingSchemaTermId());
        }
        return $this->_leaseTerm;
    }

    /**
     * Sets the lease term object
     *
     * @param Quotegen_Model_LeasingSchemaTerm $_leaseTerm            
     */
    public function setLeaseTerm ($_leaseTerm)
    {
        $this->_leaseTerm = $_leaseTerm;
        return $this;
    }
}
