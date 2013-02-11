<?php

class Application_Model_Acl extends Zend_Acl
{
    /**
     * Roles
     * These are like user groups
     */
    const ROLE_GUEST              = "-1";
    const ROLE_AUTHENTICATED_USER = "0";
    const ROLE_SYSTEM_ADMIN       = "1";
    const ROLE_PROPOSAL_ADMIN     = "2";
    const ROLE_PROPOSAL_USER      = "3";
    const ROLE_QUOTE_ADMIN        = "4";
    const ROLE_QUOTE_USER         = "5";

    /**
     * Resource parameters
     */
    const SEPARATOR = "__";
    const WILDCARD  = "%";

    /**
     * Resources
     * These are module controller action combinations
     */
    const RESOURCE_ADMIN_WILDCARD       = "admin__%__%";
    const RESOURCE_ADMIN_TONER_WILDCARD = "admin__toner__%";


    const RESOURCE_DEFAULT_WILDCARD                = "default__%__%";
    const RESOURCE_PROPOSALGEN_WILDCARD            = "proposalgen__%__%";
    const RESOURCE_PROPOSALGEN_SURVEY_WILDCARD     = "proposalgen__survey__%";
    const RESOURCE_PROPOSALGEN_REPORT_WILDCARD     = "proposalgen__report_assessment__%";
    const RESOURCE_QUOTEGEN_WILDCARD               = "quotegen__%__%";
    const RESOURCE_QUOTEGEN_CLIENT_WILDCARD        = "quotegen__client__%";
    const RESOURCE_QUOTEGEN_CONFIGURATION_WILDCARD = "quotegen__configuration__%";
    const RESOURCE_QUOTEGEN_QUOTEDEVICES_WILDCARD  = "quotegen__quote_devices__%";
    const RESOURCE_QUOTEGEN_QUOTEREPORTS_WILDCARD  = "quotegen__quote_reports__%";


    /**
     * Admin Constants
     */
    const RESOURCE_ADMIN_INDEX_INDEX  = "admin__index__index";
    const RESOURCE_ADMIN_USER_PROFILE = "admin__user__profile";
    const RESOURCE_ADMIN_USER_WILDCARD = "admin__user__%";

    /**
     * Default constants
     */
    const RESOURCE_DEFAULT_AUTH_LOGIN          = "default__auth__login";
    const RESOURCE_DEFAULT_AUTH_LOGOUT         = "default__auth__logout";
    const RESOURCE_DEFAULT_AUTH_FORGOTPASSWORD = "default__auth__forgotpassword";

