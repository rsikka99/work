<?php

/**
 * Class Default_Model_Acl
 */
class Default_Model_Acl
{
    const RESOURCE_DEFAULT_WILDCARD            = "default__%__%";
    const RESOURCE_DEFAULT_AUTH_WILDCARD       = "default__auth__%";
    const RESOURCE_DEFAULT_ERROR_WILDCARD      = "default__error__%";
    const RESOURCE_DEFAULT_INFO_WILDCARD       = "default__info__%";
    const RESOURCE_DEFAULT_INDEX_WILDCARD      = "default__index__%";
    const RESOURCE_DEFAULT_INDEX_INDEX         = "default__index__index";
    const RESOURCE_DEFAULT_INDEX_CREATECLIENT  = "default__index__createClient";
    const RESOURCE_DEFAULT_INDEX_EDITCLIENT    = "default__index__editClient";
    const RESOURCE_DEFAULT_INDEX_SEARCHCLIENT  = "default__index__search-for-client";
    const RESOURCE_DEFAULT_INDEX_VIEWCLIENTS   = "default__index__view-all-clients";
    const RESOURCE_DEFAULT_AUTH_LOGIN          = "default__auth__login";
    const RESOURCE_DEFAULT_AUTH_LOGOUT         = "default__auth__logout";
    const RESOURCE_DEFAULT_AUTH_FORGOTPASSWORD = "default__auth__forgotpassword";
    const RESOURCE_DEFAULT_AUTH_RESETPASSWORD  = "default__auth__resetpassword";


    /**
     * Sets up ACL resources and access for a module
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
        $acl->addResource(self::RESOURCE_DEFAULT_AUTH_WILDCARD);
        $acl->addResource(self::RESOURCE_DEFAULT_ERROR_WILDCARD);
        $acl->addResource(self::RESOURCE_DEFAULT_INFO_WILDCARD);
        $acl->addResource(self::RESOURCE_DEFAULT_INDEX_WILDCARD);
        $acl->addResource(self::RESOURCE_DEFAULT_INDEX_INDEX);
        $acl->addResource(self::RESOURCE_DEFAULT_INDEX_CREATECLIENT);
        $acl->addResource(self::RESOURCE_DEFAULT_INDEX_EDITCLIENT);
        $acl->addResource(self::RESOURCE_DEFAULT_INDEX_SEARCHCLIENT);
        $acl->addResource(self::RESOURCE_DEFAULT_INDEX_VIEWCLIENTS);
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
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_AUTH_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_ERROR_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_INFO_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_INDEX_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_INDEX_SEARCHCLIENT, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_INDEX_VIEWCLIENTS, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_GUEST, self::RESOURCE_DEFAULT_AUTH_LOGIN, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_GUEST, self::RESOURCE_DEFAULT_AUTH_FORGOTPASSWORD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_GUEST, self::RESOURCE_DEFAULT_AUTH_RESETPASSWORD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_DEFAULT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_COMPANY_ADMINISTRATOR, self::RESOURCE_DEFAULT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_DEFAULT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
    }

}