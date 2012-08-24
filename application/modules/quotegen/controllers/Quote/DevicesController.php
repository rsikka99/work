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
        $form = new Quotegen_Form_AddDevice();
        
        $this->requireQuote();
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ['addConfiguration']))
            {
                $deviceConfigurationId = (int)$values ["deviceConfigurationId"];
                if ($deviceConfigurationId === - 1)
                {
                    $this->_helper->redirector('create-new-quote-device', null, null, array (
                            'quoteId' => $this->_quoteId 
                    ));
                }
                else
                {
                    // Get the system and user defaults and apply overrides for user settings $quoteSetting =
                    $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
                    $userSetting = Quotegen_Model_Mapper_UserQuoteSetting::getInstance()->fetchUserQuoteSetting(Zend_Auth::getInstance()->getIdentity()->id);
                    $quoteSetting->applyOverride($userSetting);

                    
                    $newQuoteDeviceId = $this->cloneFavoriteDeviceToQuote($deviceConfigurationId,  $quoteSetting->getDeviceMargin());
                    if ($newQuoteDeviceId)
                    {
                        $this->_helper->redirector('edit-quote-device', null, null, array (
                                'id' => $newQuoteDeviceId, 
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
        }
        
        $this->view->form = $form;
        $this->view->devices = Quotegen_Model_Mapper_QuoteDevice::getInstance()->fetchDevicesForQuote($this->_quoteId);
    }

    /**
     * This function handles dealing with the giant form that is "build devices"
     *
     * @param unknown_type $data            
     * @param unknown_type $form            
     */
    protected function processBuildDevicesForm ($data, $form)
    {
        if ($form->isValid($data))
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            
            try
            {
                $db->beginTransaction();
                $changesMade = false;
                $quoteDeviceMapper = Quotegen_Model_Mapper_QuoteDevice::getInstance();
                foreach ( $form->getQuoteDeviceGroups() as $group )
                {
                    // Save devices and options
                    foreach ( $group->sets as $set )
                    {
                        // We have a flag to see if we need to save the device
                        $deviceHasChanges = false;
                        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
                        $quoteDevice = $set->quoteDevice;
                        $quoteDeviceId = $quoteDevice->getId();
                        $quantity = (int)$form->getValue("quantity{$quoteDeviceId}");
                        
                        // Might as well only save the quantity if it's changed
                        if ($quantity !== (int)$quoteDevice->getQuantity())
                        {
                            $quoteDevice->setQuantity($quantity);
                            $deviceHasChanges = true;
                        }
                        
                        $residual = (int)$form->getValue("residual{$quoteDeviceId}");
                        
                        // Might as well only save the quantity if it's changed
                        if ($residual !== (int)$quoteDevice->getResidual())
                        {
                            $quoteDevice->setResidual($residual);
                            $deviceHasChanges = true;
                        }
                        
                        // We need to figure out if we've changed the margin or price.
                        $margin = (float)$form->getValue("margin{$quoteDeviceId}");
                        $packagePrice = (float)$form->getValue("packagePrice{$quoteDeviceId}");
                        
                        /*
                         * Here we recalculate. If the user has changes both the margin and package price, we'll take
                         * margin as the preferred item to keep changes for.
                         */
                        if ($margin !== (float)$quoteDevice->getMargin())
                        {
                            // Recalculate the package price
                            $quoteDevice->setMargin($margin);
                            $packagePrice = $quoteDevice->calculatePackagePrice();
                            $quoteDevice->setPackagePrice($packagePrice);
                            $deviceHasChanges = true;
                        }
                        else if ($packagePrice !== (float)$quoteDevice->getPackagePrice())
                        {
                            // Recalculate the margin
                            $quoteDevice->setPackagePrice($packagePrice);
                            $margin = $quoteDevice->calculateMargin();
                            $quoteDevice->setMargin($margin);
                            $deviceHasChanges = true;
                        }
                        
                        // Only save if we have changes
                        if ($deviceHasChanges)
                        {
                            $residualElement = $form->getElement("residual{$quoteDeviceId}");
                            $packagePriceElement = $form->getElement("packagePrice{$quoteDeviceId}");
                            $packagePriceElement->setValue($quoteDevice->getPackagePrice());
                            
                            // Throw an exception if invalid so that we may roll back our changes
                            if (! $residualElement->isValid($residual))
                            {
                                throw new Exception("Residual is no longer valid!");
                            }
                            $quoteDeviceMapper->save($quoteDevice);
                            
                            $changesMade = true;
                        }
                    }
                    
                    $pagesMapper = Quotegen_Model_Mapper_QuoteDeviceGroupPage::getInstance();
                    // Save Pages
                    foreach ( $group->quoteDeviceGroupPages as $set )
                    {
                        $pageHasChanges = false;
                        /* @var $quoteDeviceGroupPage Quotegen_Model_QuoteDeviceGroupPage */
                        $quoteDeviceGroupPage = $set->quoteDeviceGroupPage;
                        $quoteDeviceGroupPageId = $quoteDeviceGroupPage->getId();
                        $includedQuantity = (int)$form->getValue("includedQuantity{$quoteDeviceGroupPageId}");
                        $includedPrice = (float)$form->getValue("includedPrice{$quoteDeviceGroupPageId}");
                        $pricePerPage = (float)$form->getValue("pricePerPage{$quoteDeviceGroupPageId}");
                        
                        if ($includedQuantity !== (int)$quoteDeviceGroupPage->getIncludedQuantity())
                        {
                            $pageHasChanges = true;
                            $quoteDeviceGroupPage->setIncludedQuantity($includedQuantity);
                        }
                        
                        if ($includedPrice !== (float)$quoteDeviceGroupPage->getIncludedPrice())
                        {
                            $pageHasChanges = true;
                            $quoteDeviceGroupPage->setIncludedPrice($includedPrice);
                        }
                        
                        if ($pricePerPage !== (float)$quoteDeviceGroupPage->getPricePerPage())
                        {
                            $pageHasChanges = true;
                            $quoteDeviceGroupPage->setPricePerPage($pricePerPage);
                        }
                        
                        if ($pageHasChanges)
                        {
                            $pagesMapper->save($quoteDeviceGroupPage);
                            $changesMade = true;
                        }
                    }
                }
                
                // Only update the quote if we made changes
                if ($changesMade)
                {
                    $this->saveQuote();
                }
                
                $db->commit();
                
                // Let the user know that we have made changes to the quote
                if ($changesMade)
                {
                    $this->_helper->flashMessenger(array (
                            'success' => 'Changes were saved successfully.' 
                    ));
                }
                
                return true;
            }
            catch ( Exception $e )
            {
                $db->rollback();
                $this->_helper->flashMessenger(array (
                        'danger' => 'Please fix the errors below before saving.' 
                ));
                $form->buildBootstrapErrorDecorators();
            }
        }
        else
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'Please fix the errors below before saving.' 
            ));
            $form->buildBootstrapErrorDecorators();
        }
        return false;
    }

    /**
     * Create and add a new device configuration to the quote
     */
    public function createNewQuoteDeviceAction ()
    {
        // Require that we have a quote object in the database to use this page
        $this->requireQuote();
        $this->requireQuoteDeviceGroup();
        $quoteId = $this->_getParam('quoteId');
        
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
                            $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
                            $userQuoteSetting = Quotegen_Model_Mapper_UserQuoteSetting::getInstance()->fetchUserQuoteSetting($this->_userId);
                            $quoteSetting->applyOverride($userQuoteSetting);
                            
                            $masterDeviceId = $form->getValue('masterDeviceId');
                            $device = Quotegen_Model_Mapper_Device::getInstance()->find($masterDeviceId);
                            // Create Quote Device
                            $quoteDevice = $this->syncDevice(new Quotegen_Model_QuoteDevice(), $device);
                            
                            // Setup some defaults that don't get synced
                            $quoteDevice->setQuoteId($quoteId);
                            $quoteDevice->setMargin($quoteSetting->getDeviceMargin());
                            $quoteDevice->setPackagePrice($quoteDevice->calculatePackagePrice());
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
                $this->_helper->redirector('index', null, null, array (
                        'quoteId' => $this->_quoteId 
                ));
            }
        }
        $this->view->form = $form;
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
        
        // TODO: Let them edit the quote device
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
            if (isset($values ['cancel']))
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
                        /* @var $quoteDeviceOption Quotegen_Model_QuoteDeviceOption */
                        foreach ( $form->getOptionElements() as $element )
                        {
                            $optionHasChanged = false;
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
                            if (isset($values ['saveAndFinish']))
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
                            $quoteDevice->setPackagePrice($quoteDevice->calculatePackagePrice());
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
                    $quoteDevice->setPackagePrice($quoteDevice->calculatePackagePrice());
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
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            /* @var $quoteDevice Quotegen_Model_QuoteDevice */
            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDevice )
            {
                if ($this->performSyncOnQuoteDevice($quoteDevice))
                {
                    $devicesSynced ++;
                }
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

    /**
     * This function takes care of adding pages to a quote group
     */
    public function addPagesAction ()
    {
        $quoteDeviceGroupId = $this->_getParam('quoteDeviceGroupId', FALSE);
        if (! $quoteDeviceGroupId)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => "You must be adding pages to a valid group." 
            ));
            // Redirect
            $this->_helper->redirector('index', null, null, array (
                    'quoteId' => $this->_quoteId 
            ));
        }
        
        $quoteDeviceGroup = Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->find($quoteDeviceGroupId);
        if (! $quoteDeviceGroup || $quoteDeviceGroup->getQuoteId() !== $this->_quote->getId())
        {
            $this->_helper->flashMessenger(array (
                    'danger' => "You cannot add pages to this group." 
            ));
            // Redirect
            $this->_helper->redirector('index', null, null, array (
                    'quoteId' => $this->_quoteId 
            ));
        }
        
        $form = new Quotegen_Form_QuoteDeviceGroupPage();
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if (isset($values ['cancel']))
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index', null, null, array (
                        'quoteId' => $this->_quoteId 
                ));
            }
            else
            {
                if ($form->isValid($values))
                {
                    try
                    {
                        // Add pages
                        $quoteDeviceGroupPage = new Quotegen_Model_QuoteDeviceGroupPage();
                        $quoteDeviceGroupPage->setQuoteDeviceGroupId($quoteDeviceGroupId);
                        $quoteDeviceGroupPage->populate($form->getValues());
                        
                        Quotegen_Model_Mapper_QuoteDeviceGroupPage::getInstance()->insert($quoteDeviceGroupPage);
                        
                        // Update the quote
                        $this->saveQuote();
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "Pages successfully added." 
                        ));
                        
                        // Redirect
                        $this->_helper->redirector('index', null, null, array (
                                'quoteId' => $this->_quoteId 
                        ));
                    }
                    catch ( Exception $e )
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => "There was an error saving. Please try again or contact your system administrator." 
                        ));
                    }
                }
                else
                {
                    $form->buildBootstrapErrorDecorators();
                    $this->_helper->flashMessenger(array (
                            'danger' => "Please correct the errors below to continue." 
                    ));
                }
            }
        }
        
        $this->view->form = $form;
    }

    /**
     * Deletes a page from a quote device group
     */
    public function deletePagesAction ()
    {
        $quoteDeviceGroupPageId = $this->_getParam('quoteDeviceGroupPageId', FALSE);
        if (! $quoteDeviceGroupPageId)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => "Please select a valid page to delete" 
            ));
            
            // Redirect
            $this->_helper->redirector('index', null, null, array (
                    'quoteId' => $this->_quoteId 
            ));
        }
        
        $quoteDeviceGroupPage = Quotegen_Model_Mapper_QuoteDeviceGroupPage::getInstance()->find($quoteDeviceGroupPageId);
        if (! $quoteDeviceGroupPage || $quoteDeviceGroupPage->getQuoteDeviceGroup()->getQuoteId() !== $this->_quote->getId())
        {
            $this->_helper->flashMessenger(array (
                    'danger' => "Please select a valid page to delete" 
            ));
            
            // Redirect
            $this->_helper->redirector('index', null, null, array (
                    'quoteId' => $this->_quoteId 
            ));
        }
        
        try
        {
            Quotegen_Model_Mapper_QuoteDeviceGroupPage::getInstance()->delete($quoteDeviceGroupPage);
            
            // Update the quote
            $this->saveQuote();
            
            $this->_helper->flashMessenger(array (
                    'success' => "Pages successfully deleted." 
            ));
        }
        catch ( Exception $e )
        {
            $this->_helper->flashMessenger(array (
                    'danger' => "There was an error deleting the page. Please try again or contact your system administrator." 
            ));
        }
        
        // Redirect
        $this->_helper->redirector('index', null, null, array (
                'quoteId' => $this->_quoteId 
        ));
    }

    /**
     * This action handles removing a quote device group
     */
    public function deleteGroupAction ()
    {
        $quoteDeviceGroupId = $this->_getParam('quoteDeviceGroupId', FALSE);
        if (! $quoteDeviceGroupId)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => "You must select a valid group to delete." 
            ));
            // Redirect
            $this->_helper->redirector('index', null, null, array (
                    'quoteId' => $this->_quoteId 
            ));
        }
        
        $quoteDeviceGroup = Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->find($quoteDeviceGroupId);
        if (! $quoteDeviceGroup || $quoteDeviceGroup->getQuoteId() !== $this->_quote->getId())
        {
            $this->_helper->flashMessenger(array (
                    'danger' => "You are not allowed to delete this group." 
            ));
            // Redirect
            $this->_helper->redirector('index', null, null, array (
                    'quoteId' => $this->_quoteId 
            ));
        }
        
        // Make sure we don't have quote devices
        if (count($quoteDeviceGroup->getQuoteDeviceGroupDevices()) > 0)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => "You cannot delete a group that still has devices. Please remove all pages and devices before attempting to delete a group." 
            ));
            // Redirect
            $this->_helper->redirector('index', null, null, array (
                    'quoteId' => $this->_quoteId 
            ));
        }
        
        // Make sure we don't have pages
        if (count($quoteDeviceGroup->getPages()) > 0)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => "You cannot delete a group that still has pages. Please remove all pages and devices before attempting to delete a group." 
            ));
            // Redirect
            $this->_helper->redirector('index', null, null, array (
                    'quoteId' => $this->_quoteId 
            ));
        }
        
        try
        {
            Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->delete($quoteDeviceGroup);
            
            // Update the quote
            $this->saveQuote();
            
            $this->_helper->flashMessenger(array (
                    'success' => "Group successfully deleted." 
            ));
        }
        catch ( Exception $e )
        {
            $this->_helper->flashMessenger(array (
                    'danger' => "There was an error deleting the group. Please try again or contact your system administrator." 
            ));
        }
        
        // Redirect
        $this->_helper->redirector('index', null, null, array (
                'quoteId' => $this->_quoteId 
        ));
    }
}

