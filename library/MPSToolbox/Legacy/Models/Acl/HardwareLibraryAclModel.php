<?php

namespace MPSToolbox\Legacy\Models\Acl;

/**
 * Class HardwareLibraryAclModel
 *
 * @package MPSToolbox\Legacy\Models\Acl
 */
class HardwareLibraryAclModel
{

    const RESOURCE_HARDWARE_LIBRARY_INDEX_INDEX      = 'hardware-library__index__index';
    const RESOURCE_HARDWARE_LIBRARY_QUOTEINDEX_INDEX = 'hardware-library__quote_index__index';
    const RESOURCE_HARDWARE_LIBRARY_TONER_WILDCARD   = 'hardware-library__toner__%';
    const RESOURCE_HARDWARE_LIBRARY_TONERS_WILDCARD  = 'hardware-library__toners__%';
    const RESOURCE_HARDWARE_LIBRARY_OPTION_WILDCARD  = 'hardware-library__option__%';


    const RESOURCE_HARDWARE_LIBRARY_DEVICES_WILDCARD        = 'hardware-library__devices__%';
    const RESOURCE_HARDWARE_LIBRARY_COMPUTERS_WILDCARD        = 'hardware-library__computers__%';
    const RESOURCE_HARDWARE_LIBRARY_MANAGE_DEVICES_WILDCARD = 'hardware-library__manage-devices__%';

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
        $acl->addResource(self::RESOURCE_HARDWARE_LIBRARY_DEVICES_WILDCARD);
        $acl->addResource(self::RESOURCE_HARDWARE_LIBRARY_COMPUTERS_WILDCARD);
        $acl->addResource(self::RESOURCE_HARDWARE_LIBRARY_MANAGE_DEVICES_WILDCARD);
        $acl->addResource(self::RESOURCE_HARDWARE_LIBRARY_INDEX_INDEX);
        $acl->addResource(self::RESOURCE_HARDWARE_LIBRARY_QUOTEINDEX_INDEX);
        $acl->addResource(self::RESOURCE_HARDWARE_LIBRARY_TONER_WILDCARD);
        $acl->addResource(self::RESOURCE_HARDWARE_LIBRARY_OPTION_WILDCARD);
        $acl->addResource(self::RESOURCE_HARDWARE_LIBRARY_TONERS_WILDCARD);
    }

    /**
     * Sets up access to resources for a module
     *
     * @param AppAclModel $acl
     */
    private static function setupAclAccess (AppAclModel $acl)
    {
        // Hardware Admin
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_HARDWARE_LIBRARY_INDEX_INDEX, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_HARDWARE_LIBRARY_QUOTEINDEX_INDEX, AppAclModel::PRIVILEGE_VIEW);


        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_HARDWARE_LIBRARY_DEVICES_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_HARDWARE_LIBRARY_COMPUTERS_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_HARDWARE_LIBRARY_MANAGE_DEVICES_WILDCARD, AppAclModel::PRIVILEGE_VIEW);

        /**
         * Normal user
         */
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_HARDWARE_LIBRARY_INDEX_INDEX, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_HARDWARE_LIBRARY_OPTION_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_HARDWARE_LIBRARY_TONER_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_HARDWARE_LIBRARY_TONERS_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
    }
}