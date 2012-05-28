<?php

/**
 * AuthController - Controller to handle user authentication.
 * 
 * @author Chris Garrah
 * @version 1.0
 */
class Proposalgen_AuthController extends Zend_Controller_Action
{

    function init ()
    {
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('login', 'json')->initContext();
        
        $config = Zend_Registry::get('config');
        $this->initView();
        $this->view->app = $config->app;
        $this->MPSProgramName = $config->app->MPSProgramName;
        $this->ApplicationName = $config->app->ApplicationName;
    }

    /**
     * Index action simply redirects to the index/index page.
     * Users
     * are not to visit this page directly
     */
    function indexAction ()
    {
        $this->_redirect('/');
    }

    /**
     * Action to handle requests to log into the system
     */
    function loginAction ()
    {
        // Disable the default layout
        $this->_helper->layout->setLayout('auth');
        $this->view->loggedIn = false;
        $this->view->message = ''; // reset the error message box
        

        if ($this->_request->isPost())
        {
            $filter = new Zend_Filter_StripTags();
            $username = $filter->filter($this->_request->getParam('username'));
            $password = $filter->filter($this->_request->getParam('password'));
            if ((empty($username)) || (empty($password)))
            {
                $this->view->message = 'Your username and password must not be empty.';
            }
            else
            {
                // check credentials against the database
                // NOTE: password in database is encrypted using md5
                $db = Zend_Db_Table::getDefaultAdapter();
                $adapter = new Zend_Auth_Adapter_DbTable($db);
                $adapter->setTableName('users');
                $adapter->setIdentityColumn('username');
                $adapter->setCredentialColumn('password');
                // Set the input credential values to authenticate against
                $adapter->setIdentity($username);
                $adapter->setCredential(md5($password));
                // do the authentication
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($adapter);
                if ($result->isValid())
                {
                    // success : store database row to auth's storage system
                    // (not the password though!)
                    $data = $adapter->getResultRowObject(null, 'password');
                    
                    $currentSessionId = session_id();
                    $config = Zend_Registry::get('config');
                    $userSessionMapper = Proposalgen_Model_Mapper_UserSession::getInstance();
                    $sessions = $userSessionMapper->fetchAll(array (
                            "user_id = ?" => $data->user_id 
                    ));
                    foreach ( $sessions as $session )
                    {
                        if ($session->SessionId != $currentSessionId)
                        {
                            @unlink($config->resources->session->save_path . '/sess_' . $session->SessionId);
                            $userSessionMapper->Delete(array (
                                    "user_id = ?" => $data->user_id, 
                                    "session_id != ?" => $currentSessionId 
                            ));
                        }
                    }
                    
                    $userSession = new Proposalgen_Model_UserSession();
                    $userSession->setUserId($data->user_id)->setSessionId($currentSessionId);
                    $userSessionMapper->save($userSession);
                    
                    try
                    {
                        $select = $db->select()
                            ->from(array (
                                'u' => 'user_privileges' 
                        ), array ())
                            ->join(array (
                                'p' => 'privileges' 
                        ), 'p.priv_id = u.priv_id', array (
                                'priv_type' 
                        ))
                            ->where('u.user_id = ?', $data->user_id);
                        $stmt = $select->query();
                        $stmt->setFetchMode(Zend_Db::FETCH_OBJ);
                        $result = $stmt->fetchall();
                        
                        foreach ( $result as $key => $value )
                        {
                            $privs [$key] = $value->priv_type;
                        } // end foreach
                        

                        // Add an array of privileges to the authentication
                        // token
                        $data->privileges = $privs;
                    
                    }
                    catch ( Exception $e )
                    {
                        // TODO insert error handling for failure to grab
                        // privledges
                    }
                    
                    // write the authentication token to storage
                    $auth->getStorage()->write($data);
                    // direct back to the main entry point of the site
                    // (index/index)
                    // this time user with have the authenication token,
                    // and be allowed to access
                    $this->_redirect('/');
                
                }
                else
                {
                    $this->view->message = 'Your username or password is incorrect.';
                } // end if
            } // end else
        } // end if
    } // end loginAction

    
    /**
     * Action to handle requests to log out of the system
     */
    function logoutAction ()
    {
        $this->killSession();
        // Redirect to the login page (index/index with no authentication token)
        $this->_redirect('/');
    } // end logout

    
    /**
     * Kills the users current sessions
     */
    private function killSession ()
    {
        // Kill identity, cookie, and session
        Zend_Auth::getInstance()->clearIdentity();
        
        // expire session
        if (isset($_COOKIE [session_name()]))
        {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        
        // destroy session
        session_destroy();
    }
    
    // validates captcha response
    function validateCaptcha ($captcha)
    {
        $captchaId = $captcha ['id'];
        $captchaInput = $captcha ['input'];
        $captchaSession = new Zend_Session_Namespace('Zend_Form_Captcha_' . $captchaId);
        $captchaIterator = $captchaSession->getIterator();
        $captchaWord = $captchaIterator ['word'];
        
        if ($captchaWord)
        {
            if ($captchaInput != $captchaWord)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            return false;
        }
    }
    
    // create captcha
    function generateCaptcha ()
    {
        $captcha = new Zend_Captcha_Image();
        $captcha->setWordlen('5')
            ->setWidth(215)
            ->setFont(DATA_PATH . '/fonts/arial.ttf')
            ->setImgDir(PUBLIC_FOLDER . '/cache/captcha/')
            ->setDotNoiseLevel('3')
            ->setLineNoiseLevel('3')
            ->setTimeout('300');
        $captcha->generate();
        
        return $captcha->getId();
    
    }

    /**
     * Gets a random number from /dev/urandom
     * @param int $min The minimum number generated
     * @param int $max The maximum number generated
     */
    function getRandom ($min, $max)
    {
        $bits = '';
        
        $diff = $max - $min;
        $bytes = ceil($diff / 256);
        
        $fp = @fopen('/dev/urandom', 'rb');
        if ($fp !== FALSE)
        {
            $bits .= @fread($fp, $bytes);
            @fclose($fp);
        }
        $bitlength = strlen($bits);
        for($i = 0; $i < $bitlength; $i ++)
        {
            $int = 1 + (ord($bits [$i]) % (($max - $min) + 1));
        }
        return $int;
    }

    function forgotpasswordAction ()
    {
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->_helper->layout->setLayout('auth');
        $this->view->message = '';
        
        // setup CAPTCHA
        $captcha_id = $this->generateCaptcha();
        $this->view->captcha = $captcha_id;
        
        if ($this->_request->isPost())
        {
            $username = $this->_request->getParam('username');
            
            if (isset($_POST ['captcha']))
            {
                $captcha = $_POST ['captcha'];
                
                if ($this->validateCaptcha($captcha))
                {
                    $validator = new Zend_Validate_EmailAddress();
                    if (empty($username))
                    {
                        // prepare error message stating user not found
                        $this->view->message = "You must enter a valid username.";
                    }
                    else
                    {
                        // find user by username
                        $user = Proposalgen_Model_Mapper_User::getInstance()->fetchRow(array (
                                "username = ?" => $username 
                        ));
                        
                        if ($user)
                        {
                            $db->beginTransaction();
                            try
                            {
                                
                                // get user_id
                                $user_id = $user->getUserId();
                                $user_email = $user->getEmail();
                                $user_fullname = $user->getFirstname() . " " . $user->getLastname();
                                
                                $currentDate = new DateTime();
                                
                                $passwordResetRequest = new Proposalgen_Model_User_PasswordResetRequest();
                                $passwordResetRequest->setDateRequested($currentDate->getTimestamp());
                                $passwordResetRequest->setIpAddress($_SERVER ['REMOTE_ADDR']);
                                $passwordResetRequest->setResetVerified(false);
                                $passwordResetRequest->setUserId($user->getUserId());
                                $passwordResetRequest->setResetUsed(false);
                                
                                // Create a unique hash for the reset token
                                // Random number + user id + unique id
                                $passwordResetRequest->setResetToken(uniqid($this->getRandom(0, 12800) . $user->getUserId(), true));
                                
                                Proposalgen_Model_Mapper_User_PasswordResetRequest::getInstance()->save($passwordResetRequest);
                                
                                // get user_id
                                $user_id = $user->getUserId();
                                $user_email = $user->getEmail();
                                $user_fullname = $user->getFirstname() . " " . $user->getLastname();
                                
                                // if save is successful - prepare message to be
                                // sent by email
                                $subject = $this->ApplicationName . " Password Reset Request";
                                
                                $body = "<body>";
                                $body .= "<h2>" . $this->ApplicationName . " Password Reset Request</h2>";
                                $body .= "<p>A password reset request for your " . $this->ApplicationName . " account has been submitted. If you did not make this request, please contact your system administrator immediately.</p>";
                                $body .= "<p>To Reset your password please <a href='" . $this->view->FullUrl("/auth/resetpassword/verify/" . $passwordResetRequest->getResetToken()) . "'>Click Here</a></p>";
                                $body .= "</body>";
                                
                                $email = new Custom_Common();
                                $email_config = Zend_Registry::get('config');
                                
                                $fromemail = $email_config->email->username;
                                ;
                                $fromname = $this->ApplicationName;
                                $toname = ucwords(strtolower($user_fullname));
                                $toemail = $user_email;
                                
                                $email->send_email($body, $fromname, $fromemail, $toname, $toemail, $subject);
                                
                                // prepare error message stating user not found
                                $this->view->message = "A password reset confirmation link has been emailed to you.";
                                // $this->_redirect('/');
                                

                                // save to database
                                $db->commit();
                            
                            }
                            catch ( Exception $e )
                            {
                                $db->rollback();
                                throw new Exception("Error saving password reset request", 0, $e);
                                // prepare error message stating user not found
                                $this->view->message = "An error has occurred updating the database. The password was not reset and no email was sent.";
                            }
                        
                        }
                        else
                        {
                            // prepare error message stating user not found
                            $this->view->message = "Username not found";
                        }
                    }
                }
                else
                {
                    // captcha invalid, text doesn't match
                    $this->view->message = "Security code did not match. Please try again.";
                }
            }
        }
    }

    /**
     * Checks the validity of a password reset hash uid
     * @param string $resetUid
     * @return Proposalgen_Model_User_PasswordResetRequest
     */
    function verifyPasswordReset ($resetUid)
    {
        $passwordresetrequest = false;
        if ($resetUid !== null)
        {
            // Step 2. Verify the reset id and update the request
            $passwordresetrequest = Proposalgen_Model_Mapper_User_PasswordResetRequest::getInstance()->fetchRow(array (
                    "reset_token = ?" => $resetUid 
            ));
            if ($passwordresetrequest)
            {
                // If we already reset our password with this hash, then it should no longer be valid
                if ($passwordresetrequest->getResetUsed() == false)
                {
                    /*
                     * Check the timeframe of the password reset. 
                     * This should be within 24 hours of the request
                     */
                    $timeRequested = new DateTime();
                    $timeRequested->setTimestamp($passwordresetrequest->getDateRequested());
                    $currentTime = new DateTime();
                    $timeDiff = $currentTime->diff($timeRequested, true);
                    
                    if ($timeDiff->h > 24)
                    {
                        $passwordresetrequest = false;
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
     * This function takes care of verifying the users reset token and 
     * providing them with a form to reset their password.
     */
    function resetpasswordAction ()
    {
        // If the user is here, we want to ensure that they have no session first.
        $this->_helper->layout->setLayout('auth');
        
        $this->killSession();
        
        $validVerification = false;
        // Step 1. Get the reset id
        if ($this->_hasParam("verify"))
        {
            $verification = $this->_getParam("verify");
            $this->view->reset_token = $verification;
            // Get the password reset object
            $passwordRequest = $this->verifyPasswordReset($verification);
            if ($passwordRequest !== FALSE)
            {
                $validVerification = true;
                if (! $passwordRequest->getResetVerified())
                {
                    $passwordRequest->setResetVerified(true);
                    Proposalgen_Model_Mapper_User_PasswordResetRequest::getInstance()->save($passwordRequest);
                }
                
                if ($this->getRequest()->isPost())
                {
                    $filter = new Zend_Filter_StripTags();
                    $password = $filter->filter($this->_request->getParam('password'));
                    $confirm = $filter->filter($this->_request->getParam('confirm'));
                    
                    if (! empty($password) && strcmp($password, $confirm) === 0)
                    {
                        $passwordRequest->setResetUsed(true);
                        Proposalgen_Model_Mapper_User_PasswordResetRequest::getInstance()->save($passwordRequest);
                        
                        $user = Proposalgen_Model_Mapper_User::getInstance()->find($passwordRequest->getUserId());
                        $user->setPassword(md5($password));
                        Proposalgen_Model_Mapper_User::getInstance()->save($user);
                        
                        $this->_helper->viewRenderer->setRender("passwordchanged");
                    }
                }
            }
        
        }
        
        // TODO: We should probably make this page display an error if it is not valid instead of sending them on their way
        // Send them on their way if they aren't supposed to be here
        if (! $validVerification)
        {
            $this->_redirect("/");
        }
    }

    /**
     * This function takes care of allowing the user to change their password
     */
    function changepasswordAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $this->view->message = '';
        $authUser = Zend_Auth::getInstance()->getIdentity();
        if ($authUser->update_password)
        {
            $this->view->message = "You must reset your password before proceeding.";
            $this->_helper->layout->setLayout('auth');
        }
        
        $user_id = $authUser->user_id;
        $this->view->updatePassword = $authUser->update_password;
        
        if ($this->_request->isPost())
        {
            $isCancel = $this->_request->getParam('cancel');
            if (! isset($isCancel))
            {
                $filter = new Zend_Filter_StripTags();
                $password = $filter->filter($this->_request->getParam('password'));
                $confirm = $filter->filter($this->_request->getParam('confirm'));
                $db->beginTransaction();
                try
                {
                    if ($password === $confirm && ! empty($password))
                    {
                        // update password and update_password field
                        $userTable = new Proposalgen_Model_DbTable_Users();
                        $userData = array (
                                'password' => md5($password), 
                                'update_password' => 0 
                        );
                        
                        $where = $userTable->getAdapter()->quoteInto('user_id = ?', $user_id);
                        $userTable->update($userData, $where);
                        $db->commit();
                        
                        // If the user was forced to change their password, then
                        // send to /
                        if ($authUser->update_password)
                        {
                            $authUser->update_password = 0;
                            $this->_redirect('/');
                        }
                        else
                        {
                            // Otherwise the user chose to change it
                            $this->view->message = "Your password has been changed.";
                        }
                    }
                    else
                    {
                        $db->rollback();
                        if ($password)
                            $this->view->message = "Your password and confirmation password do not match. Please try again.";
                        else
                            $this->view->message = "Your password cannot be empty.";
                    
                    }
                }
                
                catch ( Exception $e )
                {
                    $db->rollback();
                    $this->view->message = "";
                    $this->view->message .= "An error has occurred and your password was not updated. Please go back and try again. ";
                    $this->view->message .= "If you continue to have problems please contact your administrator.";
                }
            }
            else
            {
                if ($authUser->update_password)
                {
                    $this->_redirect('/auth/logout');
                }
                else
                {
                    $this->_redirect('/admin');
                }
            }
        }
    
    }

    function accepteulaAction ()
    {
        $auth = Zend_Auth::getInstance();
        if (! $auth->hasIdentity())
        {
            $this->_redirect('/auth/login');
            exit();
        } // end if
        

        $authUser = $auth->getIdentity();
        $user_id = $authUser->user_id;
        $this->_helper->layout->disableLayout();
        
        // EULA text generation
        $this->view->EulaPath = APPLICATION_PATH . "/../docs/EULA/officeDepotEULA.txt";
        
        if ($this->_request->isPost())
        {
            $filter = new Zend_Filter_StripTags();
            $accepted = $filter->filter($this->_request->getParam('eulaAccepted'));
            $refuse = $filter->filter($this->_request->getParam('refuseeula'));
            
            if (! $refuse)
            {
                if ($accepted)
                {
                    $db = Zend_Db_Table::getDefaultAdapter();
                    $db->beginTransaction();
                    try
                    {
                        // update password and update_password field
                        $userTable = new Proposalgen_Model_DbTable_Users();
                        $userData = array (
                                'eula_accepted' => date('Y-m-d H:i:s') 
                        );
                        $where = $userTable->getAdapter()->quoteInto('user_id = ?', $user_id);
                        $userTable->update($userData, $where);
                        $db->commit();
                        $authUser->eula_accepted = $userData ['eula_accepted'];
                        
                        // redirect to home
                        $this->_redirect('/');
                        exit();
                    } // end try
                    catch ( Exception $e )
                    {
                        $db->rollback();
                        $this->view->message = "An error has occurred and the EULA was not accepted. Please go back and try again. ";
                        $this->view->message .= "If you continue to have problems please contact your administrator.";
                    
                    } // end catch
                

                }
                else
                {
                    $this->view->message = "You <strong>must</strong> accept the EULA to continue.";
                
                } // end else
            

            }
            else
            {
                // Log the user out
                $this->_redirect('/auth/logout');
            
            }
        } // endif
    

    } // end accept eula Action


} // end auth controller

