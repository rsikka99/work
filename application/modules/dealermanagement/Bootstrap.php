<?php
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\DealerManagementAclModel;

/**
 * Class Dealermanagement_Bootstrap
 */
class Dealermanagement_Bootstrap extends Tangent\ModuleBootstrap
{
    protected function _initAddToAcl ()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof AppAclModel)
        {
            DealerManagementAclModel::setupAcl($acl);
        }
    }
}