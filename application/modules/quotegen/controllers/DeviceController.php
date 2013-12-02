<?php

/**
 * Class Quotegen_DeviceController
 */
class Quotegen_DeviceController extends Tangent_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    /**
     * Gets a device from the database
     *
     * @param int $id
     *
     * @return \Quotegen_Model_Device
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

        if (!$deviceId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'warning' => 'Please select a device to delete first.'
                                               ));
            $this->redirector('index');
        }

        $device = $this->getDevice($deviceId);
        if (!$device)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'There was an error selecting the device to delete.'
                                               ));
            $this->redirector('index');
        }

        // Get all the deviceConfiguration associated with the masterDeviceId
        $deviceConfigurations = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->fetchAllDeviceConfigurationByDeviceId($deviceId);

        $message = "Are you sure you want to delete {$device->masterDeviceId}?";
        $form    = new Application_Form_Delete($message);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                // Delete device from database
                if ($form->isValid($values))
                {
                    // Delete quote device options link 
                    Quotegen_Model_Mapper_DeviceOption::getInstance()->deleteOptionsByDeviceId($deviceId);

                    /* @var $deviceConfiguration Quotegen_Model_DeviceConfiguration */
                    foreach ($deviceConfigurations as $deviceConfiguration)
                    {
                        $deviceConfigurationId = $deviceConfiguration->id;
                        // Delete user device configuration link
                        Quotegen_Model_Mapper_UserDeviceConfiguration::getInstance()->deleteUserDeviceConfigurationByDeviceId($deviceConfigurationId);
                        // Delete global device configurations link
                        Quotegen_Model_Mapper_GlobalDeviceConfiguration::getInstance()->delete($deviceConfigurationId);
                        // Delete the device configuration options 
                        Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance()->deleteDeviceConfigurationOptionById($deviceConfigurationId);
                        // Delete the deviceConfiguration
                        Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->delete($deviceConfiguration);
                    }
                    $this->getDeviceMapper()->delete($device);
                    $this->_flashMessenger->addMessage(array(
                                                            'success' => "Device  '{$device->getMasterDevice()->getFullDeviceName()}' was deleted successfully."
                                                       ));
                    $this->redirector('index');
                }
            }
            else
            {
                $this->redirector('index');
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
        $form    = new Quotegen_Form_Device();

        if ($request->isPost())
        {
            $values = $request->getPost();

            if (!isset($values ['cancel']))
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

                            $this->_flashMessenger->addMessage(array(
                                                                    'success' => "Device {$device->masterDeviceId} was added successfully."
                                                               ));

                            // Redirect them here so that the form reloads
                            $this->redirector('edit', null, null, array(
                                                                       'id' => $deviceId
                                                                  ));
                        }
                        catch (Exception $e)
                        {
                            $this->_flashMessenger->addMessage(array(
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
                catch (Zend_Validate_Exception $e)
                {
                    $form->buildBootstrapErrorDecorators();
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirector('index');
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
        if (!$deviceId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'warning' => 'Please select a device to edit first.'
                                               ));
            $this->redirector('index');
        }

        // Get the device
        $deviceMapper = Quotegen_Model_Mapper_Device::getInstance();

        $device = $deviceMapper->find($deviceId);
        // If the device doesn't exist, send them back to the view all devices page
        if (!$device)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'There was an error selecting the device to edit.'
                                               ));
            $this->redirector('index');
        }

        // Create a new form with the mode and roles set
        $form = new Quotegen_Form_Device($deviceId);

        // Prepare the data for the form
        $request = $this->getRequest();
        $form->populate($device->toArray());

        // Make sure we are posting data
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();
            // Set the device name 
            $values['deviceName'] = $device->getMasterDevice()->getFullDeviceName();

            // If we cancelled we don't need to validate anything	
            if (!isset($values ['back']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        // Save individual option included quantities
                        $deviceOptionMapper = Quotegen_Model_Mapper_DeviceOption::getInstance();
                        foreach ($form->getDeviceOptionElements() as $object)
                        {
                            $includeQuantity = $object->deviceOptionElement->getValue();
                            if (!$includeQuantity)
                            {
                                $includeQuantity = 0;
                            }
                            $object->deviceOption->includedQuantity = $includeQuantity;
                            $deviceOptionMapper->save($object->deviceOption);
                        }

                        $this->_flashMessenger->addMessage(array(
                                                                'success' => "Device '" . $this->view->escape($device->getMasterDevice()->getFullDeviceName()) . "' was updated successfully."
                                                           ));

                        // Save new device attributes (SKU)
                        $deviceMapper->save($device);

                        if (isset($values ['addOption']))
                        {
                            $this->redirector('addoptions', null, null, array(
                                                                             'deviceId' => $deviceId
                                                                        ));
                        }
                        $form->populate($values);
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch (InvalidArgumentException $e)
                {
                    $this->_flashMessenger->addMessage(array(
                                                            'danger' => $e->getMessage()
                                                       ));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Adds options to a device
     */
    public function addoptionsAction ()
    {
        $deviceId = $this->_getParam('deviceId', false);

        $availableOptions = Quotegen_Model_Mapper_Option::getInstance()->fetchAllAvailableOptionsForDevice($deviceId);
        if (count($availableOptions) < 1)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'info' => "There are no more options to add to this device."
                                               ));
            $this->redirector('edit', null, null, array(
                                                       'id' => $deviceId
                                                  ));
        }

        $form = new Quotegen_Form_SelectOptions($availableOptions);
        // Prepare the data for the form
        $request = $this->getRequest();

        $device = $this->getDevice($deviceId);

        $form->populate($device->toArray());

        // Make sure we are posting data
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();

            // If we cancelled we don't need to validate anything
            if (!isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        $deviceOptionMapper           = Quotegen_Model_Mapper_DeviceOption::getInstance();
                        $deviceOption                 = new Quotegen_Model_DeviceOption();
                        $deviceOption->masterDeviceId = $device->masterDeviceId;

                        $insertedOptions = 0;

                        foreach ($values ['options'] as $optionId)
                        {
                            $deviceOption->optionId         = (int)$optionId;
                            $deviceOption->includedQuantity = 0;
                            try
                            {
                                $deviceOptionMapper->insert($deviceOption);
                                $insertedOptions++;
                            }
                            catch (Exception $e)
                            {
                                // Do nothing
                            }
                        }

                        $this->_flashMessenger->addMessage(array(
                                                                'success' => "Successfully added {$insertedOptions} options to {$device->getMasterDevice()->getFullDeviceName()} successfully."
                                                           ));
                        $this->redirector('edit', null, null, array(
                                                                   'id' => $deviceId
                                                              ));
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch (InvalidArgumentException $e)
                {
                    $this->_flashMessenger->addMessage(array(
                                                            'danger' => $e->getMessage()
                                                       ));
                }
            }
            else
            {
                // User has cancelled. Go back to the edit page
                $this->redirector('edit', null, null, array(
                                                           'id' => $deviceId
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
        $id = $this->_getParam('id', false);

        $optionId = $this->_getParam('optionId', false);

        try
        {
            $deviceOption                 = new Quotegen_Model_DeviceOption();
            $deviceOption->masterDeviceId = $id;
            $deviceOption->optionId       = $optionId;
            Quotegen_Model_Mapper_DeviceOption::getInstance()->delete($deviceOption);
            $this->_flashMessenger->addMessage(array(
                                                    'success' => "Option deleted successfully."
                                               ));
        }
        catch (Exception $e)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'error' => "Could not delete that option."
                                               ));
        }

        $this->redirector('edit', null, null, array(
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

