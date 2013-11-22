<?php

class My_View_Helper_Username extends Zend_View_Helper_Abstract
{

    /**
     * Returns application settings
     */
    public function Username ()
    {

        $auth = Zend_Auth::getInstance();
        $name = "Not Logged In";
        if ($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $name     = $this->view->escape($identity->firstname . " " . $identity->lastname);
        }

        return $name;
    }
}