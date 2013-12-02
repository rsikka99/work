<?php

/**
 * Class Proposalgen_ManufacturerController
 */
class Proposalgen_ManufacturerController extends Tangent_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    public function indexAction ()
    {
        // Display all of the manufacturers
        $mapper    = Proposalgen_Model_Mapper_Manufacturer::getInstance();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($mapper));

        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Set how many items to show
        $paginator->setItemCountPerPage(15);

        // Pass the view the paginator
        $this->view->paginator = $paginator;
        $this->view->canEdit   = $this->view->IsAllowed(Proposalgen_Model_Acl::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE, Application_Model_Acl::PRIVILEGE_ADMIN);
    }

    public function deleteAction ()
    {
        $manufacturerId = $this->_getParam('id', false);

        if (!$manufacturerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'warning' => 'Please select a manufacturer to delete first.'
                                               ));
            $this->redirector('index');
        }

        $mapper       = new Proposalgen_Model_Mapper_Manufacturer();
        $manufacturer = $mapper->find($manufacturerId);

        if (!$manufacturerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'There was an error selecting the manufacturer to delete.'
                                               ));
            $this->redirector('index');
        }

        $message = "Are you sure you want to delete {$manufacturer->fullname}? This is VERY DESTRUCTIVE! Please edit the manufacturer and mark it as deleted if you want to preserve anything that relies on the manufacturer instead.";
        $form    = new Application_Form_Delete($message);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                // Delete manufacturer from database
                if ($form->isValid($values))
                {
                    $mapper->delete($manufacturer);
                    $this->_flashMessenger->addMessage(array(
                                                            'success' => "Manufacturer  {$this->view->escape($manufacturer->fullname)} was deleted successfully."
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

    public function createAction ()
    {
        $request = $this->getRequest();
        $form    = new Proposalgen_Form_Manufacturer();

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
                            $mapper       = new Proposalgen_Model_Mapper_Manufacturer();
                            $manufacturer = new Proposalgen_Model_Manufacturer();
                            if (!isset($values ['displayname']) || empty($values ['displayname']))
                            {
                                $values ['displayname'] = $values ['fullname'];
                            }

                            $manufacturer->populate($values);
                            $mapper->insert($manufacturer);

                            $this->_flashMessenger->addMessage(array(
                                                                    'success' => "Manufacturer " . $this->view->escape($manufacturer->fullname) . " was added successfully."
                                                               ));

                            // Reset the form after everything is saved successfully
                            $form->reset();
                        }
                        catch (Zend_Db_Statement_Mysqli_Exception $e)
                        {
                            // Check to see what error code was thrown
                            switch ($e->getCode())
                            {
                                // Duplicate column
                                case 1062 :
                                    $this->_flashMessenger->addMessage(array(
                                                                            'danger' => 'Manufacturer already exists.'
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

    public function editAction ()
    {
        $manufacturerId = $this->_getParam('id', false);

        // If they haven't provided an id, send them back to the view all manufacturer
        // page
        if (!$manufacturerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'warning' => 'Please select a manufacturer to edit first.'
                                               ));
            $this->redirector('index');
        }

        // Get the manufacturer
        $mapper       = new Proposalgen_Model_Mapper_Manufacturer();
        $manufacturer = $mapper->find($manufacturerId);
        // If the manufacturer doesn't exist, send them back t the view all manufacturers page
        if (!$manufacturer)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'There was an error selecting the manufacturer to edit.'
                                               ));
            $this->redirector('index');
        }

        // Create a new form with the mode and roles set
        $form = new Proposalgen_Form_Manufacturer();

        // Prepare the data for the form
        $request = $this->getRequest();
        $form->populate($manufacturer->toArray());

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
                        $mapper       = new Proposalgen_Model_Mapper_Manufacturer();
                        $manufacturer = new Proposalgen_Model_Manufacturer();
                        $manufacturer->populate($values);
                        $manufacturer->id = $manufacturerId;

                        // Save to the database with cascade insert turned on
                        $mapper->save($manufacturer, $manufacturerId);

                        $this->_flashMessenger->addMessage(array(
                                                                'success' => "Manufacturer '" . $this->view->escape($manufacturer->fullname) . "' was updated successfully."
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
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    public function viewAction ()
    {
        $this->view->manufacturer = Proposalgen_Model_Mapper_Manufacturer::getInstance()->find($this->_getParam('id', false));
    }
}

