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
    const RESOURCE_DEFAULT_WILDCARD     = "default__%__%";
    const RESOURCE_PROPOSALGEN_WILDCARD = "proposalgen__%__%";
    const RESOURCE_QUOTEGEN_WILDCARD    = "quotegen__%__%";


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
        $this->addResource(self::RESOURCE_DEFAULT_WILDCARD);
        $this->addResource(self::RESOURCE_PROPOSALGEN_WILDCARD);
        $this->addResource(self::RESOURCE_QUOTEGEN_WILDCARD);
    }

    /**
     * Sets up the guest role
     */
    protected function _setupGuestRole ()
    {
        // Add our role
        $this->addRole(self::ROLE_GUEST);

        // Add our privileges
        $this->allow(self::ROLE_GUEST, self::RESOURCE_DEFAULT_WILDCARD, self::PRIVILEGE_VIEW);
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
    }

    /**
     * Sets up the proposal admin role
     */
    protected function _setupProposalAdminRole ()
    {
        // Add our role
        $this->addRole(self::ROLE_PROPOSAL_ADMIN, self::ROLE_PROPOSAL_USER);

        // Add our privileges

        $this->allow(self::ROLE_PROPOSAL_ADMIN, self::RESOURCE_PROPOSALGEN_WILDCARD, self::PRIVILEGE_ADMIN);
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
        $this->allow(self::ROLE_PROPOSAL_USER, self::RESOURCE_PROPOSALGEN_WILDCARD, self::PRIVILEGE_VIEW);
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
        $this->allow(self::ROLE_QUOTE_ADMIN, self::RESOURCE_QUOTEGEN_WILDCARD, self::PRIVILEGE_ADMIN);
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
        $this->allow(self::ROLE_QUOTE_USER, self::RESOURCE_QUOTEGEN_WILDCARD, self::PRIVILEGE_VIEW);
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
    }


}