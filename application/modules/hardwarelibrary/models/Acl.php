<?php
class Hardwarelibrary_Model_Acl
{

    const RESOURCE_HARDWARELIBRARY_INDEX_INDEX = "hardwarelibrary__index__index";

    /**
     * Sets up acl resources and access for a module
     *
     * @param Application_Model_Acl $acl
     */
    static function setupAcl (Application_Model_Acl $acl)
    {
        self::setupAclResources($acl);
        self::setupAclAccess($acl);
    }

    /**
     * Sets up the resources for a module
     *
     * @param Application_Model_Acl $acl
     */
    private static function setupAclResources (Application_Model_Acl $acl)
    {
        $acl->addResource(self::RESOURCE_HARDWARELIBRARY_INDEX_INDEX);
    }

    /**
     * Sets up access to resources for a module
     *
     * @param Application_Model_Acl $acl
     */
    private static function setupAclAccess (Application_Model_Acl $acl)
    {
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_HARDWARELIBRARY_INDEX_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_HARDWARELIBRARY_INDEX_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
    }
}