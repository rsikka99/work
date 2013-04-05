<?php
class Admin_Model_Acl
{
    /**
     * Admin Constants
     */
    const RESOURCE_ADMIN_CLIENT_WILDCARD        = "admin__client__%";
    const RESOURCE_ADMIN_CLIENT_INDEX           = "admin__client__index";
    const RESOURCE_ADMIN_DEALER_INDEX           = "admin__dealer__index";
    const RESOURCE_ADMIN_INDEX_INDEX            = "admin__index__index";
    const RESOURCE_ADMIN_LEASINGSCHEMA_INDEX    = "admin__leasingschema__index";
    const RESOURCE_ADMIN_LEASINGSCHEMA_WILDCARD = "admin__leasingschema__%";
    const RESOURCE_ADMIN_TONER_WILDCARD         = "admin__toner__%";
    const RESOURCE_ADMIN_USER_PROFILE           = "admin__user__profile";
    const RESOURCE_ADMIN_USER_INDEX             = "admin__user__index";
    const RESOURCE_ADMIN_USER_WILDCARD          = "admin__user__%";
    const RESOURCE_ADMIN_WILDCARD               = "admin__%__%";

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
        $acl->addResource(self::RESOURCE_ADMIN_CLIENT_WILDCARD);
        $acl->addResource(self::RESOURCE_ADMIN_CLIENT_INDEX);
        $acl->addResource(self::RESOURCE_ADMIN_DEALER_INDEX);
        $acl->addResource(self::RESOURCE_ADMIN_INDEX_INDEX);
        $acl->addResource(self::RESOURCE_ADMIN_LEASINGSCHEMA_INDEX);
        $acl->addResource(self::RESOURCE_ADMIN_LEASINGSCHEMA_WILDCARD);
        $acl->addResource(self::RESOURCE_ADMIN_TONER_WILDCARD);
        $acl->addResource(self::RESOURCE_ADMIN_USER_PROFILE);
        $acl->addResource(self::RESOURCE_ADMIN_USER_INDEX);
        $acl->addResource(self::RESOURCE_ADMIN_USER_WILDCARD);
        $acl->addResource(self::RESOURCE_ADMIN_WILDCARD);
    }

    /**
     * Sets up access to resources for a module
     *
     * @param Application_Model_Acl $acl
     */
    private static function setupAclAccess (Application_Model_Acl $acl)
    {
        // Authenticated users
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_ADMIN_INDEX_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_ADMIN_USER_PROFILE, Application_Model_Acl::PRIVILEGE_VIEW);

        // Quote Admin
//        $acl->allow(Application_Model_Acl::ROLE_QUOTE_ADMIN, self::RESOURCE_ADMIN_TONER_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);

        // System Admin
        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_ADMIN_INDEX_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_ADMIN_USER_WILDCARD, Application_Model_Acl::PRIVILEGE_ADMIN);
        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_ADMIN_CLIENT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_ADMIN_CLIENT_WILDCARD, Application_Model_Acl::PRIVILEGE_ADMIN);
        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_ADMIN_LEASINGSCHEMA_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_ADMIN_LEASINGSCHEMA_WILDCARD, Application_Model_Acl::PRIVILEGE_ADMIN);
    }

}