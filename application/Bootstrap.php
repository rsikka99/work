<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function __construct ($application)
    {
        parent::__construct($application);
    
    }

    protected function _initConfig ()
    {
        $config = new Zend_Config($this->getOptions());
        if (! Zend_Registry::isRegistered("config"))
        {
            Zend_Registry::set("config", $config);
        }
        return $config;
    }

    protected function _initPhPDefaultTimezone ()
    {
        date_default_timezone_set("America/New_York");
    }

    protected function _initDoctype ()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('HTML5');
    }
    
    protected function _initTwitterBootstrap()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->registerHelper(new My_View_Helper_Navigation_Menu(), 'menu');
    }

    protected function _initAcl ()
    {
        $acl = new Application_Model_Acl();
        Zend_Registry::set('acl', $acl);
        return $acl;
    }

    protected function _initNavigation ()
    {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
        $container = new Zend_Navigation($config);
        $view->navigation($container);
    }
}

