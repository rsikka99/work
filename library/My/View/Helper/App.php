<?php

class My_View_Helper_App extends Zend_View_Helper_Abstract
{

    /**
     * Returns application settings
     */
    public function App ()
    {
        return Zend_Registry::get('config')->app;
    }
}