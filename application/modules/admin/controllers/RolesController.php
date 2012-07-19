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
     * Shows all the roles and allows the user to choose to create, edit, or delete a role.
     */
    public function indexAction ()
    {
        $roleMapper = new Admin_Model_Mapper_Role();
        $roleList = Admin_Model_Mapper_Role::getInstance()->fetchAll();
        $this->view->roles = $roleList;
    }

    /**
     * Allows creation of a role
     */
    public function createAction ()
    {
        $form = new Admin_Form_Role();
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            if ($this->_getParam('cancel', FALSE))
            {
                $this->_helper->redirector('index');
            }
            else
            {
                
                if ($form->isValid($request->getPost()))
                {
                    try
                    {
                        $role = new Admin_Model_Role();
                        $role->populate($form->getValues());
                        $roleId = Admin_Model_Mapper_Role::getInstance()->insert($role);
                        if ($roleId > 0)
                        {
                            $this->_helper->flashMessenger(array (
                                    'success' => "Successfully deleted the role '{$role->getName()}'." 
                            ));
                            $this->_helper->redirector('edit', null, null, array (
                                    'roleId' => $roleId 
                            ));
                        }
                        else
                        {
                            $this->_helper->flashMessenger(array (
                                    'danger' => "There was an issue creating your role. Please try again and if the problem persists contact your system administrator." 
                            ));
                        }
                    }
                    catch ( Exception $e )
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => "There was an issue creating your role. Please try again and if the problem persists contact your system administrator." 
                        ));
                    }
                }
            }
        }
        
        $this->view->form = $form;
    }

    /**
     * Allows deletion of a role
     */
    public function deleteAction ()
    {
        $roleId = $this->_getParam('roleId', FALSE);
        
        if (! $roleId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a role to edit' 
            ));
            $this->_helper->redirector('index');
        }
        
        $role = Admin_Model_Mapper_Role::getInstance()->find($roleId);
        if (! $role)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'It appears that this role does not exist. Please try again if you think this is in error.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $form = new Application_Form_Delete("Are you sure you want to delete the role '{$role->getName()}'?");
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            if ($form->isValid($request->getPost()))
            {
                $result = Admin_Model_Mapper_Role::getInstance()->delete($role);
                if ($result > 0)
                {
                    $this->_helper->flashMessenger(array (
                            'success' => "Successfully deleted the role '{$role->getName()}'." 
                    ));
                    $this->_helper->redirector('index');
                }
                else
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => "Unable to delete the role '{$role->getName()}'. Please try again and contact your system administrator if this problem persists." 
                    ));
                    $this->_helper->redirector('index');
                }
            }
        }
        
        $this->view->form = $form;
    }

    /**
     * Allows the editing of a role
     */
    public function editAction ()
    {
        $roleId = $this->_getParam('roleId', FALSE);
        
        if (! $roleId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a role to edit' 
            ));
            $this->_helper->redirector('index');
        }
        
        $role = Admin_Model_Mapper_Role::getInstance()->find($roleId);
        if (! $role)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'It appears that this role does not exist. Please try again if you think this is in error.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $this->view->role = $role;
        
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
}

