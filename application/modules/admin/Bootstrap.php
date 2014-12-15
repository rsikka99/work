<?php
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\AdminAclModel;

/**
 * Class Admin_Bootstrap
 */
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initAddToAcl ()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof AppAclModel)
        {
            AdminAclModel::setupAcl($acl);
        }
    }

    /**
     * @return Zend_Loader_Autoloader_Resource
     */
    protected function _initViewModelAutoloader ()
    {
        return $this->getResourceLoader()->addResourceType('ViewModel', 'viewmodels', 'ViewModel');
    }
}

