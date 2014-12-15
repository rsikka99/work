<?php
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\DefaultAclModel;

/**
 * Class Default_Bootstrap
 */
class Default_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initAddToAcl ()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof AppAclModel)
        {
            DefaultAclModel::setupAcl($acl);
        }
    }
}