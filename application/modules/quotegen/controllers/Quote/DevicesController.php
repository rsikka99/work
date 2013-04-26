<?php

class Quotegen_Quote_DevicesController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::DEVICES_CONTROLLER);
    }

    /**
     * The index action is for the main page of building a quote.
     * If we don't have a quote here then we should be sent to the "new" page.
     */
    public function indexAction ()
    {
        $buildConfigurationsService = new Quotegen_Service_BuildConfiguration();
        $addDeviceForm              = $buildConfigurationsService->getAddDeviceForm();
        $addFavoriteDeviceForm      = $buildConfigurationsService->getAddFavoriteDeviceForm();

        $this->requireQuote();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ['addDeviceConfiguration']))
            {
                $deviceConfigurationId = (int)$values ["deviceConfigurationId"];
                if ($deviceConfigurationId !== -1)
                {
                    $user = Application_Model_Mapper_User::getInstance()->find($this->_userId);
                    // Get the system and user defaults and apply overrides for user settings $quoteSetting =
                    $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
                    $userSetting  = $user->getUserSettings()->getQuoteSettings();
                    $quoteSetting->applyOverride($userSetting);

                    $newQuoteDeviceId = $this->cloneFavoriteDeviceToQuote($deviceConfigurationId, $quoteSetting->deviceMargin);
                    if ($newQuoteDeviceId)
                    {
                        $this->redirector(null, null, null, array('quoteId' => $this->_quoteId));
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array('danger' => 'There was an error while trying to add the favorite device. Please try again or contact your administrator if the issue persists.'));
                    }
                }
            }
            else if (isset($values ['addDevice']))
            {
                if ($addDeviceForm->isValid($values))
                {
                    $masterDeviceId = (int)$addDeviceForm->getValue("masterDeviceId");
                    if ($masterDeviceId !== -1)
                    {
                        // Save to the database
                        try
                        {

                            $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
                            $userSetting  = Preferences_Model_Mapper_User_Setting::getInstance()->find($this->_userId);
                            $quoteSetting->applyOverride($userSetting->getQuoteSettings());

                            $device = Quotegen_Model_Mapper_Device::getInstance()->find(array($masterDeviceId, Zend_Auth::getInstance()->getIdentity()->dealerId));
                            // Create Quote Device
                            $quoteDevice = $this->syncDevice(new Quotegen_Model_QuoteDevice(), $device);

                            // Setup some defaults that don't get synced
                            $quoteDevice->quoteId       = $this->_quoteId;
                            $quoteDevice->margin        = $quoteSetting->deviceMargin;
                            $quoteDevice->packageCost   = $quoteDevice->calculatePackageCost();
                            $quoteDevice->packageMarkup = 0;
                            $quoteDevice->residual      = 0;
                            $quoteDevice->cost          = $device->cost;
                            // Save our device
                            $quoteDeviceId = Quotegen_Model_Mapper_QuoteDevice::getInstance()->insert($quoteDevice);

                            // Add to default group
                            Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance()->insertDeviceInDefaultGroup($this->_quote->id, (int)$quoteDeviceId);

                            // Create Link to Device
                            $quoteDeviceConfiguration                 = new Quotegen_Model_QuoteDeviceConfiguration();
                            $quoteDeviceConfiguration->masterDeviceId = $masterDeviceId;
                            $quoteDeviceConfiguration->quoteDeviceId  = $quoteDeviceId;
                            Quotegen_Model_Mapper_QuoteDeviceConfiguration::getInstance()->insert($quoteDeviceConfiguration);

                            // Update the quote
                            $this->saveQuote();

                            $this->_flashMessenger->addMessage(array('success' => "Device was added to your quote successfully. Please make any modifications that you wish now."));

                            $this->redirector('edit-quote-device', null, null, array('id' => $quoteDeviceId, 'quoteId' => $this->_quoteId));
                        }
                        catch (Exception $e)
                        {
                            $this->_flashMessenger->addMessage(array('danger' => 'There was an error processing this request.  Please try again.'));
                            Tangent_Log::logException($e);
                        }
                    }

                }
                else
                {
                    throw new Zend_Validate_Exception("Form Validation Failed");
                }

            }
            else if (isset($values ['saveAndContinue']))
            {
                $this->redirector('index', 'quote_groups', null, array('quoteId' => $this->_quoteId));
            }
        }

        $this->view->addDeviceForm         = $addDeviceForm;
        $this->view->addFavoriteDeviceForm = $addFavoriteDeviceForm;
        $this->view->navigationForm        = new Quotegen_Form_Quote_Navigation(Quotegen_Form_Quote_Navigation::BUTTONS_NEXT);
        $this->view->devices               = Quotegen_Model_Mapper_QuoteDevice::getInstance()->fetchDevicesForQuote($this->_quoteId);
    }

    /**
     * Removes a device configuration from the quote
     */
    public function deleteQuoteDeviceAction ()
    {
        // Require that we have a quote object in the database to use this page
        $this->requireQuote();

        // Get the quote device (Also does validation)
        $quoteDevice    = $this->getQuoteDevice('id');
        $optionsDeleted = Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->deleteAllOptionsForQuoteDevice($quoteDevice->id);

        // Delete grouped devices as well.
        //$qouteGroupDevicesDelete = Quotegen_Model_Mapper_


        $quoteDevicesDeleted = Quotegen_Model_Mapper_QuoteDevice::getInstance()->delete($quoteDevice);

        // Update the quote
        $this->saveQuote();

        $this->_flashMessenger->addMessage(array(
                                                'success' => 'Device deleted successfully.'
                                           ));

        $this->redirector('index', null, null, array(
                                                    'quoteId' => $this->_quoteId
                                               ));
    }

    /**
     * This is editing the device configuration
     */
    public function editQuoteDeviceAction ()
    {
        // Require that we have a quote object in the database to use this page
        $this->requireQuote();

        // Get the quote device (Also does validation)
        $quoteDevice = $this->getQuoteDevice('id');

//        $device = Quotegen_Model_Mapper_Device::getInstance()->find(array($quoteDevice->id,Zend_Auth::getInstance()->getIdentity()->dealerId);
        $device = $quoteDevice->getDevice();
        // If the device doesn't exist, we send them back to the normal page
        if (!$device)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'This device no longer has a device attached to it.'
                                               ));
            $this->redirector('index', null, null, array(
                                                        'quoteId' => $this->_quoteId
                                                   ));
        }

        $this->view->device = $device;

        // Create a new form with the mode and roles set
        $form = new Quotegen_Form_QuoteDevice($quoteDevice->id);

        // Prepare the data for the form
        $request = $this->getRequest();
        $form->populate($quoteDevice->toArray());

        // Make sure we are posting data
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();

            // If we cancelled we don't need to validate anything
            if (isset($values ['goBack']))
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirector('index', null, null, array(
                                                            'quoteId' => $this->_quoteId
                                                       ));
            }
            else
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        $formValues = $form->getValues();

                        $numberOfOptionsSaved = 0;


                        // Save the options first

                        foreach ($form->getOptionElements() as $element)
                        {
                            $optionHasChanged = false;

                            /* @var $quoteDeviceOption Quotegen_Model_QuoteDeviceOption */
                            $quoteDeviceOption = $element->quoteDeviceOption;
                            $optionId          = $quoteDeviceOption->id;
                            $quantity          = (int)$formValues ["option{$optionId}quantity"];
                            if ($quantity !== (int)$quoteDeviceOption->quantity)
                            {
                                $quoteDeviceOption->quantity = $quantity;
                                $optionHasChanged            = true;
                            }

                            if ($optionHasChanged)
                            {
                                Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->save($quoteDeviceOption);
                                $numberOfOptionsSaved++;
                            }
                        }

                        Quotegen_Model_Mapper_QuoteDevice::getInstance()->save($quoteDevice);

                        // Update the quote
                        $this->saveQuote();

                        if (isset($values ['add']))
                        {
                            $this->_flashMessenger->addMessage(array('success' => "Device configuration saved."));

                            // Send to the add options page like they asked
                            $this->redirector('add-options-to-quote-device', null, null, array('id' => $quoteDevice->id, 'quoteId' => $this->_quoteId));
                        }
                        else
                        {
                            $this->_flashMessenger->addMessage(array('success' => "Device configuration was updated successfully."));

                            // Send back to the main list if they are finished
                            if (isset($values ['saveAndContinue']))
                            {
                                $this->redirector('index', null, null, array('quoteId' => $this->_quoteId));
                            }
                            $form->populate($quoteDevice->toArray());
                        }
                    }
                    else
                    {
                        $form->populate($quoteDevice->toArray());
                        $form->populate($values);
                        $this->_flashMessenger->addMessage(array('danger' => 'Please correct the errors below'));
                    }
                }
                catch (Exception $e)
                {
                    $form->populate($quoteDevice->toArray());
                    $this->_flashMessenger->addMessage(array('danger' => 'Please correct the errors below'));
                }
            }
        }
        $this->view->form = $form;
    }

    /**
     * This adds one or more options to a device configuration
     */
    public function addOptionsToQuoteDeviceAction ()
    {
        // Require that we have a quote object in the database to use this page
        $this->requireQuote();

        // Get the quote device (Also does validation)
        $quoteDevice = $this->getQuoteDevice('id');

        $quoteDeviceConfiguration = Quotegen_Model_Mapper_QuoteDeviceConfiguration::getInstance()->findByQuoteDeviceId($quoteDevice->id);
        if (!$quoteDeviceConfiguration)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'info' => "Invalid device selected or the device no longer has the ability to be configured."
                                               ));
            $this->redirector('edit-quote-device', null, null, array(
                                                                    'id'      => $quoteDevice->id,
                                                                    'quoteId' => $this->_quoteId
                                                               ));
        }

        $availableOptions = Quotegen_Model_Mapper_Option::getInstance()->fetchAllAvailableOptionsForQuoteDevice($quoteDevice->id, $quoteDeviceConfiguration->masterDeviceId);
        if (count($availableOptions) < 1)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'info' => "There are no more options to add to this device."
                                               ));
            $this->redirector('edit-quote-device', null, null, array(
                                                                    'id'      => $quoteDevice->id,
                                                                    'quoteId' => $this->_quoteId
                                                               ));
        }

        $form = new Quotegen_Form_SelectOptions($availableOptions);
        // Prepare the data for the form
        $request = $this->getRequest();

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
                        // Create a blank quote device option
                        $quoteDeviceOption                   = new Quotegen_Model_QuoteDeviceOption();
                        $quoteDeviceOption->quoteDeviceId    = $quoteDevice->id;
                        $quoteDeviceOption->quantity         = 1;
                        $quoteDeviceOption->includedQuantity = 0;

                        // Create a ready to use link
                        $quoteDeviceConfigurationOption = new Quotegen_Model_QuoteDeviceConfigurationOption();

                        $masterDeviceId                                 = $quoteDevice->getDevice()->masterDeviceId;
                        $quoteDeviceConfigurationOption->masterDeviceId = $masterDeviceId;

                        $deviceOptionMapper                   = Quotegen_Model_Mapper_DeviceOption::getInstance();
                        $quoteDeviceOptionMapper              = Quotegen_Model_Mapper_QuoteDeviceOption::getInstance();
                        $quoteDeviceConfigurationOptionMapper = Quotegen_Model_Mapper_QuoteDeviceConfigurationOption::getInstance();
                        $insertedOptions                      = 0;
                        foreach ($values ['options'] as $optionId)
                        {

                            $quoteDeviceOption   = $this->syncOption($quoteDeviceOption, $deviceOptionMapper->find(array(
                                                                                                                        $masterDeviceId,
                                                                                                                        $optionId
                                                                                                                   )));
                            $quoteDeviceOptionId = $quoteDeviceOptionMapper->insert($quoteDeviceOption);

                            $quoteDeviceConfigurationOption->optionId = $optionId;

                            $quoteDeviceConfigurationOption->quoteDeviceOptionId = $quoteDeviceOptionId;
                            try
                            {
                                $quoteDeviceConfigurationOptionMapper->insert($quoteDeviceConfigurationOption);
                                $insertedOptions++;
                            }
                            catch (Exception $e)
                            {
                                // Do nothing
                            }
                        }

                        if ($insertedOptions > 0)
                        {
                            $quoteDevice->packageCost = $quoteDevice->calculatePackageCost();
                            Quotegen_Model_Mapper_QuoteDevice::getInstance()->save($quoteDevice);

                            // Update the quote
                            $this->saveQuote();
                        }

                        $this->_flashMessenger->addMessage(array(
                                                                'success' => "Successfully added {$insertedOptions} options."
                                                           ));
                        $this->redirector('edit-quote-device', null, null, array(
                                                                                'id'      => $quoteDevice->id,
                                                                                'quoteId' => $this->_quoteId
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
                $this->redirector('edit-quote-device', null, null, array(
                                                                        'id'      => $quoteDevice->id,
                                                                        'quoteId' => $this->_quoteId
                                                                   ));
            }
        }

        $this->view->form = $form;
    }

    /**
     * Removes an option from a device configuration
     */
    public function deleteOptionFromQuoteDeviceAction ()
    {
        // Require that we have a quote object in the database to use this page
        $this->requireQuote();

        // Get the quote device (Also does validation)
        $quoteDevice = $this->getQuoteDevice('id');

        $quoteDeviceOptionId = $this->_getParam('quoteDeviceOptionId', false);
        if ($quoteDeviceOptionId)
        {
            try
            {
                $rowsAffected = Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->delete($quoteDeviceOptionId);

                if ($rowsAffected > 0)
                {
                    $quoteDevice->packageCost = $quoteDevice->calculatePackageCost();
                    Quotegen_Model_Mapper_QuoteDevice::getInstance()->save($quoteDevice);

                    // Update the quote
                    $this->saveQuote();
                }

                $this->_flashMessenger->addMessage(array(
                                                        'success' => "Configuration Option deleted successfully."
                                                   ));
            }
            catch (Exception $e)
            {
                $this->_flashMessenger->addMessage(array(
                                                        'error' => "Could not delete that configuration option."
                                                   ));
            }
        }
        else
        {
            $this->_flashMessenger->addMessage(array(
                                                    'warning' => "You forgot to select an option to delete."
                                               ));
        }
        $this->redirector('edit-quote-device', null, null, array(
                                                                'id'      => $quoteDevice->id,
                                                                'quoteId' => $this->_quoteId
                                                           ));
    }

    /**
     * Sync a device with the master device record
     */
    public function syncDeviceConfigurationAction ()
    {

        // Get the quote device (Also does validation)
        $quoteDevice = $this->getQuoteDevice('id');

        $device = $quoteDevice->getDevice();
        if ($this->performSyncOnQuoteDevice($quoteDevice))
        {
            // Update the quote
            $this->saveQuote();

            $this->_flashMessenger->addMessage(array(
                                                    'success' => "Configuration synced successfully."
                                               ));
        }

        $this->redirector('index', null, null, array(
                                                    'quoteId' => $this->_quoteId
                                               ));
    }

    /**
     * Sync all devices with the master device records for a quote
     */
    public function syncAllDeviceConfigurationsAction ()
    {
        // Require that we have a quote object in the database to use this page
        $this->requireQuote();

        $devicesSynced = 0;
        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
        foreach ($this->_quote->getQuoteDevices() as $quoteDevice)
        {
            if ($this->performSyncOnQuoteDevice($quoteDevice))
            {
                $devicesSynced++;
            }
        }

        if ($devicesSynced > 0)
        {
            // Update the quote
            $this->saveQuote();

            $this->_flashMessenger->addMessage(array(
                                                    'success' => "All device configurations synced successfully. Note: If any device is no longer offered and has been deleted from the system, we have no way of syncing it."
                                               ));
        }
        else
        {
            $this->_flashMessenger->addMessage(array(
                                                    'warning' => 'No device configurations were synced. This could be caused by the devices no longer being in the system.'
                                               ));
        }
        $this->redirector('index', null, null, array(
                                                    'quoteId' => $this->_quoteId
                                               ));
    }
}

