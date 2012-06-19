<?php

class Quotegen_DeviceController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    public function indexAction ()
    {
        // Display all of the devices
        $mapper = Quotegen_Model_Mapper_Device::getInstance();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($mapper));
        
        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        // Set how many items to show
        $paginator->setItemCountPerPage(15);
        
        // Pass the view the paginator
        $this->view->paginator = $paginator;
    }

    public function deleteAction ()
    {
        // TODO: deleteAction
        $deviceId = $this->_getParam('id', false);
        
        if (! $deviceId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a device to delete first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $mapper = new Quotegen_Model_Mapper_Device();
        $device = $mapper->find($deviceId);
        
        if (! $deviceId)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the device to delete.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $message = "Are you sure you want to delete {$device->getName()}?";
        $form = new Application_Form_Delete($message);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                // delete device from database
                if ($form->isValid($values))
                {
                    $mapper->delete($device);
                    $this->_helper->flashMessenger(array (
                            'success' => "Device  {$this->view->escape ( $device->getName() )} was deleted successfully." 
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

    public function createAction ()
    {
        // TODO: createAction
        $request = $this->getRequest();
        $form = new Quotegen_Form_Device();
        
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
                            $mapper = new Quotegen_Model_Mapper_Device();
                            $device = new Quotegen_Model_Device();
                            $values ['deviceId'] = Zend_Auth::getInstance()->getIdentity()->id;
                            $device->populate($values);
                            $deviceId = $mapper->insert($device);
                            
                            $this->_helper->flashMessenger(array (
                                    'success' => "Device " . $this->view->escape($device->getName()) . " was added successfully." 
                            ));
                            
                            // Reset the form after everything is saved
        // successfully
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
                                            'danger' => 'Device already exists.' 
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

    public function editAction ()
    {
        $deviceId = $this->_getParam('id', false);
        
        // If they haven't provided an id, send them back to the view all device
        // page
        if (! $deviceId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a device to edit first.' 
            ));
            $this->_redirect('/gen/device');
        }
        
        // Get the device
        $mapper = new Quotegen_Model_Mapper_Device();
        $device = $mapper->find($deviceId);
        // If the device doesn't exist, send them back t the view all devices page
        if (! $device)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the device to edit.' 
            ));
            $this->_redirect('/gen/device');
        }
        
        // Create a new form with the mode and roles set
        $form = new Quotegen_Form_Device();
        
        // Prepare the data for the form
        $request = $this->getRequest();
        $form->populate($device->toArray());
        
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
                        $mapper = new Quotegen_Model_Mapper_Device();
                        $device = new Quotegen_Model_Device();
                        $device->populate($values);
                        $device->setId($deviceId);
                        
                        // Save to the database with cascade insert turned on
                        $deviceId = $mapper->save($device, $deviceId);
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "Device '" . $this->view->escape($device->getMasterDeviceId()) . "' was updated sucessfully." 
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

    public function viewAction ()
    {
        // TODO: viewAction
    }
}

