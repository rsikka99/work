<?php
/**
 * Class Hardwarelibrary_Model_Acl
 */
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
        //Hardware Admin
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_HARDWARELIBRARY_INDEX_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);

        /**
         * Quote User
         */
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_HARDWARELIBRARY_INDEX_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
    }
}