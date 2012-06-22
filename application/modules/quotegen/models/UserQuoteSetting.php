<?php

/**
 * Quotegen_Model_UserQuoteSetting
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_UserQuoteSetting extends My_Model_Abstract
{
    
    /**
     * The user id
     *
     * @var int
     */
    protected $_userId = 0;
    
    /**
     * The quote setting id
     *
     * @var int
     */
    protected $_quoteSettingId = 0;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->userId) && ! is_null($params->userId))
            $this->setUserId($params->userId);
        
        if (isset($params->quoteSettingId) && ! is_null($params->quoteSettingId))
            $this->setQuoteSettingId($params->quoteSettingId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'userId' => $this->getUserId(), 
                'quoteSettingId' => $this->getQuoteSettingId() 
        );
    }

    /**
     * Gets the user id
     *
     * @return number The user id
     */
    public function getUserId ()
    {
        return $this->_userId;
    }

    /**
     * Sets the user id of the object
     *
     * @param number $_userId
     *            the new user id
     */
    public function setUserId ($_userId)
    {
        $this->_userId = $_userId;
    }

    /**
     * Gets the quote setting id
     *
     * @return number The quote setting id
     */
    public function getQuoteSettingId ()
    {
        return $this->_quoteSettingId;
    }

    /**
     * Sets the quote setting id
     *
     * @param number $_quoteSettingId
     *            The new quote setting id
     */
    public function setQuoteSettingId ($_quoteSettingId)
    {
        $this->_quoteSettingId = $_quoteSettingId;
        return $this;
    }
}
