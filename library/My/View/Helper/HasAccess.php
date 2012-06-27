<?php

class My_View_Helper_HasAccess extends Zend_View_Helper_Abstract
{

    /**
     * Returns application settings
     */
    public function hasAccess ($role, $action, $controller, $module)
    {
        return Zend_Registry::get('config')->app;
    }
}