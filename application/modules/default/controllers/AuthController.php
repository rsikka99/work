<?php

/**
 * AuthController - Controller to handle authentication.
 *
 * @author Voziv
 * @version 1.0
 */
class Default_AuthController extends Zend_Controller_Action
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
     * @return Zend_Auth_Adapter_DbTable
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
        $form = new Default_Form_Login();
        
        if ($this->getRequest()->isPost())
        {
            if ($request->getParam('forgotpassword', false))
            {
                $this->_helper->getHelper('Redirector')->gotoRoute(array (
                        'username' => $request->getParam('username', '') 
                ), 'forgotpassword');
            }
            if ($form->isValid($request->getPost()))
            {
                $auth = Zend_Auth::getInstance();
                $authAdapter = $this->getAuthAdapter();
                $authAdapter->setIdentity($form->getValue("username"));
                
                $password = $form->getValue("password");
                $authAdapter->setCredential($password);
                //$password = $this->cryptPassword($form->getValue("password"));
                

                // Authenticate against the database
                $result = $auth->authenticate($authAdapter);
                
                // If the value is valid, store the information
                if ($result->isValid())
                {
                    // Get all the user information and only omit the password
                    // since we don't want to store it in the session.
                    $userInfo = $authAdapter->getResultRowObject(null, 'password');
                    
                    $authStorage = $auth->getStorage();
                    $authStorage->write($userInfo);
                    
                    $session = new Zend_Session_Namespace("authRedirect");
                    if (isset($session->module))
                    {
                        $module = $session->module;
                        $controller = $session->controller;
                        $action = $session->action;
                        $params = $session->params;
                        $session->unsetAll();
                        $this->_helper->redirector($action, $controller, $module, $params);
                    }
                    else
                    {
                        $this->_redirect('/');
                    }
                }
                else
                {
                    switch ($result->getCode())
                    {
                        case Zend_Auth_Result::FAILURE_UNCATEGORIZED :
                            // I'm using this in the custom adapter, so messages will be user friendly coming out of it.
                            foreach ( $result->getMessages() as $message )
                            {
                                $this->_helper->flashMessenger(array (
                                        'danger' => $message 
                                ));
                            }
                            break;
                        default :
                            // Put a generic invalid credential message
                            $this->_helper->flashMessenger(array (
                                    'danger' => 'The username/password combination you entered was invalid.' 
                            ));
                            
                            break;
                    }
                    
                    // Build the bootstrap error decorator
                    $form->buildBootstrapErrorDecorators();
                }
            }
            else
            {
                // Build the bootstrap error decorator
                $form->buildBootstrapErrorDecorators();
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
        $this->_helper->redirector('login');
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

    public function registerAction ()
    {
    }

    public function forgotpasswordAction ()
    {
    }

    public function changepasswordAction ()
    {
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        
        // If customer is flaged for a reset send them to the login page layout
        if ($identity->resetPasswordOnNextLogin)
        {
            $this->view->layout()->setLayout('auth');
            $this->_helper->viewRenderer('forcechangepassword');
        }
        $form = new Default_Form_ChangePassword();
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ['submit']))
            {
                if ($form->isValid($values))
                {
                    
                    $userMapper = new Application_Model_Mapper_User();
                    
                    $user = $userMapper->find($identity->id);
                    $password = crypt($form->getValue("current_password"), $user->getPassword());
                    
                    // Check that the user has entered in the correct password
                    if (strcmp($password, $user->getPassword()) === 0)
                    {
                        
                        // Process password change and remove change flag
                        $user->setPassword(crypt($form->getValue("password"), $user->getPassword()));
                        $user->setResetPasswordOnNextLogin(0);
                        $userMapper->save($user);
                        
                        // Remove flag on session
                        $identity->resetPasswordOnNextLogin = false;
                        $auth->getStorage()->write($identity);
                        
                        $this->_helper->flashMessenger(array (
                                'success' => 'Password Changed Successfully' 
                        ));
                        
                        // Redirects user to logout screen to login again
                        $r = new Zend_Controller_Action_Helper_Redirector();
                        $r->gotoSimple('index', 'index', 'default');
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array (
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
                    $this->_helper->redirector('login');
                }
                else
                {
                    $this->_helper->redirector('index', 'index');
                }
            }
        }
        
        $this->view->form = $form;
    }
} // end auth controller

