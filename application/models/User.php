<?php

/**
 * Application_Model_User is a model that represents a user row in the database.
 *
 * @author Lee Robert
 *        
 */
class Application_Model_User extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The user's username
     *
     * @var string
     */
    protected $_username = "";
    
    /**
     * The user's encrypted password
     *
     * @var string
     */
    protected $_password = "";
    
    /**
     * The user's first name
     *
     * @var string
     */
    protected $_firstname = "";
    
    /**
     * The user's last name
     *
     * @var string
     */
    protected $_lastname = "";
    
    /**
     * The user's email
     *
     * @var string
     */
    protected $_email = "";
    
    /**
     * The number of unsuccessful login attempts since the last successful one.
     *
     * @var integer
     */
    protected $_loginAttempts = 0;
    
    /**
     * The date that a user account is frozen until
     *
     * @var String
     */
    protected $_frozenUntil = null;
    
    /**
     * The flag that indicates if a user is locked out of the system
     *
     * @var int
     */
    protected $_locked = 0;

    public function __construct ($options = null)
    {
        // Set the filters
        $alnum = new Zend_Filter_Alnum(true);
        $this->_filters = array (
                'id' => array (), 
                
                'username' => array (
                        'StringTrim', 
                        'StripTags', 
                        $alnum 
                ), 
                'password' => array (), 
                'firstname' => array (
                        'StringTrim', 
                        'StripTags', 
                        $alnum 
                ), 
                'lastname' => array (
                        'StringTrim', 
                        'StripTags', 
                        $alnum 
                ), 
                'email' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'frozenUntil' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'loginAttempts' => array (
                        'Int' 
                ), 
                'locked' => array (
                        new Zend_Filter_Boolean(Zend_Filter_Boolean::ALL) 
                ) 
        );
        
        // Set the validators
        $this->_validators = array (
                'id' => array (
                        'Int' 
                ), 
                'username' => array (
                        new Zend_Validate_StringLength(array (
                                'min' => 4, 
                                'max' => 30 
                        )) 
                ), 
                'password' => array (
                        new Zend_Validate_StringLength(array (
                                'min' => 4, 
                                'max' => 255 
                        )) 
                ), 
                'firstname' => array (
                        new Zend_Validate_StringLength(array (
                                'min' => 2, 
                                'max' => 30 
                        )) 
                ), 
                'lastname' => array (
                        new Zend_Validate_StringLength(array (
                                'min' => 2, 
                                'max' => 30 
                        )) 
                ), 
                'email' => array (
                        new Zend_Validate_StringLength(array (
                                'min' => 4, 
                                'max' => 200 
                        )), 
                        new Zend_Validate_EmailAddress(array (
                                'allow' => Zend_Validate_Hostname::ALLOW_DNS 
                        )) 
                ), 
                'frozenUntil' => array (
                        new My_Validate_DateTime() 
                ), 
                'loginAttempts' => array (
                        'Int', 
                        new Zend_Validate_Between(array (
                                'min' => 0, 
                                'max' => 500 
                        )) 
                ), 
                'locked' => array (
                        Zend_Filter_Input::ALLOW_EMPTY => true 
                ) 
        );
        parent::__construct($options);
    }

    public function isFrozen ()
    {
        $frozenDate = new DateTime($this->_frozenUntil);
        $currentDate = new DateTime();
        $diff = $currentDate->diff($frozenDate);
        
        return (! $diff->invert && ($diff->s > 0 || $diff->i > 0 || $diff->h > 0 || $diff->days > 0));
    }
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->id))
            $this->setId($params->id);
        if (isset($params->username))
            $this->setUsername($params->username);
        
        if (isset($params->password))
            $this->setPassword($params->password);
        
        if (isset($params->firstname))
            $this->setFirstname($params->firstname);
        
        if (isset($params->lastname))
            $this->setLastname($params->lastname);
        
        if (isset($params->email))
            $this->setEmail($params->email);
        
        if (isset($params->frozenUntil))
            $this->setFrozenUntil($params->frozenUntil);
        
        if (isset($params->loginAttempts))
            $this->setLoginAttempts($params->loginAttempts);
        
        if (isset($params->locked))
            $this->setLocked($params->locked);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'username' => $this->getUsername(), 
                'password' => $this->getPassword(), 
                'firstname' => $this->getFirstname(), 
                'lastname' => $this->getLastname(), 
                'email' => $this->getEmail(), 
                'frozenUntil' => $this->getFrozenUntil(), 
                'loginAttempts' => $this->getLoginAttempts(), 
                'locked' => $this->getLocked() 
        );
    }

    /**
     *
     * @return the $_id
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     *
     * @param number $_id            
     */
    public function setId ($_id)
    {
        $input = new Zend_Filter_Input($this->_filters, $this->_validators, array (
                'id' => $_id 
        ));
        
        if (! $input->isValid('id'))
        {
            throw new Exception('Invalid id provided');
        }
        
        $this->_id = $input->id;
        return $this;
    }

    /**
     *
     * @return the $_username
     */
    public function getUsername ()
    {
        return $this->_username;
    }

    /**
     *
     * @param string $_username            
     */
    public function setUsername ($_username)
    {
        $input = new Zend_Filter_Input($this->_filters, $this->_validators, array (
                'username' => $_username 
        ));
        
        if (! $input->isValid('username'))
        {
            throw new Exception('Invalid username provided');
        }
        
        $this->_username = $input->username;
        return $this;
    }

    /**
     *
     * @return the $_password
     */
    public function getPassword ()
    {
        return $this->_password;
    }

    /**
     *
     * @param string $_password            
     */
    public function setPassword ($_password)
    {
        $input = new Zend_Filter_Input($this->_filters, $this->_validators, array (
                'password' => $_password 
        ));
        
        if (! $input->isValid('password'))
        {
            throw new Exception('Invalid password provided');
        }
        
        $this->_password = $input->password;
        return $this;
    }

    /**
     *
     * @return the $_firstname
     */
    public function getFirstname ()
    {
        return $this->_firstname;
    }

    /**
     *
     * @param string $_firstname            
     */
    public function setFirstname ($_firstname)
    {
        $input = new Zend_Filter_Input($this->_filters, $this->_validators, array (
                'firstname' => $_firstname 
        ));
        
        if (! $input->isValid('firstname'))
        {
            throw new Exception('Invalid firstname provided');
        }
        
        $this->_firstname = $input->firstname;
        return $this;
    }

    /**
     *
     * @return the $_lastname
     */
    public function getLastname ()
    {
        return $this->_lastname;
    }

    /**
     *
     * @param string $_lastname            
     */
    public function setLastname ($_lastname)
    {
        $input = new Zend_Filter_Input($this->_filters, $this->_validators, array (
                'lastname' => $_lastname 
        ));
        
        if (! $input->isValid('lastname'))
        {
            throw new Exception('Invalid lastname provided');
        }
        
        $this->_lastname = $input->lastname;
        return $this;
    }

    /**
     *
     * @return the $_email
     */
    public function getEmail ()
    {
        return $this->_email;
    }

    /**
     *
     * @param string $_email            
     */
    public function setEmail ($_email)
    {
        $input = new Zend_Filter_Input($this->_filters, $this->_validators, array (
                'email' => $_email 
        ));
        
        if (! $input->isValid('email'))
        {
            throw new Exception('Invalid email provided');
        }
        
        $this->_email = $input->email;
        return $this;
    }

    /**
     *
     * @return the $_loginAttempts
     */
    public function getLoginAttempts ()
    {
        return $this->_loginAttempts;
    }

    /**
     *
     * @param number $_loginAttempts            
     */
    public function setLoginAttempts ($_loginAttempts)
    {
        $input = new Zend_Filter_Input($this->_filters, $this->_validators, array (
                'loginAttempts' => $_loginAttempts 
        ));
        
        if (! $input->isValid('loginAttempts'))
        {
            throw new Exception('Invalid login attempts provided');
        }
        
        $this->_loginAttempts = (int)$input->loginAttempts;
        return $this;
    }

    /**
     *
     * @return the $_frozenUntil
     */
    public function getFrozenUntil ()
    {
        return $this->_frozenUntil;
    }

    /**
     *
     * @param String $_frozenUntil            
     */
    public function setFrozenUntil ($_frozenUntil)
    {
        $input = new Zend_Filter_Input($this->_filters, $this->_validators, array (
                'frozenUntil' => $_frozenUntil 
        ));
        
        if ($_frozenUntil !== null && ! $input->isValid('frozenUntil'))
        {
            throw new Exception('Invalid date for frozen until provided.');
        }
        
        $this->_frozenUntil = $input->frozenUntil;
        return $this;
    }

    /**
     *
     * @return the $_locked
     */
    public function getLocked ()
    {
        return $this->_locked;
    }

    /**
     *
     * @param boolean $_locked            
     */
    public function setLocked ($_locked)
    {
        
        $input = new Zend_Filter_Input($this->_filters, $this->_validators, array (
                'locked' => $_locked 
        ));
        
        if (! $input->isValid('locked'))
        {
            $message = $input->getMessages();
            throw new Exception('Invalid value for locked provided.');
        }
        
        $this->_locked = (int)$input->locked;
        return $this;
    }

}
