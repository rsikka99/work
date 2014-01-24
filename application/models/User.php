<?php

/**
 * Class Application_Model_User
 */
class Application_Model_User extends My_Model_Abstract
{
    /**
     * The id assigned by the database
     *
     * @var int
     */
    public $id;

    /**
     * The user's encrypted password
     *
     * @var string
     */
    public $password;

    /**
     * The user's first name
     *
     * @var string
     */
    public $firstname;

    /**
     * The user's last name
     *
     * @var string
     */
    public $lastname;

    /**
     * The user's email
     *
     * @var string
     */
    public $email;

    /**
     * The number of unsuccessful login attempts since the last successful one.
     *
     * @var integer
     */
    public $loginAttempts = 0;

    /**
     * The date that a user account is frozen until
     *
     * @var String
     */
    public $frozenUntil;

    /**
     * The flag that indicates if a user is locked out of the system
     *
     * @var int
     */
    public $locked = 0;

    /**
     * The date that a user accepted the eula
     *
     * @var String
     */
    public $eulaAccepted;

    /**
     * The flag that indicates that the user must reset their password on the next login
     *
     * @var int
     */
    public $resetPasswordOnNextLogin = 0;

    /**
     * The id that relates to the dealer id
     *
     * @var int
     */
    public $dealerId;

    /**
     * @var string
     */
    public $lastSeen;

    /**
     * The user settings row
     *
     * @var Preferences_Model_User_Setting
     */
    protected $_userSettings;

    /**
     * @var Admin_Model_Dealer
     */
    protected $_dealer;

    /**
     * @var Admin_Model_UserRole[]
     */
    protected $_userRoles;

    /**
     * Checks if the user is frozen
     *
     * @return bool
     */
    public function isFrozen ()
    {
        $frozenDate  = new DateTime($this->frozenUntil);
        $currentDate = new DateTime();
        $diff        = $currentDate->diff($frozenDate);

        return (!$diff->invert && ($diff->s > 0 || $diff->i > 0 || $diff->h > 0 || $diff->days > 0));
    }

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->id) && !is_null($params->id))
        {
            $this->id = $params->id;
        }
        if (isset($params->password) && !is_null($params->password))
        {
            $this->password = $params->password;
        }
        if (isset($params->firstname) && !is_null($params->firstname))
        {
            $this->firstname = $params->firstname;
        }
        if (isset($params->lastname) && !is_null($params->lastname))
        {
            $this->lastname = $params->lastname;
        }
        if (isset($params->email) && !is_null($params->email))
        {
            $this->email = $params->email;
        }
        if (isset($params->frozenUntil) && !is_null($params->frozenUntil))
        {
            $this->frozenUntil = $params->frozenUntil;
        }
        if (isset($params->loginAttempts) && !is_null($params->loginAttempts))
        {
            $this->loginAttempts = $params->loginAttempts;
        }
        if (isset($params->locked) && !is_null($params->locked))
        {
            $this->locked = $params->locked;
        }
        if (isset($params->eulaAccepted) && !is_null($params->eulaAccepted))
        {
            $this->eulaAccepted = $params->eulaAccepted;
        }
        if (isset($params->resetPasswordOnNextLogin) && !is_null($params->resetPasswordOnNextLogin))
        {
            $this->resetPasswordOnNextLogin = $params->resetPasswordOnNextLogin;
        }
        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }
        if (isset($params->lastSeen) && !is_null($params->lastSeen))
        {
            $this->lastSeen = $params->lastSeen;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            'id'                       => $this->id,
            'password'                 => $this->password,
            'firstname'                => $this->firstname,
            'lastname'                 => $this->lastname,
            'email'                    => $this->email,
            'frozenUntil'              => $this->frozenUntil,
            'loginAttempts'            => $this->loginAttempts,
            'resetPasswordOnNextLogin' => $this->resetPasswordOnNextLogin,
            'eulaAccepted'             => $this->eulaAccepted,
            'locked'                   => $this->locked,
            "dealerId"                 => $this->dealerId,
            "lastSeen"                 => $this->lastSeen
        );
    }

    /**
     * Gets the users privileges
     *
     * @return Admin_Model_UserRole[]
     */
    public function getUserRoles ()
    {
        if (!isset($this->_userRoles))
        {
            $this->_userRoles = Admin_Model_Mapper_UserRole::getInstance()->fetchAllRolesForUser($this->id);
        }

        return $this->_userRoles;
    }

    /**
     * Sets the users privileges
     *
     * @param $userRoles  Admin_Model_UserRole[]
     *                    The users roles
     *
     * @return Application_Model_User
     */
    public function setUserRoles ($userRoles)
    {
        $this->_userRoles = $userRoles;

        return $this;
    }

    /**
     * Encrypts a password using a salt.
     *
     * @param string $password
     *
     * @throws Exception
     * @return string
     */
    public static function cryptPassword ($password)
    {
        if (!defined("CRYPT_SHA512") || CRYPT_SHA512 != 1)
        {
            throw new Exception("Error, SHA512 encryption not available");
        }

        // What method to use (6 is SHA512)
        $method = '6';
        // How many rounds to do.
        $rounds = 'rounds=5000';
        // Random string to make it better
        $pepper = 'lunchisdabest';

        // Combine them all '$6$rounds=5000$randomstring$'
        $salt = sprintf('$%1$s$%2$s$%3$s$', $method, $rounds, $pepper);

        return crypt($password, $salt);
    }

    /**
     * Gets the users settings
     *
     * @return Preferences_Model_User_Setting
     */
    public function getUserSettings ()
    {
        if (!isset($this->_userSettings))
        {
            $this->_userSettings = Preferences_Model_Mapper_User_Setting::getInstance()->find($this->id);
            if (!$this->_userSettings instanceof Preferences_Model_User_Setting)
            {
                $this->_userSettings         = new Preferences_Model_User_Setting();
                $this->_userSettings->userId = $this->id;

                Preferences_Model_Mapper_User_Setting::getInstance()->insert($this->_userSettings);
            }
        }

        return $this->_userSettings;
    }

    /**
     * Gets the dealer object that the user belongs to.
     *
     * @return Admin_Model_Dealer
     */
    public function getDealer ()
    {
        if (!isset($this->_dealer))
        {
            $this->_dealer = Admin_Model_Mapper_Dealer::getInstance()->find($this->dealerId);
        }

        return $this->_dealer;
    }
}