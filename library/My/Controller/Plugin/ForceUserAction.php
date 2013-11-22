<?php

class My_Controller_Plugin_ForceUserAction extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
        // Check if the user is logged in
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity())
        {
            // Get user data
            $authSession = $auth->getIdentity();
            $currentPage = $request->getModuleName() . '_' . $request->getControllerName() . '_' . $request->getActionName();
            // Send user to specific pages depending on what user data is set.
            if ($authSession->resetPasswordOnNextLogin)
            {
                if (strcasecmp($currentPage, 'default_auth_changepassword') !== 0)
                {
                    // Redirect to the login page
                    $r = new Zend_Controller_Action_Helper_Redirector();
                    $r->gotoSimple('changepassword', 'auth', 'default');
                }
            }

            // Add elseif for eula?


            // Add elseif for resetPasswordRequest (forgotpassword)?
        }
    }
}