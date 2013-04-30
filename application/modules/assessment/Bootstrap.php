<?php
/**
 * Class Assessment_Bootstrap
 */
class Assessment_Bootstrap extends Zend_Application_Module_Bootstrap
{
    /**
     * Adds navigation
     */
    protected function _initNavigation ()
    {
        $config = new Zend_Config_Xml(__DIR__ . '/configs/navigation.xml', 'nav');
        /* @var $container Zend_Navigation */
        $container = Zend_Registry::get('Zend_Navigation');
        $container->addPages($config);
    }


    /**
     * @return Zend_Loader_Autoloader_Resource
     */
    protected function _initLibraryAutoloader ()
    {
        return $this->getResourceLoader()->addResourceType('library', 'library', 'library');
    }

    /**
     * @return Zend_Loader_Autoloader_Resource
     */
    protected function _initViewModelAutoloader ()
    {
        return $this->getResourceLoader()->addResourceType('ViewModel', 'viewmodels', 'ViewModel');
    }

    /**
     * Adds ACL
     *
     * @return Application_Model_Acl|mixed
     */
    protected function _initAddToAcl ()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof Application_Model_Acl)
        {
            Assessment_Model_Acl::setupAcl($acl);
        }

        return $acl;
    }

}