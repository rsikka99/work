<?php
class Preferences_Model_Acl
{
    /**
     * Preferences Constants
     */
    const RESOURCE_PREFERENCES_WILDCARD        = "preferences__%";
    const RESOURCE_PREFERENCES_INDEX_INDEX     = "preferences__index__index";
    const RESOURCE_PREFERENCES_INDEX_DEALER     = "preferences__index__dealer";
    const RESOURCE_PREFERENCES_PROPOSAL_DEALER = "preferences__proposal__dealer";
    const RESOURCE_PREFERENCES_PROPOSAL_USER   = "preferences__proposal__user";
    const RESOURCE_PREFERENCES_PROPOSAL_SYSTEM = "preferences__proposal__system";
    const RESOURCE_PREFERENCES_QUOTE_DEALER    = "preferences__quote__dealer";
    const RESOURCE_PREFERENCES_QUOTE_USER      = "preferences__quote__user";
    const RESOURCE_PREFERENCES_QUOTE_SYSTEM    = "preferences__quote__system";

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
        /**
         * Preference Resources
         */
        $acl->addResource(self::RESOURCE_PREFERENCES_WILDCARD);
        $acl->addResource(self::RESOURCE_PREFERENCES_INDEX_INDEX);
        $acl->addResource(self::RESOURCE_PREFERENCES_INDEX_DEALER);
        $acl->addResource(self::RESOURCE_PREFERENCES_PROPOSAL_DEALER);
        $acl->addResource(self::RESOURCE_PREFERENCES_PROPOSAL_USER);
        $acl->addResource(self::RESOURCE_PREFERENCES_PROPOSAL_SYSTEM);
        $acl->addResource(self::RESOURCE_PREFERENCES_QUOTE_DEALER);
        $acl->addResource(self::RESOURCE_PREFERENCES_QUOTE_USER);
        $acl->addResource(self::RESOURCE_PREFERENCES_QUOTE_SYSTEM);
    }

    /**
     * Sets up access to resources for a module
     *
     * @param Application_Model_Acl $acl
     */
    private static function setupAclAccess (Application_Model_Acl $acl)
    {
        // Proposal Admin
        $acl->allow(Application_Model_Acl::ROLE_ASSESSMENT_ADMIN, self::RESOURCE_PREFERENCES_INDEX_DEALER, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_ASSESSMENT_ADMIN, self::RESOURCE_PREFERENCES_PROPOSAL_DEALER, Application_Model_Acl::PRIVILEGE_VIEW);

        // Proposal User
        $acl->allow(Application_Model_Acl::ROLE_ASSESSMENT_USER, self::RESOURCE_PREFERENCES_INDEX_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_ASSESSMENT_USER, self::RESOURCE_PREFERENCES_PROPOSAL_USER, Application_Model_Acl::PRIVILEGE_VIEW);

        // Quote Admin
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_ADMIN, self::RESOURCE_PREFERENCES_INDEX_DEALER, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_ADMIN, self::RESOURCE_PREFERENCES_QUOTE_DEALER, Application_Model_Acl::PRIVILEGE_VIEW);

        // Quote User
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_PREFERENCES_INDEX_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_PREFERENCES_QUOTE_USER, Application_Model_Acl::PRIVILEGE_VIEW);
    }

}