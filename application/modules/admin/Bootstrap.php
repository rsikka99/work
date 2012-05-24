<?php

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{

    protected function _initNavigation ()
    {
        $view = $this->getApplication()->getResource('view');
        $config = new Zend_Config_Xml(__DIR__ . '/configs/navigation.xml', 'nav');
        /* @var $container Zend_Navigation */
        $container = $view->navigation()->getContainer();
        $container->addPages($config);
    }
}

