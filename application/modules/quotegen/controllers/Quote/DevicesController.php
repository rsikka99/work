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
        $addDeviceForm = $buildConfigurationsService->getAddDeviceForm();
        $addFavoriteDeviceForm = $buildConfigurationsService->getAddFavoriteDeviceForm();

        $this->requireQuote();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ['addDeviceConfiguration']))
            {
                $deviceConfigurationId = (int)$values ["deviceConfigurationId"];
                if ($deviceConfigurationId !== - 1)
                {
                    // Get the system and user defaults and apply overrides for user settings $quoteSetting =
                    $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
                    $userSetting = Quotegen_Model_Mapper_UserQuoteSetting::getInstance()->fetchUserQuoteSetting(Zend_Auth::getInstance()->getIdentity()->id);
                    $quoteSetting->applyOverride($userSetting);

                    $newQuoteDeviceId = $this->cloneFavoriteDeviceToQuote($deviceConfigurationId, $quoteSetting->getDeviceMargin());
                    if ($newQuoteDeviceId)
                    {
                        $this->_helper->redirector(null, null, null, array (
                                'quoteId' => $this->_quoteId
                        ));
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => 'There was an error while trying to add the favorite device. Please try again or contact your administrator if the issue persists.'
                        ));
                    }
                }
            }
            else if (isset($values ['addDevice']))
            {
                if ($addDeviceForm->isValid($values))
                {
                    $masterDeviceId = (int)$addDeviceForm->getValue("masterDeviceId");
                    if ($masterDeviceId !== - 1)
                    {
                        // Save to the database
                        try
                        {
                            $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
                            $userQuoteSetting = Quotegen_Model_Mapper_UserQuoteSetting::getInstance()->fetchUserQuoteSetting($this->_userId);
                            $quoteSetting->applyOverride($userQuoteSetting);

                            $device = Quotegen_Model_Mapper_Device::getInstance()->find($masterDeviceId);
                            // Create Quote Device
                            $quoteDevice = $this->syncDevice(new Quotegen_Model_QuoteDevice(), $device);

                            // Setup some defaults that don't get synced
                            $quoteDevice->setQuoteId($this->_quoteId);
                            $quoteDevice->setMargin($quoteSetting->getDeviceMargin());
                            $quoteDevice->setPackageCost($quoteDevice->calculatePackageCost());
                            $quoteDevice->setPackageMarkup(0);
                            $quoteDevice->setResidual(0);

                            // Save our device
                            $quoteDeviceId = Quotegen_Model_Mapper_QuoteDevice::getInstance()->insert($quoteDevice);

                            // Add to default group
                            Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance()->insertDeviceInDefaultGroup($this->_quote->getId(), (int)$quoteDeviceId);

                            // Create Link to Device
                            $quoteDeviceConfiguration = new Quotegen_Model_QuoteDeviceConfiguration();
                            $quoteDeviceConfiguration->setMasterDeviceId($masterDeviceId);
                            $quoteDeviceConfiguration->setQuoteDeviceId($quoteDeviceId);
                            Quotegen_Model_Mapper_QuoteDeviceConfiguration::getInstance()->insert($quoteDeviceConfiguration);

                            // Update the quote
                            $this->saveQuote();

                            $this->_helper->flashMessenger(array (
                                    'success' => "Device was added to your quote successfully. Please make any modifications that you wish now."
                            ));

                            $this->_helper->redirector('edit-quote-device', null, null, array (
                                    'id' => $quoteDeviceId,
                                    'quoteId' => $this->_quoteId
                            ));
                        }
                        catch ( Exception $e )
                        {
                            $this->_helper->flashMessenger(array (
                                    'danger' => 'There was an error processing this request.  Please try again.'
                            ));
                            My_Log::logException($e);
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
                $this->_helper->redirector('index', 'quote_groups', null, array (
                        'quoteId' => $this->_quoteId
                ));
            }
        }

        $this->view->addDeviceForm = $addDeviceForm;
        $this->view->addFavoriteDeviceForm = $addFavoriteDeviceForm;
        $this->view->navigationForm = new Quotegen_Form_Quote_Navigation(Quotegen_Form_Quote_Navigation::BUTTONS_NEXT);
        $this->view->devices = Quotegen_Model_Mapper_QuoteDevice::getInstance()->fetchDevicesForQuote($this->_quoteId);
    }

    /**
     * Removes a device configuration from the quote
     */
    public function deleteQuoteDeviceAction ()
    {
        // Require that we have a quote object in the database to use this page
        $this->requireQuote();

        // Get the quote device (Also does validation)
        $quoteDevice = $this->getQuoteDevice('id');
        $optionsDeleted = Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->deleteAllOptionsForQuoteDevice($quoteDevice->getId());

        // Delete grouped devices as well.
        //$qouteGroupDevicesDelete = Quotegen_Model_Mapper_


        $quoteDevicesDeleted = Quotegen_Model_Mapper_QuoteDevice::getInstance()->delete($quoteDevice);

        // Update the quote
        $this->saveQuote();

        $this->_helper->flashMessenger(array (
                'success' => 'Device deleted successfully.'
        ));

        $this->_helper->redirector('index', null, null, array (
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

        // Get the device
        $device = $quoteDevice->getDevice();

        // If the device doesn't exist, we send them back to the normal page
        if (! $device)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'This device no longer has a device attached to it.'
            ));
            $this->_helper->redirector('index', null, null, array (
                    'quoteId' => $this->_quoteId
            ));
        }

        $this->view->device = $device;

        // Create a new form with the mode and roles set
        $form = new Quotegen_Form_QuoteDevice($quoteDevice->getId());

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
                $this->_helper->redirector('index', null, null, array (
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

                        foreach ( $form->getOptionElements() as $element )
                        {
                            $optionHasChanged = false;

                            /* @var $quoteDeviceOption Quotegen_Model_QuoteDeviceOption */
                            $quoteDeviceOption = $element->quoteDeviceOption;
                            $optionId = $quoteDeviceOption->getId();
                            $quantity = (int)$formValues ["option{$optionId}quantity"];
                            if ($quantity !== (int)$quoteDeviceOption->getQuantity())
                            {
                                $quoteDeviceOption->setQuantity($quantity);
                                $optionHasChanged = true;
                            }

                            if ($optionHasChanged)
                            {
                                Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->save($quoteDeviceOption);
                                $numberOfOptionsSaved ++;
                            }
                        }

                        Quotegen_Model_Mapper_QuoteDevice::getInstance()->save($quoteDevice);

                        // Update the quote
                        $this->saveQuote();

                        if (isset($values ['add']))
                        {
                            $this->_helper->flashMessenger(array (
                                    'success' => "Device configuration saved."
                            ));

                            // Send to the add options page like they asked
                            $this->_helper->redirector('add-options-to-quote-device', null, null, array (
                                    'id' => $quoteDevice->getId(),
                                    'quoteId' => $this->_quoteId
                            ));
                        }
                        else
                        {
                            $this->_helper->flashMessenger(array (
                                    'success' => "Device configuration was updated successfully."
                            ));

                            // Send back to the main list if they are finished
                            if (isset($values ['saveAndContinue']))
                            {
                                $this->_helper->redirector('index', null, null, array (
                                        'quoteId' => $this->_quoteId
                                ));
                            }
                            $form->populate($quoteDevice->toArray());
                        }
                    }
                    else
                    {
                        $form->populate($quoteDevice->toArray());
                        $form->populate($values);
                        $form->buildBootstrapErrorDecorators();
                        $this->_helper->flashMessenger(array (
                                'danger' => 'Please correct the errors below'
                        ));
                    }
                }
                catch ( Exception $e )
                {
                    $form->populate($quoteDevice->toArray());
                    $form->buildBootstrapErrorDecorators();
                    $this->_helper->flashMessenger(array (
                            'danger' => 'Please correct the errors below'
                    ));
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

        $quoteDeviceConfiguration = Quotegen_Model_Mapper_QuoteDeviceConfiguration::getInstance()->findByQuoteDeviceId($quoteDevice->getId());
        if (! $quoteDeviceConfiguration)
        {
            $this->_helper->flashMessenger(array (
                    'info' => "Invalid device selected or the device no longer has the ability to be configured."
            ));
            $this->_helper->redirector('edit-quote-device', null, null, array (
                    'id' => $quoteDevice->getId(),
                    'quoteId' => $this->_quoteId
            ));
        }

        $availableOptions = Quotegen_Model_Mapper_Option::getInstance()->fetchAllAvailableOptionsForQuoteDevice($quoteDevice->getId(), $quoteDeviceConfiguration->getMasterDeviceId());
        if (count($availableOptions) < 1)
        {
            $this->_helper->flashMessenger(array (
                    'info' => "There are no more options to add to this device."
            ));
            $this->_helper->redirector('edit-quote-device', null, null, array (
                    'id' => $quoteDevice->getId(),
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
            if (! isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        // Create a blank quote device option
                        $quoteDeviceOption = new Quotegen_Model_QuoteDeviceOption();
                        $quoteDeviceOption->setQuoteDeviceId($quoteDevice->getId());
                        $quoteDeviceOption->setQuantity(1);
                        $quoteDeviceOption->setIncludedQuantity(0);

                        // Create a ready to use link
                        $quoteDeviceConfigurationOption = new Quotegen_Model_QuoteDeviceConfigurationOption();

                        $masterDeviceId = $quoteDevice->getDevice()->getMasterDeviceId();
                        $quoteDeviceConfigurationOption->setMasterDeviceId($masterDeviceId);

                        $deviceOptionMapper = Quotegen_Model_Mapper_DeviceOption::getInstance();
                        $quoteDeviceOptionMapper = Quotegen_Model_Mapper_QuoteDeviceOption::getInstance();
                        $quoteDeviceConfigurationOptionMapper = Quotegen_Model_Mapper_QuoteDeviceConfigurationOption::getInstance();
                        $insertedOptions = 0;
                        foreach ( $values ['options'] as $optionId )
                        {

                            $quoteDeviceOption = $this->syncOption($quoteDeviceOption, $deviceOptionMapper->find(array (
                                    $masterDeviceId,
                                    $optionId
                            )));
                            $quoteDeviceOptionId = $quoteDeviceOptionMapper->insert($quoteDeviceOption);

                            $quoteDeviceConfigurationOption->setOptionId($optionId);

                            $quoteDeviceConfigurationOption->setQuoteDeviceOptionId($quoteDeviceOptionId);
                            try
                            {
                                $quoteDeviceConfigurationOptionMapper->insert($quoteDeviceConfigurationOption);
                                $insertedOptions ++;
                            }
                            catch ( Exception $e )
                            {
                                // Do nothing
                            }
                        }

                        if ($insertedOptions > 0)
                        {
                            $quoteDevice->setPackageCost($quoteDevice->calculatePackageCost());
                            Quotegen_Model_Mapper_QuoteDevice::getInstance()->save($quoteDevice);

                            // Update the quote
                            $this->saveQuote();
                        }

                        $this->_helper->flashMessenger(array (
                                'success' => "Successfully added {$insertedOptions} options."
                        ));
                        $this->_helper->redirector('edit-quote-device', null, null, array (
                                'id' => $quoteDevice->getId(),
                                'quoteId' => $this->_quoteId
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
                        'id' => $quoteDevice->getId(),
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

        $quoteDeviceOptionId = $this->_getParam('quoteDeviceOptionId', FALSE);
        if ($quoteDeviceOptionId)
        {
            try
            {
                $rowsAffected = Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->delete($quoteDeviceOptionId);

                if ($rowsAffected > 0)
                {
                    $quoteDevice->setPackageCost($quoteDevice->calculatePackageCost());
                    Quotegen_Model_Mapper_QuoteDevice::getInstance()->save($quoteDevice);

                    // Update the quote
                    $this->saveQuote();
                }

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
        }
        else
        {
            $this->_helper->flashMessenger(array (
                    'warning' => "You forgot to select an option to delete."
            ));
        }
        $this->_helper->redirector('edit-quote-device', null, null, array (
                'id' => $quoteDevice->getId(),
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

            $this->_helper->flashMessenger(array (
                    'success' => "Configuration synced successfully."
            ));
        }

        $this->_helper->redirector('index', null, null, array (
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
        foreach ( $this->_quote->getQuoteDevices() as $quoteDevice )
        {
            if ($this->performSyncOnQuoteDevice($quoteDevice))
            {
                $devicesSynced ++;
            }
        }

        if ($devicesSynced > 0)
        {
            // Update the quote
            $this->saveQuote();

            $this->_helper->flashMessenger(array (
                    'success' => "All device configurations synced successfully. Note: If any device is no longer offered and has been deleted from the system, we have no way of syncing it."
            ));
        }
        else
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'No device configurations were synced. This could be caused by the devices no longer being in the system.'
            ));
        }
        $this->_helper->redirector('index', null, null, array (
                'quoteId' => $this->_quoteId
        ));
    }
}

