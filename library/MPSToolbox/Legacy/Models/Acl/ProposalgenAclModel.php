<?php

namespace MPSToolbox\Legacy\Models\Acl;

/**
 * Class ProposalgenAclModel
 *
 * @package MPSToolbox\Legacy\Models\Acl
 */
class ProposalgenAclModel
{
    const RESOURCE_PROPOSALGEN_CLIENT_PRICING_WILDCARD = "proposalgen__client-pricing__%";

    const RESOURCE_PROPOSALGEN_REPORT_ASSESSMENT_WILDCARD = "proposalgen__report_assessment__%";

    const RESOURCE_PROPOSALGEN_ADMIN_DEVICETONERS       = "proposalgen__admin__devicetoners";
    const RESOURCE_PROPOSALGEN_ADMIN_DEVICETONERCOUNT   = "proposalgen__admin__devicetonercount";
    const RESOURCE_PROPOSALGEN_ADMIN_REPLACETONER       = "proposalgen__admin__replacetoner";
    const RESOURCE_PROPOSALGEN_ADMIN_FILTERLISTITEMS    = "proposalgen__admin__filterlistitems";
    const RESOURCE_PROPOSALGEN_ADMIN_INDEX              = "proposalgen__admin__index";
    const RESOURCE_PROPOSALGEN_ADMIN_MANAGEMATCHUPS     = "proposalgen__admin__managematchups";
    const RESOURCE_PROPOSALGEN_ADMIN_SETMAPPEDTO        = "proposalgen__admin__set-mapped-to";
    const RESOURCE_PROPOSALGEN_ADMIN_MATCHUPLIST        = "proposalgen__admin__matchuplist";
    const RESOURCE_PROPOSALGEN_ADMIN_MANAGEREPLACEMENTS = "proposalgen__admin__managereplacements";
    const RESOURCE_PROPOSALGEN_ADMIN_MASTERDEVICESLIST  = "proposalgen__admin__masterdeviceslist";
    const RESOURCE_PROPOSALGEN_ADMIN_PRINTERMODELS      = "proposalgen__admin__printermodels";
    const RESOURCE_PROPOSALGEN_ADMIN_SEARCHFORDEVICE    = "proposalgen__admin__search-for-device";
    const RESOURCE_PROPOSALGEN_ADMIN_TONERSLIST         = "proposalgen__admin__tonerslist";
    const RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE     = "proposalgen__admin__saveandapprove";

    const RESOURCE_PROPOSALGEN_COSTS_BULKDEVICEPRICING = "proposalgen__costs__bulkdevicepricing";
    const RESOURCE_PROPOSALGEN_COSTS_EXPORTPRICING     = "proposalgen__costs__export-pricing";

    const RESOURCE_PROPOSALGEN_COSTS_BULKFILETONERPRICING   = "proposalgen__costs__bulk-file-toner-pricing";
    const RESOURCE_PROPOSALGEN_COSTS_BULKFILETONERMATCHUP   = "proposalgen__costs__bulk-file-toner-matchup";
    const RESOURCE_PROPOSALGEN_COSTS_BULKFILEDEVICEPRICNG   = "proposalgen__costs__bulk-file-device-pricing";
    const RESOURCE_PROPOSALGEN_COSTS_BULKFILEDEVICEFEATURES = "proposalgen__costs__bulk-file-device-features";

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
    const RESOURCE_PROPOSALGEN_FLEET_TOGGLELEASEDFLAG      = "proposalgen__fleet__toggle-leased-flag";
    const RESOURCE_PROPOSALGEN_FLEET_TOGGLEMANAGEDFLAG     = "proposalgen__fleet__toggle-managed-flag";
    const RESOURCE_PROPOSALGEN_FLEET_TOGGLEJITFLAG         = "proposalgen__fleet__toggle-jit-flag";
    const RESOURCE_PROPOSALGEN_FLEET_REMOVEUNKNOWNDEVICE   = "proposalgen__fleet__remove-unknown-device";
    const RESOURCE_PROPOSALGEN_FLEET_SETMAPPEDTO           = "proposalgen__fleet__set-mapped-to";
    const RESOURCE_PROPOSALGEN_FLEET_SUMMARY               = "proposalgen__fleet__summary";

    const RESOURCE_PROPOSALGEN_INDEX_INDEX            = "proposalgen__index__index";
    const RESOURCE_PROPOSALGEN_MANUFACTURER_WILDCARD  = "proposalgen__manufacturer__%";
    const RESOURCE_PROPOSALGEN_MANAGEDEVICES_WILDCARD = "proposalgen__managedevices__%";
    const RESOURCE_PROPOSALGEN_OPTIMIZATION_WILDCARD  = "proposalgen__optimization__%";
    const RESOURCE_PROPOSALGEN_SURVEY_WILDCARD        = "proposalgen__survey__%";

