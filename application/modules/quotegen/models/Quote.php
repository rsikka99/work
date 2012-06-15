<?php

/**
 * Quotegen_Model_Quote
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_Quote extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The client id that the quote was made for
     *
     * @var number
     */
    protected $_clientId;
    
    /**
     * The date the quote was created
     *
     * @var string
     */
    protected $_dateCreated;
    
    /**
     * The date the quote was last modified
     *
     * @var string
     */
    protected $_dateModified;
    
    /**
     * The date the quote was made for
     *
     * @var string
     */
    protected $_quoteDate;
    
    /**
     * The date the quote was created
     *
     * @var string
     */
    protected $_isLeased;
    
    /**
     * The user who created the quote/owns the quote?
     *
     * @var number
     */
    protected $_userId;
    
    /**
     * The name that will be shown on the report
     *
     * @var string
     */
    protected $_clientDisplayName;
    
    /**
     * The client associated with the quote
     *
     * @var Quotegen_Model_Client
     */
    protected $_client;
    
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
        if (isset($params->clientId) && ! is_null($params->clientId))
            $this->setClientId($params->clientId);
        if (isset($params->dateCreated) && ! is_null($params->dateCreated))
            $this->setDateCreated($params->dateCreated);
        if (isset($params->dateModified) && ! is_null($params->dateModified))
            $this->setDateModified($params->dateModified);
        if (isset($params->userId) && ! is_null($params->userId))
            $this->setUserId($params->userId);
        if (isset($params->clientDisplayName) && ! is_null($params->clientDisplayName))
            $this->setClientDisplayName($params->clientDisplayName);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId() 
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
     * Gets the client id of the quote
     *
     * @return number
     */
    public function getClientId ()
    {
        return $this->_clientId;
    }

    /**
     * Sets the client id of the quote
     *
     * @param number $_clientId
     *            The new client id
     */
    public function setClientId ($_clientId)
    {
        $this->_clientId = $_clientId;
        return $this;
    }

    /**
     * Gets the date the quote was created
     *
     * @return string The date created in MySQL format.
     */
    public function getDateCreated ()
    {
        return $this->_dateCreated;
    }

    /**
     * Sets the date the quote was created
     *
     * @param string $_dateCreated
     *            The date modified in MySQL format
     */
    public function setDateCreated ($_dateCreated)
    {
        $this->_dateCreated = $_dateCreated;
        return $this;
    }

    /**
     * Gets the date that the quote was last modified on
     *
     * @return string The date modified in MySQL format
     */
    public function getDateModified ()
    {
        return $this->_dateModified;
    }

    /**
     * Sets the date that the quote was last modified on
     *
     * @param string $_dateModified
     *            The date modified in MySQL format
     */
    public function setDateModified ($_dateModified)
    {
        $this->_dateModified = $_dateModified;
        return $this;
    }

    /**
     * Gets the date the quote was made for
     *
     * @return string The date in MySQL format
     */
    public function getQuoteDate ()
    {
        return $this->_quoteDate;
    }

    /**
     * Sets the date the quote was made for
     *
     * @param string $_quoteDate
     *            The date in MySQL format
     */
    public function setQuoteDate ($_quoteDate)
    {
        $this->_quoteDate = $_quoteDate;
        return $this;
    }

    /**
     * Gets the leased flag of the quote
     *
     * @return string True if the quote is a leased quote
     */
    public function getIsLeased ()
    {
        return $this->_isLeased;
    }

    /**
     * Sets the leased flag of the quote
     *
     * @param string $_isLeased            
     */
    public function setIsLeased ($_isLeased)
    {
        $this->_isLeased = $_isLeased;
        return $this;
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
     * Sets the user id
     *
     * @param number $_userId
     *            The user id
     */
    public function setUserId ($_userId)
    {
        $this->_userId = $_userId;
        return $this;
    }

    /**
     * Gets the client display name
     *
     * @return string The client name
     */
    public function getClientDisplayName ()
    {
        return $this->_clientDisplayName;
    }

    /**
     * Sets the client's display name
     *
     * @param string $_clientDisplayName            
     */
    public function setClientDisplayName ($_clientDisplayName)
    {
        $this->_clientDisplayName = $_clientDisplayName;
        return $this;
    }

    /**
     * Gets the client for the report
     *
     * @return Quotegen_Model_Client
     */
    public function getClient ()
    {
        if (! isset($this->_client) && isset($this->_clientId))
        {
            $this->_client = Quotegen_Model_Mapper_Client::getInstance()->find($this->getClientId());
        }
        return $this->_client;
    }

    /**
     * Sets the client for the report (Also sets the client id of the report if the client has one.
     *
     * @param Quotegen_Model_Client $_client
     *            The new client
     */
    public function setClient (Quotegen_Model_Client $_client)
    {
        $this->_client = $_client;
        if ($_client->getId() !== null)
        {
            $this->setClientId($_client->getId());
        }
        return $this;
    }
}
