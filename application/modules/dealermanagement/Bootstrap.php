<?php
/**
 * Class Dealermanagement_Bootstrap
 */
class Dealermanagement_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initAddToAcl()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof Application_Model_Acl)
        {
            Dealermanagement_Model_Acl::setupAcl($acl);
        }
    }
}