<?php

/**
 * Class Default_AuthController
 */
class Default_AuthController extends Tangent_Controller_Action
{

    /**
     * The index action of the auth controller does nothing important.
     * We are just going to redirect the user over the login page instead.
     */
    function indexAction ()
    {
        $this->redirector('default', 'auth', 'index');
    }

    /**
     * Gets the auth adapter to use for authentication
     *
     * @return My_Auth_Adapter
     */
    function getAuthAdapter ()
    {
        $authAdapter = new My_Auth_Adapter();
        $authAdapter->setTableName('users');
        $authAdapter->setIdentityColumn('email');
        $authAdapter->setCredentialColumn('password');

        return $authAdapter;
    }

    /**
     * The login action authenticates a user with our system.
     * After a successful authentication we should send them back to the page
     * that they were trying to access.
     */
    function loginAction ()
    {
        $this->view->headTitle('Login');
        $this->view->layout()->setLayout('auth');
        $request = $this->getRequest();
        $form    = new Default_Form_Login();

        Tangent_Statsd::increment('mpstoolbox.login.attempts');

        if ($this->getRequest()->isPost())
        {
            if ($request->getParam('forgotPassword', false))
            {
                $this->redirector('forgotpassword', null, null, array('email' => $request->getParam('email', '')));
            }
            if ($form->isValid($request->getPost()))
            {
                $auth        = Zend_Auth::getInstance();
                $authAdapter = $this->getAuthAdapter();
                $authAdapter->setIdentity($form->getValue("email"));

                $password = $form->getValue("password");
                $authAdapter->setCredential($password);

                // Authenticate against the database
                $result = $auth->authenticate($authAdapter);

                // If the value is valid, store the information
                if ($result->isValid())
                {
                    Tangent_Statsd::increment('mpstoolbox.login.successes');

                    // Get all the user information and only omit the password
                    // since we don't want to store it in the session.
                    $userInfo    = $authAdapter->getResultRowObject(null, 'password');
                    $authStorage = $auth->getStorage();
                    $authStorage->write($userInfo);

                    try
                    {
                        $currentSessionId = @session_id();
                        // Get all the sessions attached to current user signed in
                        $userSessionMapper = Application_Model_Mapper_User_Session::getInstance();
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
                        if ($userSession->sessionId == $currentSessionId)
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
                            $userSession            = new Application_Model_User_Session();
                            $userSession->userId    = $userInfo->id;
                            $userSession->sessionId = $currentSessionId;
                            $userSessionMapper->insert($userSession);
                        }
                    }
                    catch (Exception $e)
                    {
                        throw new Exception('Passing up the chain', 0, $e);
                    }

                    /*
                     * Redirect them to the home page now that they are logged in.
                     */
                    $this->redirector('index', 'index', 'index');
                }
                else
                {
                    Tangent_Statsd::increment('mpstoolbox.login.failures');

                    switch ($result->getCode())
                    {
                        case Zend_Auth_Result::FAILURE_UNCATEGORIZED :
                            // I'm using this in the custom adapter, so messages will be user friendly coming out of it.
                            foreach ($result->getMessages() as $message)
                            {
                                $this->_flashMessenger->addMessage(array('danger' => $message));
                            }
                            break;
                        default :
                            // Put a generic invalid credential message
                            $this->_flashMessenger->addMessage(array('danger' => 'The email/password combination you entered was invalid.'));

                            break;
                    }
                }
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
        $this->view->headTitle('Logout');
        $this->logout();
        $this->redirector('login');
    } // end logoutAction


    /**
     * This function handles logging a user out of the system and destroying any
     * sensitive session information.
     * We should persist all other session data as it can help keep a user
     * friendly experience
     */
    public function logout ()
    {
        // Destroy only information that is part of a user being logged in.
        Zend_Auth::getInstance()->clearIdentity();
    }

    /**
     * Handles when users forget their password
     */
    public function forgotpasswordAction ()
    {
        $this->view->headTitle('Forgot Password');
        // check if this page has been posted to
        $request = $this->getRequest();
        $email   = $request->getParam('email', false);
        if ($email !== false)
        {
            // Find user by email
            $user = Application_Model_Mapper_User::getInstance()->fetchUserByEmail($email);

            if ($user)
            {
                $db = Zend_Db_Table::getDefaultAdapter();

                $db->beginTransaction();
                try
                {
                    Application_Model_Mapper_User_PasswordResetRequest::getInstance()->deleteByUserId($user->id);
                    $time                                = date("Y-m-d H:i:s");
                    $passwordResetRequest                = new Application_Model_User_PasswordResetRequest();
                    $passwordResetRequest->dateRequested = $time;
                    $passwordResetRequest->ipAddress     = $_SERVER ['REMOTE_ADDR'];
                    $passwordResetRequest->resetVerified = false;
                    $passwordResetRequest->userId        = $user->id;
                    $passwordResetRequest->resetUsed     = false;
                    // Create a unique hash for the reset token
                    // Random number + user id + unique id
                    $passwordResetRequest->resetToken = uniqid($this->getRandom(0, 12800) . $user->id, true);

                    Application_Model_Mapper_User_PasswordResetRequest::getInstance()->insert($passwordResetRequest);
                    $db->commit();
                    $this->sendForgotPasswordEmail($user, $passwordResetRequest->resetToken);

                }
                catch (Exception $e)
                {
                    $db->rollback();
                    Tangent_Log::logException($e);
                }
            }

            $this->_flashMessenger->addMessage(array('success' => "A verification email will be sent out if an account exists with this email"));
        }
        else
        {
            $this->_flashMessenger->addMessage(array('danger' => 'A email is required to use forgot password'));
        }

        $this->redirector('auth', 'login');

    }

    /**
     * Gets a random number from /dev/urandom
     *
     * @param int $min The minimum number generated
     * @param int $max The maximum number generated
     *
     * @return int
     */
    function getRandom ($min, $max)
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
        $this->view->headTitle('Change Password');
        $auth     = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();

        // If customer is flaged for a reset send them to the login page layout
        if ($identity->resetPasswordOnNextLogin)
        {
            $this->view->layout()->setLayout('auth');
            $this->_helper->viewRenderer('forcechangepassword');
        }
        $form    = new Default_Form_ChangePassword();
        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ['submit']))
            {
                if ($form->isValid($values))
                {

                    $userMapper = new Application_Model_Mapper_User();

                    $user     = $userMapper->find($identity->id);
                    $password = crypt($form->getValue("current_password"), $user->password);

                    // Check that the user has entered in the correct password
                    if (strcmp($password, $user->password) === 0)
                    {
                        // Process password change and remove change flag
                        $user->password                 = (Application_Model_User::cryptPassword($form->getValue("password")));
                        $user->resetPasswordOnNextLogin = 0;
                        $userMapper->save($user);

                        // Remove flag on session
                        $identity->resetPasswordOnNextLogin = false;
                        $auth->getStorage()->write($identity);

                        $this->_flashMessenger->addMessage(array(
                            'success' => 'Password Changed Successfully'
                        ));

                        // Redirects user to logout screen to login again
                        $r = new Zend_Controller_Action_Helper_Redirector();
                        $r->gotoSimple('index', 'index', 'default');
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array(
                            'danger' => 'You entered the incorrect current password!'
                        ));
                    }
                }
                else
                {
                    $form->buildBootstrapErrorDecorators();
                }
            }
            else // is $values ['cancel'] is set 
            {

                if ($identity->resetPasswordOnNextLogin)
                {
                    $this->logout();
                    $this->redirector('login');
                }
                else
                {
                    $this->redirector('index', 'index');
                }
            }
        }

        $this->view->form = $form;
    }

    /**
     * This function takes care of verifying the users reset token and
     * providing them with a form to reset their password.
     */
    function resetpasswordAction ()
    {
        $this->view->layout()->setLayout('auth');
        $this->view->headTitle('Reset Password');
        $this->logout();
        $form = new Default_Form_ResetPassword();
        // Step 1. Get the reset id
        $request = $this->getRequest();
        $values  = $request->getPost();
        //IF Cancel IS NOT SELECTED
        if (!isset($values ['cancel']))
        {
            if ($this->_getParam("verify"))
            {
                $verification            = $this->_getParam("verify");
                $this->view->reset_token = $verification;
                // Get the password reset object
                $passwordRequest = $this->verifyPasswordReset($verification);
                if ($passwordRequest !== false)
                {
                    if (!$passwordRequest->resetVerified)
                    {
                        $passwordRequest->resetVerified = true;
                        Application_Model_Mapper_User_PasswordResetRequest::getInstance()->save($passwordRequest);
                    }
                    if ($request->isPost())
                    {
                        $request = $this->getRequest();
                        $values  = $request->getPost();
                        if ($form->isValid($values))
                        {
                            $filter   = new Zend_Filter_StripTags();
                            $password = $filter->filter($this->_request->getParam('password'));
                            $confirm  = $filter->filter($this->_request->getParam('password_confirm'));

                            if (!empty($password) && strcmp($password, $confirm) === 0)
                            {
                                $passwordRequest->resetUsed = true;
                                Application_Model_Mapper_User_PasswordResetRequest::getInstance()->save($passwordRequest);
                                $user           = Application_Model_Mapper_User::getInstance()->find($passwordRequest->userId);
                                $user->password = (Application_Model_User::cryptPassword($this->_request->getParam('password')));
                                Application_Model_Mapper_User::getInstance()->save($user);
                                Application_Model_Mapper_User_PasswordResetRequest::getInstance()->deleteByUserId($user->id);
                                $this->_flashMessenger->addMessage(array(
                                    "success" => 'Password has been updated'
                                ));
                                $this->redirector('index', 'index');

                            }
                        }
                    }
                    $this->view->form = $form;
                }
                else
                {
                    $this->view->errors = "<h3>Link Expired</h3>";
                }

            }
            else
            {
                $this->redirector('index', 'index');
            }

        }
        else
        {
            $this->redirector('index', 'index');
        }

    }

    /**
     * Checks the validity of a password reset hash uid
     *
     * @param string $resetUid
     *
     * @return Application_Model_User_PasswordResetRequest
     */
    function verifyPasswordReset ($resetUid)
    {
        $passwordResetRequest = false;
        if ($resetUid !== null)
        {
            // Step 2. Verify the reset id and update the request
            $passwordResetRequest = Application_Model_Mapper_User_PasswordResetRequest::getInstance()->fetch(array(
                "resetToken = ?" => $resetUid
            ));
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
     * @param $user  Application_Model_User
     * @param $token String
     */
    public function sendForgotPasswordEmail ($user, $token)
    {
        $config = Zend_Registry::get('config');
        $email  = $config->email;

        //grab the email configuration settings from application.ini
        $emailConfig = array(
            'auth'     => 'login',
            'username' => $email->username,
            'password' => $email->password,
            'ssl'      => $email->ssl,
            'port'     => $email->port,
            'host'     => $email->host
        );

        //grab the email host from application.ini
        $mailTransport = new Zend_Mail_Transport_Smtp($emailConfig['host'], $emailConfig);
        Zend_Mail::setDefaultTransport($mailTransport);

        $mail = new Zend_Mail ();
        $mail->setFrom($email->username, 'Forgot Password');
        $mail->addTo($user->email, $user->firstname . ' ' . $user->lastname);
        $mail->setSubject('Password Reset Request');
        $link = $this->view->serverUrl($this->view->url(array(
            'action'     => 'resetpassword',
            'controller' => 'auth',
            'module'     => 'default',
            'verify'     => $token
        )));


        $body = "<body>";
        $body .= "<h2>" . $this->view->App()->title . " Password Reset Request</h2>";
        $body .= "<p>A password reset request for your " . $this->view->App()->title . " account has been submitted. If you did not make this request, please contact your system administrator immediately.</p>";
        $body .= "To reset your password please <a href='$link'>Click Here</a>";
        $body .= "</body>";
        $mail->setBodyHtml($body);
        $mail->send();
    }
}

