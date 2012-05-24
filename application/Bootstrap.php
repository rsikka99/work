<?php

/**
 * Class Bootstrap
 * This class prepares the application with all the needed settings before anything is routed
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initConfig ()
    {
        $config = new Zend_Config($this->getOptions());
        if (! Zend_Registry::isRegistered("config"))
        {
            Zend_Registry::set("config", $config);
        }
        return $config;
    }

    /**
     * Initializes php runtime settings
     */
    protected function _initPhpSettings ()
    {
        $options = $this->getOptions();
        date_default_timezone_set($options ["phpSettings"] ["timezone"]);
        
        // Turn on the display of errors
        if (APPLICATION_ENV != 'production')
        {
            @ini_set("display_errors", 1);
        }
    }

    /**
     * Registers the logger with Zend_Registry.
     * Under no circumstances should this be renamed to _initLog as it will
     * override ini settings
     */
    protected function _initLoggerToRegistry ()
    {
        $this->bootstrap('Log');
        $this->bootstrap('Db');
        if ($this->hasResource('Log'))
        {
            $logger = $this->getResource('Log');
            assert($logger != null);
            $db = Zend_Db_Table::getDefaultAdapter();
            // Set up the logging system and put it in the Zend Registry.
            $columnMapping = array (
                    'priority' => 'priority', 
                    'message' => 'message', 
                    'logTypeId' => 'logTypeId', 
                    'userId' => 'userId' 
            );
            $logger->addWriter(new Zend_Log_Writer_Db($db, 'logs', $columnMapping));
            
            Zend_Registry::set('Zend_Log', $this->getResource('Log'));
        }
    }

    /**
     * Initializes settings for the view
     */
    protected function _initViewSettings ()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        
        // Set the doctype to HTML5 so components know how to render items
        $view->doctype('HTML5');
        
        // Initialize the twitter bootstrap menu plugin
        $view->registerHelper(new My_View_Helper_Navigation_Menu(), 'menu');
    }

    /**
     * Initializes the ACL for the project
     */
    protected function _initAcl ()
    {
        $acl = new Application_Model_Acl();
        Zend_Registry::set('Zend_Acl', $acl);
        return $acl;
    }

    /**
     * Loads the navigation.xml and sets up navigation to be used by the navigation view helper
     */
    protected function _initNavigation ()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity())
        {
            $id = $auth->getIdentity()->id;
            $view->navigation()->setRole("$id");
        }
        else
        {
            $view->navigation()->setRole(null);
        }
    }
}

