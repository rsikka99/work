<?php

namespace MPSToolbox\Legacy\Models\Acl;

/**
 * Class DealerManagementAclModel
 *
 * @package MPSToolbox\Legacy\Models\Acl
 */
class DealerManagementAclModel
{
    const RESOURCE_DEALERMANAGEMENT_WILDCARD               = "dealermanagement__%__%";
    const RESOURCE_DEALERMANAGEMENT_BRANDING_WILDCARD      = "dealermanagement__branding__%";
    const RESOURCE_DEALERMANAGEMENT_CLIENT_WILDCARD        = "dealermanagement__client__%";
    const RESOURCE_DEALERMANAGEMENT_CLIENT_INDEX           = "dealermanagement__client__index";
    const RESOURCE_DEALERMANAGEMENT_CLIENT_CREATE          = "dealermanagement__client__create";
    const RESOURCE_DEALERMANAGEMENT_CLIENT_EDIT            = "dealermanagement__client__edit";
    const RESOURCE_DEALERMANAGEMENT_CLIENT_VIEW            = "dealermanagement__client__view";
    const RESOURCE_DEALERMANAGEMENT_CLIENT_DELETE          = "dealermanagement__client__delete";
    const RESOURCE_DEALERMANAGEMENT_INDEX                  = "dealermanagement__index__index";
    const RESOURCE_DEALERMANAGEMENT_LEASINGSCHEMA_WILDCARD = "dealermanagement__leasingschema__%";
    const RESOURCE_DEALERMANAGEMENT_USER_WILDCARD          = "dealermanagement__user__%";
    const RESOURCE_DEALERMANAGEMENT_DEALER_WILDCARD        = "dealermanagement__dealer__%";

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
        $acl->addResource(self::RESOURCE_DEALERMANAGEMENT_WILDCARD);
        $acl->addResource(self::RESOURCE_DEALERMANAGEMENT_BRANDING_WILDCARD);
        $acl->addResource(self::RESOURCE_DEALERMANAGEMENT_CLIENT_WILDCARD);
        $acl->addResource(self::RESOURCE_DEALERMANAGEMENT_CLIENT_INDEX);
        $acl->addResource(self::RESOURCE_DEALERMANAGEMENT_CLIENT_CREATE);
        $acl->addResource(self::RESOURCE_DEALERMANAGEMENT_CLIENT_EDIT);
        $acl->addResource(self::RESOURCE_DEALERMANAGEMENT_CLIENT_VIEW);
        $acl->addResource(self::RESOURCE_DEALERMANAGEMENT_CLIENT_DELETE);
        $acl->addResource(self::RESOURCE_DEALERMANAGEMENT_INDEX);
        $acl->addResource(self::RESOURCE_DEALERMANAGEMENT_LEASINGSCHEMA_WILDCARD);
        $acl->addResource(self::RESOURCE_DEALERMANAGEMENT_USER_WILDCARD);
        $acl->addResource(self::RESOURCE_DEALERMANAGEMENT_DEALER_WILDCARD);
    }

    /**
     * Sets up access to resources for a module
     *
     * @param AppAclModel $acl
     */
    private static function setupAclAccess (AppAclModel $acl)
    {

        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEALERMANAGEMENT_CLIENT_VIEW, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEALERMANAGEMENT_CLIENT_EDIT, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEALERMANAGEMENT_CLIENT_INDEX, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEALERMANAGEMENT_CLIENT_CREATE, AppAclModel::PRIVILEGE_VIEW);

        // Client Admin
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_DEALERMANAGEMENT_CLIENT_WILDCARD, AppAclModel::PRIVILEGE_VIEW);

        // Dealer Admin
        $acl->allow(AppAclModel::ROLE_COMPANY_ADMINISTRATOR, self::RESOURCE_DEALERMANAGEMENT_USER_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_COMPANY_ADMINISTRATOR, self::RESOURCE_DEALERMANAGEMENT_DEALER_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_COMPANY_ADMINISTRATOR, self::RESOURCE_DEALERMANAGEMENT_BRANDING_WILDCARD, AppAclModel::PRIVILEGE_VIEW);

        // Lease Rate Admin
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_DEALERMANAGEMENT_LEASINGSCHEMA_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_DEALERMANAGEMENT_INDEX, AppAclModel::PRIVILEGE_VIEW);
    }

}