<?php
/**
 * Class Preferences_Model_Acl
 */
class Preferences_Model_Acl
{
    /**
     * Preferences Constants
     */
    const RESOURCE_PREFERENCES_WILDCARD                    = "preferences__%";
    const RESOURCE_PREFERENCES_INDEX_INDEX                 = "preferences__index__index";
    const RESOURCE_PREFERENCES_INDEX_USER                  = "preferences__index__user";
    const RESOURCE_PREFERENCES_INDEX_DEALER                = "preferences__index__dealer";
    const RESOURCE_PREFERENCES_INDEX_SYSTEM                = "preferences__index__system";
    const RESOURCE_PREFERENCES_PROPOSAL_DEALER             = "preferences__proposal__dealer";
    const RESOURCE_PREFERENCES_PROPOSAL_USER               = "preferences__proposal__user";
    const RESOURCE_PREFERENCES_PROPOSAL_SYSTEM             = "preferences__proposal__system";
    const RESOURCE_PREFERENCES_QUOTE_DEALER                = "preferences__quote__dealer";
    const RESOURCE_PREFERENCES_QUOTE_USER                  = "preferences__quote__user";
    const RESOURCE_PREFERENCES_QUOTE_SYSTEM                = "preferences__quote__system";
    const RESOURCE_PREFERENCES_HEALTHCHECK_SYSTEM          = "preferences__healthcheck__system";
    const RESOURCE_PREFERENCES_HEALTHCHECK_USER            = "preferences__healthcheck__user";
    const RESOURCE_PREFERENCES_HEALTHCHECK_DEALER          = "preferences__healthcheck__dealer";
    const RESOURCE_PREFERENCES_HARDWAREOPTIMIZATION_SYSTEM = "preferences__hardwareoptimization__system";
    const RESOURCE_PREFERENCES_HARDWAREOPTIMIZATION_USER   = "preferences__hardwareoptimization__user";
    const RESOURCE_PREFERENCES_HARDWAREOPTIMIZATION_DEALER = "preferences__hardwareoptimization__dealer";
    const RESOURCE_PREFERENCES_MEMJETOPTIMIZATION_SYSTEM   = "preferences__memjet_optimization__system";
    const RESOURCE_PREFERENCES_MEMJETOPTIMIZATION_USER     = "preferences__memjet_optimization__user";
    const RESOURCE_PREFERENCES_MEMJETOPTIMIZATION_DEALER   = "preferences__memjet_optimization__dealer";

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
        $acl->addResource(self::RESOURCE_PREFERENCES_INDEX_USER);
        $acl->addResource(self::RESOURCE_PREFERENCES_INDEX_DEALER);
        $acl->addResource(self::RESOURCE_PREFERENCES_INDEX_SYSTEM);
        $acl->addResource(self::RESOURCE_PREFERENCES_PROPOSAL_DEALER);
        $acl->addResource(self::RESOURCE_PREFERENCES_PROPOSAL_USER);
        $acl->addResource(self::RESOURCE_PREFERENCES_PROPOSAL_SYSTEM);
        $acl->addResource(self::RESOURCE_PREFERENCES_QUOTE_DEALER);
        $acl->addResource(self::RESOURCE_PREFERENCES_QUOTE_USER);
        $acl->addResource(self::RESOURCE_PREFERENCES_QUOTE_SYSTEM);
        $acl->addResource(self::RESOURCE_PREFERENCES_HEALTHCHECK_SYSTEM);
        $acl->addResource(self::RESOURCE_PREFERENCES_HEALTHCHECK_USER);
        $acl->addResource(self::RESOURCE_PREFERENCES_HEALTHCHECK_DEALER);
        $acl->addResource(self::RESOURCE_PREFERENCES_HARDWAREOPTIMIZATION_SYSTEM);
        $acl->addResource(self::RESOURCE_PREFERENCES_HARDWAREOPTIMIZATION_USER);
        $acl->addResource(self::RESOURCE_PREFERENCES_HARDWAREOPTIMIZATION_DEALER);
        $acl->addResource(self::RESOURCE_PREFERENCES_MEMJETOPTIMIZATION_SYSTEM);
        $acl->addResource(self::RESOURCE_PREFERENCES_MEMJETOPTIMIZATION_USER);
        $acl->addResource(self::RESOURCE_PREFERENCES_MEMJETOPTIMIZATION_DEALER);
    }

    /**
     * Sets up access to resources for a module
     *
     * @param Application_Model_Acl $acl
     */
    private static function setupAclAccess (Application_Model_Acl $acl)
    {
        // Proposal Admin
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PREFERENCES_INDEX_DEALER, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PREFERENCES_PROPOSAL_DEALER, Application_Model_Acl::PRIVILEGE_VIEW);

        // Proposal User
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PREFERENCES_INDEX_USER, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PREFERENCES_INDEX_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PREFERENCES_PROPOSAL_USER, Application_Model_Acl::PRIVILEGE_VIEW);

        // Quote Admin
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PREFERENCES_INDEX_DEALER, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PREFERENCES_QUOTE_DEALER, Application_Model_Acl::PRIVILEGE_VIEW);

        // Quote User
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PREFERENCES_INDEX_USER, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PREFERENCES_INDEX_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PREFERENCES_QUOTE_USER, Application_Model_Acl::PRIVILEGE_VIEW);

        // Healthcheck User
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PREFERENCES_HEALTHCHECK_USER, Application_Model_Acl::PRIVILEGE_VIEW);

        // Healthcheck Administrator
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PREFERENCES_HEALTHCHECK_DEALER, Application_Model_Acl::PRIVILEGE_VIEW);

        // Hardware Optimization User
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PREFERENCES_HARDWAREOPTIMIZATION_USER, Application_Model_Acl::PRIVILEGE_VIEW);

        // Hardware Optimization Administrator
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PREFERENCES_HARDWAREOPTIMIZATION_DEALER, Application_Model_Acl::PRIVILEGE_VIEW);

        // Memjet Optimization
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PREFERENCES_MEMJETOPTIMIZATION_USER, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PREFERENCES_MEMJETOPTIMIZATION_DEALER, Application_Model_Acl::PRIVILEGE_VIEW);

        // System Admin
        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_PREFERENCES_HEALTHCHECK_SYSTEM, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_PREFERENCES_QUOTE_SYSTEM, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_PREFERENCES_PROPOSAL_SYSTEM, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_PREFERENCES_HARDWAREOPTIMIZATION_SYSTEM, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_PREFERENCES_MEMJETOPTIMIZATION_SYSTEM, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_PREFERENCES_INDEX_SYSTEM, Application_Model_Acl::PRIVILEGE_VIEW);

    }

}