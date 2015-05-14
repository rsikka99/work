<?php
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\HardwareLibraryAclModel;

/**
 * Class Hardwarelibrary_Bootstrap
 */
class Hardwarelibrary_Bootstrap extends Tangent\ModuleBootstrap
{
    protected function _initAddToAcl ()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof AppAclModel)
        {
            HardwareLibraryAclModel::setupAcl($acl);
        }
    }
}