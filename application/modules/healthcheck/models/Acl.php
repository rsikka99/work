<?php
/**
 * Class Healthcheck_Model_Acl
 */
class Healthcheck_Model_Acl
{


    const RESOURCE_HEALTHCHECK_INDEX_WILDCARD = "healthcheck__index__%";


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
        $acl->addResource(self::RESOURCE_HEALTHCHECK_INDEX_WILDCARD);

    }

    /**
     * Sets up access to resources for a module
     *
     * @param Application_Model_Acl $acl
     */
    private static function setupAclAccess (Application_Model_Acl $acl)
    {
        /**
         * Healthcheck user
         */
        $acl->allow(Application_Model_Acl::ROLE_HEALTHCHECK_USER, self::RESOURCE_HEALTHCHECK_INDEX_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
    }
}