<?php

class Quotegen_DevicesetupController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    /**
     * Displays all devices
     */
    public function indexAction ()
    {
        // Display all of the devices
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter(Proposalgen_Model_Mapper_MasterDevice::getInstance()));
        
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
        $masterDeviceId = $this->_getParam('id', false);
        $this->view->id = $masterDeviceId;
        
        // If they haven't provided an id, send them back to the view all masterDevice
        // page
        if (! $masterDeviceId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a masterDevice to edit first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Get the masterDevice
        $mapper = new Proposalgen_Model_Mapper_MasterDevice();
        $masterDevice = $mapper->find($masterDeviceId);
        $this->view->devicename = $masterDevice->getManufacturer()->getDisplayname() . ' ' . $masterDevice->getPrinterModel();
        
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
        
        // Populate SKU
        $sku = null;
        $devicemapper = new Quotegen_Model_Mapper_Device();
        $device = $devicemapper->find($masterDeviceId);
        if ($device)
        {
            $sku = $device->getSku();
        }
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
                        if (strlen($values ['sku']) > 0)
                        {
                            // Save Device SKU
                            $device = new Quotegen_Model_Device();
                            $devicevalues = array (
                                    'masterDeviceId' => $masterDeviceId, 
                                    'sku' => $values ['sku'] 
                            );
                            $device->populate($devicevalues);
                            
                            // If $sku set above, then record exists to update
                            if ($sku)
                            {
                                $deviceId = $devicemapper->save($device, $masterDeviceId);
                            }
                            
                            // Else no record, so insert it
                            else
                            {
                                $deviceId = $devicemapper->insert($device);
                            }
                        }
                        else
                        {
                            //TODO: Delete Devices record if exists
        // move delete logic into it's own function
        // so it can be called from here and the deleteAction if needed
        // ?? May be easier to turn on cascading deletes?
                            Quotegen_Model_Mapper_DeviceOption::getInstance()->deleteOptionsByDeviceId($masterDeviceId);
                            $devicemapper->delete($device);
                        }
                        
                        // Save Master Device
                        $mapper = new Proposalgen_Model_Mapper_MasterDevice();
                        $masterDevice = new Proposalgen_Model_MasterDevice();
                        foreach ( $values as &$value )
                        {
                            if (strlen($value) < 1)
                                $value = null;
                        }
                        $masterDevice->populate($values);
                        $masterDevice->setId($masterDeviceId);
                        
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
        
        $device = Quotegen_Model_Mapper_Device::getInstance()->find($deviceId);
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
                    Quotegen_Model_Mapper_DeviceOption::getInstance()->delete($device);
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
        $this->view->devicename = $masterDevice->getManufacturer()->getDisplayname() . ' ' . $masterDevice->getPrinterModel();
        
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
        $optionId = null;
        $masterDeviceId = $this->_getParam('id', false);
        $this->view->id = $masterDeviceId;
        
        // If they haven't provided an id, send them back to the view all masterDevice page
        if (! $masterDeviceId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a masterDevice to edit first.' 
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
                    // Get Option Id
                    $optionId = $values ['optionid'];
                    $deviceOptionMapper = new Quotegen_Model_Mapper_DeviceOption();
                    
                    // Save if option and device id
                    if ($optionId && $masterDeviceId)
                    {
                        // Assign Option
                        if (isset($values ['btnAssign']))
                        {
                            // Save device option
                            $deviceOption = new Quotegen_Model_DeviceOption();
                            $deviceOption->setMasterDeviceId($masterDeviceId);
                            $deviceOption->setOptionId($optionId);
                            $deviceOptionMapper->insert($deviceOption);
                            
                            $this->_helper->flashMessenger(array (
                                    'success' => "The option was assigned successfully." 
                            ));
                        }
                        else if (isset($values ['btnUnassign']))
                        {
                            // Delete device option
                            $deviceOption = new Quotegen_Model_DeviceOption();
                            $deviceOption->setMasterDeviceId($masterDeviceId);
                            $deviceOption->setOptionId($optionId);
                            $deviceOptionMapper->delete($deviceOption);
                            
                            $this->_helper->flashMessenger(array (
                                    'success' => "The option was unassigned successfully." 
                            ));
                        }
                    }
                    
                    // Filter
                    else if (isset($values ['btnSearch']))
                    {
                        // Get device options list
                        $device = Quotegen_Model_Mapper_Device::getInstance()->find($masterDeviceId);
                        $assignedOptions = array ();
                        foreach ( $device->getOptions() as $option )
                        {
                            $assignedOptions [] = $option->getId();
                        }
                        
                        // Filter view
                        $view = $values ['cboView'];
                        $this->view->view_filter = $view;
                        
                        if ($view == "assigned")
                        {
                            if ($assignedOptions)
                            {
                                $where = array (
                                        'id IN ( ? )' => $assignedOptions 
                                );
                            }
                            else
                            {
                                $where = array (
                                        'id IN ( ? )' => "NULL" 
                                );
                            }
                        }
                        else if ($view == "unassigned")
                        {
                            if ($assignedOptions)
                            {
                                $where = array (
                                        'id NOT IN ( ? )' => $assignedOptions 
                                );
                            }
                            else
                            {
                                $where = array (
                                        'id NOT IN ( ? )' => "NULL" 
                                );
                            }
                        }
                        
                        // Options Search Filter
                        if (isset($values ['txtCriteria']))
                        {
                            $filter = $values ['criteria_filter'];
                            $criteria = $values ['txtCriteria'];
                            $where = array_merge((array)$where, array (
                                    "{$filter} LIKE ( ? )" => "%{$criteria}%" 
                            ));
                        }
                    }
                    
                    // Clear Filter
                    else
                    {
                        $this->view->view_filter = "all";
                    }
                }
                catch ( Exception $e )
                {
                    $this->_helper->flashMessenger(array (
                            'error' => "An error has occurred." 
                    ));
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
        
        // Get the device and assigned options
        $device = Quotegen_Model_Mapper_Device::getInstance()->find($masterDeviceId);
        $this->view->devicename = $device->getMasterDevice()
            ->getManufacturer()
            ->getDisplayname() . ' ' . $device->getMasterDevice()->getPrinterModel();
        
        $assignedOptions = array ();
        foreach ( $device->getOptions() as $option )
        {
            $assignedOptions [] = $option->getId();
        }
        $this->view->device = $device;
        $this->view->assignedOptions = $assignedOptions;
        
        // Display filterd list of options
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter(Quotegen_Model_Mapper_Option::getInstance(), $where));
        
        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        // Set how many items to show
        $paginator->setItemCountPerPage(15);
        
        // Pass the view the paginator
        $this->view->paginator = $paginator;
    }

    /**
     * Edit device configurations
     */
    public function configurationsAction ()
    {
        // Get master device 
        $masterDeviceId = $this->_getParam('id', false);
        $this->view->id = $masterDeviceId;

        // Default where to this device
        $where = array (
                'masterDeviceId = ?' => $masterDeviceId
        );
        
        // If they haven't provided an id, send them back to the view all masterDevice page
        if (! $masterDeviceId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a masterDevice to edit first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Get the device
        $device = Quotegen_Model_Mapper_Device::getInstance()->find($masterDeviceId);
        $this->view->devicename = $device->getMasterDevice()
            ->getManufacturer()
            ->getDisplayname() . ' ' . $device->getMasterDevice()->getPrinterModel();
        
        // Get device configurations list
        $deviceConfiguration = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->fetchAll(array (
                'masterDeviceId = ?' => $masterDeviceId 
        ));
        $assignedConfigurations = array ();
        foreach ( $deviceConfiguration as $configuration )
        {
            $assignedConfigurations [] = $configuration->getId();
        }
        $this->view->assignedConfigurations = $assignedConfigurations;
        
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
                catch ( Exception $e )
                {
                    $this->_helper->flashMessenger(array (
                            'error' => "An error has occurred." 
                    ));
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
        
        // Display all of the devices
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter(Quotegen_Model_Mapper_DeviceConfiguration::getInstance(), $where));
        
        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        // Set how many items to show
        $paginator->setItemCountPerPage(15);
        
        // Pass the view the paginator
        $this->view->paginator = $paginator;
    }
}
