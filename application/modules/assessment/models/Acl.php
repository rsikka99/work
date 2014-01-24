<?php

/**
 * Class Assessment_Model_Acl
 */
class Assessment_Model_Acl
{
    const RESOURCE_ASSESSMENT_WILDCARD = "assessment__%__%";

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
        $acl->addResource(self::RESOURCE_ASSESSMENT_WILDCARD);

    }

    /**
     * Sets up access to resources for a module
     *
     * @param Application_Model_Acl $acl
     */
    private static function setupAclAccess (Application_Model_Acl $acl)
    {
        /**
         * Any logged in user
         */
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_ASSESSMENT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
    }
}