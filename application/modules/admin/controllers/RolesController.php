<?php

class Admin_RolesController extends Zend_Controller_Action
{

    /**
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::init()
     */
    public function init ()
    {
        /* Initialize action controller here */
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('getactions', 'json')
            ->addActionContext('getmodules', 'json')
            ->addActionContext('getcontrollers', 'json')
            ->addActionContext('getprivileges', 'json')
            ->addActionContext('getroles', 'json')
            ->addActionContext('edit', 'json')
            ->initContext();
    }

    /**
     * Beta action for role management
     */
    public function testAction ()
    {
        $roleId = $this->_getParam('roleId', 2);
        $role = Admin_Model_Mapper_Role::getInstance()->find($roleId);
        $this->view->roles = array (
                $role 
        );
        
        $currentPrivileges [] = array ();
        $alphaNumeric = new Zend_Filter_Alnum();
        /* @var $privilege Admin_Model_Privilege */
        foreach ( $role->getPrivileges() as $privilege )
        {
            $permissionPath = $privilege->getPermissionPath();
            $fieldName = $alphaNumeric->filter($permissionPath);
            $currentPrivileges ["privileges-$fieldName"] = $privilege->getPermissionPath();
        }
        
        $privilegeList = $this->getModuleList(true);
        $form = new Admin_Form_Privileges($privilegeList);
        $form->populate(array (
                'privileges' => $currentPrivileges 
        ));
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            if ($form->isValid($request->getPost()))
            {
                $formValues = $form->getValues();
                $privilegeValues = $formValues ['privileges'];
                $valuesToInsert = array ();
                $valuesToDelete = array ();
                
                $privilegeMapper = Admin_Model_Mapper_Privilege::getInstance();
                $newPrivilege = new Admin_Model_Privilege();
                $newPrivilege->setRoleId($roleId);
                
                foreach ( $privilegeValues as $value )
                {
                    $hasPrivilegeAlready = false;
                    foreach ( $role->getPrivileges() as $privilege )
                    {
                        if (strcasecmp($value, $privilege->getPermissionPath()) === 0)
                        {
                            $hasPrivilegeAlready = true;
                            break;
                        }
                    }
                    
                    if (! $hasPrivilegeAlready)
                    {
                        // Insert
                        $newPrivilege->setFromPermissionPath($value);
                        $privilegeMapper->insert($newPrivilege);
                    }
                }
                
                foreach ( $role->getPrivileges() as $privilege )
                {
                    if (! in_array($privilege->getPermissionPath(), $privilegeValues))
                    {
                        // Delete
                        $privilegeMapper->delete($privilege);
                    }
                }
                
                $this->_helper->flashMessenger(array (
                        'success' => 'New privileges saved successfully' 
                ));
            }
        }
        
