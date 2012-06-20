<?php

class Quotegen_DeviceConfigurationController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    /**
     * Displays all deviceConfigurations
     */
    public function indexAction ()
    {
        // Display all of the deviceConfigurations
        $mapper = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();
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
        
        if (! $deviceConfigurationId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a device configuration to delete first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $mapper = new Quotegen_Model_Mapper_DeviceConfiguration();
        $deviceConfiguration = $mapper->find($deviceConfigurationId);
        
        if (! $deviceConfiguration)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the device configuration to delete.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $message = "Are you sure you want to delete {$deviceConfiguration->getMasterDeviceId()}?";
        $form = new Application_Form_Delete($message);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                // delete deviceConfiguration from database
                if ($form->isValid($values))
                {
                    $mapper->delete($deviceConfiguration);
                    $this->_helper->flashMessenger(array (
                            'success' => "Device configuration  {$deviceConfiguration->getMasterDeviceId()} was deleted successfully." 
                    ));
                    $this->_helper->redirector('index');
                }
            }
            else // go back
            {
                $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Creates a deviceConfiguration
     */
    public function createAction ()
    {
        // TODO: createAction
        $request = $this->getRequest();
        $form = new Quotegen_Form_CreateDeviceConfiguration();
        
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
                            $mapper = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();
                            $deviceConfiguration = new Quotegen_Model_DeviceConfiguration();
                            $deviceConfiguration->populate($values);
                            $deviceConfigurationId = $mapper->insert($deviceConfiguration);
                            
                            $this->_helper->flashMessenger(array (
                                    'success' => "Device configuration {$deviceConfiguration->getMasterDeviceId()} was added successfully." 
                            ));
                            
                            // Reset the form after everything is saved successfully
                            $form->reset();
                        }
                        catch ( Zend_Db_Statement_Mysqli_Exception $e )
                        {
                            // Check to see what error code was thrown
                            switch ($e->getCode())
                            {
                                // Duplicate column
                                case 1062 :
                                    $this->_helper->flashMessenger(array (
                                            'danger' => 'Device configuration already exists.' 
                                    ));
                                    break;
                                default :
                                    $this->_helper->flashMessenger(array (
                                            'danger' => 'Error saving to database.  Please try again.' 
                                    ));
                                    break;
                            }
                            
                            $form->populate($request->getPost());
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
     * Edits a deviceConfiguration
     */
    public function editAction ()
    {
        $deviceConfigurationId = $this->_getParam('id', false);
        
        // If they haven't provided an id, send them back to the view all deviceConfiguration page
        if (! $deviceConfigurationId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a device configuration to edit first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Get the deviceConfiguration
        $mapper = new Quotegen_Model_Mapper_DeviceConfiguration();
        $deviceConfiguration = $mapper->find($deviceConfigurationId);
        // If the deviceConfiguration doesn't exist, send them back t the view all deviceConfigurations page
        if (! $deviceConfiguration)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the device configuration to edit.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Create a new form with the mode and roles set
        $form = new Quotegen_Form_CreateDeviceConfiguration();
        
        // Prepare the data for the form
        $request = $this->getRequest();
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
                        $mapper = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();
                        $deviceConfiguration = new Quotegen_Model_DeviceConfiguration();
                        $deviceConfiguration->populate($values);
                        $deviceConfiguration->setId($deviceConfigurationId);
                        
                        // Save to the database with cascade insert turned on
                        $deviceConfigurationId = $mapper->save($deviceConfiguration, $deviceConfigurationId);
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "Device configuration '{$deviceConfiguration->getMasterDeviceId()}' was updated sucessfully." 
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
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Displays a deviceConfiguration
     */
    public function viewAction ()
    {
        $this->view->deviceConfiguration = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->find($this->_getParam('id', false));
    }
}

