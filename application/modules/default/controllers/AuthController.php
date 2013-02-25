<?php
class Default_AuthController extends Tangent_Controller_Action
{

    /**
     * The index action of the auth controller does nothing important.
     * We are just going to redirect the user over the login page instead.
     */
    function indexAction ()
    {
        $this->_redirect('/auth/login');
    }

    /**
     * Gets the auth adapter to use for authenticaiton
     *
     * @return My_Auth_Adapter
     */
    function getAuthAdapter ()
    {
        $authAdapter = new My_Auth_Adapter();
        $authAdapter->setTableName('users');
        $authAdapter->setIdentityColumn('username');
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
        $this->view->layout()->setLayout('auth');
        $request = $this->getRequest();
        $form    = new Default_Form_Login();

        if ($this->getRequest()->isPost())
        {
            if ($request->getParam('forgotPassword', false))
            {
                $this->redirector('forgotpassword', null, null, array('username' => $request->getParam('username', '')));
            }
            if ($form->isValid($request->getPost()))
            {
                $auth        = Zend_Auth::getInstance();
                $authAdapter = $this->getAuthAdapter();
                $authAdapter->setIdentity($form->getValue("username"));

                $password = $form->getValue("password");
                $authAdapter->setCredential($password);

                // Authenticate against the database
                $result = $auth->authenticate($authAdapter);

                // If the value is valid, store the information
                if ($result->isValid())
                {
                    // Get all the user information and only omit the password
                    // since we don't want to store it in the session.
                    $userInfo    = $authAdapter->getResultRowObject(null, 'password');
                    $authStorage = $auth->getStorage();
                    $authStorage->write($userInfo);

                    try
                    {
                        $currentSessionId = @session_id();
                        // Get all the sessions attached to current user signed in
                        $sessionMapper = Application_Model_Mapper_User_Session::getInstance();
                        $userSessions  = $sessionMapper->fetchSessionsByUserId($userInfo->id);
                        foreach ($userSessions as $userSession)
                        {
                            if ($userSession->id != $currentSessionId)
                            {
                                //@unlink($config->resources->session->save_path . '/sess_' . $session->SessionId);
                                $sessionMapper->delete($userSession);
                            }
                        }

                        $userSession = $sessionMapper->find($currentSessionId);
                        // Do we have a session id that matches our current session
                        if($userSession->id == $currentSessionId)
                        {
                          // If it's a new userId with this current session, update the userId for this sessionId
                          if($userInfo->id != $userSession->userId)
                          {
                                $sessionMapper->save($userSession);
                          }
                        }
                        else
                        {
                            // Create a new sessions for the new user
                            $userSession         = new Application_Model_User_Session();
                            $userSession->userId = $userInfo->id;
                            $userSession->id     = $currentSessionId;
                            $sessionMapper->insert($userSession);
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
                    switch ($result->getCode())
                    {
                        case Zend_Auth_Result::FAILURE_UNCATEGORIZED :
                            // I'm using this in the custom adapter, so messages will be user friendly coming out of it.
                            foreach ($result->getMessages() as $message)
                            {
                                $this->_helper->flashMessenger(array('danger' => $message));
                            }
                            break;
                        default :
                            // Put a generic invalid credential message
                            $this->_helper->flashMessenger(array('danger' => 'The username/password combination you entered was invalid.'));

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

        $request = $this->getRequest();
        if ($request->isGet())
        {
            $values = $request->getParam('username');
            if ($values)
            {
                $username = $this->_request->getParam('username');
                // find user by username
                $user = Application_Model_Mapper_User::getInstance()->fetch(Application_Model_Mapper_User::getInstance()->getWhereUsername($username));

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
                        $this->view->forgotMessage = "A verification email has been sent!";
//                        $this->view->forgotMessage = "<p>To Reset your password please <a href='/auth/resetpassword/verify/" . $passwordResetRequest->resetToken . "'>Click Here</a></p>";
                    }
                    catch (Exception $e)
                    {
                        $db->rollback();
                        throw new Exception("Error saving password reset request", 0, $e);
                        // prepare error message stating user not found
                        $this->view->message = "An error has occurred updating the database. The password was not reset and no email was sent.";
                    }
                }
                else
                {
                    $this->_helper->flashMessenger(array(
                                                        'danger' => "There are no users with the username '" . $username . "'"
                                                   ));
                    $this->redirector('index', 'index');
                }

            }
            else
            {
                $this->_helper->flashMessenger(array(
                                                    'danger' => 'A username is required to use forgot password'
                                               ));
                $this->redirector('index', 'index');
            }
        }

    }

    /**
     * Gets a random number from /dev/urandom
     *
     * @param int $min The minimum number generated
     * @param int $max The maximum number generated
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

                        $this->_helper->flashMessenger(array(
                                                            'success' => 'Password Changed Successfully'
                                                       ));

                        // Redirects user to logout screen to login again
                        $r = new Zend_Controller_Action_Helper_Redirector();
                        $r->gotoSimple('index', 'index', 'default');
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array(
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
        $this->logout();
        $validVerification = false;
        $form              = new Default_Form_ResetPassword();
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
                                $this->_helper->flashMessenger(array(
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

            }else
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
        $passwordresetrequest = false;
        if ($resetUid !== null)
        {
            // Step 2. Verify the reset id and update the request
            $passwordresetrequest = Application_Model_Mapper_User_PasswordResetRequest::getInstance()->fetch(array(
                                                                                                                  "resetToken = ?" => $resetUid
                                                                                                             ));
            if ($passwordresetrequest)
            {
                // If we already reset our password with this hash, then it should no longer be valid
                if ($passwordresetrequest->resetUsed == false)
                {
                    //Check to see if we are the same ip address
                    $currentIp = $_SERVER ['REMOTE_ADDR'];
                    if ($passwordresetrequest->ipAddress != $currentIp)
                    {


                        /*
                         * Check the timeframe of the password reset.
                         * This should be within 24 hours of the request
                         */
                        $timeRequested = new DateTime($passwordresetrequest->dateRequested);
                        $currentTime   = new DateTime();
                        $timeDiff      = $currentTime->diff($timeRequested, true);
                        //Check to see if it has expired
                        if ($timeDiff->h > 24 || $timeDiff->d > 0 || $timeDiff->m > 0 || $timeDiff->y > 0)
                        {
                            $passwordresetrequest = false;
                        }
                    }
                }
                else
                {
                    $passwordresetrequest = false;
                }
            }
            else
            {
                $passwordresetrequest = false;
            }

        }

        return $passwordresetrequest;
    }

    /**
     * @param $user  Application_Model_User
     * @param $token String
     */
    public
    function sendForgotPasswordEmail ($user, $token)
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
        $mailTransport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $emailConfig);
        Zend_Mail::setDefaultTransport($mailTransport);

        $mail = new Zend_Mail ();
        $mail->setFrom($email->username, 'Forgot Password');
        $mail->addTo($user->email, $user->firstname . ' ' . $user->lastname);
        $mail->setSubject('Password Reset Request');
        $link = $this->view->ServerUrl('/auth/resetpassword/verify/' . $token);
        $body = "<body>";
        $body .= "<h2>" . $this->view->App()->title . " Password Reset Request</h2>";
        $body .= "<p>A password reset request for your " . $this->view->App()->title . " account has been submitted. If you did not make this request, please contact your system administrator immediately.</p>";
        $body .= "To reset your password please <a href='$link'>Click Here</a>";
        $body .= "</body>";
        $mail->setBodyHtml($body);
        $mail->send();
    }
}

