<?php

/**
 * Class Dealermanagement_Service_User
 */
class Dealermanagement_Service_User extends Tangent_Service_Abstract
{
    const ERROR_USERNAME_EXISTS     = "UsernameExists";
    const ERROR_USEREMAIL_EXISTS    = "UserEmailExists";
    const ERROR_USER_DOES_NOT_EXIST = "UserDoesNotExist";
    const ERROR_FORM_INVALID        = "FormInvalid";

    /**
     * The form
     *
     * @var Dealermanagement_Form_User
     */
    protected $_form;

    /**
     * @var Admin_Model_Role[]
     */
    protected $_roles;

    /**
     * @var int
     */
    protected $_dealerId;

    /**
     * @param Admin_Model_Role[] $roles
     * @param                    $dealerId
     * @param bool               $createMode
     */
    public function __construct ($roles, $dealerId, $createMode = false)
    {
        $this->_roles    = $roles;
        $this->_dealerId = $dealerId;
        $this->_form     = new Dealermanagement_Form_User($roles, $createMode);
    }

    /**
     * @param Application_Model_User $user
     *
     * @return Dealermanagement_Form_User
     */
    public function getForm ($user = null)
    {
        if ($user instanceof Application_Model_User)
        {
            $this->_populateForm($user);
        }

        return $this->_form;
    }

    /**
     * Populates the form with user data
     *
     * @param Application_Model_User $user
     */
    protected function _populateForm (Application_Model_User $user)
    {
        $populateData = $user->toArray();
        $userRoles    = array();
        foreach ($user->getUserRoles() as $userRole)
        {
            $userRoles[] = $userRole->roleId;
        }

        $populateData['userRoles'] = $userRoles;
        $this->_form->populate($populateData);

    }

    /**
     * Handles creation
     *
     * @param array $data The post data
     *
     * @return bool
     */
    public function create ($data)
    {
        $success      = false;
        $filteredData = $this->validateAndFilterData($data);
        if ($filteredData !== false)
        {
            $usersByEmail = Application_Model_Mapper_User::getInstance()->fetchUserByEmail($filteredData['email']);
            if ($usersByEmail === false)
            {
                $user = new Application_Model_User();
                $user->populate($filteredData);
                $user->dealerId = $this->_dealerId;

                // Make sure the users password is encrypted.
                $user->password = $user->cryptPassword($user->password);

                $userId = Application_Model_Mapper_User::getInstance()->insert($user);
                if ($userId > 0)
                {
                    $userRoleMapper   = Admin_Model_Mapper_UserRole::getInstance();
                    $userRole         = new Admin_Model_UserRole();
                    $userRole->userId = $userId;

                    if (isset($filteredData['userRoles']) && is_array($filteredData['userRoles']))
                    {
                        foreach ($filteredData['userRoles'] as $roleId)
                        {
                            $userRole->roleId = (int)$roleId;
                            $userRoleMapper->insert($userRole);
                        }
                    }

                    $success = true;

                    Dealermanagement_Service_User::sendNewUserEmail($user, $filteredData['password']);
                }
            }
            else
            {
                if (count($usersByEmail->toArray()) > 0)
                {
                    $this->addError(self::ERROR_USEREMAIL_EXISTS, "A user with this email already exists");
                    $this->_form->getElement('email')->addError("Email already exists");
                }

            }
        }

        return $success;
    }

