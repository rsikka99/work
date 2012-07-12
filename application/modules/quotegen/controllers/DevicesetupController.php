<?php

class Quotegen_DevicesetupController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    /**
     * Gets a device from the database
     *
     * @param int $id            
     */
    public function getDevice ($id)
    {
        return $this->getDeviceMapper()->find($id);
    }

    /**
     * Gets the mapper
     *
     * @return Quotegen_Model_Mapper_Device
     */
    public function getDeviceMapper ()
    {
        return Quotegen_Model_Mapper_Device::getInstance();
    }

    /**
     * Displays all devices
     */
    public function indexAction ()
    {
        // Display all of the devices
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($this->getDeviceMapper()));
        
        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        // Set how many items to show
        $paginator->setItemCountPerPage(15);
        
        // Pass the view the paginator
        $this->view->paginator = $paginator;
    }

    /**
     * Edit device details
     */
    public function editAction ()
    {
        $id = $this->_getParam('id', false);
        $this->view->id = $id;
        
        // If they haven't provided an id, send them back to the view all masterDevice
        // page
        if (! $id)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a masterDevice to edit first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Get the masterDevice
        $mapper = new Proposalgen_Model_Mapper_MasterDevice();
        $masterDevice = $mapper->find($id);
        
        // If the masterDevice doesn't exist, send them back t the view all masterDevices page
        if (! $masterDevice)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the masterDevice to edit.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Create a new form with the mode and roles set
        $form = new Quotegen_Form_DeviceSetup();
        
        // Prepare the data for the form
        $form->populate($masterDevice->toArray());
        
        // Get SKU
        $devicemapper = new Quotegen_Model_Mapper_Device();
        $device = $devicemapper->find($id);
        $sku = $device->getSku();
        $form->getElement('sku')->setValue($sku);
        
        // Make sure we are posting data
        $request = $this->getRequest();
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();
            
            // If we cancelled we don't need to validate anything
            if (! isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Save Device SKU
                        $devicevalues = array (
                                'masterDeviceId' => $id, 
                                'sku' => $values ['sku'] 
                        );
                        $device->populate($devicevalues);
                        $deviceId = $devicemapper->save($device, $id);
                        
                        // Save Master Device
                        $mapper = new Proposalgen_Model_Mapper_MasterDevice();
                        $masterDevice = new Proposalgen_Model_MasterDevice();
                        foreach ( $values as &$value )
                        {
                            if (strlen($value) < 1)
                                $value = null;
                        }
                        $masterDevice->populate($values);
                        $masterDevice->setId($id);
                        
                        // Save to the database with cascade insert turned on
                        $masterDeviceId = $mapper->save($masterDevice, $masterDeviceId);
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "MasterDevice '{$masterDevice->getFullDeviceName()}' was updated sucessfully." 
                        ));
                    }
                    
                    // Error
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch ( InvalidArgumentException $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => $e->getMessage() 
                    ));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Deletes a device
     */
    public function deleteAction ()
    {
        $deviceId = $this->_getParam('id', false);
        
        if (! $deviceId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a device to delete first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $device = $this->getDevice($deviceId);
        if (! $device)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the device to delete.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // TODO: Show how many of each option will be deleted
        // Get all the deviceConfiguration associated with the masterDeviceId
        $deviceConfigurations = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->fetchAllDeviceConfigurationByDeviceId($deviceId);
        
        // Set up all mappers required for deletion
        $userDeviceConfigurationMapper = Quotegen_Model_Mapper_UserDeviceConfiguration::getInstance();
        $globalDeviceConfigurationMapper = Quotegen_Model_Mapper_GlobalDeviceConfiguration::getInstance();
        $quoteDeviceConfigurationMapper = Quotegen_Model_Mapper_QuoteDeviceConfiguration::getInstance();
        $deviceConfigurationMapper = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();
        $deviceConfigurationOptionsMapper = Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance();
        
        // TODO: Show what is being deleted in messages
        //         foreach ( $deviceConfigurations as $deviceConfiguration)
        //         {
        

        //             $deviceConfigurationId = $deviceConfiguration->getId();
        //             $userDeviceConfigurationMapper->countByDeviceId($deviceConfigurationId);
        //             $globalDeviceConfigurationMapper->countByDeviceId($deviceConfigurationId);
        //             $quoteDeviceConfigurationMapper->countByDeviceId($deviceConfigurationId);
        //             $deviceConfigurationMapper->countByDeviceId($deviceConfigurationId);
        //             $deviceConfigurationOptionsMapper->countByDeviceId($deviceConfigurationId);
        //         }
        

        $message = "Are you sure you want to delete {$device->getMasterDeviceId()}?";
        $form = new Application_Form_Delete($message);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                // delete device from database
                if ($form->isValid($values))
                {
                    // Delete quote device options link
                    Quotegen_Model_Mapper_DeviceOption::getInstance()->deleteOptionsByDeviceId($deviceId);
                    
                    /* @var $deviceConfiguration Quotegen_Model_DeviceConfiguration */
                    foreach ( $deviceConfigurations as $deviceConfiguration )
                    {
                        $deviceConfigurationId = $deviceConfiguration->getId();
                        // Delete user device configuration link
                        $userDeviceConfigurationMapper->deleteUserDeviceConfigurationByDeviceId($deviceConfigurationId);
                        // Delete global device configurations link
                        $globalDeviceConfigurationMapper->delete($deviceConfigurationId);
                        // Delete the device configuration options
                        $deviceConfigurationOptionsMapper->deleteDeviceConfigurationOptionById($deviceConfigurationId);
                        // Delete the deviceConfiguration
                        $deviceConfigurationMapper->delete($deviceConfiguration);
                    }
                    $this->getDeviceMapper()->delete($device);
                    $this->_helper->flashMessenger(array (
                            'success' => "Device  {$device->getMasterDeviceId()} was deleted successfully." 
                    ));
                    $this->_helper->redirector('index');
                }
            }
            else
            {
                $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Edit device toners
     */
    public function tonersAction ()
    {
        $where = null;
        $tonerId = null;
        $masterDeviceId = $this->_getParam('id', false);
        
        // Pass values back to view
        $this->view->id = $masterDeviceId;
        
        // Default view
        $this->view->view_filter = "assigned";
        
        // If they haven't provided an id, send them back to the view all master device page
        if (! $masterDeviceId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a master device to edit first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Get the master device
        $mapper = new Proposalgen_Model_Mapper_MasterDevice();
        $masterDevice = $mapper->find($masterDeviceId);
        
        // If the master device doesn't exist, send them back to the view all master devices page
        if (! $masterDevice)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the master device to edit.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Populate manufacturers dropdown
        $manufacturers = Proposalgen_Model_Mapper_Manufacturer::getInstance()->fetchAll();
        $this->view->manufacturers = $manufacturers;
        
        // Make sure we are posting data
        $request = $this->getRequest();
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();
            
            // If we cancelled we don't need to validate anything
            if (! isset($values ['cancel']))
            {
                try
                {
                    // Get Toner Id
                    $tonerId = $values ['tonerid'];
                    
                    // Assign Toner
                    if (isset($values ['btnAssign']))
                    {
                        // Save if tonerid and device id
                        if ($tonerId && $masterDeviceId)
                        {
                            // Validate Toner is allowed for Device
                            $threeColorCombined = array (
                                    "3 COLOR", 
                                    "BLACK" 
                            );
                            $threeColorSeparated = array (
                                    "BLACK", 
                                    "CYAN", 
                                    "MAGENTA", 
                                    "YELLOW" 
                            );
                            $fourColorCombined = array (
                                    "4 COLOR" 
                            );
                            $blackOnly = array (
                                    "BLACK" 
                            );
                            
                            // Get toner
                            $toner = Admin_Model_Mapper_Toner::getInstance()->find($tonerId);
                            $tonerColor = $toner->getTonerColor()->getTonerColorName();
                            
                            // Get master device toner config
                            $tonerConfig = $masterDevice->getTonerConfig()->getTonerConfigName();
                            
                            $validToner = false;
                            switch ($tonerConfig)
                            {
                                case "3 COLOR - COMBINED" :
                                    if (in_array($tonerColor, $threeColorCombined))
                                    {
                                        $validToner = true;
                                    }
                                    break;
                                case "3 COLOR - SEPARATED" :
                                    if (in_array($tonerColor, $threeColorSeparated))
                                    {
                                        $validToner = true;
                                    }
                                    break;
                                case "4 COLOR - COMBINED" :
                                    if (in_array($tonerColor, $fourColorCombined))
                                    {
                                        $validToner = true;
                                    }
                                    break;
                                case "BLACK ONLY" :
                                    if (in_array($tonerColor, $blackOnly))
                                    {
                                        $validToner = true;
                                    }
                                    break;
                            }
                            
                            if ($validToner)
                            {
                                // Save device toner
                                $deviceTonerMapper = new Proposalgen_Model_Mapper_DeviceToner();
                                $deviceToner = new Proposalgen_Model_DeviceToner();
                                $deviceToner->setTonerId($tonerId);
                                $deviceToner->setMasterDeviceId($masterDeviceId);
                                $deviceTonerMapper->save($deviceToner);
                                
                                $this->_helper->flashMessenger(array (
                                        'success' => "The toner was assigned successfully." 
                                ));
                            }
                            else
                            {
                                $this->_helper->flashMessenger(array (
                                        'danger' => "The toner is an invalid toner for this device." 
                                ));
                            }
                        }
                    }
                    
                    // Unassign Toner
                    else if (isset($values ['btnUnassign']))
                    {
                        $devicetonerMapper = new Proposalgen_Model_Mapper_DeviceToner();
                        $devicetonerMapper->delete(array (
                                'toner_id = ?' => $tonerId, 
                                'master_device_id = ?' => $masterDeviceId 
                        ));
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "The toner was unassigned successfully." 
                        ));
                    }
                    
                    // Filter
                    else if (isset($values ['btnSearch']))
                    {
                        // Get Device Toners List
                        $deviceToners = Proposalgen_Model_Mapper_DeviceToner::getInstance()->getDeviceToners($masterDeviceId);
                        $assignedToners = array ();
                        foreach ( $deviceToners as $toner )
                        {
                            $assignedToners [] = $toner->getId();
                        }
                        
                        // Filter view
                        $view = $values ['cboView'];
                        $this->view->view_filter = $view;
                        
                        if ($view == "assigned")
                        {
                            $where = array (
                                    'id IN ( ? )' => $assignedToners 
                            );
                        }
                        else if ($view == "unassigned")
                        {
                            $where = array (
                                    'id NOT IN ( ? )' => $assignedToners 
                            );
                        }
                        
                        // Toners Search Filter
                        $filter = $values ['criteria_filter'];
                        
                        if ($filter == 'sku')
                        {
                            $criteria = $values ['txtCriteria'];
                            $where = array_merge((array)$where, array (
                                    'sku LIKE ( ? )' => '%' . $criteria . '%' 
                            ));
                        }
                        else
                        {
                            $criteria = $values ['cboCriteria'];
                            $where = array_merge((array)$where, array (
                                    'manufacturer_id = ?' => $criteria 
                            ));
                        }
                    }
                    
                    // Clear Filter
                    else
                    {
                        $this->view->view_filter = "all";
                    }
                }
                catch ( InvalidArgumentException $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => $e->getMessage() 
                    ));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }
        
        $deviceToners = Proposalgen_Model_Mapper_DeviceToner::getInstance()->getDeviceToners($masterDeviceId);
        $assignedToners = array ();
        foreach ( $deviceToners as $toner )
        {
            $assignedToners [] = $toner->getId();
        }
        $this->view->assignedToners = $assignedToners;
        
        // Display filterd list of toners
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter(Admin_Model_Mapper_Toner::getInstance(), $where));
        
        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        // Set how many items to show
        $paginator->setItemCountPerPage(15);
        
        // Pass the view the paginator
        $this->view->paginator = $paginator;
    }

    /**
     * Edit device options
     */
    public function optionsAction ()
    {
        $where = null;
        $id = $this->_getParam('id', false);
        $this->view->id = $id;
    
        // If they haven't provided an id, send them back to the view all masterDevice page
        if (! $id)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a masterDevice to edit first.'
            ));
            $this->_helper->redirector('index');
        }
        
        // Get the device
        $mapper = Quotegen_Model_Mapper_Device::getInstance();
        $device = $mapper->find($id);
        // If the device doesn't exist, send them back t the view all devices page
        if (! $device)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the device to edit.' 
            ));
            $this->_helper->redirector('index');
        }
        $this->view->device = $device;
        
        // Make sure we are posting data
        $request = $this->getRequest();
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();
    
            // If we cancelled we don't need to validate anything
            if (! isset($values ['cancel']))
            {
                try
                {
                }
                catch ( InvalidArgumentException $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => $e->getMessage()
                    ));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }
    }
    
    /**
     * Edit device configurations
     */
    public function configurationsAction ()
    {
        $where = null;
        $id = $this->_getParam('id', false);
        $this->view->id = $id;
        
        // If they haven't provided an id, send them back to the view all masterDevice page
        if (! $id)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a masterDevice to edit first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Get the masterDevice
        $mapper = new Proposalgen_Model_Mapper_MasterDevice();
        $masterDevice = $mapper->find($id);
        
        // If the masterDevice doesn't exist, send them back t the view all masterDevices page
        if (! $masterDevice)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the masterDevice to edit.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Make sure we are posting data
        $request = $this->getRequest();
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();
            
            // If we cancelled we don't need to validate anything
            if (! isset($values ['cancel']))
            {
                try
                {
                }
                catch ( InvalidArgumentException $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => $e->getMessage() 
                    ));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }
    }

}
