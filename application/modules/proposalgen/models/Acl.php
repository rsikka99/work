<?php
class Proposalgen_Model_Acl
{
    const RESOURCE_PROPOSALGEN_WILDCARD                              = "proposalgen__%__%";
    const RESOURCE_PROPOSALGEN_SURVEY_WILDCARD                       = "proposalgen__survey__%";
    const RESOURCE_PROPOSALGEN_REPORT_ASSESSMENT_WILDCARD            = "proposalgen__report_assessment__%";
    const RESOURCE_PROPOSALGEN_INDEX_INDEX                           = "proposalgen__index__index";
    const RESOURCE_PROPOSALGEN_ADMIN_INDEX                           = "proposalgen__admin__index";
    const RESOURCE_PROPOSALGEN_ADMIN_BULKUSERPRICING                 = "proposalgen__admin__bulkuserpricing";
    const RESOURCE_PROPOSALGEN_ADMIN_USERDEVICES                     = "proposalgen__admin__userdevices";
    const RESOURCE_PROPOSALGEN_ADMIN_USERTONERS                      = "proposalgen__admin__usertoners";
    const RESOURCE_PROPOSALGEN_ADMIN_FILTERLISTITEMS                 = "proposalgen__admin__filterlistitems";
    const RESOURCE_PROPOSALGEN_ADMIN_TRANSFERREPORTS                 = "proposalgen__admin__transferreports";
    const RESOURCE_PROPOSALGEN_ADMIN_MANAGEMYREPORTS                 = "proposalgen__admin__managemyreports";
    const RESOURCE_PROPOSALGEN_ADMIN_MYREPORTSLIST                   = "proposalgen__admin__myreportslist";
    const RESOURCE_PROPOSALGEN_ADMIN_FILTERREPORTSLIST               = "proposalgen__admin__filterreportslist";
    const RESOURCE_PROPOSALGEN_ADMIN_FILTERUSERSLIST                 = "proposalgen__admin__filteruserslist";
    const RESOURCE_PROPOSALGEN_ADMIN_SEARCHFORDEVICE                 = "proposalgen__admin__search-for-device";
    const RESOURCE_PROPOSALGEN_FLEET                                 = "proposalgen__fleet__index";
    const RESOURCE_PROPOSALGEN_FLEET_MAPPING                         = "proposalgen__fleet__mapping";
    const RESOURCE_PROPOSALGEN_FLEET_SUMMARY                         = "proposalgen__fleet__summary";
    const RESOURCE_PROPOSALGEN_FLEET_DEVICESUMMARYLIST               = "proposalgen__fleet__device-summary-list";
    const RESOURCE_PROPOSALGEN_FLEET_EXCLUDEDLIST                    = "proposalgen__fleet__excluded-list";
    const RESOURCE_PROPOSALGEN_FLEET_REPORTSETTINGS                  = "proposalgen__fleet__reportsettings";
    const RESOURCE_PROPOSALGEN_FLEET_EDITUNKNOWNDEVICES              = "proposalgen__fleet__edit-unknown-device";
    const RESOURCE_PROPOSALGEN_FLEET_DEVICEMAPPINGLIST               = "proposalgen__fleet__device-mapping-list";
    const RESOURCE_PROPOSALGEN_FLEET_DEVICEINSTANCEDETAILS           = "proposalgen__fleet__device-instance-details";
    const RESOURCE_PROPOSALGEN_FLEET_TOGGLEEXCLUDEDFLAG              = "proposalgen__fleet__toggle-excluded-flag";
    const RESOURCE_PROPOSALGEN_FLEET_REMOVEUNKNOWNDEVICE             = "proposalgen__fleet__remove-unknown-device";
    const RESOURCE_PROPOSALGEN_FLEET_SETMAPPEDTO                     = "proposalgen__fleet__set-mapped-to";
    const RESOURCE_PROPOSALGEN_MANUFACTURER_WILDCARD                 = "proposalgen__manufacturer__%";
    const RESOURCE_PROPOSALGEN_REPORT_INDEX                          = "proposalgen__report_index__index";
    const RESOURCE_PROPOSALGEN_REPORT_SOLUTION_WILDCARD              = "proposalgen__report_solution__%";
    const RESOURCE_PROPOSALGEN_REPORT_GROSSMARGIN_WILDCARD           = "proposalgen__report_grossmargin__%";
    const RESOURCE_PROPOSALGEN_REPORT_PRINTINGDEVICELIST_WILDCARD    = "proposalgen__report_printingdevicelist__%";
    const RESOURCE_PROPOSALGEN_REPORT_TONERS_WILDCARD                = "proposalgen__report_toners__%";
    const RESOURCE_PROPOSALGEN_REPORT_HEALTHCHECK_WILDCARD           = "proposalgen__report_healthcheck__%";
    const RESOURCE_PROPOSALGEN_REPORT_OPTIMIZATION_DEALER_WILDCARD   = "proposalgen__report_optimization_dealer__%";
    const RESOURCE_PROPOSALGEN_REPORT_OPTIMIZATION_CUSTOMER_WILDCARD = "proposalgen__report_optimization_customer__%";

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
        $acl->addResource(self::RESOURCE_PROPOSALGEN_MANUFACTURER_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_SURVEY_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_INDEX_INDEX);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_INDEX);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_BULKUSERPRICING);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_USERDEVICES);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_USERTONERS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_FILTERLISTITEMS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_MANAGEMYREPORTS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_MYREPORTSLIST);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_FILTERREPORTSLIST);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_TRANSFERREPORTS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_FILTERUSERSLIST);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_SEARCHFORDEVICE);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_MAPPING);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_SUMMARY);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_DEVICESUMMARYLIST);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_EXCLUDEDLIST);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_REPORTSETTINGS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_EDITUNKNOWNDEVICES);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_DEVICEMAPPINGLIST);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_DEVICEINSTANCEDETAILS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_TOGGLEEXCLUDEDFLAG);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_REMOVEUNKNOWNDEVICE);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_SETMAPPEDTO);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_INDEX);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_ASSESSMENT_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_SOLUTION_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_GROSSMARGIN_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_PRINTINGDEVICELIST_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_TONERS_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_HEALTHCHECK_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_OPTIMIZATION_DEALER_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_OPTIMIZATION_CUSTOMER_WILDCARD);
    }

    /**
     * Sets up access to resources for a module
     *
     * @param Application_Model_Acl $acl
     */
    private static function setupAclAccess (Application_Model_Acl $acl)
    {
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_ADMIN_SEARCHFORDEVICE, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_ADMIN, self::RESOURCE_PROPOSALGEN_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_ADMIN, self::RESOURCE_PROPOSALGEN_ADMIN_TRANSFERREPORTS, Application_Model_Acl::PRIVILEGE_ADMIN);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_ADMIN, self::RESOURCE_PROPOSALGEN_ADMIN_FILTERUSERSLIST, Application_Model_Acl::PRIVILEGE_ADMIN);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_ADMIN_MANAGEMYREPORTS, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_ADMIN_MYREPORTSLIST, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_ADMIN_TRANSFERREPORTS, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_ADMIN_FILTERREPORTSLIST, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_ADMIN_FILTERUSERSLIST, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_INDEX_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_SURVEY_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_MAPPING, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_SUMMARY, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_DEVICESUMMARYLIST, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_EXCLUDEDLIST, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_REPORTSETTINGS, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_EDITUNKNOWNDEVICES, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_DEVICEMAPPINGLIST, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_DEVICEINSTANCEDETAILS, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_TOGGLEEXCLUDEDFLAG, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_REMOVEUNKNOWNDEVICE, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_SETMAPPEDTO, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_REPORT_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_REPORT_ASSESSMENT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_REPORT_SOLUTION_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_REPORT_GROSSMARGIN_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_REPORT_PRINTINGDEVICELIST_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_REPORT_TONERS_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_REPORT_HEALTHCHECK_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_REPORT_OPTIMIZATION_DEALER_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_REPORT_OPTIMIZATION_CUSTOMER_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_QUOTE_ADMIN, self::RESOURCE_PROPOSALGEN_MANUFACTURER_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_PROPOSALGEN_ADMIN_FILTERUSERSLIST, Application_Model_Acl::PRIVILEGE_ADMIN);
    }
}