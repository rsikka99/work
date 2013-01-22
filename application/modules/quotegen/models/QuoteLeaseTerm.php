<?php
class Quotegen_Model_QuoteLeaseTerm extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $quoteId;

    /**
     * @var int
     */
    public $leasingSchemaTermId;
    
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

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->quoteId) && ! is_null($params->quoteId))
            $this->quoteId = $params->quoteId;

        if (isset($params->leasingSchemaTermId) && ! is_null($params->leasingSchemaTermId))
            $this->leasingSchemaTermId = $params->leasingSchemaTermId;

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array (
            "quoteId" => $this->quoteId,
            "leasingSchemaTermId" => $this->leasingSchemaTermId,
        );
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
            $this->_quote = Quotegen_Model_Mapper_Quote::getInstance()->find($this->quoteId);
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
            $this->_leaseTerm = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance()->find($this->leasingSchemaTermId);
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