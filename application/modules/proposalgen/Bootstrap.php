<?php

class Proposalgen_Bootstrap extends Zend_Application_Module_Bootstrap
{

    protected function _initNavigation ()
    {
        $view = $this->getApplication()->getResource('view');
        $config = new Zend_Config_Xml(__DIR__ . '/configs/navigation.xml', 'nav');
        /* @var $container Zend_Navigation */
        $container = Zend_Registry::get('Zend_Navigation');
        $container->addPages($config);
    }

    protected function _initAutoloader ()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Custom_');
        $autoloader->registerNamespace('Tangent_');
        $autoloader->registerNamespace('gchart');
    }

    protected function _initLibraryAutoloader ()
    {
        return $this->getResourceLoader()->addResourceType('library', 'library', 'library');
    }
}

