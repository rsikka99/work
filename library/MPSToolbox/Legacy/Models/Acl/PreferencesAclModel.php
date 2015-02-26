<?php

namespace MPSToolbox\Legacy\Models\Acl;

use MPSToolbox\Legacy\Models\Acl\AppAclModel;

/**
 * Class PreferencesAclModel
 *
 * @package MPSToolbox\Legacy\Modules\Preferences\Models
 */
class PreferencesAclModel
{
    /**
     * Preferences Constants
     */
    const RESOURCE_PREFERENCES_WILDCARD        = 'preferences__%__%';
    const RESOURCE_PREFERENCES_CLIENT_WILDCARD = 'preferences__client__%';
    const RESOURCE_PREFERENCES_DEALER_WILDCARD = 'preferences__dealer__%';
    const RESOURCE_PREFERENCES_USER_WILDCARD   = 'preferences__user__%';

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
        /**
         * Preference Resources
         */
        $acl->addResource(self::RESOURCE_PREFERENCES_WILDCARD);
        $acl->addResource(self::RESOURCE_PREFERENCES_CLIENT_WILDCARD);
        $acl->addResource(self::RESOURCE_PREFERENCES_DEALER_WILDCARD);
        $acl->addResource(self::RESOURCE_PREFERENCES_USER_WILDCARD);
    }

    /**
     * Sets up access to resources for a module
     *
     * @param AppAclModel $acl
     */
    private static function setupAclAccess (AppAclModel $acl)
    {
        // Everyone can view settings
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PREFERENCES_WILDCARD, AppAclModel::PRIVILEGE_VIEW);

        // Only company admins can edit their dealer settings
        $acl->allow(AppAclModel::ROLE_COMPANY_ADMINISTRATOR, self::RESOURCE_PREFERENCES_DEALER_WILDCARD, AppAclModel::PRIVILEGE_EDIT);

        /**
         * Normal Users
         */
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PREFERENCES_USER_WILDCARD, AppAclModel::PRIVILEGE_EDIT);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PREFERENCES_CLIENT_WILDCARD, AppAclModel::PRIVILEGE_EDIT);
    }

}