    /**
     * Proposalgen Constants
     */
    const RESOURCE_PROPOSALGEN_INDEX_INDEX                        = "proposalgen__index__index";
    const RESOURCE_PROPOSALGEN_ADMIN_INDEX                        = "proposalgen__admin__index";
    const RESOURCE_PROPOSALGEN_ADMIN_BULKUSERPRICING              = "proposalgen__admin__bulkuserpricing";
    const RESOURCE_PROPOSALGEN_ADMIN_USERDEVICES                  = "proposalgen__admin__userdevices";
    const RESOURCE_PROPOSALGEN_ADMIN_USERTONERS                   = "proposalgen__admin__usertoners";
    const RESOURCE_PROPOSALGEN_ADMIN_FILTERLISTITEMS              = "proposalgen__admin__filterlistitems";
    const RESOURCE_PROPOSALGEN_ADMIN_TRANSFERREPORTS              = "proposalgen__admin__transferreports";
    const RESOURCE_PROPOSALGEN_ADMIN_MANAGEMYSETTINGS             = "proposalgen__admin__managemysettings";
    const RESOURCE_PROPOSALGEN_ADMIN_MANAGEMYREPORTS              = "proposalgen__admin__managemyreports";
    const RESOURCE_PROPOSALGEN_ADMIN_MYREPORTSLIST                = "proposalgen__admin__myreportslist";
    const RESOURCE_PROPOSALGEN_ADMIN_FILTERREPORTSLIST            = "proposalgen__admin__filterreportslist";
    const RESOURCE_PROPOSALGEN_ADMIN_FILTERUSERSLIST              = "proposalgen__admin__filteruserslist";
    const RESOURCE_PROPOSALGEN_ADMIN_SEARCHFORDEVICE              = "proposalgen__admin__search-for-device";
    const RESOURCE_PROPOSALGEN_FLEET                              = "proposalgen__fleet__index";
    const RESOURCE_PROPOSALGEN_FLEET_MAPPING                      = "proposalgen__fleet__mapping";
    const RESOURCE_PROPOSALGEN_FLEET_SUMMARY                      = "proposalgen__fleet__summary";
    const RESOURCE_PROPOSALGEN_FLEET_DEVICESUMMARYLIST            = "proposalgen__fleet__device-summary-list";
    const RESOURCE_PROPOSALGEN_FLEET_REPORTSETTINGS               = "proposalgen__fleet__reportsettings";
    const RESOURCE_PROPOSALGEN_FLEET_EDITUNKNOWNDEVICES           = "proposalgen__fleet__edit-unknown-device";
    const RESOURCE_PROPOSALGEN_FLEET_DEVICEMAPPINGLIST            = "proposalgen__fleet__device-mapping-list";
    const RESOURCE_PROPOSALGEN_FLEET_DEVICEINSTANCEDETAILS        = "proposalgen__fleet__device-instance-details";
    const RESOURCE_PROPOSALGEN_FLEET_TOGGLEEXCLUDEDFLAG           = "proposalgen__fleet__toggle-excluded-flag";
    const RESOURCE_PROPOSALGEN_FLEET_REMOVEUNKNOWNDEVICE          = "proposalgen__fleet__remove-unknown-device";
    const RESOURCE_PROPOSALGEN_FLEET_SETMAPPEDTO                  = "proposalgen__fleet__set-mapped-to";
    const RESOURCE_PROPOSALGEN_MANUFACTURER_WILDCARD              = "proposalgen__manufacturer__%";
    const RESOURCE_PROPOSALGEN_REPORT_INDEX                       = "proposalgen__report_index__index";
    const RESOURCE_PROPOSALGEN_REPORT_SOLUTION                    = "proposalgen__report_solution__%";
    const RESOURCE_PROPOSALGEN_REPORT_GROSSMARGIN_WILDCARD        = "proposalgen__report_grossmargin__%";
    const RESOURCE_PROPOSALGEN_REPORT_PRINTINGDEVICELIST_WILDCARD = "proposalgen__report_printingdevicelist__%";
    const RESOURCE_PROPOSALGEN_REPORT_PIQESSENTIALS_WILDCARD      = "proposalgen__report_piqessentials__%";

    /**
     * Quotegen constants
     */
    const RESOURCE_QUOTEGEN                           = "quotegen__index__index";
    const RESOURCE_QUOTEGEN_QUOTEGROUPS_INDEX         = "quotegen__quote_groups__index";
    const RESOURCE_QUOTEGEN_QUOTEPAGES_INDEX          = "quotegen__quote_pages__index";
    const RESOURCE_QUOTEGEN_QUOTEPROFITABILITY_INDEX  = "quotegen__quote_profitability__index";
    const RESOURCE_QUOTEGEN_INDEX_EXISTINGQUOTE       = "quotegen__index__existing-quote";
    const RESOURCE_QUOTEGEN_INDEX_GETREPORTSFORCLIENT = "quotegen__index__get-reports-for-client";
    const RESOURCE_QUOTEGEN_INDEX_CREATECLIENT        = "quotegen__index__create-client";
    const RESOURCE_QUOTEGEN_QUOTE_INDEX               = "quotegen__quote__index";
    const RESOURCE_QUOTEGEN_QUOTE_DELETE              = "quotegen__quote__delete";
    const RESOURCE_QUOTEGEN_QUOTESETTING_EDIT         = "quotegen__quotesetting__edit";


    /**
     * This is what kind of access we want to allow. We can use this to provide dynamic pages based on acl
     */
    const PRIVILEGE_ADMIN = "Admin";
    const PRIVILEGE_VIEW  = "View";

