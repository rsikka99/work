<?php
class Preferences_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initNavigation ()
    {
        /* @var $container Zend_Navigation */

        $view      = $this->getApplication()->getResource('view');
        $config    = new Zend_Config_Xml(__DIR__ . '/configs/navigation.xml', 'nav');
        $container = Zend_Registry::get('Zend_Navigation');
        $container->addPages($config);
    }
}