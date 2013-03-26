<?php

class Quotegen_Bootstrap extends Zend_Application_Module_Bootstrap
{

    /**
     * Adds our navigation menus to the main navigation
     */
    protected function _initNavigation ()
    {
        $view = $this->getApplication()->getResource('view');
        $config = new Zend_Config_Xml(__DIR__ . '/configs/navigation.xml', 'nav');
        /* @var $container Zend_Navigation */
        $container = Zend_Registry::get('Zend_Navigation');
        $container->addPages($config);
    }

    /**
     * Sets the paginator view partials
     */
    protected function _initPaginatorViewPartial ()
    {
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('_partials/paginator.phtml');
    }

    protected function _initLibraryAutoloader ()
    {
        return $this->getResourceLoader()->addResourceType('library', 'library', 'library');
    }

    protected function _initAddToAcl ()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof Application_Model_Acl)
        {
            Quotegen_Model_Acl::setupAcl($acl);
        }
    }
}

