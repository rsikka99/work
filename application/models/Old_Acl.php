<?php

class Application_Model_Old_Acl extends Zend_Acl
{
    protected static $_instance;
    protected static $UnrestrictedPages = array(
        'default' => array(
            'auth'  => array(
                'changepassword'
            ),
            'error' => array(
                '%'
            )
        )
    );
    protected static $UnrestrictedMemberPages = array(
        'default' => array(
            'auth'  => array(
                'logout'
            ),
            'index' => array(
                '%'
            ),
            'info'  => array(
                '%'
            )
        ),
        'admin'   => array(
            'index' => array(
                'index'
            ),
            'user'  => array(
                'profile'
            )
        )
    );
    protected static $UnrestrictedGuestOnlyPages = array(
        'default' => array(
            'auth' => array(
                'login',
                'forgotpassword'
            )
        )
    );

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


    public function __construct ()
    {
        $this->addResource("Resource", "ParentResource");
        $this->addRole("Role Name", "Role Parent");
        $this->isAllowed("Role", "Request?", "Privilege");
        $this->allow("Role", "Resource(s)", "Privileges");
    }

    /**
     * Gets privileges for a role.
     * Checks the cache before hitting the database
     *
     * @param int $role_id
     *
     * @return array The privileges for the given role.
     */
    private function getCachedPrivilegesForRole ($role_id)
    {
        // Attempt to load the role from cache
        $role = Zend_Registry::get('Zend_Cache')->load('acl_role_' . $role_id);
        // If the cache does not have the role, lets grab it from the database
        if ($role === false)
        {

            // Grab all the privileges for this role.
            $db            = Zend_Db_Table::getDefaultAdapter();
            $stmt          = $db->query('SELECT module, controller, action FROM privilege WHERE roleId = ?', array(
                                                                                                                  $role_id
                                                                                                             ));
            $privilegeList = $stmt->fetchAll();

            $role = array();
            foreach ($privilegeList as $privilege)
            {
                $role [] = $privilege ['moduleName'] . '_' . $privilege ['controllerName'] . '_' . $privilege ['actionName'];
            }

            // Save the role into the cache now that we've retrieved it
            Zend_Registry::get('Zend_Cache')->save($role, 'acl_role_' . $role_id);
        }

        return $role;
    }

    /**
     * Sees if a request is allowed based on a role list
     *
     * @param array                            $roleList
     * @param Zend_Controller_Request_Abstract $request
     *
     * @return boolean
     */
    private function hasAccessForRequest ($roleList, Zend_Controller_Request_Abstract $request)
    {
        $hasAccess = false;
        foreach ($roleList as $role)
        {
            $privilegeList = $this->getCachedPrivilegesForRole($role ['roleId']);
            if (in_array('%_%_%', $privilegeList) || in_array($request ["moduleName"] . '_%_%', $privilegeList) || in_array($request ["moduleName"] . '_' . $request ["controllerName"] . '_%', $privilegeList) || in_array($request ["moduleName"] . '_' . $request ["controllerName"] . '_' . $request ["actionName"], $privilegeList))
            {
                $hasAccess = true;
                break;
            }
        }

        return $hasAccess;
    }

