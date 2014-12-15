<?php
use MPSToolbox\Legacy\Forms\DeleteConfirmationForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\DeviceConfigurationForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\SelectOptionsForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationOptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationModel;
use Tangent\Controller\Action;

/**
 * Class Quotegen_DeviceConfigurationController
 */
class Quotegen_DeviceConfigurationController extends Action
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
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($mapper));

        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Set how many items to show
        $paginator->setItemCountPerPage(15);

        // Pass the view the paginator
        $this->view->paginator = $paginator;
    }

    public function viewglobalAction ()
    {
        // Display all of the deviceConfigurations
        $mapper    = DeviceConfigurationMapper::getInstance();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($mapper));

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
        $deviceConfigurationId = $this->_getParam('id', false);

        if (!$deviceConfigurationId)
        {
            $this->_flashMessenger->addMessage(array(
                'warning' => 'Please select a device configuration to delete first.'
            ));
            $this->redirectToRoute('quotes.device-configurations');
        }

        $mapper              = DeviceConfigurationMapper::getInstance();
        $deviceConfiguration = $mapper->find($deviceConfigurationId);

        if (!$deviceConfiguration)
        {
            $this->_flashMessenger->addMessage(array(
                'danger' => 'There was an error selecting the device configuration to delete.'
            ));
            $this->redirectToRoute('quotes.device-configurations');
        }

        $message = "Are you sure you want to delete {$deviceConfiguration->id}?";
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
                        'success' => "Device configuration  {$deviceConfiguration->id} was deleted successfully."
                    ));
                    $this->redirectToRoute('quotes.device-configurations');
                }
            }
            else // Go back
            {
                $this->redirectToRoute('quotes.device-configurations');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Creates a deviceConfiguration
     */
    public function createAction ()
    {
        $request = $this->getRequest();
        $form    = new DeviceConfigurationForm();

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
                            $mapper              = DeviceConfigurationMapper::getInstance();
                            $deviceConfiguration = new DeviceConfigurationModel();
                            $deviceConfiguration->populate($values);
                            $deviceConfigurationId = $mapper->insert($deviceConfiguration);

                            $this->_flashMessenger->addMessage(array(
                                'success' => "Device configuration {$deviceConfiguration->id} was added successfully."
                            ));

                            $this->redirectToRoute('quotes.device-configurations.edit', array(
                                'id' => $deviceConfigurationId
                            ));
                        }
                        catch (Zend_Db_Statement_Mysqli_Exception $e)
                        {
                            // Check to see what error code was thrown
                            switch ($e->getCode())
                            {
                                // Duplicate column
                                case 1062 :
                                    $this->_flashMessenger->addMessage(array(
                                        'danger' => 'Device configuration already exists.'
                                    ));
                                    break;
                                default :
                                    $this->_flashMessenger->addMessage(array(
                                        'danger' => 'Error saving to database.  Please try again.'
                                    ));
                                    break;
                            }

                            $form->populate($request->getPost());
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
                $this->redirectToRoute('quotes.device-configurations');
            }
        }
        // Add form to page
        $form->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/quotegen/create-device-configuration-form.phtml',
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
        $deviceConfigurationId = $this->_getParam('id', false);

        // If they haven't provided an id, send them back to the view all deviceConfiguration page
        if (!$deviceConfigurationId)
        {
            $this->_flashMessenger->addMessage(array(
                'warning' => 'Please select a device configuration to edit first.'
            ));
            $this->redirectToRoute('quotes.device-configurations');
        }

        // Get the deviceConfiguration
        $deviceConfigurationMapper = DeviceConfigurationMapper::getInstance();
        $deviceConfiguration       = $deviceConfigurationMapper->find((int)$deviceConfigurationId);

        // If the deviceConfiguration doesn't exist, send them back to the view all deviceConfigurations page
        if (!$deviceConfiguration)
        {
            $this->_flashMessenger->addMessage(array(
                'danger' => 'There was an error selecting the device configuration to edit.'
            ));
            $this->redirectToRoute('quotes.device-configurations');
        }

        $this->view->deviceConfiguration = $deviceConfiguration;

        // Create a new form with the mode and roles set
        $form = new DeviceConfigurationForm($deviceConfiguration->id);

        // Prepare the data for the form
        $request = $this->getRequest();
        $form->populate($deviceConfiguration->toArray());

        // Make sure we are posting data
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();

            // If we cancelled we don't need to validate anything
            if (isset($values ['cancel']))
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirectToRoute('quotes.device-configurations');
            }
            else
            {

                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        // Save option quantities
                        $deviceConfigurationOption                        = new DeviceConfigurationOptionModel();
                        $deviceConfigurationOption->deviceConfigurationId = $deviceConfigurationId;

                        foreach ($form->getOptionElements() as $element)
                        {
                            $optionId                            = (int)$element->getDescription();
                            $deviceConfigurationOption->optionId = $optionId;
                            $deviceConfigurationOption->quantity = $values ["option$optionId"];
                            DeviceConfigurationOptionMapper::getInstance()->save($deviceConfigurationOption);
                        }

                        if (isset($values ['add']))
                        {
                            $this->_flashMessenger->addMessage(array(
                                'success' => "Device configuration saved."
                            ));

                            // Send to the add options page like they asked
                            $this->redirectToRoute('quotes.configurations.edit', array(
                                'configurationid' => $deviceConfigurationId
                            ));
                        }
                        else
                        {
                            $this->_flashMessenger->addMessage(array(
                                'success' => "Device configuration '{$deviceConfiguration->id}' was updated successfully."
                            ));

                            // Send back to the main list
                            $this->redirectToRoute('quotes.device-configurations');
                        }
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
        }
        // Add form to page
        $form->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/quotegen/edit-device-configuration-form.phtml',
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
        $id = $this->_getParam('id', false);

        $availableOptions = OptionMapper::getInstance()->fetchAllAvailableOptionsForDeviceConfiguration($id);
        if (count($availableOptions) < 1)
        {
            $this->_flashMessenger->addMessage(array(
                'info' => "There are no more options to add to this device."
            ));
            $this->redirectToRoute('quotes.add-hardware.edit', array(
                'id' => $id
            ));
        }

        $form = new SelectOptionsForm($availableOptions);
        // Prepare the data for the form
        $request = $this->getRequest();

        $deviceConfiguration = DeviceConfigurationMapper::getInstance()->find($id);

        $form->populate($deviceConfiguration->toArray());

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
                        $deviceConfigurationOptionMapper                  = DeviceConfigurationOptionMapper::getInstance();
                        $deviceConfigurationOption                        = new DeviceConfigurationOptionModel();
                        $deviceConfigurationOption->deviceConfigurationId = $deviceConfiguration->id;

                        $insertedOptions = 0;
                        foreach ($values ['options'] as $optionId)
                        {
                            $deviceConfigurationOption->optionId = $optionId;
                            try
                            {
                                $deviceConfigurationOptionMapper->insert($deviceConfigurationOption);
                                $insertedOptions++;
                            }
                            catch (Exception $e)
                            {
                                // Do nothing
                            }
                        }

                        $this->_flashMessenger->addMessage(array(
                            'success' => "Successfully added {$insertedOptions} options to {$deviceConfiguration->getDevice()->getMasterDevice()->getFullDeviceName()} successfully."
                        ));
                        $this->redirectToRoute('quotes.add-hardware.edit', array(
                            'id' => $id
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
                $this->redirectToRoute('quotes.add-hardware.edit', array(
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
        $id       = $this->_getParam('id', false);
        $optionId = $this->_getParam('optionId', false);

        try
        {
            $deviceConfigurationOption                        = new DeviceConfigurationOptionModel();
            $deviceConfigurationOption->deviceConfigurationId = $id;
            $deviceConfigurationOption->optionId              = $optionId;
            DeviceConfigurationOptionMapper::getInstance()->delete($deviceConfigurationOption);
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

        $this->redirectToRoute('quotes.add-hardware.edit', array(
            'id' => $id
        ));
    }

    /**
     * Displays a deviceConfiguration
     */
    public function viewAction ()
    {
        $this->view->deviceConfiguration = DeviceConfigurationMapper::getInstance()->find($this->_getParam('id', false));
    }
}

