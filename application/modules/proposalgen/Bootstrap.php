<?php
/**
 * Class Proposalgen_Bootstrap
 */
class Proposalgen_Bootstrap extends Zend_Application_Module_Bootstrap
{

    protected function _initNavigation ()
    {
        $config = new Zend_Config_Xml(__DIR__ . '/configs/navigation.xml', 'nav');
        /* @var $container Zend_Navigation */
        $container = Zend_Registry::get('Zend_Navigation');
        $container->addPages($config);
    }

    /**
     * @return Zend_Loader_Autoloader
     */
    protected function _initAutoLoader ()
    {
        $autoLoader = Zend_Loader_Autoloader::getInstance();
        $autoLoader->registerNamespace('Custom_');
        $autoLoader->registerNamespace('Tangent_');
        $autoLoader->registerNamespace('gchart');

        return $autoLoader;
    }

    /**
     * @return Zend_Loader_Autoloader_Resource
     */
    protected function _initLibraryAutoLoader ()
    {
        return $this->getResourceLoader()->addResourceType('library', 'library', 'library');
    }

    protected function _initAddToAcl ()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof Application_Model_Acl)
        {
            Proposalgen_Model_Acl::setupAcl($acl);
        }
    }
}