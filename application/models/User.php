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
    public $id;

    /**
     * The user's username
     *
     * @var string
     */
    public $username;

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
     * This is a combined array of report and survey settings
     *
     * @var array
     */
    protected $_reportSettings;


    /**
     * @var Admin_Model_UserRole[]
     */
    protected $_userRoles;

    public function isFrozen ()
    {
        $frozenDate  = new DateTime($this->frozenUntil);
        $currentDate = new DateTime();
        $diff        = $currentDate->diff($frozenDate);

        return (!$diff->invert && ($diff->s > 0 || $diff->i > 0 || $diff->h > 0 || $diff->days > 0));
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
        if (isset($params->id) && !is_null($params->id))
        {
            $this->id = $params->id;
        }
        if (isset($params->username) && !is_null($params->username))
        {
            $this->username = $params->username;
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
    }

    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array(
            'id'                       => $this->id,
            'username'                 => $this->username,
            'password'                 => $this->password,
            'firstname'                => $this->firstname,
            'lastname'                 => $this->lastname,
            'email'                    => $this->email,
            'frozenUntil'              => $this->frozenUntil,
            'loginAttempts'            => $this->loginAttempts,
            'resetPasswordOnNextLogin' => $this->resetPasswordOnNextLogin,
            'eulaAccepted'             => $this->eulaAccepted,
            'locked'                   => $this->locked,
            "dealerId"                 => $this->dealerId
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
     * Gets the report and survey settings for the user
     *
     * @param $userId int
     *
     * @return array
     */
    public function getReportSettings ($userId)
    {
        if (!isset($this->_reportSettings))
        {
            $userReportSetting                        = Proposalgen_Model_Mapper_Report_Setting::getInstance()->fetchUserReportSetting($userId);
            $userSurveySetting                        = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->fetchUserSurveySetting($userId);
            $this->_reportSettings                    = array_merge($userReportSetting->toArray(), $userSurveySetting->toArray());
            $this->_reportSettings['reportSettingId'] = $userReportSetting->id;
            $this->_reportSettings['surveySettingId'] = $userSurveySetting->id;
            unset($this->_reportSettings['id']);
        }

        return $this->_reportSettings;
    }


}
