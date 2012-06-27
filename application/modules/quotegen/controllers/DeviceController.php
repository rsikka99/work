<?php

class Quotegen_DeviceController extends Zend_Controller_Action
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
                        $globalDeviceConfigurationMapper->deleteGlobalDeviceConfigurationById($deviceConfigurationId);
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
     * Creates a new device
     */
    public function createAction ()
    {
        $request = $this->getRequest();
        $form = new Quotegen_Form_Device();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if (! isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Save to the database
                        try
                        {
                            $device = new Quotegen_Model_Device();
                            $device->populate($values);
                            $deviceId = $this->getDeviceMapper()->insert($device);
                            
                            $this->_helper->flashMessenger(array (
                                    'success' => "Device {$device->getMasterDeviceId()} was added successfully." 
                            ));
                            
                            // Redirect them here so that the form reloads
                            $this->_helper->redirector('edit', null, null, array (
                                    'id' => $deviceId 
                            ));
                        }
                        catch ( Exception $e )
                        {
                            $this->_helper->flashMessenger(array (
                                    'danger' => 'There was an error processing this request.  Please try again.' 
                            ));
                            $form->populate($request->getPost());
                        }
                    }
                    else
                    {
                        throw new Zend_Validate_Exception("Form Validation Failed");
                    }
                }
                catch ( Zend_Validate_Exception $e )
                {
                    $form->buildBootstrapErrorDecorators();
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
     * Edits a device
     */
    public function editAction ()
    {
        $deviceId = $this->_getParam('id', false);
        
        // If they haven't provided an id, send them back to the view all device page
        if (! $deviceId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a device to edit first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Get the device
        $mapper = Quotegen_Model_Mapper_Device::getInstance();
        $device = $mapper->find($deviceId);
        // If the device doesn't exist, send them back t the view all devices page
        if (! $device)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the device to edit.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Create a new form with the mode and roles set
        $form = new Quotegen_Form_Device($device->getMasterDevice()->getFullDeviceName());
        
        // Prepare the data for the form
        $request = $this->getRequest();
        $form->populate($device->toArray());
        
        // Make sure we are posting data
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();
            
            // If we cancelled we don't need to validate anything
            if (! isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        $device->populate($values);
                        
                        // Save to the database with cascade insert turned on
                        $deviceId = $mapper->save($device, $deviceId);
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "Device '" . $this->view->escape($device->getMasterDeviceId()) . "' was updated sucessfully." 
                        ));
                        $this->_helper->redirector('index');
                    }
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
        
        $this->view->device = $device;
        $this->view->form = $form;
    }

    /**
     * Adds options to a device
     */
    public function addoptionsAction ()
    {
        $id = $this->_getParam('id', FALSE);
        
        $availableOptions = Quotegen_Model_Mapper_Option::getInstance()->fetchAllAvailableOptionsForDevice($id);
        if (count($availableOptions) < 1)
        {
            $this->_helper->flashMessenger(array (
                    'info' => "There are no more options to add to this device." 
            ));
            $this->_helper->redirector('edit', null, null, array (
                    'id' => $id 
            ));
        }
        
        $form = new Quotegen_Form_SelectOptions($availableOptions);
        // Prepare the data for the form
        $request = $this->getRequest();
        
        $device = $this->getDevice($id);
        
        $form->populate($device->toArray());
        
        // Make sure we are posting data
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();
            
            // If we cancelled we don't need to validate anything
            if (! isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        $deviceOptionMapper = Quotegen_Model_Mapper_DeviceOption::getInstance();
                        $deviceOption = new Quotegen_Model_DeviceOption();
                        $deviceOption->setMasterDeviceId($device->getMasterDeviceId());
                        
                        $insertedOptions = 0;
                        foreach ( $values ['options'] as $optionId )
                        {
                            $deviceOption->setOptionId($optionId);
                            try
                            {
                                $deviceOptionMapper->insert($deviceOption);
                                $insertedOptions ++;
                            }
                            catch ( Exception $e )
                            {
                                // Do nothing
                            }
                        }
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "Successfully added {$insertedOptions} options to {$device->getMasterDevice()->getFullDeviceName()} successfully." 
                        ));
                        $this->_helper->redirector('edit', null, null, array (
                                'id' => $id 
                        ));
                    }
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
                // User has cancelled. Go back to the edit page
                $this->_helper->redirector('edit', null, null, array (
                        'id' => $id 
                ));
            }
        }
        
        $this->view->form = $form;
    }

    /**
     * Deletes an option from a device
     */
    public function deleteoptionAction ()
    {
        $id = $this->_getParam('id', FALSE);
        $optionId = $this->_getParam('optionId', FALSE);
        
        try
        {
            $deviceOption = new Quotegen_Model_DeviceOption();
            $deviceOption->setMasterDeviceId($id);
            $deviceOption->setOptionId($optionId);
            Quotegen_Model_Mapper_DeviceOption::getInstance()->delete($deviceOption);
            $this->_helper->flashMessenger(array (
                    'success' => "Option deleted successfully." 
            ));
        }
        catch ( Exception $e )
        {
            $this->_helper->flashMessenger(array (
                    'error' => "Could not delete that option." 
            ));
        }
        
        $this->_helper->redirector('edit', null, null, array (
                'id' => $id 
        ));
    }

    /**
     * View a device
     */
    public function viewAction ()
    {
        $this->view->device = Quotegen_Model_Mapper_Device::getInstance()->find($this->_getParam('id', false));
    }
}

