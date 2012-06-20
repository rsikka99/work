<?php

class Proposalgen_MasterdeviceController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    public function indexAction ()
    {
        // Display all of the masterDevices
        $mapper = Proposalgen_Model_Mapper_MasterDevice::getInstance();
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
        $masterDeviceId = $this->_getParam('id', false);
        
        if (! $masterDeviceId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a master device to delete first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $mapper = new Proposalgen_Model_Mapper_MasterDevice();
        $masterDevice = $mapper->find($masterDeviceId);
        
        if (! $masterDeviceId)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the master device to delete.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $message = "Are you sure you want to delete {$masterDevice->getFullDeviceName()}?";
        $form = new Application_Form_Delete($message);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                // delete masterDevice from database
                if ($form->isValid($values))
                {
                    $mapper->delete($masterDevice);
                    $this->_helper->flashMessenger(array (
                            'success' => "Master device  '{$masterDevice->getFullDeviceName()}' was deleted successfully." 
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
        $form = new Proposalgen_Form_MasterDevice();
        
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
                            
                            $mapper = Proposalgen_Model_Mapper_MasterDevice::getInstance();
                            $masterDevice = new Proposalgen_Model_MasterDevice();
                            $currentDate = date('Y-m-d H:i:s');
                            $masterDevice->setDateCreated($currentDate);
                            
                            foreach ( $values as &$value )
                            {
                                if (strlen($value) < 1)
                                    $value = null;
                            }
                            
                            $masterDevice->populate($values);
                            $masterDeviceId = $mapper->insert($masterDevice);
                            
                            $this->_helper->flashMessenger(array (
                                    'success' => "MasterDevice " . $this->view->escape($masterDevice->getFullDeviceName()) . " was added successfully." 
                            ));
                            
                            // Reset the form after everything is saved successfully
                            $form->reset();
                        }
                        catch ( Exception $e )
                        {
                            throw new Exception($e);
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
        $masterDeviceId = $this->_getParam('id', false);
        
        // If they haven't provided an id, send them back to the view all masterDevice
        // page
        if (! $masterDeviceId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a masterDevice to edit first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Get the masterDevice
        $mapper = new Proposalgen_Model_Mapper_MasterDevice();
        $masterDevice = $mapper->find($masterDeviceId);
        
        // If the masterDevice doesn't exist, send them back t the view all masterDevices page
        if (! $masterDevice)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the masterDevice to edit.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Create a new form with the mode and roles set
        $form = new Proposalgen_Form_MasterDevice();
        
        // Prepare the data for the form
        $request = $this->getRequest();
        $form->populate($masterDevice->toArray());
        
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
                        $mapper = new Proposalgen_Model_Mapper_MasterDevice();
                        $masterDevice = new Proposalgen_Model_MasterDevice();
                        foreach ( $values as &$value )
                        {
                            if (strlen($value) < 1)
                                $value = null;
                        }
                        $masterDevice->populate($values);
                        $masterDevice->setId($masterDeviceId);
                        
                        // Save to the database with cascade insert turned on
                        $masterDeviceId = $mapper->save($masterDevice, $masterDeviceId);
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "MasterDevice '{$masterDevice->getFullDeviceName()}' was updated sucessfully." 
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
        $this->view->masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($this->_getParam('id', false));
    }
}

