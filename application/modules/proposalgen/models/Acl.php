<?php
/**
 * Class Proposalgen_Model_Acl
 */
class Proposalgen_Model_Acl
{
    const RESOURCE_PROPOSALGEN_REPORT_ASSESSMENT_WILDCARD = "proposalgen__report_assessment__%";

    const RESOURCE_PROPOSALGEN_ADMIN_DEVICETONERS            = "proposalgen__admin__devicetoners";
    const RESOURCE_PROPOSALGEN_ADMIN_DEVICETONERCOUNT        = "proposalgen__admin__devicetonercount";
    const RESOURCE_PROPOSALGEN_ADMIN_REPLACETONER            = "proposalgen__admin__replacetoner";
    const RESOURCE_PROPOSALGEN_ADMIN_FILTERLISTITEMS         = "proposalgen__admin__filterlistitems";
    const RESOURCE_PROPOSALGEN_ADMIN_INDEX                   = "proposalgen__admin__index";
    const RESOURCE_PROPOSALGEN_ADMIN_MANAGEMATCHUPS          = "proposalgen__admin__managematchups";
    const RESOURCE_PROPOSALGEN_ADMIN_MANAGEREPLACEMENTS      = "proposalgen__admin__managereplacements";
    const RESOURCE_PROPOSALGEN_ADMIN_MASTERDEVICESLIST       = "proposalgen__admin__masterdeviceslist";
    const RESOURCE_PROPOSALGEN_ADMIN_PRINTERMODELS           = "proposalgen__admin__printermodels";
    const RESOURCE_PROPOSALGEN_ADMIN_REPLACEMENTDETAILS      = "proposalgen__admin__replacementdetails";
    const RESOURCE_PROPOSALGEN_ADMIN_REPLACEMENTPRINTERSLIST = "proposalgen__admin__replacementprinterslist";
    const RESOURCE_PROPOSALGEN_ADMIN_SAVEREPLACEMENTPRINTER  = "proposalgen__admin__savereplacementprinter";
    const RESOURCE_PROPOSALGEN_ADMIN_SEARCHFORDEVICE         = "proposalgen__admin__search-for-device";
    const RESOURCE_PROPOSALGEN_ADMIN_TONERSLIST              = "proposalgen__admin__tonerslist";

    const RESOURCE_PROPOSALGEN_COSTS_BULKDEVICEPRICING = "proposalgen__costs__bulkdevicepricing";
    const RESOURCE_PROPOSALGEN_COSTS_BULKFILEPRICING   = "proposalgen__costs__bulkfilepricing";
    const RESOURCE_PROPOSALGEN_COSTS_EXPORTPRICING     = "proposalgen__costs__exportpricing";

    const RESOURCE_PROPOSALGEN_FLEET_DEVICESUMMARYLIST     = "proposalgen__fleet__device-summary-list";
    const RESOURCE_PROPOSALGEN_FLEET_EXCLUDEDLIST          = "proposalgen__fleet__excluded-list";
    const RESOURCE_PROPOSALGEN_FLEET_REPORTSETTINGS        = "proposalgen__fleet__reportsettings";
    const RESOURCE_PROPOSALGEN_FLEET_EDITUNKNOWNDEVICES    = "proposalgen__fleet__edit-unknown-device";
    const RESOURCE_PROPOSALGEN_FLEET_DEVICEMAPPINGLIST     = "proposalgen__fleet__device-mapping-list";
    const RESOURCE_PROPOSALGEN_FLEET_DEVICEINSTANCEDETAILS = "proposalgen__fleet__device-instance-details";
    const RESOURCE_PROPOSALGEN_FLEET_RMSUPLOADLIST         = "proposalgen__fleet__rms-upload-list";
    const RESOURCE_PROPOSALGEN_FLEET_INDEX                 = "proposalgen__fleet__index";
    const RESOURCE_PROPOSALGEN_FLEET_MAPPING               = "proposalgen__fleet__mapping";
    const RESOURCE_PROPOSALGEN_FLEET_TOGGLEEXCLUDEDFLAG    = "proposalgen__fleet__toggle-excluded-flag";
    const RESOURCE_PROPOSALGEN_FLEET_REMOVEUNKNOWNDEVICE   = "proposalgen__fleet__remove-unknown-device";
    const RESOURCE_PROPOSALGEN_FLEET_SETMAPPEDTO           = "proposalgen__fleet__set-mapped-to";
    const RESOURCE_PROPOSALGEN_FLEET_SUMMARY               = "proposalgen__fleet__summary";

    const RESOURCE_PROPOSALGEN_INDEX_INDEX            = "proposalgen__index__index";
    const RESOURCE_PROPOSALGEN_MANUFACTURER_WILDCARD  = "proposalgen__manufacturer__%";
    const RESOURCE_PROPOSALGEN_MANAGEDEVICES_WILDCARD = "proposalgen__managedevices__%";
    const RESOURCE_PROPOSALGEN_OPTIMIZATION_WILDCARD  = "proposalgen__optimization__%";
    const RESOURCE_PROPOSALGEN_SURVEY_WILDCARD        = "proposalgen__survey__%";

