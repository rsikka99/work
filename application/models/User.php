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
    protected $_username;
    
    /**
     * The user's encrypted password
     *
     * @var string
     */
    protected $_password;
    
    /**
     * The user's first name
     *
     * @var string
     */
    protected $_firstname;
    
    /**
     * The user's last name
     *
     * @var string
     */
    protected $_lastname;
    
    /**
     * The user's email
     *
     * @var string
     */
    protected $_email;
    
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
    protected $_frozenUntil;
    
    /**
     * The flag that indicates if a user is locked out of the system
     *
     * @var int
     */
    protected $_locked = 0;
    
    /**
     * The date that a user accepted the eula
     *
     * @var String
     */
    protected $_eulaAccepted;
    
    /**
     * The flag that indicates that the user must reset their password on the next login
     *
     * @var int
     */
    protected $_resetPasswordOnNextLogin = 0;

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
        if (isset($params->id) && ! is_null($params->id))
            $this->setId($params->id);
        
        if (isset($params->username) && ! is_null($params->username))
            $this->setUsername($params->username);
        
        if (isset($params->password) && ! is_null($params->password))
            $this->setPassword($params->password);
        
        if (isset($params->firstname) && ! is_null($params->firstname))
            $this->setFirstname($params->firstname);
        
        if (isset($params->lastname) && ! is_null($params->lastname))
            $this->setLastname($params->lastname);
        
        if (isset($params->email) && ! is_null($params->email))
            $this->setEmail($params->email);
        
        if (isset($params->frozenUntil) && ! is_null($params->frozenUntil))
            $this->setFrozenUntil($params->frozenUntil);
        
        if (isset($params->loginAttempts) && ! is_null($params->loginAttempts))
            $this->setLoginAttempts($params->loginAttempts);
        
        if (isset($params->locked) && ! is_null($params->locked))
            $this->setLocked($params->locked);
        
        if (isset($params->eulaAccepted) && ! is_null($params->eulaAccepted))
            $this->setEulaAccepted($params->eulaAccepted);
        
        if (isset($params->resetPasswordOnNextLogin) && ! is_null($params->resetPasswordOnNextLogin))
            $this->setResetPasswordOnNextLogin($params->resetPasswordOnNextLogin);
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
                'resetPasswordOnNextLogin' => $this->getResetPasswordOnNextLogin(),
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
        $this->_id = $_id;
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
        $this->_username = $_username;
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
        $this->_password = $_password;
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
        $this->_firstname = $_firstname;
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
        $this->_lastname = $_lastname;
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
        $this->_email = $_email;
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
        $this->_loginAttempts = $_loginAttempts;
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
     * @param string $_frozenUntil            
     */
    public function setFrozenUntil ($_frozenUntil)
    {
        $this->_frozenUntil = $_frozenUntil;
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
     * @param number $_locked            
     */
    public function setLocked ($_locked)
    {
        $this->_locked = $_locked;
    }

    /**
     *
     * @return the $_eulaAccepted
     */
    public function getEulaAccepted ()
    {
        return $this->_eulaAccepted;
    }

    /**
     *
     * @param string $_eulaAccepted            
     */
    public function setEulaAccepted ($_eulaAccepted)
    {
        $this->_eulaAccepted = $_eulaAccepted;
    }

    /**
     *
     * @return the $_resetPasswordOnNextLogin
     */
    public function getResetPasswordOnNextLogin ()
    {
        return $this->_resetPasswordOnNextLogin;
    }

    /**
     *
     * @param number $_resetPasswordOnNextLogin            
     */
    public function setResetPasswordOnNextLogin ($_resetPasswordOnNextLogin)
    {
        $this->_resetPasswordOnNextLogin = $_resetPasswordOnNextLogin;
    }
}
