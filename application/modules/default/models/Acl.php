<?php
class Default_Model_Acl
{
    const RESOURCE_DEFAULT_WILDCARD            = "default__%__%";
    const RESOURCE_DEFAULT_AUTH_LOGIN          = "default__auth__login";
    const RESOURCE_DEFAULT_AUTH_LOGOUT         = "default__auth__logout";
    const RESOURCE_DEFAULT_AUTH_FORGOTPASSWORD = "default__auth__forgotpassword";
    const RESOURCE_DEFAULT_AUTH_RESETPASSWORD  = "default__auth__resetpassword";


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
        $acl->addResource(self::RESOURCE_DEFAULT_WILDCARD);
        $acl->addResource(self::RESOURCE_DEFAULT_AUTH_LOGIN);
        $acl->addResource(self::RESOURCE_DEFAULT_AUTH_LOGOUT);
        $acl->addResource(self::RESOURCE_DEFAULT_AUTH_FORGOTPASSWORD);
        $acl->addResource(self::RESOURCE_DEFAULT_AUTH_RESETPASSWORD);
    }

    /**
     * Sets up access to resources for a module
     *
     * @param Application_Model_Acl $acl
     */
    private static function setupAclAccess (Application_Model_Acl $acl)
    {
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_GUEST, self::RESOURCE_DEFAULT_AUTH_LOGIN, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_GUEST, self::RESOURCE_DEFAULT_AUTH_FORGOTPASSWORD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_GUEST, self::RESOURCE_DEFAULT_AUTH_RESETPASSWORD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_DEFAULT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_ADMIN, self::RESOURCE_DEFAULT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_DEFAULT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_DEALER_ADMIN, self::RESOURCE_DEFAULT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_DEFAULT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
    }

}