<?php
class Quotegen_Model_Acl
{

    const RESOURCE_QUOTEGEN_CLIENT_WILDCARD                   = "quotegen__client__%";
    const RESOURCE_QUOTEGEN_CONFIGURATION_INDEX               = "quotegen__configuration__index";
    const RESOURCE_QUOTEGEN_CONFIGURATION_WILDCARD            = "quotegen__configuration__%";
    const RESOURCE_QUOTEGEN_DEVICESETUP_ALLDEVICESLIST        = "quotegen__devicesetup__all-devices-list";
    const RESOURCE_QUOTEGEN_DEVICESETUP_CONFIGURATIONS        = "quotegen__devicesetup__configurations";
    const RESOURCE_QUOTEGEN_DEVICESETUP_CREATE                = "quotegen__devicesetup__create";
    const RESOURCE_QUOTEGEN_DEVICESETUP_EDIT                  = "quotegen__devicesetup__edit";
    const RESOURCE_QUOTEGEN_DEVICESETUP_DELETE                = "quotegen__devicesetup__delete";
    const RESOURCE_QUOTEGEN_DEVICESETUP_INDEX                 = "quotegen__devicesetup__index";
    const RESOURCE_QUOTEGEN_DEVICESETUP_OPTIONS               = "quotegen__devicesetup__options";
    const RESOURCE_QUOTEGEN_DEVICESETUP_WILDCARD              = "quotegen__devicesetup__%";
    const RESOURCE_QUOTEGEN_CATEGORY_WILDCARD                 = "quotegen__category__%";
    const RESOURCE_QUOTEGEN_INDEX                             = "quotegen__index__index";
    const RESOURCE_QUOTEGEN_INDEX_EXISTINGQUOTE               = "quotegen__index__existing-quote";
    const RESOURCE_QUOTEGEN_INDEX_GETREPORTSFORCLIENT         = "quotegen__index__get-reports-for-client";
    const RESOURCE_QUOTEGEN_INDEX_CREATECLIENT                = "quotegen__index__create-client";
    const RESOURCE_QUOTEGEN_OPTION_INDEX                      = "quotegen__option__index";
    const RESOURCE_QUOTEGEN_OPTION_WILDCARD                   = "quotegen__option__%";
    const RESOURCE_QUOTEGEN_QUOTE_INDEX                       = "quotegen__quote__index";
    const RESOURCE_QUOTEGEN_QUOTE_DELETE                      = "quotegen__quote__delete";
    const RESOURCE_QUOTEGEN_QUOTEDEVICES_WILDCARD             = "quotegen__quote_devices__%";
    const RESOURCE_QUOTEGEN_QUOTEGROUPS_INDEX                 = "quotegen__quote_groups__index";
    const RESOURCE_QUOTEGEN_QUOTEPAGES_INDEX                  = "quotegen__quote_pages__index";
    const RESOURCE_QUOTEGEN_QUOTEPROFITABILITY_INDEX          = "quotegen__quote_profitability__index";
    const RESOURCE_QUOTEGEN_QUOTEPROFITABILITY_LEASINGDETAILS = "quotegen__quote_profitability__leasingdetails";
    const RESOURCE_QUOTEGEN_QUOTEREPORTS_WILDCARD             = "quotegen__quote_reports__%";
    const RESOURCE_QUOTEGEN_WILDCARD                          = "quotegen__%__%";

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
        $acl->addResource(self::RESOURCE_QUOTEGEN_CLIENT_WILDCARD);
        $acl->addResource(self::RESOURCE_QUOTEGEN_CONFIGURATION_INDEX);
        $acl->addResource(self::RESOURCE_QUOTEGEN_CONFIGURATION_WILDCARD);
        $acl->addResource(self::RESOURCE_QUOTEGEN_DEVICESETUP_ALLDEVICESLIST);
        $acl->addResource(self::RESOURCE_QUOTEGEN_DEVICESETUP_CONFIGURATIONS);
        $acl->addResource(self::RESOURCE_QUOTEGEN_DEVICESETUP_CREATE);
        $acl->addResource(self::RESOURCE_QUOTEGEN_DEVICESETUP_EDIT);
        $acl->addResource(self::RESOURCE_QUOTEGEN_DEVICESETUP_DELETE);
        $acl->addResource(self::RESOURCE_QUOTEGEN_DEVICESETUP_INDEX);
        $acl->addResource(self::RESOURCE_QUOTEGEN_DEVICESETUP_OPTIONS);
        $acl->addResource(self::RESOURCE_QUOTEGEN_DEVICESETUP_WILDCARD);
        $acl->addResource(self::RESOURCE_QUOTEGEN_CATEGORY_WILDCARD);
        $acl->addResource(self::RESOURCE_QUOTEGEN_INDEX);
        $acl->addResource(self::RESOURCE_QUOTEGEN_INDEX_EXISTINGQUOTE);
        $acl->addResource(self::RESOURCE_QUOTEGEN_INDEX_GETREPORTSFORCLIENT);
        $acl->addResource(self::RESOURCE_QUOTEGEN_INDEX_CREATECLIENT);
        $acl->addResource(self::RESOURCE_QUOTEGEN_OPTION_INDEX);
        $acl->addResource(self::RESOURCE_QUOTEGEN_OPTION_WILDCARD);
        $acl->addResource(self::RESOURCE_QUOTEGEN_QUOTE_INDEX);
        $acl->addResource(self::RESOURCE_QUOTEGEN_QUOTE_DELETE);
        $acl->addResource(self::RESOURCE_QUOTEGEN_QUOTEDEVICES_WILDCARD);
        $acl->addResource(self::RESOURCE_QUOTEGEN_QUOTEGROUPS_INDEX);
        $acl->addResource(self::RESOURCE_QUOTEGEN_QUOTEPAGES_INDEX);
        $acl->addResource(self::RESOURCE_QUOTEGEN_QUOTEPROFITABILITY_INDEX);
        $acl->addResource(self::RESOURCE_QUOTEGEN_QUOTEPROFITABILITY_LEASINGDETAILS);
        $acl->addResource(self::RESOURCE_QUOTEGEN_QUOTEREPORTS_WILDCARD);
        $acl->addResource(self::RESOURCE_QUOTEGEN_WILDCARD);
    }

    /**
     * Sets up access to resources for a module
     *
     * @param Application_Model_Acl $acl
     */
    private static function setupAclAccess (Application_Model_Acl $acl)
    {
        //Quote User
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_QUOTEDEVICES_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_QUOTEGROUPS_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_QUOTEPAGES_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_QUOTEPROFITABILITY_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_QUOTEPROFITABILITY_LEASINGDETAILS, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_QUOTEREPORTS_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_INDEX_EXISTINGQUOTE, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_INDEX_GETREPORTSFORCLIENT, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_INDEX_CREATECLIENT, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_QUOTE_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_QUOTE_DELETE, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_CLIENT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_CONFIGURATION_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);

        //Hardware Admin
        $acl->allow(Application_Model_Acl::ROLE_HARDWARE_ADMIN, self::RESOURCE_QUOTEGEN_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_HARDWARE_ADMIN, self::RESOURCE_QUOTEGEN_DEVICESETUP_ALLDEVICESLIST, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_HARDWARE_ADMIN, self::RESOURCE_QUOTEGEN_DEVICESETUP_CONFIGURATIONS, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_HARDWARE_ADMIN, self::RESOURCE_QUOTEGEN_DEVICESETUP_EDIT, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_HARDWARE_ADMIN, self::RESOURCE_QUOTEGEN_DEVICESETUP_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_HARDWARE_ADMIN, self::RESOURCE_QUOTEGEN_DEVICESETUP_OPTIONS, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_HARDWARE_ADMIN, self::RESOURCE_QUOTEGEN_DEVICESETUP_DELETE, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_HARDWARE_ADMIN, self::RESOURCE_QUOTEGEN_CONFIGURATION_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_HARDWARE_ADMIN, self::RESOURCE_QUOTEGEN_CATEGORY_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_HARDWARE_ADMIN, self::RESOURCE_QUOTEGEN_OPTION_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);

        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_QUOTEGEN_DEVICESETUP_WILDCARD, Application_Model_Acl::PRIVILEGE_ADMIN);
    }
}