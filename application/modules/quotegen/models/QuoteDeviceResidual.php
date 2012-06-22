<?php

/**
 * Quotegen_Model_QuoteDeviceResidual
 *
 * @author Shawn Wilder
 *        
 */
class Quotegen_Model_QuoteDeviceResidual extends My_Model_Abstract
{
    /**
     * id of a quoteDevice
     *
     * @var int
     */
    protected $_quoteDeviceId;
    
    /**
     * residual amount
     *
     * @var double
     */
    protected $_amount;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->quoteDeviceId) && ! is_null($params->quoteDeviceId))
            $this->setId($params->quoteDeviceId);
        if (isset($params->amount) && ! is_null($params->amount))
            $this->setId($params->amount);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'quoteDeviceId' => $this->getQuoteDeviceId(), 
                'amount' => $this->getAmount() 
        );
    }

    /**
     *
     * @return the $_quoteDeviceId
     */
    public function getQuoteDeviceId ()
    {
        return $this->_quoteDeviceId;
    }

    /**
     *
     * @param number $_quoteDeviceId            
     */
    public function setQuoteDeviceId ($_quoteDeviceId)
    {
        $this->_quoteDeviceId = $_quoteDeviceId;
    }

    /**
     *
     * @return the $_amount
     */
    public function getAmount ()
    {
        return $this->_amount;
    }

    /**
     *
     * @param number $_amount            
     */
    public function setAmount ($_amount)
    {
        $this->_amount = $_amount;
    }
}
