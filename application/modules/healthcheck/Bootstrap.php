<?php
class Healthcheck_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initNavigation ()
    {
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

    protected function _initViewModelAutoloader ()
    {
        $this->getResourceLoader()->addResourceType('ViewModel', 'viewmodels', 'ViewModel');
    }

    protected function _initAddToAcl ()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof Application_Model_Acl)
        {
            Healthcheck_Model_Acl::setupAcl($acl);
        }
    }

}