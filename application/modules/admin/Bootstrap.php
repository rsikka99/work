<?php
/**
 * Class Admin_Bootstrap
 */
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{

    protected function _initNavigation ()
    {
        $config = new Zend_Config_Xml(__DIR__ . '/configs/navigation.xml', 'nav');
        /* @var $container Zend_Navigation */
        $container = Zend_Registry::get('Zend_Navigation');
        $container->addPages($config);
    }

    protected function _initAddToAcl ()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof Application_Model_Acl)
        {
            Admin_Model_Acl::setupAcl($acl);
        }
    }
}