    /**
     * @var Application_Model_Acl
     */
    protected static $_instance;

    /**
     * Gets a instance of Application_Model_Acl
     *
     * @return Application_Model_Acl
     */
    public static function getInstance ()
    {
        if (null === self::$_instance)
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Clears the instance and instantiates a new one
     */
    public static function resetInstance ()
    {
        self::$_instance = null;
        self::getInstance();
    }


    /**
     * The constructor for our ACL
     */
    public function __construct ()
    {
        /**
         * Setup our available resources
         */
        $this->_setupResources();

        /*
         * Add our various roles
         */
        $this->_setupGuestRole();
        $this->_setupAuthenticatedUserRole();
        $this->_setupProposalUserRole();
        $this->_setupProposalAdminRole();
        $this->_setupQuoteUserRole();
        $this->_setupQuoteAdminRole();
        $this->_setupSystemAdminRole();
    }

    /**
     * Returns true if and only if the Role has access to the Resource
     *
     * @param null|string|Zend_Acl_Role_Interface                                      $role
     * @param null|string|Zend_Acl_Resource_Interface|Zend_Controller_Request_Abstract $resource
     * @param null|string                                                              $privilege
     *
     * @see  Zend_Acl::isAllowed
     *
     * @return bool
     */
    public function isAllowed ($role = null, $resource = null, $privilege = null)
    {
        $isAllowed             = false;
        $roles                 = array();
        $resourceAssertionList = array();

        /*
         * Massage roles into a workable array
         */
        if (is_array($role))
        {
            if (count($role) > 0)
            {
                $roles = $role;
            }
            else
            {
                $roles[] = self::ROLE_GUEST;
            }
        }
        else if ($role === null)
        {
            $roles[] = self::ROLE_GUEST;
        }
        else if ($role > 0)
        {
            $userId = (int)$role;
            if ($userId === 1)
            {
                // Is Super User
                return true;
            }
            else
            {
                $user = Application_Model_Mapper_User::getInstance()->find($userId);
                if ($user)
                {
                    $userRoles = $user->getUserRoles();
                    if ($userRoles && count($userRoles) > 0)
                    {
                        foreach ($userRoles as $userRole)
                        {
                            $roles[] = $userRole->roleId;
                        }
                    }
                    else
                    {
                        $roles[] = self::ROLE_GUEST;
                    }
                }
                else
                {
                    $roles[] = self::ROLE_GUEST;
                }
            }
        }
        else
        {
            $roles[] = $role;
        }

        /*
         * Massage our resource into a list of resources to test
         */
        if ($resource instanceof Zend_Controller_Request_Abstract)
        {
            // Module Wildcard
            $resourceAssertionList[] = $resource->getModuleName() . self::SEPARATOR . self::WILDCARD . self::SEPARATOR . self::WILDCARD;

            // Controller Wildcard
            $resourceAssertionList[] = $resource->getModuleName() . self::SEPARATOR . $resource->getControllerName() . self::SEPARATOR . self::WILDCARD;

            // Full module_controller_action
            $resourceAssertionList[] = $resource->getModuleName() . self::SEPARATOR . $resource->getControllerName() . self::SEPARATOR . $resource->getActionName();
        }
        else if (is_array($resource))
        {
            $resourceAssertionList = $resource;
        }
        else
        {
            $resourceExplosion = explode(self::SEPARATOR, $resource);

            // Module Wildcard
            $resourceAssertionList[] = $resourceExplosion[0] . self::SEPARATOR . self::WILDCARD . self::SEPARATOR . self::WILDCARD;

            // Controller Wildcard
            $resourceAssertionList[] = $resourceExplosion[0] . self::SEPARATOR . $resourceExplosion[1] . self::SEPARATOR . self::WILDCARD;

            // Full module_controller_action
            $resourceAssertionList[] = $resourceExplosion[0] . self::SEPARATOR . $resourceExplosion[1] . self::SEPARATOR . $resourceExplosion[2];
        }

        /*
         * Set our privilege to View if we didn't get passed a privilege
         */
        if ($privilege === null)
        {
            $privilege = self::PRIVILEGE_VIEW;
        }


        /*
         * Test each resource to see if we have access
         */
        foreach ($roles as $roleToTest)
        {
            foreach ($resourceAssertionList as $resourceToTest)
            {
                try
                {
                    if (parent::isAllowed((string)$roleToTest, $resourceToTest, $privilege))
                    {
                        $isAllowed = true;
                        break;
                    }
                }
                catch (Exception $e)
                {
                    // Do nothing as the user is not allowed.
                }
            }
        }

        return $isAllowed;
    }


    /**
     * Handles setting up all our available resources that we will use for ACL
     */
    protected function _setupResources ()
    {
        $this->addResource(self::RESOURCE_ADMIN_WILDCARD);
        $this->addResource(self::RESOURCE_ADMIN_TONER_WILDCARD);
        $this->addResource(self::RESOURCE_PROPOSALGEN_MANUFACTURER_WILDCARD);

        $this->addResource(self::RESOURCE_DEFAULT_WILDCARD);
        $this->addResource(self::RESOURCE_PROPOSALGEN_WILDCARD);
        $this->addResource(self::RESOURCE_PROPOSALGEN_SURVEY_WILDCARD);
        $this->addResource(self::RESOURCE_QUOTEGEN_WILDCARD);
        $this->addResource(self::RESOURCE_QUOTEGEN_CLIENT_WILDCARD);

        $this->addResource(self::RESOURCE_DEFAULT_AUTH_LOGIN);
        $this->addResource(self::RESOURCE_DEFAULT_AUTH_LOGOUT);
        $this->addResource(self::RESOURCE_DEFAULT_AUTH_FORGOTPASSWORD);

        //Assessment User
        $this->addResource(self::RESOURCE_PROPOSALGEN_INDEX_INDEX);
        $this->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_INDEX);
        $this->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_BULKUSERPRICING);
        $this->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_USERDEVICES);
        $this->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_USERTONERS);
        $this->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_FILTERLISTITEMS);

        $this->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_MANAGEMYSETTINGS);
        $this->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_MANAGEMYREPORTS);
        $this->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_MYREPORTSLIST);
        $this->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_FILTERREPORTSLIST);
        $this->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_TRANSFERREPORTS);
        $this->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_FILTERUSERSLIST);
        $this->addResource(self::RESOURCE_PROPOSALGEN_ADMIN_SEARCHFORDEVICE);


        $this->addResource(self::RESOURCE_PROPOSALGEN_FLEET);
        $this->addResource(self::RESOURCE_PROPOSALGEN_FLEET_MAPPING);
        $this->addResource(self::RESOURCE_PROPOSALGEN_FLEET_SUMMARY);
        $this->addResource(self::RESOURCE_PROPOSALGEN_FLEET_DEVICESUMMARYLIST);
        $this->addResource(self::RESOURCE_PROPOSALGEN_FLEET_REPORTSETTINGS);
        $this->addResource(self::RESOURCE_PROPOSALGEN_FLEET_EDITUNKNOWNDEVICES);
        $this->addResource(self::RESOURCE_PROPOSALGEN_FLEET_DEVICEMAPPINGLIST);
        $this->addResource(self::RESOURCE_PROPOSALGEN_FLEET_DEVICEINSTANCEDETAILS);
        $this->addResource(self::RESOURCE_PROPOSALGEN_FLEET_TOGGLEEXCLUDEDFLAG);
        $this->addResource(self::RESOURCE_PROPOSALGEN_FLEET_REMOVEUNKNOWNDEVICE);
        $this->addResource(self::RESOURCE_PROPOSALGEN_FLEET_SETMAPPEDTO);

        $this->addResource(self::RESOURCE_PROPOSALGEN_REPORT_INDEX);
        $this->addResource(self::RESOURCE_PROPOSALGEN_REPORT_WILDCARD);
        $this->addResource(self::RESOURCE_PROPOSALGEN_REPORT_SOLUTION);
        $this->addResource(self::RESOURCE_PROPOSALGEN_REPORT_GROSSMARGIN_WILDCARD);
        $this->addResource(self::RESOURCE_PROPOSALGEN_REPORT_PRINTINGDEVICELIST_WILDCARD);
        $this->addResource(self::RESOURCE_PROPOSALGEN_REPORT_PIQESSENTIALS_WILDCARD);

        //Quote User
        $this->addResource(self::RESOURCE_QUOTEGEN);
        $this->addResource(self::RESOURCE_QUOTEGEN_QUOTEDEVICES_WILDCARD);
        $this->addResource(self::RESOURCE_QUOTEGEN_QUOTEGROUPS_INDEX);
        $this->addResource(self::RESOURCE_QUOTEGEN_QUOTEPAGES_INDEX);
        $this->addResource(self::RESOURCE_QUOTEGEN_QUOTEPROFITABILITY_INDEX);
        $this->addResource(self::RESOURCE_QUOTEGEN_QUOTEREPORTS_WILDCARD);
        $this->addResource(self::RESOURCE_QUOTEGEN_INDEX_EXISTINGQUOTE);
        $this->addResource(self::RESOURCE_QUOTEGEN_INDEX_GETREPORTSFORCLIENT);
        $this->addResource(self::RESOURCE_QUOTEGEN_INDEX_CREATECLIENT);
        $this->addResource(self::RESOURCE_QUOTEGEN_QUOTE_INDEX);
        $this->addResource(self::RESOURCE_QUOTEGEN_QUOTE_DELETE);
        $this->addResource(self::RESOURCE_QUOTEGEN_QUOTESETTING_EDIT);
        $this->addResource(self::RESOURCE_QUOTEGEN_CONFIGURATION_WILDCARD);

        $this->addResource(self::RESOURCE_ADMIN_USER_PROFILE);
        $this->addResource(self::RESOURCE_ADMIN_USER_WILDCARD);

        $this->addResource(self::RESOURCE_ADMIN_INDEX_INDEX);
    }

    /**
     * Sets up the guest role
     */
    protected function _setupGuestRole ()
    {
        // Add our role
        $this->addRole(self::ROLE_GUEST);

        // Add our privileges
        //$this->allow(self::ROLE_GUEST, self::RESOURCE_DEFAULT_WILDCARD, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_GUEST, self::RESOURCE_DEFAULT_AUTH_LOGIN, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_GUEST, self::RESOURCE_DEFAULT_AUTH_FORGOTPASSWORD, self::PRIVILEGE_VIEW);
    }

    /**
     * Sets up the guest role
     */
    protected function _setupAuthenticatedUserRole ()
    {
        // Add our role
        $this->addRole(self::ROLE_AUTHENTICATED_USER);

        // Add our privileges
        $this->allow(self::ROLE_AUTHENTICATED_USER, self::RESOURCE_DEFAULT_WILDCARD, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_AUTHENTICATED_USER, self::RESOURCE_PROPOSALGEN_ADMIN_SEARCHFORDEVICE, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_AUTHENTICATED_USER, self::RESOURCE_ADMIN_USER_PROFILE, self::PRIVILEGE_VIEW);

    }

    /**
     * Sets up the proposal admin role
     */
    protected function _setupProposalAdminRole ()
    {
        // Add our role
        $this->addRole(self::ROLE_PROPOSAL_ADMIN, self::ROLE_PROPOSAL_USER);

        // Add our privileges

        $this->allow(self::ROLE_PROPOSAL_ADMIN, self::RESOURCE_PROPOSALGEN_WILDCARD, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_ADMIN, self::RESOURCE_PROPOSALGEN_ADMIN_TRANSFERREPORTS, self::PRIVILEGE_ADMIN);
        $this->allow(self::ROLE_PROPOSAL_ADMIN, self::RESOURCE_PROPOSALGEN_ADMIN_FILTERUSERSLIST, self::PRIVILEGE_ADMIN);
    }

    /**
     * Sets up the proposal user role
     */
    protected function _setupProposalUserRole ()
    {
        // Add our role
        $this->addRole(self::ROLE_PROPOSAL_USER, self::ROLE_AUTHENTICATED_USER);

        // Add our privileges
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_DEFAULT_WILDCARD, self::PRIVILEGE_VIEW);

