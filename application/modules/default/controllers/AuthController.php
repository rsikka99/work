<?php
use MPSToolbox\Legacy\Mappers\EventLogMapper;
use MPSToolbox\Legacy\Mappers\UserEventLogMapper;
use MPSToolbox\Legacy\Mappers\UserPasswordResetRequestMapper;
use MPSToolbox\Legacy\Mappers\UserSessionMapper;
use MPSToolbox\Legacy\Mappers\UserMapper;
use MPSToolbox\Legacy\Models\UserPasswordResetRequestModel;
use MPSToolbox\Legacy\Models\UserSessionModel;
use MPSToolbox\Legacy\Models\EventLogTypeModel;
use MPSToolbox\Legacy\Models\UserModel;
use MPSToolbox\Legacy\Modules\DDefault\Forms\ChangePasswordForm;
use MPSToolbox\Legacy\Modules\DDefault\Forms\ForgotPasswordForm;
use MPSToolbox\Legacy\Modules\DDefault\Forms\LoginForm;
use MPSToolbox\Legacy\Modules\DDefault\Forms\ResetPasswordForm;
use Tangent\Controller\Action;

/**
 * Class Default_AuthController
 */
class Default_AuthController extends Action
{

    /**
     * The index action of the auth controller does nothing important.
     * We are just going to redirect the user over the login page instead.
     */
    function indexAction ()
    {
        $this->sendJsonError('Action not implemented.');
    }

    /**
     * The login action authenticates a user with our system.
     * After a successful authentication we should send them back to the page
     * that they were trying to access.
     */
    public function loginAction ()
    {
        $this->_pageTitle = ['Login'];

        $this->view->layout()->setLayout('auth');
        $request = $this->getRequest();
        $form    = new LoginForm();

        #Statsd::increment('mpstoolbox.login.attempts');

        if ($this->getRequest()->isPost())
        {
            if ($request->getParam('forgotPassword', false))
            {
                $this->redirectToRoute('auth.login.forgotPassword', ['email' => $this->getParam('email')]);
            }
            if ($form->isValid($request->getParams()))
            {
                $auth        = Zend_Auth::getInstance();

                $authAdapter = new My_Auth_Adapter();
                $authAdapter->setTableName('users');
                $authAdapter->setIdentityColumn('email');
                $authAdapter->setCredentialColumn('password');
                $authAdapter->setIdentity($form->getValue("email"));
                $authAdapter->setCredential($form->getValue("password"));

                // Authenticate against the database
                $result = $auth->authenticate($authAdapter);

                // If the value is valid, store the information
                if ($result->isValid())
                {
                    #Statsd::increment('mpstoolbox.login.successes');

                    // Get all the user information and only omit the password
                    // since we don't want to store it in the session.
                    $userInfo    = $authAdapter->getResultRowObject(null, 'password');
                    $userInfo->dealer = \MPSToolbox\Legacy\Mappers\DealerMapper::getInstance()->find($userInfo->dealerId);
                    $userInfo->currency = $userInfo->dealer->currency;
                    $authStorage = $auth->getStorage();
                    $authStorage->write($userInfo);

                    try
                    {
                        $currentSessionId = @session_id();
                        // Get all the sessions attached to current user signed in
                        $userSessionMapper = UserSessionMapper::getInstance();
                        $config            = Zend_Registry::get('config');

                        $userSessions = $userSessionMapper->fetchSessionsByUserId($userInfo->id);
                        foreach ($userSessions as $userSession)
                        {
                            if ($userSession->sessionId != $currentSessionId)
                            {
                                // If we are saving the session to the database or to the file system,
                                if (file_exists($config->resources->session->save_path . '/sess_' . $userSession->sessionId))
                                {
                                    @unlink($config->resources->session->save_path . '/sess_' . $userSession->sessionId);
                                }
                                $userSessionMapper->delete($userSession);
                            }
                        }

                        $userSession = $userSessionMapper->find($currentSessionId);
                        // Do we have a session id that matches our current session
                        if ($userSession && $userSession->sessionId == $currentSessionId)
                        {
                            // If it's a new userId with this current session, update the userId for this sessionId
                            if ($userInfo->id != $userSession->userId)
                            {
                                $userSessionMapper->save($userSession);
                            }
                        }
                        else
                        {
                            // Create a new sessions for the new user
                            $userSession            = new UserSessionModel();
                            $userSession->userId    = $userInfo->id;
                            $userSession->sessionId = $currentSessionId;
                            $userSessionMapper->insert($userSession);
                        }

                        UserEventLogMapper::getInstance()->createUserEventLog($userInfo->id, EventLogTypeModel::LOGIN);
                    }
                    catch (Exception $e)
                    {
                        throw new Exception('Passing up the chain', 0, $e);
                    }

                    /*
                     * Redirect them to the home page now that they are logged in.
                     */
                    $this->redirectToRoute('app.dashboard');
                }
                else
                {
                    #Statsd::increment('mpstoolbox.login.failures');
                    EventLogMapper::getInstance()->createEventLog(EventLogTypeModel::LOGIN_FAIL, "Email: " . $request->getParam('email', ''));

                    switch ($result->getCode())
                    {
                        case Zend_Auth_Result::FAILURE_UNCATEGORIZED :
                            // I'm using this in the custom adapter, so messages will be user friendly coming out of it.
                            foreach ($result->getMessages() as $message)
                            {
                                $this->_flashMessenger->addMessage(['danger' => $message]);
                            }
                            break;
                        default :
                            // Put a generic invalid credential message
                            $this->_flashMessenger->addMessage(['danger' => 'The email/password combination you entered was invalid.']);

                            break;
                    }
                }
            }
            else
            {
                EventLogMapper::getInstance()->createEventLog(EventLogTypeModel::LOGIN_FAIL, "Email: " . $request->getParam('email', ''));
            }
        }

        $this->view->form = $form;
    } // end loginAction


