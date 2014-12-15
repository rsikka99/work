<?php
use MPSToolbox\Legacy\Forms\DeleteConfirmationForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\ConfigurationForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\SelectOptionsForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationOptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationModel;
use Tangent\Controller\Action;

/**
 * Class Quotegen_ConfigurationController
 */
class Quotegen_ConfigurationController extends Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    /**
     * Displays all global device configurations
     */
    public function indexAction ()
    {
        // Display all of the deviceConfigurations
        $mapper    = DeviceConfigurationMapper::getInstance();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($mapper, array("dealerId = ?" => Zend_Auth::getInstance()->getIdentity()->dealerId)));

        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Set how many items to show
        $paginator->setItemCountPerPage(15);

        // Pass the view the paginator
        $this->view->paginator = $paginator;
    }

    /**
     * Deletes a deviceConfigurations
     */
    public function deleteAction ()
    {
        $id                    = $this->_getParam('id', false);
        $deviceConfigurationId = $this->_getParam('configurationid', false);
        $page                  = $this->_getParam('page', false);

        if (!$deviceConfigurationId)
        {
            $this->_flashMessenger->addMessage(array(
                'warning' => 'Please select a device configuration to delete first.'
            ));
            $this->redirectToRoute('quotes.configurations');
        }

        $mapper              = DeviceConfigurationMapper::getInstance();
        $deviceConfiguration = $mapper->find($deviceConfigurationId);

        if (!$deviceConfiguration)
        {
            $this->_flashMessenger->addMessage(array(
                'danger' => 'There was an error selecting the device configuration to delete.'
            ));

            if ($page == "configurations")
            {
                // User has cancelled. Go back to the edit page
                $this->redirectToRoute('hardware-library.all-devices.configurations', array('id' => $id));
            }
            else
            {
                // User has cancelled. Go back to the edit page
                $this->redirectToRoute('quotes.configurations');
            }
        }

        $message = "Are you sure you want to delete the &quot;{$deviceConfiguration->name}&quot; configuration?";
        $form    = new DeleteConfirmationForm($message);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                // Delete deviceConfiguration from database
                if ($form->isValid($values))
                {
                    $mapper->delete($deviceConfiguration);
                    $this->_flashMessenger->addMessage(array(
                        'success' => "Device configuration \"{$deviceConfiguration->name}\" was deleted successfully."
                    ));

                    if ($page == "configurations")
                    {
                        // User has cancelled. Go back to the edit page
                        $this->redirectToRoute('hardware-library.all-devices.configurations', array('id' => $id));
                    }
                    else
                    {
                        // User has cancelled. Go back to the edit page
                        $this->redirectToRoute('quotes.configurations');
                    }
                }
            }
            else // go back
            {
                if ($page == "configurations")
                {
                    // User has cancelled. Go back to the edit page
                    $this->redirectToRoute('hardware-library.all-devices.configurations', array('id' => $id));
                }
                else
                {
                    // User has cancelled. Go back to the edit page
                    $this->redirectToRoute('quotes.configurations');
                }
            }
        }
        $this->view->form = $form;
    }

    /**
     * Creates a deviceConfiguration
     */
    public function createAction ()
    {
        // Get master device id if passed in
        $page           = $this->_getParam('page', false);
        $masterDeviceId = $this->_getParam('id', false);

        // Get form
        $form = new ConfigurationForm();
        if (count($form->getElement('masterDeviceId')->getMultiOptions()) < 1)
        {
            $this->_flashMessenger->addMessage(array("warning" => "There are no available devices to configure."));
            $this->redirectToRoute('quotes.configurations');
        }

        // Prep the device dropdown
        $form->getElement('masterDeviceId')->setAttrib("onchange", "javascript: document.location.href='/quotegen/configuration/create/id/'+this.value");
        if ($masterDeviceId)
        {
            $form->getElement('masterDeviceId')->setValue($masterDeviceId);
        }
        else
        {
            // Get first master device id from list
            foreach (DeviceMapper::getInstance()->fetchQuoteDeviceListForDealer(Zend_Auth::getInstance()->getIdentity()->dealerId) as $quoteDevice)
            {
                $masterDeviceId = $quoteDevice->masterDeviceId;
                break;

            }
        }
        $deviceOptions = DeviceOptionMapper::getInstance()->fetchDeviceOptionListForDealerAndDevice($masterDeviceId, Zend_Auth::getInstance()->getIdentity()->dealerId);
        // Get device options
        $request = $this->getRequest();
        if ($request->isPost())
        {
            // When user is posting data, get the values that have been posted.
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {

                        // Attempt to save the configuration to the database.
                        $mapper                    = DeviceConfigurationMapper::getInstance();
                        $deviceConfiguration       = new DeviceConfigurationModel();
                        $values ['masterDeviceId'] = $masterDeviceId;
                        $values ['dealerId']       = Zend_Auth::getInstance()->getIdentity()->dealerId;
                        $deviceConfiguration->populate($values);

                        $deviceConfigurationId = $mapper->insert($deviceConfiguration);

                        // Save Options
                        foreach ($deviceOptions as $option)
                        {
                            $optionId = $option->optionId;
                            $quantity = $values ["quantity{$optionId}"];

                            $where                           = "deviceConfigurationId = {$deviceConfigurationId} AND optionId = {$optionId}";
                            $deviceConfigurationOptionMapper = DeviceConfigurationOptionMapper::getInstance();
                            $deviceConfigurationOptionModel  = new DeviceConfigurationOptionModel();

                            if ($quantity > 0)
                            {
                                // Update if option exists
                                $deviceConfigurationOption = $deviceConfigurationOptionMapper->fetch($where);
                                if ($deviceConfigurationOption)
                                {
                                    $deviceConfigurationOption->quantity = $quantity;
                                    $deviceConfigurationOptionMapper->save($deviceConfigurationOption);
                                }

                                // Else Add option
                                else
                                {
                                    $deviceConfigurationOptionModel->deviceConfigurationId = $deviceConfigurationId;
                                    $deviceConfigurationOptionModel->optionId              = $optionId;
                                    $deviceConfigurationOptionModel->quantity              = $quantity;

                                    $deviceConfigurationOptionMapper->insert($deviceConfigurationOptionModel);
                                }
                            }
                            else
                            {

                                // Delete existing device options
                                $deviceConfigurationOption = $deviceConfigurationOptionMapper->fetch($where);
                                $deviceConfigurationOptionMapper->delete($deviceConfigurationOption);
                            }

                        }

                        $this->_flashMessenger->addMessage(array('success' => 'Your configuration was successfully created.'));

                        if ($page == "configurations")
                        {
                            // User has cancelled. Go back to the edit page
                            $this->redirectToRoute('hardware-library.all-devices.configurations', array('id' => $masterDeviceId));
                        }
                        else
                        {
                            // User has cancelled. Go back to the edit page
                            $this->redirectToRoute('quotes.configurations');
                        }
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array('danger' => 'Please correct the errors below.'));
                    }
                }
                catch (Exception $e)
                {
                    $this->_flashMessenger->addMessage(array('danger' => 'There was an error creating this configuration. Please try again.'));
                    Throw new exception("Critical Company Update Error.", 0, $e);

                }

            }
            else // If user has selected cancel send user back to the index pages of this Controller
            {
                if ($page == "configurations")
                {
                    // User has cancelled. Go back to the edit page
                    $this->redirectToRoute('quotes.configurations', array('id' => $masterDeviceId));
                }
                else
                {
                    // User has cancelled. Go back to the edit page
                    $this->redirectToRoute('quotes.configurations');
                }
            }
        }
        // Add form to page
        $form->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript'    => 'forms/quotegen/configurations/create-device-configuration-form.phtml',
                    'deviceOptions' => $deviceOptions
                )
            )
        ));

        $this->view->form = $form;
    }

    /**
     * Edits a deviceConfiguration
     */
    public function editAction ()
    {
        $id                    = $this->_getParam('id', false);
        $deviceConfigurationId = $this->_getParam('configurationid', false);
        $page                  = $this->_getParam('page', false);

        // If they haven't provided an id, send them back to the view all deviceConfiguration page
        if (!$deviceConfigurationId)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a device configuration to edit first.'));

            if ($page == "configurations")
            {
                // User has cancelled. Go back to the edit page
                $this->redirectToRoute('hardware-library.all-devices.configurations', array('id' => $id));
            }
            else
            {
                // User has cancelled. Go back to the edit page
                $this->redirectToRoute('quotes.configurations');
            }
        }

        // Get the deviceConfiguration
        $deviceConfigurationMapper = DeviceConfigurationMapper::getInstance();
        $deviceConfiguration       = $deviceConfigurationMapper->find((int)$deviceConfigurationId);

        // If the deviceConfiguration doesn't exist, send them back to the view all deviceConfigurations page
        if (!$deviceConfiguration)
        {
            $this->_flashMessenger->addMessage(array('danger' => 'There was an error selecting the device configuration to edit.'));

            if ($page == "configurations")
            {
                // User has cancelled. Go back to the edit page
                $this->redirectToRoute('hardware-library.all-devices.configurations', array('id' => $id));
            }
            else
            {
                // User has cancelled. Go back to the edit page
                $this->redirectToRoute('quotes.configurations');
            }
        }
        // If there is a device configuration but it's not with our dealer
        else if ($deviceConfiguration->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array('danger' => 'You do not have permission to access this.'));

            // User has cancelled. Go back to the edit page
            $this->redirectToRoute('quotes.configurations');
        }

        // Create a new form with the mode and roles set
        $form = new ConfigurationForm($deviceConfigurationId);

        // Prepare the data for the form
        $request = $this->getRequest();
        $form->populate($deviceConfiguration->toArray());

        // Get selected options for device
        if (!$id)
        {
            $id = $form->getElement('masterDeviceId')->getValue();
        }
        $where         = "masterDeviceId = {$id}";
        $deviceOptions = DeviceOptionMapper::getInstance()->fetchAll($where);

        // Prep the device dropdown
        $form->getElement('masterDeviceId')->setAttrib("onchange", "javascript: document.location.href='/quotegen/configuration/edit/configurationid/{$deviceConfigurationId}/id/'+this.value");
        $form->getElement('masterDeviceId')->setValue($id);

        // Make sure we are posting data
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();

            // If we cancelled we don't need to validate anything
            if (isset($values ['cancel']))
            {
                if ($page == "configurations")
                {
                    // User has cancelled. Go back to the edit page
                    $this->redirectToRoute('hardware-library.all-devices.configurations', array('id' => $id));
                }
                else
                {
                    // User has cancelled. Go back to the edit page
                    $this->redirectToRoute('quotes.configurations');
                }
            }
            else
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Attempt to save the configuration to the database.
                        $mapper              = DeviceConfigurationMapper::getInstance();
                        $deviceConfiguration = new DeviceConfigurationModel();
                        $values ['id']       = $deviceConfigurationId;
                        $deviceConfiguration->populate($values);
                        $mapper->save($deviceConfiguration);

                        // Save Options
                        foreach ($deviceOptions as $option)
                        {
                            $optionId = $option->optionId;
                            $quantity = $values ["quantity{$optionId}"];

                            $where                           = "deviceConfigurationId = {$deviceConfigurationId} AND optionId = {$optionId}";
                            $deviceConfigurationOptionMapper = DeviceConfigurationOptionMapper::getInstance();
                            $deviceConfigurationOptionModel  = new DeviceConfigurationOptionModel();

                            if ($quantity > 0)
                            {
                                // Update if option exists
                                $deviceConfigurationOption = $deviceConfigurationOptionMapper->fetch($where);
                                if ($deviceConfigurationOption)
                                {
                                    $deviceConfigurationOption->quantity = $quantity;
                                    $deviceConfigurationOptionMapper->save($deviceConfigurationOption);
                                }

                                // Else Add option
                                else
                                {
                                    $deviceConfigurationOptionModel->deviceConfigurationId = $deviceConfigurationId;
                                    $deviceConfigurationOptionModel->optionId              = $optionId;
                                    $deviceConfigurationOptionModel->quantity              = $quantity;
                                    $deviceConfigurationOptionMapper->insert($deviceConfigurationOptionModel);
                                }
                            }
                            else
                            {
                                // Delete existing device options
                                $deviceConfigurationOption = $deviceConfigurationOptionMapper->fetch($where);
                                $deviceConfigurationOptionMapper->delete($deviceConfigurationOption);
                            }
                        }

                        $this->_flashMessenger->addMessage(array('success' => 'Your configuration was successfully updated.'));

                        if ($page == "configurations")
                        {
                            // User has cancelled. Go back to the edit page
                            $this->redirectToRoute('hardware-library.all-devices.configurations', array('id' => $id));
                        }
                        else
                        {
                            // User has cancelled. Go back to the edit page
                            $this->redirectToRoute('quotes.configurations');
                        }
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array(
                            'danger' => 'Please correct the errors below.'
                        ));
                    }
                }
                catch (InvalidArgumentException $e)
                {
                    $this->_flashMessenger->addMessage(array('danger' => $e->getMessage()));
                }
            }
        }

        // Add form to page
        $form->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript'            => 'forms/quotegen/configurations/edit-device-configuration-form.phtml',
                    'deviceOptions'         => $deviceOptions,
                    'deviceConfigurationId' => $deviceConfigurationId
                )
            )
        ));
        $this->view->form = $form;
    }

    /**
     * Adds options to a device
     */
    public function addoptionsAction ()
    {
        $id              = $this->_getParam('id', false);
        $configurationId = $this->_getParam('configurationid', false);
        $page            = $this->_getParam('page', false);

        $availableOptions = OptionMapper::getInstance()->fetchAll();
        if (count($availableOptions) < 1)
        {
            $this->_flashMessenger->addMessage(array('info' => "There are no more options to add to this device."));

            if ($page == "configurations")
            {
                // User has cancelled. Go back to the edit page
                $this->redirectToRoute('hardware-library.all-devices.configurations', array('id' => $id));
            }
            else
            {
                // User has cancelled. Go back to the edit page
                $this->redirectToRoute('quotes.configurations');
            }
        }

        $deviceConfiguration = DeviceConfigurationMapper::getInstance()->find($configurationId);
        $this->view->name    = $deviceConfiguration->name;

        // Prepare the data for the form
        $form = new SelectOptionsForm($availableOptions);
        $form->populate($deviceConfiguration->toArray());

        // Get selected options for device
        $where               = "deviceConfigurationId = {$configurationId}";
        $selectedOptions     = DeviceConfigurationOptionMapper::getInstance()->fetchAll($where);
        $selectedOptionsList = array();
        foreach ($selectedOptions as $option)
        {
            $selectedOptionsList [] = $option->optionId;
        }
        $form->getElement("options")->setValue($selectedOptionsList);

        // Make sure we are posting data
        $request = $this->getRequest();
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
                        $deviceConfigurationOptionMapper                  = DeviceConfigurationOptionMapper::getInstance();
                        $deviceConfigurationOption                        = new DeviceConfigurationOptionModel();
                        $deviceConfigurationOption->deviceConfigurationId = $deviceConfiguration->id;

                        try
                        {
                            // Delete current device configuration options
                            $deviceConfigurationOption = new DeviceConfigurationOptionModel();
                            DeviceConfigurationOptionMapper::getInstance()->deleteDeviceConfigurationOptionById($configurationId);

                            // Insert selected device configuration options
                            $insertedOptions = 0;
                            foreach ($values ['options'] as $optionId)
                            {
                                $deviceConfigurationOption->deviceConfigurationId = $configurationId;
                                $deviceConfigurationOption->optionId              = $optionId;
                                $deviceConfigurationOptionMapper->insert($deviceConfigurationOption);
                                $insertedOptions++;
                            }

                            $this->_flashMessenger->addMessage(array('success' => "Successfully added {$insertedOptions} options to {$deviceConfiguration->name}."));
                        }
                        catch (Exception $e)
                        {
                            $this->_flashMessenger->addMessage(array('danger' => "Failed to add options to configuration. Please try again."));
                        }


                        if ($page == "configurations")
                        {
                            // User has cancelled. Go back to the edit page
                            $this->redirectToRoute('hardware-library.all-devices.configurations', array('id' => $id));
                        }
                        else
                        {
                            // User has cancelled. Go back to the edit page
                            $this->redirectToRoute('quotes.configurations');
                        }
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch (InvalidArgumentException $e)
                {
                    $this->_flashMessenger->addMessage(array('danger' => $e->getMessage()));
                }
            }
            else
            {
                if ($page == "configurations")
                {
                    // User has cancelled. Go back to the edit page
                    $this->redirectToRoute('hardware-library.all-devices.configurations', array('id' => $id));
                }
                else
                {
                    // User has cancelled. Go back to the edit page
                    $this->redirectToRoute('quotes.configurations');
                }
            }
        }

        $this->view->form = $form;
    }

    /**
     * Deletes an option from a device
     */
    public function deleteoptionAction ()
    {
        $id       = $this->_getParam('id', false);
        $optionId = $this->_getParam('optionId', false);

        try
        {
            $deviceConfigurationOption                        = new DeviceConfigurationOptionModel();
            $deviceConfigurationOption->deviceConfigurationId = $id;
            $deviceConfigurationOption->optionId              = $optionId;
            DeviceConfigurationOptionMapper::getInstance()->delete($deviceConfigurationOption);
            $this->_flashMessenger->addMessage(array('success' => "Configuration Option deleted successfully."));
        }
        catch (Exception $e)
        {
            $this->_flashMessenger->addMessage(array('error' => "Could not delete that configuration option."));
        }

        $this->redirectToRoute('hardware-library.all-devices.edit', array('id' => $id));
    }
}

