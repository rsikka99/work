<?php

class My_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
        try
        {
            /* @var $acl Application_Model_Acl */
            $acl = Zend_Registry::get('Zend_Acl');
            
            $userId = null;
            $auth = Zend_Auth::getInstance();
            if ($auth->hasIdentity())
            {
                $userId = (string)$auth->getIdentity()->id;
            }
            
            if (! $acl->isAllowed($userId, $request))
            {
                if ($auth->hasIdentity())
                {
                    throw new Zend_Acl_Exception("Access Denied", 403);
                }
                else
                {
                    throw new Zend_Acl_Exception("Authorization Required", 401);
                }
            }
        }
        catch ( Exception $e )
        {
            switch ($e->getCode())
            {
                case 401 :
                    // Save the page we denied the user from accessing so that we can send them back afterwards
                    $session = new Zend_Session_Namespace("authRedirect");
                    $session->module = $request->getModuleName();
                    $session->controller = $request->getControllerName();
                    $session->action = $request->getActionName();
                    $session->params = $request->getParams();
                    
                    // Redirect to the login page
                    $r = new Zend_Controller_Action_Helper_Redirector();
                    $r->gotoRoute(array (), 'login');
                    break;
                default :
                    
                    // Set up the error handler
                    $error = new ArrayObject(array (), ArrayObject::ARRAY_AS_PROPS);
                    $error->type = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER;
                    $error->request = clone ($request);
                    $error->exception = $e;
                    $request->setParam('error_handler', $error);
                    
                    // Repoint the request to the default error handler
                    $request->setModuleName('default');
                    $request->setControllerName('error');
                    $request->setActionName('error');
                    break;
            }
        }
    }
}