    /**
     * Handles updates
     *
     * @param $data
     * @param $id
     *
     * @return bool
     */
    public function update ($data, $id)
    {
        $success         = false;
        $userMapper      = Application_Model_Mapper_User::getInstance();
        $userRoleMapper  = Admin_Model_Mapper_UserRole::getInstance();
        $roleMapper      = Admin_Model_Mapper_Role::getInstance();
        $user            = $userMapper->find($id);
        $passwordChanged = false;

        if ($user)
        {
            $filteredData = $this->validateAndFilterData($data);

            if ($filteredData !== false)
            {
                $user->firstname                = $filteredData['firstname'];
                $user->lastname                 = $filteredData['lastname'];
                $user->email                    = $filteredData['email'];
                $user->frozenUntil              = $filteredData['frozenUntil'];
                $user->locked                   = $filteredData['locked'];
                $user->resetPasswordOnNextLogin = $filteredData['resetPasswordOnNextLogin'];

                if ($filteredData['resetLoginAttempts'])
                {
                    $user->loginAttempts = 0;
                }

                if ($filteredData['reset_password'])
                {
                    // Make sure the users password is encrypted.
                    $user->password  = $user->cryptPassword($filteredData['password']);
                    $passwordChanged = true;
                }

                if (!isset($filteredData['userRoles']))
                {
                    $filteredData['userRoles'] = array();
                }

                $userMapper->save($user);

                $userRoles = $user->getUserRoles();


                $userRole         = new Admin_Model_UserRole();
                $userRole->userId = $user->id;

                /**
                 * Delete Roles
                 */
                foreach ($userRoles as $userRole)
                {

                    if (!in_array($userRole->roleId, $filteredData['userRoles']))
                    {
                        $role = $roleMapper->find($userRole->roleId);
                        if (!$role->systemRole)
                        {
                            $userRoleMapper->delete($userRole);
                        }
                    }
                }

                /**
                 * Insert Roles
                 */
                $newUserRole         = new Admin_Model_UserRole();
                $newUserRole->userId = $user->id;
                foreach ($this->_roles as $role)
                {
                    if (in_array($role->id, $filteredData['userRoles']))
                    {
                        $roleExists = false;
                        foreach ($userRoles as $userRole)
                        {
                            if ($role->id == $userRole->roleId)
                            {
                                $roleExists = true;
                                break;
                            }
                        }

                        if (!$roleExists)
                        {
                            $newUserRole->roleId = $role->id;
                            $userRoleMapper->insert($newUserRole);
                        }

                    }
                }

                $success = true;

                if ($passwordChanged)
                {
                    Dealermanagement_Service_User::sendPasswordChangedEmail($user, $filteredData['password']);
                }
            }
        }
        else
        {
            $this->addError(self::ERROR_USER_DOES_NOT_EXIST, "A user with this name was not found");
        }

        return $success;
    }

    /**
     * Handles deletion
     *
     * @param int $id The id to delete
     *
     * @return int The number of rows deleted.
     */
    public function delete ($id)
    {
        $success = false;
        if (Application_Model_Mapper_User::getInstance()->delete($id) > 0)
        {
            $success = true;
        }
        else
        {
            $this->addError(self::ERROR_USER_DOES_NOT_EXIST, "A user with this name was not found");
        }

        return $success;
    }

    /**
     * Validates the data with the form
     *
     * @param array $formData
     *            The array of data to validate
     *
     * @return array The array of valid and filtered data, or false on error.
     */
    protected function validateAndFilterData ($formData)
    {
        if ($this->_form->isValid($formData))
        {
            return $this->_form->getValues();
        }
        else
        {
            $this->addError(self::ERROR_FORM_INVALID, "The form has errors");
        }

        return false;
    }

    protected static function _initMailTransport ()
    {
        $config = Zend_Registry::get('config');

        $emailConfig = array(
            'auth'     => 'login',
            'username' => $config->email->username,
            'password' => $config->email->password,
            'ssl'      => $config->email->ssl,
            'port'     => $config->email->port,
            'host'     => $config->email->host
        );

        Zend_Mail::setDefaultTransport(new Zend_Mail_Transport_Smtp($emailConfig['host'], $emailConfig));
        Zend_Mail::setDefaultFrom($emailConfig['username'], $config->app->name);
    }

    /**
     * Gets the application url
     *
     * @return string
     */
    protected static function _getAppUrl ()
    {
        $urlHelper     = new Zend_View_Helper_ServerUrl();
        $baseUrlHelper = new Zend_View_Helper_BaseUrl();

        return $urlHelper->serverUrl($baseUrlHelper->baseUrl("/"));
    }

