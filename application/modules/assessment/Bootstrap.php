<?php
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\AssessmentAclModel;

/**
 * Class Assessment_Bootstrap
 */
class Assessment_Bootstrap extends Tangent\ModuleBootstrap
{
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
     * @return \MPSToolbox\Legacy\Models\Acl\AppAclModel|mixed
     */
    protected function _initAddToAcl ()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof AppAclModel)
        {
            AssessmentAclModel::setupAcl($acl);
        }

        return $acl;
    }

}