    const RESOURCE_PROPOSALGEN_REPORT_INDEX                          = "proposalgen__report_index__index";
    const RESOURCE_PROPOSALGEN_REPORT_GROSSMARGIN_WILDCARD           = "proposalgen__report_grossmargin__%";
    const RESOURCE_PROPOSALGEN_REPORT_HEALTHCHECK_WILDCARD           = "proposalgen__report_healthcheck__%";
    const RESOURCE_PROPOSALGEN_REPORT_OPTIMIZATION_CUSTOMER_WILDCARD = "proposalgen__report_optimization_customer__%";
    const RESOURCE_PROPOSALGEN_REPORT_OPTIMIZATION_DEALER_WILDCARD   = "proposalgen__report_optimization_dealer__%";
    const RESOURCE_PROPOSALGEN_REPORT_PRINTINGDEVICELIST_WILDCARD    = "proposalgen__report_printingdevicelist__%";
    const RESOURCE_PROPOSALGEN_REPORT_OLDDEVICELIST_WILDCARD         = "proposalgen__report_olddevicelist__%";
    const RESOURCE_PROPOSALGEN_REPORT_COSTANALYSIS_WILDCARD          = "proposalgen__report_costanalysis__%";
    const RESOURCE_PROPOSALGEN_REPORT_SOLUTION_WILDCARD              = "proposalgen__report_solution__%";
    const RESOURCE_PROPOSALGEN_REPORT_TONERS_WILDCARD                = "proposalgen__report_toners__%";

    const RESOURCE_PROPOSALGEN_WILDCARD = "proposalgen__%__%";

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
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_INDEX);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_DEVICETONERS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_DEVICETONERCOUNT);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_REPLACETONER);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_FILTERLISTITEMS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_MANAGEMATCHUPS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_MANAGEREPLACEMENTS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_MASTERDEVICESLIST);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_PRINTERMODELS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_REPLACEMENTDETAILS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_REPLACEMENTPRINTERSLIST);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_SAVEREPLACEMENTPRINTER);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_SEARCHFORDEVICE);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_TONERSLIST);

        $acl->addResource(self::RESOURCE_PROPOSALGEN_COSTS_BULKDEVICEPRICING);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_COSTS_BULKFILEPRICING);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_COSTS_EXPORTPRICING);

        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_DEVICEINSTANCEDETAILS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_RMSUPLOADLIST);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_DEVICEMAPPINGLIST);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_DEVICESUMMARYLIST);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_EDITUNKNOWNDEVICES);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_EXCLUDEDLIST);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_INDEX);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_MAPPING);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_REMOVEUNKNOWNDEVICE);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_REPORTSETTINGS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_SETMAPPEDTO);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_SUMMARY);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_TOGGLEEXCLUDEDFLAG);

        $acl->addResource(self::RESOURCE_PROPOSALGEN_INDEX_INDEX);

        $acl->addResource(self::RESOURCE_PROPOSALGEN_MANUFACTURER_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_MANAGEDEVICES_WILDCARD);

        $acl->addResource(self::RESOURCE_PROPOSALGEN_OPTIMIZATION_WILDCARD);

        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_ASSESSMENT_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_GROSSMARGIN_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_HEALTHCHECK_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_INDEX);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_COSTANALYSIS_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_OPTIMIZATION_CUSTOMER_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_OPTIMIZATION_DEALER_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_PRINTINGDEVICELIST_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_OLDDEVICELIST_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_SOLUTION_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_REPORT_TONERS_WILDCARD);

        $acl->addResource(self::RESOURCE_PROPOSALGEN_SURVEY_WILDCARD);

        $acl->addResource(self::RESOURCE_PROPOSALGEN_WILDCARD);

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
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_ADMIN_SEARCHFORDEVICE, Application_Model_Acl::PRIVILEGE_VIEW);

        /**
         * Assessment User
         */
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_ADMIN_DEVICETONERCOUNT, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_ADMIN_REPLACETONER, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_INDEX_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_SURVEY_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_MAPPING, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_SUMMARY, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_DEVICESUMMARYLIST, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_EXCLUDEDLIST, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_REPORTSETTINGS, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_EDITUNKNOWNDEVICES, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_DEVICEMAPPINGLIST, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_DEVICEINSTANCEDETAILS, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_RMSUPLOADLIST, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_TOGGLEEXCLUDEDFLAG, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_REMOVEUNKNOWNDEVICE, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_SETMAPPEDTO, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_MANAGEDEVICES_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_ASSESSMENT_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_COSTANALYSIS_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_SOLUTION_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_GROSSMARGIN_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_PRINTINGDEVICELIST_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_OLDDEVICELIST_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_TONERS_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_HEALTHCHECK_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_OPTIMIZATION_DEALER_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_OPTIMIZATION_CUSTOMER_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_OPTIMIZATION_WILDCARD, Application_Model_Acl::PRIVILEGE_VIEW);


        /**
         * Hardware Admin
         */
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_INDEX, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_MANAGEREPLACEMENTS, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_REPLACEMENTPRINTERSLIST, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_PRINTERMODELS, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_REPLACEMENTDETAILS, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_SAVEREPLACEMENTPRINTER, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_COSTS_BULKDEVICEPRICING, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_MASTERDEVICESLIST, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_TONERSLIST, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_FILTERLISTITEMS, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_COSTS_BULKFILEPRICING, Application_Model_Acl::PRIVILEGE_VIEW);
        $acl->allow(Application_Model_Acl::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_COSTS_EXPORTPRICING, Application_Model_Acl::PRIVILEGE_VIEW);
        /**
         * System Admin
         */
        $acl->allow(Application_Model_Acl::ROLE_SYSTEM_ADMIN, self::RESOURCE_PROPOSALGEN_WILDCARD, Application_Model_Acl::PRIVILEGE_ADMIN);
    }
}