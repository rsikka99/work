<?php

/**
 * Quotegen_Model_QuoteQuoteSetting
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_QuoteQuoteSetting extends My_Model_Abstract
{
    /**
     *
     * @var int
     */
    protected $_quoteId;
    
    /**
     *
     * @var int
     */
    protected $_quoteSettingId;
    
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
        if (isset($params->quoteSettingId) && ! is_null($params->quoteSettingId))
            $this->setQuoteSettingId($params->quoteSettingId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'quoteId' => $this->getQuoteId(), 
                'quoteSettingId' => $this->getQuoteSettingId() 
        );
    }

    /**
     * Get the quoteId from the object
     *
     * @return the $_quoteId
     */
    public function getQuoteId ()
    {
        return $this->_quoteId;
    }

    /**
     * Sets the quoteId from the object
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
     * Get the quoteSettingId
     *
     * @return the $_quoteSettingId
     */
    public function getQuoteSettingId ()
    {
        return $this->_quoteSettingId;
    }

    /**
     * Sets a new quoteSettingId
     *
     * @param number $_quoteSettingId
     *            the new quoteSettingId
     */
    public function setQuoteSettingId ($_quoteSettingId)
    {
        $this->_quoteSettingId = $_quoteSettingId;
        return $this;
    }
}
