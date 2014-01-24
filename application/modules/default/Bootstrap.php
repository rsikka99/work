<?php

/**
 * Class Default_Bootstrap
 */
class Default_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initAddToAcl ()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof Application_Model_Acl)
        {
            Default_Model_Acl::setupAcl($acl);
        }
    }
}