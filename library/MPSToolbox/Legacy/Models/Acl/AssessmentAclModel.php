<?php

namespace MPSToolbox\Legacy\Models\Acl;

/**
 * Class AssessmentAclModel
 *
 * @package MPSToolbox\Legacy\Models\Acl
 */
class AssessmentAclModel
{
    const RESOURCE_ASSESSMENT_WILDCARD = 'assessment__%__%';

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
        $acl->addResource(self::RESOURCE_ASSESSMENT_WILDCARD);

    }

    /**
     * Sets up access to resources for a module
     *
     * @param AppAclModel $acl
     */
    private static function setupAclAccess (AppAclModel $acl)
    {
        /**
         * Any logged in user
         */
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_ASSESSMENT_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
    }
}