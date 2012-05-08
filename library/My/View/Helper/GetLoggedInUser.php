<?php

class Zend_View_Helper_GetLoggedInUser extends Zend_View_Helper_Abstract
{

    /**
     * Returns application settings
     */
    public function GetLoggedInUser ()
    {
        $url = $this->view->baseUrl('/auth/login');
        $auth = Zend_Auth::getInstance();
        $html = array();
        
        
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $url = $this->view->baseUrl('/auth/logout');
            $name = $identity->firstname . " " . $identity->lastname;
            
            $html[] = "<div class='btn-group'>";
            $html[] = "<a class='btn btn-primary' href='#'><i class='icon-user icon-white'></i> " . $this->view->escape($name) . "</a>";
            $html[] = "<a class='pull-right btn btn-primary dropdown-toggle' data-toggle='dropdown' href='#'><b class='caret'></b></a>";
            $html[] = "<ul class='dropdown-menu'>";
            $html[] = "<li><a href='$url'>Logout</a></li>";
            $html[] = "</ul></div>";
        }
        else
        {
            $html[] = "<a class='btn btn-danger' href='$url'>Login</a>";
        }
        
        return join(PHP_EOL, $html);
    }
}