    const RESOURCE_PROPOSALGEN_MANUFACTURER_EDIT                     = "proposalgen__manufacturer__edit";
    const RESOURCE_PROPOSALGEN_MANUFACTURER_CREATE                   = "proposalgen__manufacturer__create";
    const RESOURCE_PROPOSALGEN_MANUFACTURER_DELETE                   = "proposalgen__manufacturer__delete";
    const RESOURCE_PROPOSALGEN_MANUFACTURER_INDEX                    = "proposalgen__manufacturer__index";
    const RESOURCE_PROPOSALGEN_MANUFACTURER_VIEW                     = "proposalgen__manufacturer__view";
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
        $acl->addResource(self::RESOURCE_PROPOSALGEN_CLIENT_PRICING_WILDCARD);

        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_INDEX);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_DEVICETONERS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_DEVICETONERCOUNT);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_REPLACETONER);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_FILTERLISTITEMS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_MANAGEMATCHUPS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_SETMAPPEDTO);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_MATCHUPLIST);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_MANAGEREPLACEMENTS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_MASTERDEVICESLIST);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_PRINTERMODELS);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_SEARCHFORDEVICE);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_TONERSLIST);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE);

        $acl->addResource(self::RESOURCE_PROPOSALGEN_COSTS_BULKDEVICEPRICING);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_COSTS_BULKFILEDEVICEFEATURES);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_COSTS_BULKFILEDEVICEPRICNG);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_COSTS_BULKFILETONERMATCHUP);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_COSTS_BULKFILETONERPRICING);
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
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_TOGGLELEASEDFLAG);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_TOGGLEMANAGEDFLAG);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_FLEET_TOGGLEJITFLAG);

        $acl->addResource(self::RESOURCE_PROPOSALGEN_INDEX_INDEX);

        $acl->addResource(self::RESOURCE_PROPOSALGEN_MANUFACTURER_WILDCARD);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_MANUFACTURER_CREATE);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_MANUFACTURER_DELETE);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_MANUFACTURER_EDIT);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_MANUFACTURER_INDEX);
        $acl->addResource(self::RESOURCE_PROPOSALGEN_MANUFACTURER_VIEW);
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
     * @param AppAclModel $acl
     */
    private static function setupAclAccess (AppAclModel $acl)
    {
        /**
         * Any logged in user
         */
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_ADMIN_SEARCHFORDEVICE, AppAclModel::PRIVILEGE_VIEW);

        /**
         * Assessment User
         */
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_CLIENT_PRICING_WILDCARD, AppAclModel::PRIVILEGE_VIEW);

        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_ADMIN_DEVICETONERCOUNT, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_ADMIN_REPLACETONER, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_INDEX_INDEX, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_SURVEY_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_INDEX, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_MAPPING, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_SUMMARY, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_DEVICESUMMARYLIST, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_EXCLUDEDLIST, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_REPORTSETTINGS, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_EDITUNKNOWNDEVICES, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_DEVICEMAPPINGLIST, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_DEVICEINSTANCEDETAILS, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_RMSUPLOADLIST, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_TOGGLEEXCLUDEDFLAG, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_TOGGLELEASEDFLAG, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_TOGGLEMANAGEDFLAG, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_TOGGLEJITFLAG, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_REMOVEUNKNOWNDEVICE, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_FLEET_SETMAPPEDTO, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_MANAGEDEVICES_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_MANUFACTURER_CREATE, AppAclModel::PRIVILEGE_VIEW);
//        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_MANUFACTURER_INDEX, AppAclModel::PRIVILEGE_VIEW);
//        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_MANUFACTURER_VIEW, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_INDEX, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_ASSESSMENT_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_COSTANALYSIS_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_SOLUTION_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_GROSSMARGIN_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_PRINTINGDEVICELIST_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_OLDDEVICELIST_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_TONERS_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_HEALTHCHECK_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_OPTIMIZATION_DEALER_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_REPORT_OPTIMIZATION_CUSTOMER_WILDCARD, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_OPTIMIZATION_WILDCARD, AppAclModel::PRIVILEGE_VIEW);

        /**
         * Hardware Admin
         */
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_INDEX, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_MANAGEREPLACEMENTS, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_PRINTERMODELS, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_COSTS_BULKDEVICEPRICING, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_MASTERDEVICESLIST, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_TONERSLIST, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_FILTERLISTITEMS, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_COSTS_BULKFILEDEVICEPRICNG, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_COSTS_BULKFILETONERMATCHUP, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_COSTS_BULKFILETONERPRICING, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_COSTS_EXPORTPRICING, AppAclModel::PRIVILEGE_VIEW);

        /**
         * Master Device Administrator
         */
        $acl->allow(AppAclModel::ROLE_MASTER_DEVICE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE, AppAclModel::PRIVILEGE_ADMIN);
        $acl->allow(AppAclModel::ROLE_MASTER_DEVICE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_MANUFACTURER_EDIT, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_MASTER_DEVICE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_COSTS_BULKFILEDEVICEFEATURES, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_MASTER_DEVICE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_MANAGEMATCHUPS, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_MASTER_DEVICE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_MATCHUPLIST, AppAclModel::PRIVILEGE_VIEW);
        $acl->allow(AppAclModel::ROLE_MASTER_DEVICE_ADMINISTRATOR, self::RESOURCE_PROPOSALGEN_ADMIN_SETMAPPEDTO, AppAclModel::PRIVILEGE_VIEW);

        /**
         * System Admin
         */
        $acl->allow(AppAclModel::ROLE_SYSTEM_ADMIN, self::RESOURCE_PROPOSALGEN_WILDCARD, AppAclModel::PRIVILEGE_ADMIN);
    }
}