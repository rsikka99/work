<?php
/**
 * Class Admin_TonerController
 */
class Admin_TonerController extends Tangent_Controller_Action
{
    /**
     * Displays all the toners in the system in  a table
     */
    public function indexAction ()
    {
        // Get all toners
        $tonerMapper = Proposalgen_Model_Mapper_Toner::getInstance();
        $paginator   = new Zend_Paginator(new My_Paginator_MapperAdapter($tonerMapper));

        // Set current page
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Set max items per page
        $paginator->setItemCountPerPage(25);

        // Save entries to view paginator
        $this->view->paginator = $paginator;
    }

    /**
     * Handles deletion of a toner
     */
    public function deleteAction ()
    {
        $tonerId = $this->_getParam('id', false);

        if (!$tonerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                'warning' => 'Please select a toner to delete first.'
                                           ));
            $this->redirector('index');
        }

        $tonerMapper = Proposalgen_Model_Mapper_Toner::getInstance();
        $toner       = $tonerMapper->find($tonerId);

        if (!$toner)
        {
            $this->_flashMessenger->addMessage(array(
                                                'danger' => 'There was an error selecting the toner to delete.'
                                           ));
            $this->redirector('index');
        }

        $deviceToners = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchDeviceTonersByTonerId($tonerId);
        if (count($deviceToners) < 1)
        {
            $message = "Are you sure you want to delete toner SKU {$toner->sku} ({$toner->getManufacturer()->displayname} {$toner->getTonerColor()->tonerColorName} {$toner->yield})?";
            $form    = new Application_Form_Delete($message);

            $request = $this->getRequest();
            if ($request->isPost())
            {
                $values = $request->getPost();
                if (!isset($values ['cancel']))
                {
                    if ($form->isValid($values))
                    {
                        $tonerMapper->delete($toner);

                        $this->_flashMessenger->addMessage(array('success' => "The toner was deleted successfully."));
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
        else
        {
            $this->_flashMessenger->addMessage(array('warning' => "This toner is being used by devices. Please remove it from devices before proceeding."));
            $this->redirector('index');
        }
    }

    /**
     * Handles creation of a toner
     *
     * @throws InvalidArgumentException
     */
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
            if (!isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Persist data to database
                        $mapper = new Proposalgen_Model_Mapper_Toner();
                        $toner  = new Proposalgen_Model_Toner();
                        $toner->populate($values);

                        $mapper->insert($toner);

                        // Redirect client back to index
                        $this->redirector('index');
                    }
                    else // Values in form data aren't valid. 
                    {
                        throw new InvalidArgumentException('Please correct the fields below');
                    }
                }
                catch (Exception $e)
                {
                    $this->_flashMessenger->addMessage(array(
                                                        'danger' => $e->getMessage()
                                                   ));
                }
            }
            else // Cancel was hit: redirect user
            {
                $this->redirector('index');
            }
        }

        $this->view->form = $form;
    }

    /**
     * Handles editing toner data
     *
     * @throws InvalidArgumentException
     */
    public function editAction ()
    {
        $tonerId = $this->_getParam('id', false);

        // If not idea is set then back to index page
        if (!$tonerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                'warning' => 'Please select a toner first'
                                           ));
            // Redirect
            $this->redirector('index');
        }

        // Find client and pass form object
        $form   = new Admin_Form_Toner();
        $mapper = new Proposalgen_Model_Mapper_Toner();
        $toner  = $mapper->find($tonerId);

        $form->populate($toner->toArray());
        // update record if post
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        // Update toner
                        $mapper = new Proposalgen_Model_Mapper_Toner;
                        $toner  = new Proposalgen_Model_Toner();
                        $toner->populate($values);
                        $toner->id = $tonerId;

                        $mapper->save($toner, $tonerId);
                        $this->_flashMessenger->addMessage(array(
                                                            'success' => "The toner was updated successfully."
                                                       ));

                        $this->redirector('index');
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
            else // Client hit cancel redicect
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirector('index');
            }
        }

        $this->view->form = $form;
    }

    /**
     * Lets the user view a toner
     */
    public function viewAction ()
    {
        // Get Toner Details
        $this->view->toner = Proposalgen_Model_Mapper_Toner::getInstance()->find($this->_getParam('id', false));
    }
}