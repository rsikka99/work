<?php

namespace MPSToolbox\Legacy\Models\Acl;

use Exception;
use MPSToolbox\Legacy\Mappers\UserMapper;
use Zend_Acl;
use Zend_Acl_Resource_Interface;
use Zend_Acl_Role_Interface;
use Zend_Cache_Core;
use Zend_Controller_Request_Abstract;
use Zend_Registry;

/**
 * Class AppAclModel
 *
 * @package MPSToolbox\Legacy\Models\Acl
 */
class AppAclModel extends Zend_Acl
{
    /**
     * Roles
     */
    const ROLE_GUEST                              = "-1";
    const ROLE_AUTHENTICATED_USER                 = "0";
    const ROLE_SYSTEM_ADMIN                       = "1";
    const ROLE_COMPANY_ADMINISTRATOR              = "2";
    const ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR = "3";
    const ROLE_MASTER_DEVICE_ADMINISTRATOR        = "4";

    /**
     * Resource parameters
     */
    const SEPARATOR = "__";
    const WILDCARD  = "%";

    /**
     * This is what kind of access we want to allow. We can use this to provide dynamic pages based on ACL
     */
    const PRIVILEGE_ADMIN = "Admin";
    const PRIVILEGE_VIEW  = "View";
    const PRIVILEGE_EDIT  = "Edit";

    /**
     * @var AppAclModel
     */
    protected static $_instance;

    /**
     * @var Zend_Cache_Core
     */
    protected $_cache;

    /**
     * Gets a instance of MPSToolbox\Legacy\Models\Acl\AppAclModel
     *
     * @return AppAclModel
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
         * Add our various roles
         */
        $this->addRole(self::ROLE_GUEST);
        $this->addRole(self::ROLE_AUTHENTICATED_USER);
        $this->addRole(self::ROLE_SYSTEM_ADMIN);
        $this->addRole(self::ROLE_COMPANY_ADMINISTRATOR);
        $this->addRole(self::ROLE_PRICING_AND_HARDWARE_ADMINISTRATOR);
        $this->addRole(self::ROLE_MASTER_DEVICE_ADMINISTRATOR);
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

        $userId = 0;

        /**
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
                $user = UserMapper::getInstance()->find($userId);
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

                    $roles[] = self::ROLE_AUTHENTICATED_USER;
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


        /**
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

        /**
         * Set our privilege to View if we didn't get passed a privilege
         */
        if ($privilege === null)
        {
            $privilege = self::PRIVILEGE_VIEW;
        }

        /**
         * Test each resource to see if we have access
         */
        foreach ($resourceAssertionList as $resourceToTest)
        {
            foreach ($roles as $roleToTest)
            {
                try
                {
                    if (parent::isAllowed((string)$roleToTest, $resourceToTest, $privilege))
                    {
                        $isAllowed = true;
                        break 2;
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
     * Setter for _cache
     *
     * @param \Zend_Cache_Core $cache
     */
    public function setCache ($cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Getter for _cache
     *
     * @return \Zend_Cache_Core
     */
    public function getCache ()
    {
        if (!isset($this->_cache))
        {
            $cache = Zend_Registry::get('aclCache');
            if ($cache)
            {
                $this->_cache = $cache;
            }
            else
            {
                $this->_cache = false;
            }
        }

        return $this->_cache;
    }

    /**
     * @param      $id
     * @param bool $doNotTestCacheValidity
     * @param bool $doNotUnserialize
     *
     * @see Zend_Cache_Core::load()
     *
     * @return bool|mixed
     */
    public function getFromCache ($id, $doNotTestCacheValidity = false, $doNotUnserialize = false)
    {
        $cache = $this->getCache();
        if ($cache !== false)
        {
            return $cache->load($id, $doNotTestCacheValidity, $doNotUnserialize);
        }

        return false;
    }

    /**
     * @param       $data
     * @param null  $id
     * @param array $tags
     * @param bool  $specificLifetime
     * @param int   $priority
     *
     * @see Zend_Cache_Core::save()
     *
     * @return bool
     */
    public function saveToCache ($data, $id = null, $tags = array(), $specificLifetime = false, $priority = 8)
    {
        $cache = $this->getCache();
        if ($cache !== false)
        {
            $cache->save($data, $id, $tags, $specificLifetime, $priority);

            return true;
        }

        return false;
    }
}