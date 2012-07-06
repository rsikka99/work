<?php

class Admin_TonerController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }
    
    // Shows all the toners in a table
    public function indexAction ()
    {
        // Get all toners
        $tonerMapper = new Admin_Model_Mapper_Toner();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($tonerMapper));
        
        // Set current page
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        // Set max items per page
        $paginator->setItemCountPerPage(25);
        
        // Save entries to view paginatior
        $this->view->paginator = $paginator;
    }

    public function deleteAction ()
    {
        $tonerId = $this->_getParam('id', false);
        
        if (! $tonerId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a toner to delete first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $mapper = new Admin_Model_Mapper_Toner();
        $toner = $mapper->find($tonerId);
        
        if (! $toner)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the toner to delete.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $message = "Are you sure you want to delete toner {$toner->getSku()} {$toner->getManufacturer()->getDisplayname()} {$toner->getTonerColor()->getNamae()} {$toner->getYield()}?";
        $form = new Application_Form_Delete($message);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                // delete toner from database
                if ($form->isValid($values))
                {
                    $mapper->delete($toner);
                    
                    // TODO: Show deletion of options relations
                    $this->_helper->flashMessenger(array (
                            'success' => "The toner was deleted successfully." 
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
        // Show the form 
        $form = new Admin_Form_Toner();
        
        // If the form is on post insert data 
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            // Get values from the form
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Persist data to database
                        $mapper = new Admin_Model_Mapper_Toner();
                        $toner = new Admin_Model_Toner();
                        $toner->populate($values);
                        
                        $mapper->insert($toner);
                        
                        // Redirect client back to index
                        $this->_helper->redirector('index');
                    }
                    else // Values in form data aren't valid. 
                    {
                        throw new InvalidArgumentException('Please correct the fields below');
                    }
                }
                catch ( Exception $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => $e->getMessage() 
                    ));
                }
            }
            else // Cancel was hit: redirect user
            {
                $this->_helper->redirector('index');
            }
        }
        
        $this->view->form = $form;
    }

    public function editAction ()
    {
        $tonerId = $this->_getParam('id', false);
        
        // If not idea is set then back to index page
        if (! $tonerId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a toner first' 
            ));
            // Redirect
            $this->_helper->redirector('index');
        }
        
        // Find client and pass form object
        $form = new Admin_Form_Toner();
        $mapper = new Admin_Model_Mapper_Toner();
        $toner = $mapper->find($tonerId);
        
        $form->populate($toner->toArray());
        // update record if post
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        // Update toner
                        $mapper = new Admin_Model_Mapper_Toner;
                        $toner = new Admin_Model_Toner();
                        $toner->populate($values);
                        $toner->setId($tonerId);
                        
                        $mapper->save($toner, $tonerId);
                        $this->_helper->flashMessenger(array (
                                'success' => "The toner was updated sucessfully." 
                        ));
                        
                        $this->_helper->redirector('index');
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
            else // Client hit cancel redicect
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }
        
        $this->view->form = $form;
    }

    public function viewAction ()
    {
        // Get Toner Details
        $this->view->toner = Admin_Model_Mapper_Toner::getInstance()->find($this->_getParam('id', false));
    }
}

