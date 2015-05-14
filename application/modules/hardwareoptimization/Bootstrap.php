<?php
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\HardwareOptimizationAclModel;

/**
 * Class Hardwareoptimization_Bootstrap
 */
class Hardwareoptimization_Bootstrap extends Tangent\ModuleBootstrap
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

    /**
     * @return Zend_Loader_Autoloader_Resource
     */
    protected function _initServiceAutoloader ()
    {
        return $this->getResourceLoader()->addResourceType('Service', 'services', 'Service');
    }

    /**
     * Adds ACL
     *
     * @return \MPSToolbox\Legacy\Models\Acl\AppAclModel|mixed
     */
    protected function _initAddToAcl ()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof AppAclModel)
        {
            HardwareOptimizationAclModel::setupAcl($acl);
        }

        return $acl;
    }

}