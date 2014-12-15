<?php

namespace MPSToolbox\Legacy\Models\Acl;

/**
 * Class HealthCheckAclModel
 *
 * @package MPSToolbox\Legacy\Models\Acl
 */
class HealthCheckAclModel
{
    const RESOURCE_HEALTHCHECK_INDEX_WILDCARD     = "healthcheck__index__%";
    const RESOURCE_HEALTHCHECK_REPORT_INDEX       = "healthcheck__report_index__%";
    const RESOURCE_HEALTHCHECK_REPORT_HEALTHCHECK = "healthcheck__report_healthcheck__%";
    const RESOURCE_HEALTHCHECK_REPORT_PRINTIQ     = "healthcheck__report_printiq_healthcheck__%";

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
        $acl->addResource(self::RESOURCE_HEALTHCHECK_INDEX_WILDCARD);
        $acl->addResource(self::RESOURCE_HEALTHCHECK_REPORT_INDEX);
        $acl->addResource(self::RESOURCE_HEALTHCHECK_REPORT_HEALTHCHECK);
        $acl->addResource(self::RESOURCE_HEALTHCHECK_REPORT_PRINTIQ);

    }

    /**
     * Sets up access to resources for a module
     *
     * @param AppAclModel $acl
     */
    private static function setupAclAccess (AppAclModel $acl)
    {
        /**
         * Healthcheck user
         */
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_HEALTHCHECK_INDEX_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_HEALTHCHECK_REPORT_INDEX, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_HEALTHCHECK_REPORT_PRINTIQ, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_HEALTHCHECK_REPORT_HEALTHCHECK, AppAclModel::PRIVILEGE_VIEW);
    }
}