    /**
     * Sends an email to a new user
     *
     * @param Application_Model_User $user
     * @param string                 $plainTextPassword
     */
    public static function sendNewUserEmail ($user, $plainTextPassword)
    {
        // FIXME: Should this be done on bootstrap?
        self::_initMailTransport();

        $config  = Zend_Registry::get('config');
        $appName = $config->app->title;
        $appUrl  = self::_getAppUrl();

        $mail = new Zend_Mail();
        $mail->addTo($user->email, $user->firstname . ' ' . $user->lastname);
        $mail->setSubject("{$appName} - Your new account");

        /**
         * Plain Text Version
         */
        $textBody = "{$appName} - New Account\n";
        $textBody .= "Your {$appName} credentials:\n";
        $textBody .= str_pad("", strlen("Your {$appName} credentials:"), '-');
        $textBody .= "Username: {$user->email}\n";
        $textBody .= "Password: {$plainTextPassword}\n";
        $textBody .= "Application URL: {$appUrl}\n";

        if ($user->resetPasswordOnNextLogin)
        {
            $textBody .= "* You will be required to change your password on your first login.\n";
        }

        $mail->setBodyText($textBody);

        /**
         * HTML Version
         */
        $htmlBody = "<body>\n";
        $htmlBody .= "<h2>{$appName} - New Account</h2>\n";
        $htmlBody .= "<p>Your {$appName} credentials:</p>\n";
        $htmlBody .= "<p>Username: {$user->email}</p>\n";
        $htmlBody .= "<p>Password: {$plainTextPassword}</p>\n";
        $htmlBody .= "<p>Application URL: {$appUrl}</p>\n";

        if ($user->resetPasswordOnNextLogin)
        {
            $htmlBody .= "<p><small><strong>*</strong> You will be required to change your password on your first login.</small></p>\n";
        }

        $htmlBody .= "</body>";
        $mail->setBodyHtml($htmlBody);

        /**
         * Send it!
         */
        $mail->send();
    }

    /**
     * Send an email about password updates
     *
     * @param Application_Model_User $user
     * @param string                 $plainTextPassword
     */
    public static function sendPasswordChangedEmail ($user, $plainTextPassword)
    {
        // FIXME: Should this be done on bootstrap?
        self::_initMailTransport();

        $config  = Zend_Registry::get('config');
        $appName = $config->app->title;
        $appUrl  = self::_getAppUrl();

        $mail = new Zend_Mail();
        $mail->addTo($user->email, $user->firstname . ' ' . $user->lastname);
        $mail->setSubject("{$appName} - Your new account");

        /**
         * Plain Text Version
         */
        $textBody = "{$appName} - Your password has been reset\n";
        $textBody .= "Your {$appName} credentials:\n";
        $textBody .= str_pad("", strlen("Your {$appName} credentials:"), '-');
        $textBody .= "Username: {$user->email}\n";
        $textBody .= "Password: {$plainTextPassword}\n";
        $textBody .= "Application URL: {$appUrl}\n";

        if ($user->resetPasswordOnNextLogin)
        {
            $textBody .= "* You will be required to change your password on your next login.\n";
        }

        $mail->setBodyText($textBody);

        /**
         * HTML Version
         */
        $htmlBody = "<body>\n";
        $htmlBody .= "<h2>{$appName} - Your password has been reset</h2>\n";
        $htmlBody .= "<p>Your {$appName} credentials:</p>\n";
        $htmlBody .= "<p>Username: {$user->email}</p>\n";
        $htmlBody .= "<p>Password: {$plainTextPassword}</p>\n";
        $htmlBody .= "<p>Application URL: {$appUrl}</p>\n";

        if ($user->resetPasswordOnNextLogin)
        {
            $htmlBody .= "<p><small><strong>*</strong> You will be required to change your password on your first login.</small></p>\n";
        }

        $htmlBody .= "</body>";
        $mail->setBodyHtml($htmlBody);

        /**
         * Send it!
         */
        $mail->send();
    }
}