//        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_ADMIN_BULKUSERPRICING, self::PRIVILEGE_VIEW);
//        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_ADMIN_USERDEVICES, self::PRIVILEGE_VIEW);
//        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_ADMIN_USERTONERS, self::PRIVILEGE_VIEW);
//        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_ADMIN_FILTERLISTITEMS, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_ADMIN_MANAGEMYSETTINGS, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_ADMIN_MANAGEMYREPORTS, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_ADMIN_MYREPORTSLIST, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_ADMIN_TRANSFERREPORTS, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_ADMIN_FILTERREPORTSLIST, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_ADMIN_FILTERUSERSLIST, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_INDEX_INDEX, self::PRIVILEGE_VIEW);

        //Survey
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_SURVEY_WILDCARD, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_MAPPING, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_SUMMARY, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_DEVICESUMMARYLIST, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_REPORTSETTINGS, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_EDITUNKNOWNDEVICES, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_DEVICEMAPPINGLIST, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_DEVICEINSTANCEDETAILS, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_TOGGLEEXCLUDEDFLAG, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_REMOVEUNKNOWNDEVICE, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_FLEET_SETMAPPEDTO, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_REPORT_INDEX, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_REPORT_WILDCARD, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_REPORT_SOLUTION, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_REPORT_GROSSMARGIN_WILDCARD, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_REPORT_PRINTINGDEVICELIST_WILDCARD, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_REPORT_PIQESSENTIALS_WILDCARD, self::PRIVILEGE_VIEW);


    }

    /**
     * Sets up the quote admin role
     */
    protected function _setupQuoteAdminRole ()
    {
        // Add our role
        $this->addRole(self::ROLE_QUOTE_ADMIN, self::ROLE_QUOTE_USER);

        // Add our privileges
        $this->allow(self::ROLE_QUOTE_ADMIN, self::RESOURCE_DEFAULT_WILDCARD, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_ADMIN, self::RESOURCE_QUOTEGEN_WILDCARD, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_ADMIN, self::RESOURCE_ADMIN_TONER_WILDCARD, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_ADMIN, self::RESOURCE_PROPOSALGEN_MANUFACTURER_WILDCARD, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_ADMIN, self::RESOURCE_ADMIN_INDEX_INDEX, self::PRIVILEGE_VIEW);
    }

    /**
     * Sets up the quote user role
     */
    protected function _setupQuoteUserRole ()
    {
        // Add our role
        $this->addRole(self::ROLE_QUOTE_USER, self::ROLE_AUTHENTICATED_USER);

        // Add our privileges
        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_DEFAULT_WILDCARD, self::PRIVILEGE_VIEW);
        //$this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_WILDCARD, self::PRIVILEGE_VIEW)

        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_QUOTEDEVICES_WILDCARD, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_QUOTEGROUPS_INDEX, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_QUOTEPAGES_INDEX, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_QUOTEPROFITABILITY_INDEX, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_QUOTEREPORTS_WILDCARD, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_INDEX_EXISTINGQUOTE, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_INDEX_GETREPORTSFORCLIENT, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_INDEX_CREATECLIENT, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_QUOTE_INDEX, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_QUOTE_DELETE, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_QUOTESETTING_EDIT, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_CLIENT_WILDCARD, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_CONFIGURATION_WILDCARD, self::PRIVILEGE_VIEW);

        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_ADMIN_INDEX_INDEX, self::PRIVILEGE_VIEW);
    }

    /**
     * Sets up the system admin role
     */
    protected function _setupSystemAdminRole ()
    {
        // Add our role
        $this->addRole(self::ROLE_SYSTEM_ADMIN, array(self::ROLE_PROPOSAL_ADMIN, self::ROLE_QUOTE_ADMIN));

        // Add our privileges
        $this->allow(self::ROLE_SYSTEM_ADMIN, self::RESOURCE_DEFAULT_WILDCARD, self::PRIVILEGE_VIEW);
        $this->allow(self::ROLE_SYSTEM_ADMIN, self::RESOURCE_ADMIN_USER_WILDCARD, self::PRIVILEGE_VIEW);
    }


}