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
     *
     * TODO: Make a redirect back to the page they came from.
     */
    function loginAction ()
    {
        $request = $this->getRequest();
        $form = new Default_Form_Login();
        
        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($request->getPost()))
            {
                $auth = Zend_Auth::getInstance();
                $authAdapter = $this->getAuthAdapter();
                $authAdapter->setIdentity($form->getValue("username"));
                
                $password = $form->getValue("password");
                //$password = $this->cryptPassword($form->getValue("password"));
                
                $authAdapter->setCredential($password);
                
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
    function logoutAction ()
    {
        // Destroy only information that is part of a user being logged in.
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    } // end logoutAction

    
    public function registerAction ()
    {
    
    }

    public function cryptAction ()
    {
        // Only do this if we posted
        if ($this->getRequest()->isPost())
        {
            $password = $this->getRequest()->getPost('password');
            if ($password !== null)
            {
                $this->view->result = $this->cryptPassword($password);
            }
        }
    }

} // end auth controller

