<?php
class Dealermanagement_Model_Acl
{
    const RESOURCE_DEALERMANAGEMENT_WILDCARD               = "dealermanagement__%__%";
    const RESOURCE_DEALERMANAGEMENT_CLIENT_WILDCARD        = "dealermanagement__client__%";
    const RESOURCE_DEALERMANAGEMENT_CLIENT_INDEX        = "dealermanagement__client__index";
    const RESOURCE_DEALERMANAGEMENT_CLIENT_CREATE        = "dealermanagement__client__create";
    const RESOURCE_DEALERMANAGEMENT_CLIENT_EDIT        = "dealermanagement__client__edit";
    const RESOURCE_DEALERMANAGEMENT_CLIENT_VIEW        = "dealermanagement__client__view";
    const RESOURCE_DEALERMANAGEMENT_CLIENT_DELETE        = "dealermanagement__client__delete";
    const RESOURCE_DEALERMANAGEMENT_INDEX                  = "dealermanagement__index__index";
    const RESOURCE_DEALERMANAGEMENT_LEASINGSCHEMA_WILDCARD = "dealermanagement__leasingschema__%";
    const RESOURCE_DEALERMANAGEMENT_USER_WILDCARD          = "dealermanagement__user__%";
    const RESOURCE_DEALERMANAGEMENT_DEALER_WILDCARD          = "dealermanagement__dealer__%";

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
        $acl->addResource(self::RESOURCE_DEALERMANAGEMENT_WILDCARD);
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
     * @param Application_Model_Acl $acl
     */
    private static function setupAclAccess (Application_Model_Acl $acl)
    {
        /*
         * Assessment User
         */
        $acl->allow(Application_Model_Acl::ROLE_ASSESSMENT_USER, self::RESOURCE_DEALERMANAGEMENT_CLIENT_VIEW, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_ASSESSMENT_USER, self::RESOURCE_DEALERMANAGEMENT_CLIENT_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_ASSESSMENT_USER, self::RESOURCE_DEALERMANAGEMENT_CLIENT_EDIT, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_ASSESSMENT_USER, self::RESOURCE_DEALERMANAGEMENT_CLIENT_CREATE, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_ASSESSMENT_USER, self::RESOURCE_DEALERMANAGEMENT_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);

        /*
         * Quote User
         */
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_DEALERMANAGEMENT_CLIENT_VIEW, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_DEALERMANAGEMENT_CLIENT_EDIT, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_DEALERMANAGEMENT_CLIENT_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_DEALERMANAGEMENT_CLIENT_CREATE, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_DEALERMANAGEMENT_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);

        // Client Admin
        $acl->allow(Application_Model_Acl::ROLE_CLIENT_ADMIN, self::RESOURCE_DEALERMANAGEMENT_CLIENT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);

        // Dealer Admin
        $acl->allow(Application_Model_Acl::ROLE_DEALER_ADMIN, self::RESOURCE_DEALERMANAGEMENT_USER_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_DEALER_ADMIN, self::RESOURCE_DEALERMANAGEMENT_DEALER_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);

        // Lease Rate Admin
        $acl->allow(Application_Model_Acl::ROLE_LEASERATE_ADMIN, self::RESOURCE_DEALERMANAGEMENT_LEASINGSCHEMA_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_LEASERATE_ADMIN, self::RESOURCE_DEALERMANAGEMENT_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
    }

}