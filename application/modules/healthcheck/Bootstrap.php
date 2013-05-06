<?php
/**
 * Class Healthcheck_Bootstrap
 */
class Healthcheck_Bootstrap extends Zend_Application_Module_Bootstrap
{
    /**
     * Sets the paginator view partials
     */
    protected function _initPaginatorViewPartial ()
    {
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('_partials/paginator.phtml');
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

    protected function _initAddToAcl ()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof Application_Model_Acl)
        {
            Healthcheck_Model_Acl::setupAcl($acl);
        }
    }

}