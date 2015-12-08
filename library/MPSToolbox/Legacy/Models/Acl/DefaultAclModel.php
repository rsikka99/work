<?php

namespace MPSToolbox\Legacy\Models\Acl;

/**
 * Class DefaultAclModel
 *
 * @package MPSToolbox\Legacy\Models\Acl
 */
class DefaultAclModel
{
    const RESOURCE_DEFAULT_WILDCARD                 = 'default__%__%';
    const RESOURCE_DEFAULT_AUTH_WILDCARD            = 'default__auth__%';
    const RESOURCE_DEFAULT_ERROR_WILDCARD           = 'default__error__%';
    const RESOURCE_DEFAULT_INFO_WILDCARD            = 'default__info__%';
    const RESOURCE_DEFAULT_INDEX_WILDCARD           = 'default__index__%';
    const RESOURCE_DEFAULT_INDEX_INDEX              = 'default__index__index';
    const RESOURCE_DEFAULT_INDEX_CREATECLIENT       = 'default__index__createClient';
    const RESOURCE_DEFAULT_INDEX_EDITCLIENT         = 'default__index__editClient';
    const RESOURCE_DEFAULT_INDEX_SEARCHCLIENT       = 'default__index__search-for-client';
    const RESOURCE_DEFAULT_INDEX_VIEWCLIENTS        = 'default__index__view-all-clients';
    const RESOURCE_DEFAULT_AUTH_LOGIN               = 'default__auth__login';
    const RESOURCE_DEFAULT_AUTH_LOGOUT              = 'default__auth__logout';
    const RESOURCE_DEFAULT_AUTH_FORGOTPASSWORD      = 'default__auth__forgot-password';
    const RESOURCE_DEFAULT_AUTH_FORGOTPASSWORDRESET = 'default__auth__forgot-password-reset';
    const RESOURCE_DEFAULT_AUTH_RESETPASSWORD       = 'default__auth__reset-password';
    const RESOURCE_DEFAULT_CRON_WILDCARD            = 'default__cron__%';
    const RESOURCE_DEFAULT_WEBHOOK_WILDCARD         = 'default__webhook__%';

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
        $acl->addResource(self::RESOURCE_DEFAULT_AUTH_FORGOTPASSWORDRESET);
        $acl->addResource(self::RESOURCE_DEFAULT_AUTH_RESETPASSWORD);
        $acl->addResource(self::RESOURCE_DEFAULT_CRON_WILDCARD);
        $acl->addResource(self::RESOURCE_DEFAULT_WEBHOOK_WILDCARD);
    }

    /**
     * Sets up access to resources for a module
     *
     * @param AppAclModel $acl
     */
    private static function setupAclAccess (AppAclModel $acl)
    {
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_AUTH_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_ERROR_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_INFO_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_INDEX_INDEX, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_INDEX_SEARCHCLIENT, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_INDEX_VIEWCLIENTS, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_GUEST, self::RESOURCE_DEFAULT_AUTH_LOGIN, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_GUEST, self::RESOURCE_DEFAULT_AUTH_FORGOTPASSWORD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_GUEST, self::RESOURCE_DEFAULT_AUTH_FORGOTPASSWORDRESET, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_GUEST, self::RESOURCE_DEFAULT_AUTH_RESETPASSWORD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_DEFAULT_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_COMPANY_ADMINISTRATOR, self::RESOURCE_DEFAULT_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_SYSTEM_ADMIN, self::RESOURCE_DEFAULT_WILDCARD, AppAclModel::PRIVILEGE_VIEW);

        $acl->allow(AppAclModel::ROLE_GUEST, self::RESOURCE_DEFAULT_CRON_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_GUEST, self::RESOURCE_DEFAULT_WEBHOOK_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
    }

}