<?php

class Admin_RolesController extends Zend_Controller_Action
{

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

    public function indexAction ()
    {
        $roleMapper = new Admin_Model_RoleMapper();
        $roleList = $roleMapper->fetchAll();
        $this->view->roles = $roleList;
    }

    public function editAction ()
    {
        // Edit Operations: add del edit
        $operation = $this->getRequest()->getParam("oper");
        $roleId = $this->getRequest()->getParam("roleId");
        $module = $this->getRequest()->getParam("module");
        $controller = $this->getRequest()->getParam("controller");
        $action = $this->getRequest()->getParam("action");
        $privilege_id = $this->getRequest()->getParam("id");
        
        $privMapper = new Admin_Model_PrivilegeMapper();
        
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

    public function getprivilegesAction ()
    {
        $this->view->layout()->disableLayout();
        $privMapper = new Admin_Model_PrivilegeMapper();
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
        
        foreach ( $privileges as $privilege )
        {
            $privilegeList [] = $privilege->toArray();
        }
        $this->view->total = ($limit) ? ceil($numberOfRecords / $limit) : 1;
        $this->view->page = 1;
        $this->view->records = $numberOfRecords;
        $this->view->rows = $privilegeList;
    }

    public function getmodulesAction ()
    {
        $this->view->layout()->disableLayout();
        $moduleList = array (
                "%" 
        );
        $moduleName = trim($this->getRequest()->getParam('moduleName'));
        
        $modulesDir = dir(APPLICATION_PATH . "/modules");
        while ( false !== ($entry = $modulesDir->read()) )
        {
            if (substr($entry, 0, 1) != '.')
            {
                $moduleList [] = $entry;
            }
        }
        
        $this->view->moduleList = $moduleList;
    }

    public function getcontrollersAction ()
    {
        $this->view->layout()->disableLayout();
        $controllerList = array (
                "%" 
        );
        $moduleName = trim($this->getRequest()->getParam('moduleName'));
        if (isset($moduleName) && strlen($moduleName) > 0 && ! (strcasecmp("%", $moduleName) === 0))
        {
            $controllersDir = dir(APPLICATION_PATH . "/modules/$moduleName/controllers");
            while ( false !== ($entry = $controllersDir->read()) )
            {
                if (substr($entry, 0, 1) != '.' && substr($entry, 0, 14) != 'RolesController')
                {
                    // Never include the error controller
                    if (strpos($entry, 'ErrorController') === FALSE)
                        $controllerList [] = substr($entry, 0, strlen($entry) - 4);
                }
            }
        }
        $this->view->controllerList = $controllerList;
    }

    public function getactionsAction ()
    {
        $this->view->layout()->disableLayout();
        $actionList = array (
                "%" 
        );
        $moduleName = trim($this->getRequest()->getParam('moduleName'));
        if (isset($moduleName) && strlen($moduleName) > 0 && strcasecmp("%", $moduleName) !== 0)
        {
            $controllerName = $this->getRequest()->getParam('controller');
            if (isset($controllerName) && strlen($controllerName) > 0 && strcasecmp("%", $controllerName) !== 0)
            {
                $controllerName = str_ireplace("controller", "", $controllerName);
                //set_include_path(APPLICATION_PATH . "/modules/" . $this->getRequest()->getParam('moduleName') . '/controllers/' . PATH_SEPARATOR . get_include_path());
                require_once APPLICATION_PATH . '/modules/' . $moduleName . '/controllers/' . $controllerName . 'Controller.php';
                $controller = new ReflectionClass(($moduleName != 'default' ? ucfirst($moduleName) . '_' : '') . $controllerName . "Controller");
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
}

