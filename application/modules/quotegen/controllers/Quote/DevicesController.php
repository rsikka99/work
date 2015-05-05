<?php
use MPSToolbox\Legacy\Mappers\UserMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\QuoteDeviceForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\SelectOptionsForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\QuoteNavigationForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceConfigurationOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceOptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceConfigurationOptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Services\BuildConfigurationService;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Services\QuoteDeviceService;

/**
 * Class Quotegen_Quote_DevicesController
 */
class Quotegen_Quote_DevicesController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        $this->_navigation->setActiveStep(\MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteStepsModel::STEP_ADD_HARDWARE);
    }

    /**
     * The index action is for the main page of building a quote.
     * If we don't have a quote here then we should be sent to the "new" page.
     */
    public function indexAction ()
    {
        $this->_pageTitle           = ['Quote', 'Add Hardware'];
        $buildConfigurationsService = new BuildConfigurationService();
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
                $defaultDeviceMargin   = $this->getQuoteDevice()->getQuote()->getClient()->getClientSettings()->quoteSettings->defaultPageMargin;
                if ($deviceConfigurationId !== -1)
                {
                    $newQuoteDeviceId = $this->cloneFavoriteDeviceToQuote($deviceConfigurationId, $defaultDeviceMargin);
                    if ($newQuoteDeviceId)
                    {
                        $this->redirectToRoute(null, ['quoteId' => $this->_quoteId]);
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(['danger' => 'There was an error while trying to add the favorite device. Please try again or contact your administrator if the issue persists.']);
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
                            $quoteDevice = $this->getQuoteDeviceService()->addDeviceToQuote($masterDeviceId);

                            // Update the quote
                            $this->saveQuote();

                            $this->_flashMessenger->addMessage(['success' => "Device was added to your quote successfully. Please make any modifications that you wish now."]);

                            $this->redirectToRoute('quotes.add-hardware.edit', ['id' => $quoteDevice->id, 'quoteId' => $this->_quoteId]);
                        }
                        catch (Exception $e)
                        {
                            $this->_flashMessenger->addMessage(['danger' => 'There was an error processing this request.  Please try again.']);
                            \Tangent\Logger\Logger::logException($e);
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
                $this->updateQuoteStepName();
                $this->saveQuote();
                $this->redirectToRoute('quotes.group-devices', ['quoteId' => $this->_quoteId]);
            }
        }

        $this->view->addDeviceForm         = $addDeviceForm;
        $this->view->addFavoriteDeviceForm = $addFavoriteDeviceForm;
        $this->view->navigationForm        = new QuoteNavigationForm(QuoteNavigationForm::BUTTONS_NEXT);
        $devices               = QuoteDeviceMapper::getInstance()->fetchDevicesForQuote($this->_quoteId);

        //make sure we don't use devices that are deleted
        foreach ($devices as $i=>$quoteDevice) {
            $deviceObj = $quoteDevice->getDevice();
            if (!$deviceObj) {
                QuoteDeviceOptionMapper::getInstance()->deleteAllOptionsForQuoteDevice($quoteDevice->id);
                QuoteDeviceMapper::getInstance()->delete($quoteDevice);
                $this->saveQuote();
                unset($devices[$i]);
            }
        }
        $this->view->devices = $devices;
    }

    /**
     * Clears and sets the options on a quote device to use the specified configurations
     */
    public function useConfigurationAction ()
    {
        $deviceConfigurationId = $this->_getParam('configurationId', false);
        $quoteDeviceId         = $this->_getParam('deviceId', false);

        if (!$deviceConfigurationId && !$quoteDeviceId)
        {
            $this->_flashMessenger->addMessage([
                'warning' => 'A configuration and quote device must be set'
            ]);

            $this->redirectToRoute('quotes', ['quoteId' => $this->_quoteId]);
        }

        $this->useConfigurationOnDevice($deviceConfigurationId, $quoteDeviceId);

        $this->redirectToRoute('quotes', ['quoteId' => $this->_quoteId]);
    }

    /**
     * Gets the options and sets them into the view object, this is used on the edit quote device page to load different configurations dynamically
     */
    public function configurationsTableAction ()
    {
        $this->_helper->layout()->disableLayout();
        $configurationId = $this->_getParam('configurationId', false);

        if ($configurationId > 0)
        {
            $deviceConfiguration = DeviceConfigurationMapper::getInstance()->find($configurationId);
            if ($deviceConfiguration instanceof DeviceConfigurationModel)
            {
                $deviceConfigurationOptions = $deviceConfiguration->getOptions();
                $options                    = [];

                foreach ($deviceConfigurationOptions as $deviceConfigurationOption)
                {
                    $data             = $deviceConfigurationOption->getOption()->toArray();
                    $data['quantity'] = $deviceConfigurationOption->quantity;
                    $options []       = $data;
                }

                $this->view->options = $options;
            }
        }
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

        QuoteDeviceOptionMapper::getInstance()->deleteAllOptionsForQuoteDevice($quoteDevice->id);
        QuoteDeviceMapper::getInstance()->delete($quoteDevice);

        // Update the quote
        $this->saveQuote();

        $this->_flashMessenger->addMessage([
            'success' => 'Device deleted successfully.'
        ]);

        $this->redirectToRoute('quotes', [
            'quoteId' => $this->_quoteId
        ]);
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
        $device      = $quoteDevice->getDevice();
        // If the device doesn't exist, we send them back to the normal page
        if (!$device)
        {
            $this->_flashMessenger->addMessage([
                'danger' => 'This device no longer has a device attached to it.'
            ]);
            $this->redirectToRoute('quotes', [
                'quoteId' => $this->_quoteId
            ]);
        }

        $this->view->device = $device;

        // Create a new form with the mode and roles set
        $form = new QuoteDeviceForm($quoteDevice->id);

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
                $this->redirectToRoute('quotes', [
                    'quoteId' => $this->_quoteId
                ]);
            }
            else if (isset($values['useConfiguration']))
            {
                $this->useConfigurationOnDevice($values['configurationId'], $quoteDevice->id);

                $this->redirectToRoute('quotes.add-hardware.edit', [
                    'id'      => $quoteDevice->id,
                    'quoteId' => $this->_quoteId
                ]);
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

                            /* @var $quoteDeviceOption QuoteDeviceOptionModel */
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
                                QuoteDeviceOptionMapper::getInstance()->save($quoteDeviceOption);
                                $numberOfOptionsSaved++;
                            }
                        }

                        QuoteDeviceMapper::getInstance()->save($quoteDevice);

                        // Update the quote
                        $this->saveQuote();

                        if (isset($values ['add']))
                        {
                            $this->_flashMessenger->addMessage(['success' => "Device configuration saved."]);

                            // Send to the add options page like they asked
                            $this->redirectToRoute('quotes.add-hardware.edit.add-options', ['id' => $quoteDevice->id, 'quoteId' => $this->_quoteId]);
                        }
                        else
                        {
                            $this->_flashMessenger->addMessage(['success' => "Device configuration was updated successfully."]);

                            // Send back to the main list if they are finished
                            if (isset($values ['saveAndContinue']))
                            {
                                $this->redirectToRoute('quotes', ['quoteId' => $this->_quoteId]);
                            }
                            $form->populate($quoteDevice->toArray());
                        }
                    }
                    else
                    {
                        $form->populate($quoteDevice->toArray());
                        $form->populate($values);
                        $this->_flashMessenger->addMessage(['danger' => 'Please correct the errors below']);
                    }
                }
                catch (Exception $e)
                {
                    $form->populate($quoteDevice->toArray());
                    $this->_flashMessenger->addMessage(['danger' => 'Please correct the errors below']);
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

        $quoteDeviceConfiguration = QuoteDeviceConfigurationMapper::getInstance()->findByQuoteDeviceId($quoteDevice->id);
        if (!$quoteDeviceConfiguration)
        {
            $this->_flashMessenger->addMessage([
                'info' => "Invalid device selected or the device no longer has the ability to be configured."
            ]);
            $this->redirectToRoute('quotes.add-hardware.edit', [
                'id'      => $quoteDevice->id,
                'quoteId' => $this->_quoteId
            ]);
        }

        $availableOptions = OptionMapper::getInstance()->fetchAllAvailableOptionsForQuoteDevice($quoteDevice->id, $quoteDeviceConfiguration->masterDeviceId);
        if (count($availableOptions) < 1)
        {
            $this->_flashMessenger->addMessage([
                'info' => "There are no more options to add to this device."
            ]);
            $this->redirectToRoute('quotes.add-hardware.edit', [
                'id'      => $quoteDevice->id,
                'quoteId' => $this->_quoteId
            ]);
        }

        $form = new SelectOptionsForm($availableOptions);
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
                        $quoteDeviceOption                   = new QuoteDeviceOptionModel();
                        $quoteDeviceOption->quoteDeviceId    = $quoteDevice->id;
                        $quoteDeviceOption->quantity         = 1;
                        $quoteDeviceOption->includedQuantity = 0;

                        // Create a ready to use link
                        $quoteDeviceConfigurationOption = new QuoteDeviceConfigurationOptionModel();

                        $masterDeviceId                                 = $quoteDevice->getDevice()->masterDeviceId;
                        $quoteDeviceConfigurationOption->masterDeviceId = $masterDeviceId;

                        $deviceOptionMapper                   = DeviceOptionMapper::getInstance();
                        $quoteDeviceOptionMapper              = QuoteDeviceOptionMapper::getInstance();
                        $quoteDeviceConfigurationOptionMapper = QuoteDeviceConfigurationOptionMapper::getInstance();
                        $insertedOptions                      = 0;
                        foreach ($values ['options'] as $optionId)
                        {

                            $quoteDeviceOption   = $this->getQuoteDeviceService()->syncOption($quoteDeviceOption, $deviceOptionMapper->find([
                                $masterDeviceId,
                                $optionId
                            ]));
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
                            QuoteDeviceMapper::getInstance()->save($quoteDevice);

                            // Update the quote
                            $this->saveQuote();
                        }

                        $this->_flashMessenger->addMessage([
                            'success' => "Successfully added {$insertedOptions} options."
                        ]);
                        $this->redirectToRoute('quotes.add-hardware.edit', [
                            'id'      => $quoteDevice->id,
                            'quoteId' => $this->_quoteId
                        ]);
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch (InvalidArgumentException $e)
                {
                    $this->_flashMessenger->addMessage([
                        'danger' => $e->getMessage()
                    ]);
                }
            }
            else
            {
                // User has cancelled. Go back to the edit page
                $this->redirectToRoute('quotes.add-hardware.edit', [
                    'id'      => $quoteDevice->id,
                    'quoteId' => $this->_quoteId
                ]);
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
        $quoteDevice = $this->getQuoteDevice('quoteDeviceId');

        $quoteDeviceOptionId = $this->_getParam('quoteDeviceOptionId', false);
        if ($quoteDeviceOptionId)
        {
            try
            {
                $rowsAffected = QuoteDeviceOptionMapper::getInstance()->delete($quoteDeviceOptionId);

                if ($rowsAffected > 0)
                {
                    $quoteDevice->packageCost = $quoteDevice->calculatePackageCost();
                    QuoteDeviceMapper::getInstance()->save($quoteDevice);

                    // Update the quote
                    $this->saveQuote();
                }

                $this->_flashMessenger->addMessage([
                    'success' => "Configuration option deleted successfully."
                ]);
            }
            catch (Exception $e)
            {
                $this->_flashMessenger->addMessage([
                    'error' => "Could not delete that configuration option."
                ]);
            }
        }
        else
        {
            $this->_flashMessenger->addMessage([
                'warning' => "You forgot to select an option to delete."
            ]);
        }
        $this->redirectToRoute('quotes.add-hardware.edit', [
            'id'      => $quoteDevice->id,
            'quoteId' => $this->_quoteId
        ]);
    }

    /**
     * Sync a device with the master device record
     */
    public function syncDeviceConfigurationAction ()
    {
        // Get the quote device (Also does validation)
        $quoteDevice = $this->getQuoteDevice('id');

        if ($this->getQuoteDeviceService()->performSyncOnQuoteDevice($quoteDevice))
        {
            // Update the quote
            $this->saveQuote();

            $this->_flashMessenger->addMessage([
                'success' => "Configuration synced successfully."
            ]);
        }

        $this->redirectToRoute('quotes', [
            'quoteId' => $this->_quoteId
        ]);
    }

    /**
     * Sync all devices with the master device records for a quote
     */
    public function syncAllDeviceConfigurationsAction ()
    {
        // Require that we have a quote object in the database to use this page
        $this->requireQuote();

        $devicesSynced = 0;
        /* @var $quoteDevice QuoteDeviceModel */
        foreach ($this->_quote->getQuoteDevices() as $quoteDevice)
        {
            if ($this->getQuoteDeviceService()->performSyncOnQuoteDevice($quoteDevice))
            {
                $devicesSynced++;
            }
        }

        if ($devicesSynced > 0)
        {
            // Update the quote
            $this->saveQuote();

            $this->_flashMessenger->addMessage([
                'success' => "All device configurations synced successfully. Note: If any device is no longer offered and has been deleted from the system, we have no way of syncing it."
            ]);
        }
        else
        {
            $this->_flashMessenger->addMessage([
                'warning' => 'No device configurations were synced. This could be caused by the devices no longer being in the system.'
            ]);
        }
        $this->redirectToRoute('quotes', [
            'quoteId' => $this->_quoteId
        ]);
    }

    /**
     *
     * @param int $deviceConfigurationId
     * @param int $quoteDeviceId
     */
    public function useConfigurationOnDevice ($deviceConfigurationId, $quoteDeviceId)
    {
        $quoteDeviceService = new QuoteDeviceService($this->_userId, $this->_quote->id);
        DeviceConfigurationMapper::getInstance()->find($deviceConfigurationId);
        $db = Zend_Db_Table::getDefaultAdapter();

        try
        {
            $mapperDeviceOption = QuoteDeviceOptionMapper::getInstance();
            $device             = QuoteDeviceMapper::getInstance()->find($quoteDeviceId);
            $deviceOptions      = $device->getQuoteDeviceOptions();

            foreach ($deviceOptions as $deviceOption)
            {
                $mapperDeviceOption->delete($deviceOption);
            }

            $db->beginTransaction();
            $deviceConfiguration = DeviceConfigurationMapper::getInstance()->find($deviceConfigurationId);

            // Prepare option link
            $quoteDeviceConfigurationOption                 = new QuoteDeviceConfigurationOptionModel();
            $quoteDeviceConfigurationOption->masterDeviceId = $deviceConfiguration->masterDeviceId;
            foreach ($deviceConfiguration->getOptions() as $option)
            {
                // Get the device option
                $deviceOption = DeviceOptionMapper::getInstance()->find([
                    $deviceConfiguration->masterDeviceId,
                    $option->optionId
                ]);

                // Insert quote device option
                $quoteDeviceOption                = $quoteDeviceService->syncOption(new QuoteDeviceOptionModel(), $deviceOption);
                $quoteDeviceOption->quoteDeviceId = $quoteDeviceId;
                $quoteDeviceOption->quantity      = $option->quantity;
                $quoteDeviceOptionId              = QuoteDeviceOptionMapper::getInstance()->insert($quoteDeviceOption);

                // Insert link
                $quoteDeviceConfigurationOption->quoteDeviceOptionId = $quoteDeviceOptionId;
                $quoteDeviceConfigurationOption->optionId            = $option->optionId;
                QuoteDeviceConfigurationOptionMapper::getInstance()->insert($quoteDeviceConfigurationOption);
                $db->commit();
                $this->_flashMessenger->addMessage(["success" => "Quote has been successfully updated with the configuration."]);
            }
        }
        catch (Exception $e)
        {
            $db->rollback();
            \Tangent\Logger\Logger::logException($e);
            $this->_flashMessenger->addMessage(["error" => "Error: The updates were not saved. Reference #: " . \Tangent\Logger\Logger::getUniqueId()]);
        }
    }
}

