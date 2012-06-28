<?php

class Quotegen_BuildController extends Quotegen_Library_Controller_Quote
{

    /**
     * The index action is for the main page of building a quote.
     * If we don't have a quote here then we should be sent to the "new" page.
     */
    public function indexAction ()
    {
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ['deviceConfigurationId']))
            {
                $deviceConfigurationId = (int)$values ['deviceConfigurationId'];
                if ($deviceConfigurationId === - 1)
                {
                    $this->_helper->redirector('create-new-quote-device');
                }
                else
                {
                    $newDeviceConfigurationId = $this->cloneDeviceConfiguration($deviceConfigurationId);
                    
                    $this->_helper->redirector('edit-quote-device', null, null, array (
                            'id' => $newDeviceConfigurationId 
                    ));
                }
            }
        }
    }

    /**
     * Create and add a new device configuration to the quote
     */
    public function createNewQuoteDeviceAction ()
    {
        $request = $this->getRequest();
        $form = new Quotegen_Form_DeviceConfiguration();
        
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
                            $masterDeviceId = $form->getValue('masterDeviceId');
                            $device = Quotegen_Model_Mapper_Device::getInstance()->find($masterDeviceId);
                            
                            // Create Quote Device
                            $quoteDevice = $this->syncDevice(new Quotegen_Model_QuoteDevice(), $device);
                            
                            // Setup some defaults that don't get synced
                            $quoteDevice->setQuoteId($this->_quoteId);
                            $quoteDevice->setMargin(0);
                            $quoteDevice->setQuantity(1);
                            
                            // Save our device
                            $quoteDeviceId = Quotegen_Model_Mapper_QuoteDevice::getInstance()->insert($quoteDevice);
                            
                            // Create Link to Device
                            $quoteDeviceConfiguration = new Quotegen_Model_QuoteDeviceConfiguration();
                            $quoteDeviceConfiguration->setMasterDeviceId($masterDeviceId);
                            $quoteDeviceConfiguration->setQuoteDeviceId($quoteDeviceId);
                            Quotegen_Model_Mapper_QuoteDeviceConfiguration::getInstance()->insert($quoteDeviceConfiguration);
                            
                            $this->_helper->flashMessenger(array (
                                    'success' => "Device was added to your quote successfully. Please make any modifications that you wish now." 
                            ));
                            
                            $this->_helper->redirector('edit-quote-device', null, null, array (
                                    'id' => $quoteDeviceId 
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
     * Removes a device configuration from the quote
     */
    public function deleteDeviceConfigurationAction ()
    {
    }

    /**
     * This is editing the device configuration
     */
    public function editQuoteDeviceAction ()
    {
        $quoteDeviceId = $this->_getParam('id', false);
        
        // Make sure they passed an id to us
        if (! $quoteDeviceId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a device to edit first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $quoteDevice = Quotegen_Model_Mapper_QuoteDevice::getInstance()->find($quoteDeviceId);
        
        // Validate that we have a quote device that is associated with the quote
        if (! $quoteDevice || $quoteDevice->getQuoteId() !== $this->_quoteId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'You may only edit devices associated with this quote.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Get the device
        $device = $quoteDevice->getDevice();
        
        // TODO: Let them edit the quote device
        // If the device doesn't exist, we send them back to the normal page
        if (! $device)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'This device no longer has a device attached to it.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $this->view->device = $device;
        
        // Create a new form with the mode and roles set
        $form = new Quotegen_Form_QuoteDevice($quoteDeviceId);
        
        // Prepare the data for the form
        $request = $this->getRequest();
        $form->populate($device->toArray());
        
        // Make sure we are posting data
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();
            
            // If we cancelled we don't need to validate anything
            if (isset($values ['cancel']))
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
            else
            {
                
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        $deviceConfigurationMapper = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();
                        //                         $deviceConfiguration->populate($values);
                        //                         $deviceConfiguration->setId($deviceConfigurationId);
                        

                        // Save option quantities
                        $deviceConfigurationOption = new Quotegen_Model_DeviceConfigurationOption();
                        $deviceConfigurationOption->setDeviceConfigurationId($deviceConfigurationId);
                        
                        foreach ( $form->getOptionQuantityElements() as $element )
                        {
                            $optionId = (int)$element->getDescription();
                            $deviceConfigurationOption->setOptionId($optionId);
                            $deviceConfigurationOption->setQuantity($values ["option$optionId"]);
                            Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance()->save($deviceConfigurationOption);
                        }
                        
                        // $rowsAffected = $deviceConfigurationMapper->save($deviceConfiguration, $deviceConfigurationId);
                        

                        if (isset($values ['add']))
                        {
                            $this->_helper->flashMessenger(array (
                                    'success' => "Device configuration saved." 
                            ));
                            
                            // Send to the add options page like they asked
                            $this->_helper->redirector('add-options-to-device-configuration', null, null, array (
                                    'id' => $deviceConfigurationId 
                            ));
                        }
                        else
                        {
                            $this->_helper->flashMessenger(array (
                                    'success' => "Device configuration '{$device->getId()}' was updated sucessfully." 
                            ));
                            
                            // Send back to the main list
                            $this->_helper->redirector('index');
                        }
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
        }
        $this->view->form = $form;
    }

    /**
     * This adds one or more options to a device configuration
     */
    public function addOptionsToDeviceConfigurationAction ()
    {
        $id = $this->_getParam('id', FALSE);
        
        $availableOptions = Quotegen_Model_Mapper_Option::getInstance()->fetchAllAvailableOptionsForDeviceConfiguration($id);
        if (count($availableOptions) < 1)
        {
            $this->_helper->flashMessenger(array (
                    'info' => "There are no more options to add to this device." 
            ));
            $this->_helper->redirector('edit-quote-device', null, null, array (
                    'id' => $id 
            ));
        }
        
        $form = new Quotegen_Form_SelectOptions($availableOptions);
        // Prepare the data for the form
        $request = $this->getRequest();
        
        $deviceConfiguration = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->find($id);
        
        $form->populate($deviceConfiguration->toArray());
        
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
                        $deviceConfigurationOptionMapper = Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance();
                        $deviceConfigurationOption = new Quotegen_Model_DeviceConfigurationOption();
                        $deviceConfigurationOption->setDeviceConfigurationId($deviceConfiguration->getId());
                        
                        $insertedOptions = 0;
                        foreach ( $values ['options'] as $optionId )
                        {
                            $deviceConfigurationOption->setOptionId($optionId);
                            try
                            {
                                $deviceConfigurationOptionMapper->insert($deviceConfigurationOption);
                                $insertedOptions ++;
                            }
                            catch ( Exception $e )
                            {
                                // Do nothing
                            }
                        }
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "Successfully added {$insertedOptions} options to {$deviceConfiguration->getDevice()->getMasterDevice()->getFullDeviceName()} successfully." 
                        ));
                        $this->_helper->redirector('edit-quote-device', null, null, array (
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
                $this->_helper->redirector('edit-quote-device', null, null, array (
                        'id' => $id 
                ));
            }
        }
        
        $this->view->form = $form;
    }

    /**
     * Removes an option from a device configuration
     */
    public function deleteOptionFromDeviceConfigurationAction ()
    {
        $id = $this->_getParam('id', FALSE);
        $optionId = $this->_getParam('optionId', FALSE);
        
        try
        {
            $deviceConfigurationOption = new Quotegen_Model_DeviceConfigurationOption();
            $deviceConfigurationOption->setDeviceConfigurationId($id);
            $deviceConfigurationOption->setOptionId($optionId);
            Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance()->delete($deviceConfigurationOption);
            $this->_helper->flashMessenger(array (
                    'success' => "Configuration Option deleted successfully." 
            ));
        }
        catch ( Exception $e )
        {
            $this->_helper->flashMessenger(array (
                    'error' => "Could not delete that configuration option." 
            ));
        }
        
        $this->_helper->redirector('edit-quote-device', null, null, array (
                'id' => $id 
        ));
    }

    /**
     * Sync a device with the master device record
     */
    public function syncDeviceConfigurationAction ()
    {
        $id = $this->_getParam('id', FALSE);
        if ($id)
        {
            $this->syncDeviceConfigurationToQuote($id);
            $this->_helper->flashMessenger(array (
                    'success' => "Configuration synced successfully." 
            ));
        }
        $this->_helper->redirector('index');
    }

    /**
     * Sync all devices with the master device records for a quote
     */
    public function syncAllDeviceConfigurationsAction ()
    {
        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
        foreach ( $this->_quote->getQuoteDevices() as $quoteDevice )
        {
            $quoteDeviceConfiguration = Quotegen_Model_Mapper_QuoteDeviceConfiguration::getInstance()->findByQuoteDeviceId($quoteDevice->getId());
            if ($quoteDeviceConfiguration)
            {
                $this->syncDeviceConfigurationToQuote($quoteDeviceConfiguration->getMasterDeviceId());
            }
        }
        
        $this->_helper->flashMessenger(array (
                'success' => "All device configurations synced successfully. Note: If the device is no longer offered and has been deleted from the system, we have no way of syncing them." 
        ));
        $this->_helper->redirector('index');
    }
}

