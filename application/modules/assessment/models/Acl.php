<?php
class Assessment_Model_Acl
{


    const RESOURCE_ASSESSMENT_INDEX_WILDCARD = "assessment__index__%";


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
        $acl->addResource(self::RESOURCE_ASSESSMENT_INDEX_WILDCARD);

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
        $acl->allow(Application_Model_Acl::ROLE_ASSESSMENT_USER, self::RESOURCE_ASSESSMENT_INDEX_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
    }
}