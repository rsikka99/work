<?php
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\EcommerceAclModel;

/**
 * Class Default_Bootstrap
 */
class Ecommerce_Bootstrap extends Tangent\ModuleBootstrap
{
    protected function _initAddToAcl ()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof AppAclModel)
        {
            EcommerceAclModel::setupAcl($acl);
        }
    }
}