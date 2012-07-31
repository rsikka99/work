<?php

class Quotegen_ConfigurationController extends Zend_Controller_Action
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
        
        $mapper = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();
        $deviceConfiguration = $mapper->find($deviceConfigurationId);
        
        if (! $deviceConfiguration)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the device configuration to delete.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $message = "Are you sure you want to delete the &quot;{$deviceConfiguration->getName()}&quot; configuration?";
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
                            'success' => "Device configuration \"{$deviceConfiguration->getName()}\" was deleted successfully." 
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
        // Get master device id if passed in
        $page = $this->_getParam('page', false);
        $masterDeviceId = $this->_getParam('id', false);
        
        // Get form
        $form = new Quotegen_Form_Configuration();
        
        // Set default master device in dropdown
        if ($masterDeviceId)
        {
            $form->getElement('masterDeviceId')->setValue($masterDeviceId);
        }
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            // When user is posting data, get the values that have been posted.
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Attempt to save the configuration to the database.
                        $mapper = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();
                        $deviceConfiguration = new Quotegen_Model_DeviceConfiguration();
                        $deviceConfiguration->populate($values);
                        $deviceConfigurationId = $mapper->insert($deviceConfiguration);
                        
                        $this->_helper->flashMessenger(array (
                                'success' => 'Your configuration was successfully created.' 
                        ));
                        
                        if ($page == "configurations")
                        {
                            // User has cancelled. Go back to the edit page
                            $this->_helper->redirector('configurations', 'devicesetup', 'quotegen', array (
                                    'id' => $masterDeviceId 
                            ));
                        }
                        else
                        {
                            // User has cancelled. Go back to the edit page
                            $this->_helper->redirector('index');
                        }
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => 'Please correct the errors below.' 
                        ));
                    }
                }
                catch ( Exception $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => 'There was an error creating this configuration. Please try again.' 
                    ));
                }
            }
            else // If user has selected cancel send user back to the index pages of this Controller
            {
                if ($page == "configurations")
                {
                    // User has cancelled. Go back to the edit page
                    $this->_helper->redirector('configurations', 'devicesetup', 'quotegen', array (
                            'id' => $masterDeviceId 
                    ));
                }
                else
                {
                    // User has cancelled. Go back to the edit page
                    $this->_helper->redirector('index');
                }
            }
        }
        $this->view->form = $form;
    }

    /**
     * Edits a deviceConfiguration
     */
    public function editAction ()
    {
        $id = $this->_getParam('id', false);
        $deviceConfigurationId = $this->_getParam('configurationid', false);
        $page = $this->_getParam('page', false);
        
        // If they haven't provided an id, send them back to the view all deviceConfiguration page
        if (! $deviceConfigurationId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a device configuration to edit first.' 
            ));
            
            if ($page == "configurations")
            {
                // User has cancelled. Go back to the edit page
                $this->_helper->redirector('configurations', 'devicesetup', 'quotegen', array (
                        'id' => $masterDeviceId 
                ));
            }
            else
            {
                // User has cancelled. Go back to the edit page
                $this->_helper->redirector('index');
            }
        }
        
        // Get the deviceConfiguration
        $deviceConfigurationMapper = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();
        $deviceConfiguration = $deviceConfigurationMapper->find((int)$deviceConfigurationId);
        
        // If the deviceConfiguration doesn't exist, send them back to the view all deviceConfigurations page
        if (! $deviceConfiguration)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the device configuration to edit.' 
            ));

            if ($page == "configurations")
            {
                // User has cancelled. Go back to the edit page
                $this->_helper->redirector('configurations', 'devicesetup', 'quotegen', array (
                        'id' => $masterDeviceId
                ));
            }
            else
            {
                // User has cancelled. Go back to the edit page
                $this->_helper->redirector('index');
            }
        }
        
        $this->view->deviceConfiguration = $deviceConfiguration;
        
        // Create a new form with the mode and roles set
        $form = new Quotegen_Form_Configuration($deviceConfigurationId);
        
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
                if ($page == "configurations")
                {
                    // User has cancelled. Go back to the edit page
                    $this->_helper->redirector('configurations', 'devicesetup', 'quotegen', array (
                            'id' => $id 
                    ));
                }
                else
                {
                    // User has cancelled. Go back to the edit page
                    $this->_helper->redirector('index');
                }
            }
            else
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Attempt to save the configuration to the database.
                        $mapper = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();
                        $deviceConfiguration = new Quotegen_Model_DeviceConfiguration();
                        $values ['id'] = $deviceConfigurationId;
                        $deviceConfiguration->populate($values);
                        $deviceConfigurationId = $mapper->save($deviceConfiguration);
                        
                        $this->_helper->flashMessenger(array (
                                'success' => 'Your configuration was successfully updated.' 
                        ));

                        if ($page == "configurations")
                        {
                            // User has cancelled. Go back to the edit page
                            $this->_helper->redirector('configurations', 'devicesetup', 'quotegen', array (
                                    'id' => $masterDeviceId
                            ));
                        }
                        else
                        {
                            // User has cancelled. Go back to the edit page
                            $this->_helper->redirector('index');
                        }
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => 'Please correct the errors below.' 
                        ));
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
     * Adds options to a device
     */
    public function addoptionsAction ()
    {
        $id = $this->_getParam('id', FALSE);
        $configurationId = $this->_getParam('configurationid', FALSE);
        $page = $this->_getParam('page', FALSE);
        
        $availableOptions = Quotegen_Model_Mapper_Option::getInstance()->fetchAll();
        if (count($availableOptions) < 1)
        {
            $this->_helper->flashMessenger(array (
                    'info' => "There are no more options to add to this device." 
            ));
            
            if ($page == "configurations")
            {
                // User has cancelled. Go back to the edit page
                $this->_helper->redirector('configurations', 'devicesetup', 'quotegen', array (
                        'id' => $id 
                ));
            }
            else
            {
                // User has cancelled. Go back to the edit page
                $this->_helper->redirector('index');
            }
        }
        
        $deviceConfiguration = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->find($configurationId);
        $this->view->name = $deviceConfiguration->getName();
        
        // Prepare the data for the form
        $form = new Quotegen_Form_SelectOptions($availableOptions);
        $form->populate($deviceConfiguration->toArray());
        
        // Get selected options for device
        $where = "deviceConfigurationId = {$configurationId}";
        $selectedOptions = Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance()->fetchAll($where);
        $selectedOptionsList = array ();
        foreach ( $selectedOptions as $option )
        {
            $selectedOptionsList [] = $option->getOptionId();
        }
        $form->getElement("options")->setValue($selectedOptionsList);
        
        // Make sure we are posting data
        $request = $this->getRequest();
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
                        
                        try
                        {
                            // Delete current device configuration options
                            $deviceConfigurationOption = new Quotegen_Model_DeviceConfigurationOption();
                            Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance()->deleteDeviceConfigurationOptionById($configurationId);
                            
                            // Insert selected device configuration options
                            $insertedOptions = 0;
                            foreach ( $values ['options'] as $optionId )
                            {
                                $deviceConfigurationOption->setDeviceConfigurationId($configurationId);
                                $deviceConfigurationOption->setOptionId($optionId);
                                $deviceConfigurationOptionMapper->insert($deviceConfigurationOption);
                                $insertedOptions ++;
                            }
                        }
                        catch ( Exception $e )
                        {
                            $this->_helper->flashMessenger(array (
                                    'danger' => "Failed to add options to configuration. Please try again." 
                            ));
                        }
                        $this->_helper->flashMessenger(array (
                                'success' => "Successfully added {$insertedOptions} options to {$deviceConfiguration->getName()}." 
                        ));
			            
			            if ($page == "configurations")
			            {
			                // User has cancelled. Go back to the edit page
			                $this->_helper->redirector('configurations', 'devicesetup', 'quotegen', array (
			                        'id' => $id 
			                ));
			            }
			            else
			            {
			                // User has cancelled. Go back to the edit page
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
            else
            {
	            if ($page == "configurations")
	            {
	                // User has cancelled. Go back to the edit page
	                $this->_helper->redirector('configurations', 'devicesetup', 'quotegen', array (
	                        'id' => $id 
	                ));
	            }
	            else
	            {
	                // User has cancelled. Go back to the edit page
	                $this->_helper->redirector('index');
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
        
        $this->_helper->redirector('edit', null, null, array (
                'id' => $id 
        ));
    }
}

