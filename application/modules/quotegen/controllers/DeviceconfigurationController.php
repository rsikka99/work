<?php

/**
 * Class Quotegen_DeviceConfigurationController
 */
class Quotegen_DeviceConfigurationController extends Tangent_Controller_Action
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
        $mapper    = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();
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
        $mapper    = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();
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
            $this->redirector('index');
        }

        $mapper              = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();
        $deviceConfiguration = $mapper->find($deviceConfigurationId);

        if (!$deviceConfiguration)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'There was an error selecting the device configuration to delete.'
                                               ));
            $this->redirector('index');
        }

        $message = "Are you sure you want to delete {$deviceConfiguration->id}?";
        $form    = new Application_Form_Delete($message);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                // delete deviceConfiguration from database
                if ($form->isValid($values))
                {
                    $mapper->delete($deviceConfiguration);
                    $this->_flashMessenger->addMessage(array(
                                                            'success' => "Device configuration  {$deviceConfiguration->id} was deleted successfully."
                                                       ));
                    $this->redirector('index');
                }
            }
            else // go back
            {
                $this->redirector('index');
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
        $form    = new Quotegen_Form_DeviceConfiguration();

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
                            $mapper              = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();
                            $deviceConfiguration = new Quotegen_Model_DeviceConfiguration();
                            $deviceConfiguration->populate($values);
                            $deviceConfigurationId = $mapper->insert($deviceConfiguration);

                            $this->_flashMessenger->addMessage(array(
                                                                    'success' => "Device configuration {$deviceConfiguration->id} was added successfully."
                                                               ));

                            $this->redirector('edit', null, null, array(
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
                $this->redirector('index');
            }
        }
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
            $this->redirector('index');
        }

        // Get the deviceConfiguration
        $deviceConfigurationMapper = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();
        $deviceConfiguration       = $deviceConfigurationMapper->find((int)$deviceConfigurationId);

        // If the deviceConfiguration doesn't exist, send them back to the view all deviceConfigurations page
        if (!$deviceConfiguration)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'There was an error selecting the device configuration to edit.'
                                               ));
            $this->redirector('index');
        }

        $this->view->deviceConfiguration = $deviceConfiguration;

        // Create a new form with the mode and roles set
        $form = new Quotegen_Form_DeviceConfiguration($deviceConfiguration->id);

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
                $this->redirector('index');
            }
            else
            {

                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        // Save option quantities
                        $deviceConfigurationOption                        = new Quotegen_Model_DeviceConfigurationOption();
                        $deviceConfigurationOption->deviceConfigurationId = $deviceConfigurationId;

                        foreach ($form->getOptionElements() as $element)
                        {
                            $optionId                            = (int)$element->getDescription();
                            $deviceConfigurationOption->optionId = $optionId;
                            $deviceConfigurationOption->quantity = $values ["option$optionId"];
                            Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance()->save($deviceConfigurationOption);
                        }

                        if (isset($values ['add']))
                        {
                            $this->_flashMessenger->addMessage(array(
                                                                    'success' => "Device configuration saved."
                                                               ));

                            // Send to the add options page like they asked
                            $this->redirector('addoptions', null, null, array(
                                                                             'id' => $deviceConfigurationId
                                                                        ));
                        }
                        else
                        {
                            $this->_flashMessenger->addMessage(array(
                                                                    'success' => "Device configuration '{$deviceConfiguration->id}' was updated sucessfully."
                                                               ));

                            // Send back to the main list
                            $this->redirector('index');
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
        $this->view->form = $form;
    }

    /**
     * Adds options to a device
     */
    public function addoptionsAction ()
    {
        $id = $this->_getParam('id', false);

        $availableOptions = Quotegen_Model_Mapper_Option::getInstance()->fetchAllAvailableOptionsForDeviceConfiguration($id);
        if (count($availableOptions) < 1)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'info' => "There are no more options to add to this device."
                                               ));
            $this->redirector('edit', null, null, array(
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
            if (!isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        $deviceConfigurationOptionMapper                  = Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance();
                        $deviceConfigurationOption                        = new Quotegen_Model_DeviceConfigurationOption();
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
                        $this->redirector('edit', null, null, array(
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
                $this->redirector('edit', null, null, array(
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
            $deviceConfigurationOption                        = new Quotegen_Model_DeviceConfigurationOption();
            $deviceConfigurationOption->deviceConfigurationId = $id;
            $deviceConfigurationOption->optionId              = $optionId;
            Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance()->delete($deviceConfigurationOption);
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

        $this->redirector('edit', null, null, array(
                                                   'id' => $id
                                              ));
    }

    /**
     * Displays a deviceConfiguration
     */
    public function viewAction ()
    {
        $this->view->deviceConfiguration = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->find($this->_getParam('id', false));
    }
}

