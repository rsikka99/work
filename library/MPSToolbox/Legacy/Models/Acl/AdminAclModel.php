<?php

namespace MPSToolbox\Legacy\Models\Acl;

/**
 * Class AdminAclModel
 *
 * @package MPSToolbox\Legacy\Models\Acl
 */
class AdminAclModel
{
    /**
     * Admin Constants
     */
    const RESOURCE_API_WILDCARD                 = "api__%__%";
    const RESOURCE_ADMIN_CLIENT_WILDCARD        = "admin__client__%";
    const RESOURCE_ADMIN_CLIENT_INDEX           = "admin__client__index";
    const RESOURCE_ADMIN_DEALER_INDEX           = "admin__dealer__index";
    const RESOURCE_ADMIN_ONBOARDING_INDEX       = "admin__onboarding__index";
    const RESOURCE_ADMIN_INDEX_INDEX            = "admin__index__index";
    const RESOURCE_ADMIN_LEASINGSCHEMA_INDEX    = "admin__leasingschema__index";
    const RESOURCE_ADMIN_LEASINGSCHEMA_WILDCARD = "admin__leasingschema__%";
    const RESOURCE_ADMIN_TONER_WILDCARD         = "admin__toner__%";
    const RESOURCE_ADMIN_USER_PROFILE           = "admin__user__profile";
    const RESOURCE_ADMIN_USER_INDEX             = "admin__user__index";
    const RESOURCE_ADMIN_USER_WILDCARD          = "admin__user__%";
    const RESOURCE_ADMIN_WILDCARD               = "admin__%__%";

    /**
     * Sets up ACL resources and access for a module
     *
     * @param AppAclModel $acl
     */
    static function setupAcl (AppAclModel $acl)
    {
        self::setupAclResources($acl);
        self::setupAclAccess($acl);
    }

    /**
     * Sets up the resources for a module
     *
     * @param AppAclModel $acl
     */
    private static function setupAclResources (AppAclModel $acl)
    {
        $acl->addResource(self::RESOURCE_API_WILDCARD);
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
     * @param AppAclModel $acl
     */
    private static function setupAclAccess (AppAclModel $acl)
    {
        // Authenticated users
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_ADMIN_INDEX_INDEX, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_ADMIN_USER_PROFILE, AppAclModel::PRIVILEGE_VIEW);

        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_API_WILDCARD, AppAclModel::PRIVILEGE_VIEW);

        // System Admin
        $acl->allow(AppAclModel::ROLE_SYSTEM_ADMIN, self::RESOURCE_ADMIN_INDEX_INDEX, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_SYSTEM_ADMIN, self::RESOURCE_ADMIN_USER_WILDCARD, AppAclModel::PRIVILEGE_ADMIN);
        $acl->allow(AppAclModel::ROLE_SYSTEM_ADMIN, self::RESOURCE_ADMIN_CLIENT_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_SYSTEM_ADMIN, self::RESOURCE_ADMIN_CLIENT_WILDCARD, AppAclModel::PRIVILEGE_ADMIN);
        $acl->allow(AppAclModel::ROLE_SYSTEM_ADMIN, self::RESOURCE_ADMIN_LEASINGSCHEMA_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_SYSTEM_ADMIN, self::RESOURCE_ADMIN_LEASINGSCHEMA_WILDCARD, AppAclModel::PRIVILEGE_ADMIN);

        // Master Device Admin
        $acl->allow(AppAclModel::ROLE_MASTER_DEVICE_ADMINISTRATOR, self::RESOURCE_ADMIN_INDEX_INDEX, AppAclModel::PRIVILEGE_ADMIN);
    }

}