        $this->view->form = $form;
    }

    /**
     */
    public function indexAction ()
    {
        $roleMapper = new Admin_Model_Mapper_Role();
        $roleList = $roleMapper->fetchAll();
        $this->view->roles = $roleList;
    }

    /**
     * Handles the jqgrid edit features.
     */
    public function editAction ()
    {
        // Edit Operations: add del edit
        $operation = $this->getRequest()->getParam("oper");
        $roleId = $this->getRequest()->getParam("roleId");
        $module = $this->getRequest()->getParam("moduleName");
        $controller = $this->getRequest()->getParam("controllerName");
        $action = $this->getRequest()->getParam("actionName");
        $privilege_id = $this->getRequest()->getParam("id");
        
        $privMapper = new Admin_Model_Mapper_Privilege();
        
        if (strcasecmp($operation, "delrole") === 0)
        {
        }
        else if (strcasecmp($operation, "addrole") === 0)
        {
        }
        else if (strcasecmp($operation, "del") === 0)
        {
            $privMapper->delete($this->getRequest()
                ->getParam("id"));
        }
        else if (strcasecmp($operation, "add") === 0)
        {
            $privilege = new Admin_Model_Privilege();
            $privilege->setRoleId($roleId)
                ->setModule($module)
                ->setController($controller)
                ->setAction($action);
            $privMapper->insert($privilege);
        }
        else if (strcasecmp($operation, "edit") === 0)
        {
            $privilege = $privMapper->find($privilege_id);
            $privilege->setModule($module)
                ->setController($controller)
                ->setAction($action);
            $privMapper->save($privilege);
        }
    }

    /**
     * Gets the privileges for a role
     */
    public function getprivilegesAction ()
    {
        $this->view->layout()->disableLayout();
        $privMapper = new Admin_Model_Mapper_Privilege();
        $order = null;
        $limit = null;
        $offset = null;
        if (isset($_REQUEST ['rows']))
        {
            $limit = $_REQUEST ['rows'];
            if (isset($_REQUEST ['page']))
            {
                $offset = (((int)$_REQUEST ['page']) - 1) * $limit;
            }
        }
        if (isset($_REQUEST ['sidx']) && isset($_REQUEST ['sord']))
        {
            $order = array (
                    $_REQUEST ['sidx'] . " " . $_REQUEST ["sord"] 
            );
        }
        
        $privilegeList = array ();
        $roleId = $this->getRequest()->getParam('roleId');
        $where = array (
                "roleId = ?" => $roleId 
        );
        
        $numberOfRecords = $privMapper->count($where);
        
        $privileges = $privMapper->fetchAll($where, $order, $limit, $offset);
        
        /* @var $privilege Admin_Model_Privilege */
        foreach ( $privileges as $privilege )
        {
            $privilegeList [] = array (
                    'id' => $privilege->getId(), 
                    'roleId' => $privilege->getRoleId(), 
                    'moduleName' => $privilege->getModule(), 
                    'controllerName' => $privilege->getController(), 
                    'actionName' => $privilege->getAction() 
            );
        }
        $this->view->total = ($limit) ? ceil($numberOfRecords / $limit) : 1;
        $this->view->page = 1;
        $this->view->records = $numberOfRecords;
        $this->view->rows = $privilegeList;
    }

    /**
     * Gets an array of module names that can be used within permissions.
     *
     * @return multitype:stdClass The list of modules.
     */
    public function getModuleList ($withChildren = false)
    {
        $percentModule = $this->createModuleObject('%', '%__');
        
        if ($withChildren)
        {
            $percentController = $this->createControllerObject('%', '%__%__');
            $percentAction = $this->createActionObject('%', '%__%__%');
            
            $percentModule->controllers = array (
                    $percentController 
            );
            $percentController->actions = array (
                    $percentAction 
            );
        }
        
        // Add % to the list
        $moduleList = array (
                $percentModule 
        );
        
        /* @var $modulesDir Directory */
        $modulesDir = dir(APPLICATION_PATH . "/modules");
        while ( false !== ($entry = $modulesDir->read()) )
        {
            $modulePath = APPLICATION_PATH . "/modules/{$entry}";
            // Omit . and .. from the list
            if (substr($entry, 0, 1) != '.' && is_dir($modulePath))
            {
                $moduleName = ucfirst($entry);
                $modulePermissionPath = "{$moduleName}__";
                $moduleList [] = $this->createModuleObject($moduleName, $modulePermissionPath, $modulePath, $withChildren);
            }
        }
        
        $modulesDir->close();
        
        return $moduleList;
    }

    public function createModuleObject ($name, $permissionPath, $path = false, $withChildren = false)
    {
        $module = new stdClass();
        $module->name = $name;
        $module->permissionPath = strtolower($permissionPath);
        $module->path = $path;
        $module->controllers = array ();
        if ($withChildren)
        {
            $module->controllers = $this->getControllerList($module, $withChildren);
        }
        return $module;
    }

    /**
     * Gets a list of controllers for a module
     *
     * @param stdClass $module
     *            The module object to get the controller list for
     * @param boolean $withChildren
     *            DEFAULT FALSE. If set to true this will fetch the actions as as well.
     * @throws InvalidArgumentException
     * @return multitype:stdClass
     */
    public function getControllerList ($module, $withChildren = false)
    {
        if (! $module instanceof stdClass)
        {
            throw new InvalidArgumentException('The parameter "module" is expected to be of type stdClass.');
        }
        
        $percentController = $this->createControllerObject('%', "{$module->permissionPath}%__");
        if ($withChildren)
        {
            $percentController->actions = array (
                    $this->createActionObject('%', $percentController->permissionPath . '%') 
            );
        }
        // Add an object for the wildcard
        $controllerList = array (
                $percentController 
        );
        
        // Read the directory
        $controllerDirectory = dir("{$module->path}/controllers");
        while ( false !== ($entry = $controllerDirectory->read()) )
        {
            $controllerPath = "{$module->path}/controllers";
            // Omit . and .. from the list
            if (substr($entry, 0, 1) != '.')
            {
                $secondControllerPath = "{$controllerPath}/{$entry}";
                // If we have a directory, we need to go into it. (Maybe find a way to recurse through infinite directories?)
                if (is_dir($secondControllerPath))
                {
                    // Read the directory
                    

                    $secondControllerDirectory = dir($secondControllerPath);
                    while ( false !== ($secondEntry = $secondControllerDirectory->read()) )
                    {
                        
                        // Omit . and .. from the list, as well as checking to see that we have a file that matches Controller.php
                        if (substr($secondEntry, 0, 1) != '.' && strpos($secondEntry, 'Controller.php'))
                        {
                            $controllerName = "{$entry}_" . substr($secondEntry, 0, strlen($secondEntry) - 14);
                            $permissionPath = "{$module->permissionPath}{$controllerName}__";
                            $controllerClassName = "{$module->name}_{$controllerName}Controller";
                            // Add a new controller object
                            $controllerList [] = $this->createControllerObject($controllerName, $permissionPath, $controllerClassName, $secondControllerPath, $secondEntry, $withChildren);
                        }
                    }
                }
                else
                {
                    // Make sure we have a controller
                    if (strpos($entry, 'Controller.php'))
                    {
                        $controllerName = substr($entry, 0, strlen($entry) - 14);
                        $permissionPath = "{$module->permissionPath}{$controllerName}__";
                        $controllerClassName = "{$module->name}_{$controllerName}Controller";
                        
                        // Add a new controller object
                        $controllerList [] = $this->createControllerObject($controllerName, $permissionPath, $controllerClassName, $controllerPath, $entry, $withChildren);
                    }
                }
            }
        }
        
        return $controllerList;
    }

    /**
     * Creates a new controller object
     *
     * @param string $name
     *            The name of the controller
     * @param string $className
     *            The classname of the controller
     * @param string $path
     *            The path where the controller is located
     * @param string $filename
     *            The filename of the controller
     * @param boolean $withChildren
     *            DEFAULT FALSE. If set to true this will fetch the actions as as well.
     * @return stdClass
     */
    public function createControllerObject ($name, $permissionPath = false, $className = false, $path = false, $filename = false, $withChildren = false)
    {
        $controller = new stdClass();
        $controller->name = $name;
        $controller->className = $className;
        $controller->permissionPath = strtolower($permissionPath);
        $controller->path = $path;
        $controller->filename = $filename;
        $controller->actions = array ();
        
        if ($withChildren)
        {
            $controller->actions = $this->getActionList($controller);
        }
        
        return $controller;
    }

    /**
     * Gets a list of actions available for a controller
     *
     * @param stdClass $controller
     *            The controller to get actions for
     * @return multitype:stdClass
     */
    public function getActionList ($controller)
    {
        $actionList = array (
                $this->createActionObject('%', "{$controller->permissionPath}%") 
        );
        
        // Load the file
        require_once "{$controller->path}/{$controller->filename}";
        
        // Work with reflection to get all the methods
        $controllerClass = new ReflectionClass($controller->className);
        foreach ( $controllerClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method => $value )
        {
            if (strcmp(substr($value->getName(), - 6), 'Action') === 0)
            {
                $actionName = substr($value->getName(), 0, strlen($value->getName()) - 6);
                
                $permissionPath = "{$controller->permissionPath}{$actionName}";
                $actionList [] = $this->createActionObject($actionName, $permissionPath);
            }
        }
        
        return $actionList;
    }

    public function createActionObject ($name, $permissionPath, $description = false)
    {
        $action = new stdClass();
        $action->name = $name;
        $action->permissionPath = strtolower($permissionPath);
        $action->description = $description;
        return $action;
    }

    /**
     * Gets a list of modules
     */
    public function getmodulesAction ()
    {
        // Disable the layout
        $this->view->layout()->disableLayout();
        
        $moduleList = $this->getModuleList();
        
        $this->view->moduleList = $moduleList;
    }

    /**
     * Gets a list of controllers in a module
     */
    public function getcontrollersAction ()
    {
        // Disable the layout
        $this->view->layout()->disableLayout();
        
        // Add % to the list
        $controllerList = array (
                htmlentities("%") 
        );
        $moduleName = trim($this->getRequest()->getParam('moduleName'));
        
        if (isset($moduleName) && strlen($moduleName) > 0 && ! (strcasecmp("%", $moduleName) === 0))
        {
            $controllersDir = dir(APPLICATION_PATH . "/modules/$moduleName/controllers");
            while ( false !== ($entry = $controllersDir->read()) )
            {
                if (substr($entry, 0, 1) != '.' && substr($entry, 0, 14) != 'RolesController')
                {
                    if (is_dir(APPLICATION_PATH . "/modules/$moduleName/controllers/$entry"))
                    {
                        $subDir = dir(APPLICATION_PATH . "/modules/$moduleName/controllers/$entry");
                        while ( false !== ($subEntry = $subDir->read()) )
                        {
                            if (substr($subEntry, 0, 1) != '.' && substr($subEntry, 0, 14) != 'RolesController')
                            {
                                // Never include the error controller
                                if (strpos($subEntry, 'ErrorController') === FALSE)
                                    $controllerList [] = "{$entry}_" . substr($subEntry, 0, strlen($subEntry) - 14);
                            }
                        }
                    }
                    else
                    {
                        // Never include the error controller
                        if (strpos($entry, 'ErrorController') === FALSE)
                            $controllerList [] = substr($entry, 0, strlen($entry) - 4);
                    }
                }
            }
        }
        $this->view->controllerList = $controllerList;
    }

    /**
     * Gets a list of actions for a controller
     */
    public function getactionsAction ()
    {
        try
        {
            
            // Disable the layout
            $this->view->layout()->disableLayout();
            
            // Add % to the list
            $actionList = array (
                    htmlentities("%") 
            );
            $moduleName = trim($this->_getParam('moduleName'));
            if (isset($moduleName) && strlen($moduleName) > 0 && strcasecmp("%", $moduleName) !== 0)
            {
                $controllerName = $this->getRequest()->getParam('controllerName');
                if (isset($controllerName) && strlen($controllerName) > 0 && strcasecmp("%", $controllerName) !== 0)
                {
                    $controllerName = str_ireplace("controller", "", $controllerName);
                    
                    $folder = APPLICATION_PATH . '/modules/' . $moduleName . '/controllers/';
                    $className = $controllerName;
                    if (strpos($controllerName, '_'))
                    {
                        $boom = explode('_', $controllerName);
                        $folder .= $boom [0] . '/';
                        $controllerName = $boom [1];
                    }
                    
                    // Include the file
                    require_once $folder . $controllerName . 'Controller.php';
                    $controller = new ReflectionClass(($moduleName != 'default' ? ucfirst($moduleName) . '_' : '') . $className . "Controller");
                    foreach ( $controller->getMethods(ReflectionMethod::IS_PUBLIC) as $method => $value )
                    {
                        if (substr($value->getName(), - 6) == 'Action')
                        {
                            $actionList [] = substr($value->getName(), 0, strlen($value->getName()) - 6);
                        }
                    }
                }
            }
            $this->view->actionList = $actionList;
        }
        catch ( Exception $e )
        {
            $this->view->actionList = array ();
        }
    }
}