    /**
     * This action handles logging a user out of the system and destroying any
     * sensitive session information.
     * We should persist all other session data as it can help keep a user
     * friendly experience
     */
    public function logoutAction ()
    {
        $this->_pageTitle = 'Logout';

        $this->doLogout();

        $this->redirectToRoute('auth.login');
    }

    /**
     * This function handles logging a user out of the system and destroying any
     * sensitive session information.
     * We should persist all other session data as it can help keep a user
     * friendly experience
     */
    private function doLogout ()
    {
        if ($this->isLoggedIn())
        {
            $userId = $this->getIdentity()->id;


            // Destroy only information that is part of a user being logged in.
            Zend_Auth::getInstance()->clearIdentity();

            Zend_Session::namespaceUnset('mps-tools');

            if ($userId)
            {
                try
                {
                    UserEventLogMapper::getInstance()->createUserEventLog($userId, EventLogTypeModel::LOGOUT);
                }
                catch (Exception $e)
                {
                    // Do Nothing
                }
            }
        }
    }

    /**
     * Handles when users forget their password
     */
    public function forgotPasswordAction ()
    {
        $this->_pageTitle = ['Forgot Password'];

        $this->view->layout()->setLayout('auth');

        $form                           = new ForgotPasswordForm();
        $this->view->forgotPasswordForm = $form;

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getParams();
            if (isset($postData['cancel']))
            {
                $this->redirectToRoute('auth.login');
            }
            else
            {
                if ($form->isValid($postData))
                {
                    $formData = $form->getValues();

                    // Find user by email
                    $user = UserMapper::getInstance()->fetchUserByEmail($formData['email']);

                    if ($user instanceof UserModel)
                    {
                        $db = Zend_Db_Table::getDefaultAdapter();

                        $db->beginTransaction();
                        $requestCreated = false;
                        try
                        {
                            UserPasswordResetRequestMapper::getInstance()->deleteByUserId($user->id);
                            $time                                = date("Y-m-d H:i:s");
                            $passwordResetRequest                = new UserPasswordResetRequestModel();
                            $passwordResetRequest->dateRequested = $time;
                            $passwordResetRequest->ipAddress     = $_SERVER ['REMOTE_ADDR'];
                            $passwordResetRequest->resetVerified = false;
                            $passwordResetRequest->userId        = $user->id;
                            $passwordResetRequest->resetUsed     = false;
                            // Create a unique hash for the reset token
                            // Random number + user id + unique id
                            $passwordResetRequest->resetToken = uniqid($this->getRandom(0, 12800) . $user->id, true);

                            UserPasswordResetRequestMapper::getInstance()->insert($passwordResetRequest);
                            $db->commit();
                            $requestCreated = true;
                            UserEventLogMapper::getInstance()->createUserEventLog($user->id, EventLogTypeModel::FORGOT_PASSWORD_SEND);
                            $this->sendForgotPasswordEmail($user, $passwordResetRequest->resetToken);
                        }
                        catch (Exception $e)
                        {
                            if (!$requestCreated)
                            {
                                $db->rollback();
                            }
                            \Tangent\Logger\Logger::logException($e);
                        }
                    }

                    $this->_flashMessenger->addMessage(['success' => "Instructions have been sent to your email address."]);

                    $this->redirectToRoute('auth.login');
                }
            }
        }
    }

    /**
     * Gets a random number from /dev/urandom
     *
     * @param int $min The minimum number generated
     * @param int $max The maximum number generated
     *
     * @return int
     */
    private function getRandom ($min, $max)
    {
        $bits = '';

        $diff  = $max - $min;
        $bytes = ceil($diff / 256);

        $fp = @fopen('/dev/urandom', 'rb');
        if ($fp !== false)
        {
            $bits .= @fread($fp, $bytes);
            @fclose($fp);
        }
        $bitlength = strlen($bits);
        $int       = 0;
        for ($i = 0; $i < $bitlength; $i++)
        {
            $int = 1 + (ord($bits [$i]) % (($max - $min) + 1));
        }

        return $int;
    }

    /**
     * This page allows a user to change their password. It also handles the case where we require the user to change their password on the next login.
     */
    public function changepasswordAction ()
    {
        $this->_pageTitle = 'Change Password';
        $auth             = Zend_Auth::getInstance();
        $identity         = $auth->getIdentity();

        // If customer is flagged for a reset send them to the login page layout
        if ($identity->resetPasswordOnNextLogin)
        {
            $this->view->layout()->setLayout('auth');
            $this->_helper->viewRenderer('forcechangepassword');
        }
        $form    = new ChangePasswordForm();
        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getParams();
            if (isset($values ['submit']))
            {
                if ($form->isValid($values))
                {

                    $userMapper = UserMapper::getInstance();

                    $user     = $userMapper->find($identity->id);
                    $password = crypt($form->getValue("current_password"), $user->password);

                    // Check that the user has entered in the correct password
                    if (strcmp($password, $user->password) === 0)
                    {
                        // Process password change and remove change flag
                        $user->password                 = (UserModel::cryptPassword($form->getValue("password")));
                        $user->resetPasswordOnNextLogin = 0;
                        $userMapper->save($user);

                        UserEventLogMapper::getInstance()->createUserEventLog($user->id, EventLogTypeModel::CHANGE_PASSWORD);
                        // Remove flag on session
                        $identity->resetPasswordOnNextLogin = false;
                        $auth->getStorage()->write($identity);

                        $this->_flashMessenger->addMessage([
                            'success' => 'Password Changed Successfully'
                        ]);

                        // Redirects user to logout screen to login again
                        $this->redirectToRoute('auth.login');
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage([
                            'danger' => 'You entered the incorrect current password!'
                        ]);
                    }
                }
            }
            else // is $values ['cancel'] is set 
            {

                if ($identity->resetPasswordOnNextLogin)
                {
                    $this->doLogout();
                    $this->redirectToRoute('auth.login');

                }
                else
                {
                    $this->redirectToRoute('app.dashboard');
                }
            }
        }

        $this->view->form = $form;
    }

    /**
     * This function takes care of verifying the users reset token and
     * providing them with a form to reset their password.
     */
    public function forgotPasswordResetAction ()
    {
        $this->_pageTitle = ['Reset Password'];

        $this->view->layout()->setLayout('auth');

        // Make sure we're logged out before resetting
        $this->doLogout();

        $verificationToken       = $this->getParam('verify', false);
        $this->view->reset_token = $verificationToken;
        $passwordRequest         = $this->verifyPasswordReset($verificationToken);
        if ($passwordRequest === false)
        {
            $this->_flashMessenger->addMessage(['warning' => 'Your reset token is either expired or invalid. Please use the forgot password link to generate a new token.']);
            $this->redirectToRoute('app.dashboard');
        }

        if (!$passwordRequest->resetVerified)
        {
            $passwordRequest->resetVerified = true;
            UserPasswordResetRequestMapper::getInstance()->save($passwordRequest);
        }

        $form = new ResetPasswordForm();
        if ($this->getRequest()->isPost())
        {
            // Step 1. Get the reset id
            $postData = $this->getRequest()->getParams();
            if (isset($postData ['cancel']))
            {
                $this->redirectToRoute('auth.login');
            }
            else
            {
                if ($form->isValid($postData))
                {
                    $formValues = $form->getValues();

                    $passwordRequest->resetUsed = true;
                    UserPasswordResetRequestMapper::getInstance()->save($passwordRequest);
                    $user           = UserMapper::getInstance()->find($passwordRequest->userId);
                    $user->password = (UserModel::cryptPassword($formValues['password']));

                    UserMapper::getInstance()->save($user);
                    UserPasswordResetRequestMapper::getInstance()->deleteByUserId($user->id);
                    UserEventLogMapper::getInstance()->createUserEventLog($user->id, EventLogTypeModel::FORGOT_PASSWORD_CHANGED);

                    $this->_flashMessenger->addMessage(["success" => 'Password has been updated. You can now log in using your new password.']);

                    $this->redirectToRoute('auth.login');
                }
            }
        }
        $this->view->form = $form;
    }

    /**
     * Checks the validity of a password reset hash uid
     *
     * @param string $resetUid
     *
     * @return UserPasswordResetRequestModel
     */
    private function verifyPasswordReset ($resetUid)
    {
        $passwordResetRequest = false;
        if ($resetUid !== null)
        {
            // Step 2. Verify the reset id and update the request
            $passwordResetRequest = UserPasswordResetRequestMapper::getInstance()->fetch([
                "resetToken = ?" => $resetUid
            ]);
            if ($passwordResetRequest)
            {
                // If we already reset our password with this hash, then it should no longer be valid
                if ($passwordResetRequest->resetUsed == false)
                {
                    //Check to see if we are the same ip address
                    $currentIp = $_SERVER ['REMOTE_ADDR'];
                    if ($passwordResetRequest->ipAddress != $currentIp)
                    {


                        /*
                         * Check the timeframe of the password reset.
                         * This should be within 24 hours of the request
                         */
                        $timeRequested = new DateTime($passwordResetRequest->dateRequested);
                        $currentTime   = new DateTime();
                        $timeDiff      = $currentTime->diff($timeRequested, true);
                        //Check to see if it has expired
                        if ($timeDiff->h > 24 || $timeDiff->d > 0 || $timeDiff->m > 0 || $timeDiff->y > 0)
                        {
                            $passwordResetRequest = false;
                        }
                    }
                }
                else
                {
                    $passwordResetRequest = false;
                }
            }
            else
            {
                $passwordResetRequest = false;
            }

        }

        return $passwordResetRequest;
    }

    /**
     * @param $user  UserModel
     * @param $token String
     */
    private function sendForgotPasswordEmail ($user, $token)
    {
        $config = \Zend_Registry::get('config');

        $mail = new Zend_Mail ();
        $mail->setFrom($config->app->supportEmail, 'Forgot Password');
        $mail->addTo($user->email, $user->firstname . ' ' . $user->lastname);
        $mail->setSubject('Password Reset Request');
        $link = $this->view->serverUrl($this->view->url([
            'action'     => 'resetpassword',
            'controller' => 'auth',
            'module'     => 'default',
            'verify'     => $token
        ], 'auth.forgot-password.reset'));


        $body = "<body>";
        $body .= "<h2>" . $this->view->App()->title . " Password Reset Request</h2>";
        $body .= "<p>A password reset request for your " . $this->view->App()->title . " account has been submitted. If you did not make this request, please contact your system administrator immediately.</p>";
        $body .= "To reset your password please <a href='$link'>Click Here</a>";
        $body .= "</body>";
        $mail->setBodyHtml($body);
        $mail->send();
    }
}