    /**
     * Checks to see if we are allowed
     *
     * @param null $role
     * @param null $request
     * @param null $privilege
     *
     * @return bool
     */
    public function isAllowed ($role = null, $request = null, $privilege = null)
    {
        $isAllowed  = false;
        $isLoggedIn = ($role !== null);

        $resource ["moduleName"]     = "";
        $resource ["controllerName"] = "";
        $resource ["actionName"]     = "";

        /**
         * If we're processing a request, not a resource
         */
        if (is_null($request) === false && $request !== false && $request instanceof Zend_Controller_Request_Abstract)
        {
            $resource ["moduleName"]     = strtolower($request->getModuleName());
            $resource ["controllerName"] = strtolower($request->getControllerName());
            $resource ["actionName"]     = strtolower($request->getActionName());
        }
        else
        {
            // We're processing a resource instead of a request object
            $boom = explode("__", strtolower($request));
            if (count($boom) !== 3)
            {
                user_error("ACL Resource did not provide exactly 3 paramters. Access will be denied to '$request'", E_USER_WARNING);

                return false;
            }
            $resource ["moduleName"]     = $boom [0];
            $resource ["controllerName"] = $boom [1];
            $resource ["actionName"]     = $boom [2];
        }

        // If it's a global page, let the user through
        if ($this->isGlobalPage($resource, $isLoggedIn))
        {
            return true;
        }

        if (!$isLoggedIn)
        {
            return false;
        }

        $userId = (int)$role;
        /**
         * Root (User 1) always has access
         */
        if ($userId === 1)
        {
            return true;
        }

        // Always allow access to the error controller in the default module
        if ($resource ["moduleName"] == "default" && ($resource ["controllerName"] == "error"))
        {

            $isAllowed = true;
        }
        else
        {
            /* If we're using Zend_Cache, we retrieve data more efficiently */
            if (Zend_Registry::isRegistered('Zend_Cache'))
            {
                $roleList = Zend_Registry::get('Zend_Cache')->load('acl_user_' . $userId);

                // Fetch the roles from the database
                if ($roleList === false)
                {
                    if (strcasecmp($userId, 'Guest') !== 0)
                    {
                        /* Looks like we haven't stored this user's roles yet */
                        $db       = Zend_Db_Table::getDefaultAdapter();
                        $stmt     = $db->query('SELECT roleId FROM user_roles WHERE userId = ?', array(
                                                                                                      $userId
                                                                                                 ));
                        $roleList = $stmt->fetchAll();
                    }

                    Zend_Registry::get('Zend_Cache')->save($roleList, 'acl_user_' . $userId);
                }

                $isAllowed = $this->hasAccessForRequest($roleList, $resource);
            }
            else
            {
                /*
                 * Without caching, we look directly for the required privileges
                 */
                $db = Zend_Db_Table::getDefaultAdapter();

                $stmt = $db->query("
                            SELECT `module`, `controller`, `action` FROM `privileges`
                            INNER JOIN `roles` ON `roles`.`id` = `privileges`.`roleId`
                            INNER JOIN `user_roles` ON `user_roles`.`roleId` = `roles`.`id`
                            WHERE `user_roles`.`userId` = ? AND 
                            ( `module` = '%' OR 
                                ( `module` = ? AND 
                                    ( `controller` = '%' OR 
                                        ( `controller` = ? AND 
                                            ( `action` = '%' OR `action` = ? 
                            ) ) ) ) );", array(
                                              $userId,
                                              $resource ["moduleName"],
                                              $resource ["controllerName"],
                                              $resource ["actionName"]
                                         ));

                // If we get a row then we are allowed in
                $stmt->execute();
                $row = $stmt->fetch();

                if ($row)
                {
                    $isAllowed = true;
                }
            }
        }

        return $isAllowed;
    }

    /**
     * Checks to see if the page is unrestricted.
     *
     * @param string  $resource
     *            The resource to check
     * @param boolean $isLoggedIn
     *            Whether or not the user is logged in.
     *
     * @return boolean
     */
    protected function isGlobalPage ($resource, $isLoggedIn = false)
    {
        // Do we have global pages?
        if ($this->checkResourceAgainstArray($resource, self::$UnrestrictedPages))
        {
            return true;
        }

        if ($isLoggedIn)
        {
            // Do we have global member pages?
            if ($this->checkResourceAgainstArray($resource, self::$UnrestrictedMemberPages))
            {
                return true;
            }
        }
        else
        {
            // Do we have guest only pages?
            if ($this->checkResourceAgainstArray($resource, self::$UnrestrictedGuestOnlyPages))
            {
                return true;
            }
        }

        // Nothing found, so we don't have global access
        return false;
    }

    /**
     * Checks to see if a given resource is within an array
     *
     * @param array $resource
     *                    An array of resources {moduleName, controllerName, actionName}
     * @param       string[][][] $array
     *                    An 3d to check against [module][controller][action]
     *
     * @return boolean True if it is in the array
     */
    private function checkResourceAgainstArray ($resource, $array)
    {
        foreach ($array as $module => $controllers)
        {
            // Check the module name
            if (strcasecmp($module, $resource ['moduleName']) === 0)
            {
                // Check the controller
                foreach ($controllers as $controller => $actions)
                {
                    // Check the controller for a wildcard
                    if (strcasecmp($controller, '%') === 0)
                    {
                        return true;
                    }

                    // Check the controller
                    if (strcasecmp($controller, $resource ['controllerName']) === 0)
                    {
                        foreach ($actions as $action)
                        {
                            // Check the action
                            if (strcasecmp($action, $resource ['actionName']) === 0 || strcasecmp($action, '%') === 0)
                            {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }
}