<?php
/**
 * Class Preferences_Bootstrap
 */
class Preferences_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initNavigation ()
    {
        /* @var $container Zend_Navigation */
        $config    = new Zend_Config_Xml(__DIR__ . '/configs/navigation.xml', 'nav');
        $container = Zend_Registry::get('Zend_Navigation');
        $container->addPages($config);
    }

    protected function _initAddToAcl()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof Application_Model_Acl)
        {
            Preferences_Model_Acl::setupAcl($acl);
        }
    }
}