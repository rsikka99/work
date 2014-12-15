<?php
use MPSToolbox\Legacy\Forms\DeleteConfirmationForm;
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\ProposalgenAclModel;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\ManufacturerForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Services\ManufacturerService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use Tangent\Controller\Action;

/**
 * Class HardwareLibrary_ManufacturerController
 */
class HardwareLibrary_ManufacturerController extends Action
{
    /**
     * Shows all the manufacturers
     */
    public function indexAction ()
    {
        $this->_pageTitle = array('Hardware Library', 'Manufacturers');
        // Display all of the manufacturers
        $mapper    = ManufacturerMapper::getInstance();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($mapper));

        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Set how many items to show
        $paginator->setItemCountPerPage(100);

        // Pass the view the paginator
        $this->view->paginator = $paginator;
        $this->view->canEdit   = $this->view->IsAllowed(ProposalgenAclModel::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE, AppAclModel::PRIVILEGE_ADMIN);
    }

    /**
     * Creates a manufacturer
     *
     * @throws Exception
     */
    public function createAction ()
    {
        $this->_pageTitle    = array('Hardware Quote Devices', 'Create Manufacturer');
        $form                = new ManufacturerForm();
        $manufacturerService = new ManufacturerService();

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            if (isset($postData ['cancel']))
            {
                $this->redirectToRoute('hardware-library.manufacturers');
            }
            else
            {
                try
                {
                    if ($form->isValid($postData))
                    {
                        try
                        {
                            $values = $form->getvalues();

                            $manufacturer = $manufacturerService->createManufacturer($values);

                            if ($manufacturerService->hasErrors())
                            {
                                foreach ($manufacturerService->getErrors() as $error)
                                {
                                    $this->_flashMessenger->addMessage(array(
                                        'danger' => $error,
                                    ));
                                }
                            }
                            else
                            {
                                $this->_flashMessenger->addMessage(array('success' => sprintf("Manufacturer %s was added successfully.", $this->view->escape($manufacturer->fullname)),
                                ));
                                $form->reset();
                            }
                        }
                        catch (Zend_Db_Statement_Mysqli_Exception $e)
                        {
                            // Check to see what error code was thrown
                            switch ($e->getCode())
                            {
                                // Duplicate column
                                case 1062 :
                                    $this->_flashMessenger->addMessage(array('danger' => 'Manufacturer already exists.'));
                                    break;
                                default :
                                    $this->_flashMessenger->addMessage(array('danger' => 'Error saving to database.  Please try again.'));
                                    break;
                            }
                        }
                        catch (Exception $e)
                        {
                            $this->_flashMessenger->addMessage(array('danger' => 'There was an error processing this request.  Please try again.'));
                        }
                    }
                }
                catch (Exception $e)
                {
                    \Tangent\Logger\Logger::logException($e);
                    throw $e;
                }
            }
        }
        $this->view->form = $form;
    }

    /**
     * Returns a manufacturer
     */
    public function viewAction ()
    {
        $this->view->manufacturer = ManufacturerMapper::getInstance()->find($this->_getParam('id', false));
    }

    /**
     * Edits a manufacturer
     */
    public function editAction ()
    {
        $this->_pageTitle = array('Hardware Quote Devices', 'Edit Manufacturer');

        /**
         * Grab the manufacturer
         */
        $manufacturerId = $this->_getParam('id', false);
        if (!$manufacturerId)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a manufacturer to edit first.'));
            $this->redirectToRoute('hardware-library.manufacturers');
        }

        // Get the manufacturer
        $mapper       = new ManufacturerMapper();
        $manufacturer = $mapper->find($manufacturerId);
        // If the manufacturer doesn't exist, send them back t the view all manufacturers page
        if (!$manufacturer)
        {
            $this->_flashMessenger->addMessage(array(
                'danger' => 'There was an error selecting the manufacturer to edit.'
            ));
            $this->redirectToRoute('hardware-library.manufacturers');
        }

        // Create a new form with the mode and roles set
        $form = new ManufacturerForm();

        $form->populate(array_merge(array('isTonerVendor' => $manufacturer->isTonerVendor()), $manufacturer->toArray()));

        // Make sure we are posting data
        if ($this->getRequest()->isPost())
        {
            // Get the post data
            $postData = $this->getRequest()->getPost();

            if (isset($postData ['cancel']))
            {
                $this->redirectToRoute('hardware-library.manufacturers');
            }
            else
            {

                // Validate the form
                if ($form->isValid($postData))
                {
                    $db = Zend_Db_Table::getDefaultAdapter();
                    try
                    {
                        $db->beginTransaction();

                        $manufacturerService = new ManufacturerService();
                        $formValues          = $form->getValues();
                        $manufacturerService->saveManufacturer($formValues, $manufacturerId);

                        if ($manufacturerService->hasErrors())
                        {
                            foreach ($manufacturerService->getErrors() as $errorMessage)
                            {
                                $this->_flashMessenger->addMessage(array('error' => $errorMessage));
                            }
                        }
                        else
                        {
                            $this->_flashMessenger->addMessage(array('success' => sprintf("Manufacturer %s was updated successfully.", $this->view->escape($manufacturer->fullname))));
                        }
                        $db->commit();

                    }
                    catch (Exception $e)
                    {
                        $db->rollBack();
                        \Tangent\Logger\Logger::logException($e);
                    }
                }
                else
                {
                    $this->_flashMessenger->addMessage(array('danger' => 'Please correct the errors below'));
                }
            }
        }
        $this->view->form = $form;
    }

    /**
     * Deletes a manufacturer
     */
    public function deleteAction ()
    {
        $this->_pageTitle = array('Hardware Quote Devices', 'Delete Manufacturer');
        $manufacturerId   = $this->_getParam('id', false);

        if (!$manufacturerId)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a manufacturer to delete first.'));
            $this->redirectToRoute('hardware-library.manufacturers');
        }

        $manufacturerService = new ManufacturerService();

        $manufacturer = ManufacturerMapper::getInstance()->find($manufacturerId);
        if (!$manufacturerId)
        {
            $this->_flashMessenger->addMessage(array('danger' => 'There was an error selecting the manufacturer to delete.'));
            $this->redirectToRoute('hardware-library.manufacturers');
        }

        $message = sprintf("Are you sure you want to delete %s? This is VERY DESTRUCTIVE! Please edit the manufacturer and mark it as deleted if you want to preserve anything that relies on the manufacturer instead.", $this->view->escape($manufacturer->fullname));

        $form = new DeleteConfirmationForm($message);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                /**
                 * Delete the manufacturer
                 */
                if ($form->isValid($values))
                {
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    try
                    {
                        $db->beginTransaction();
                        $manufacturerService->deleteManufacturer($manufacturer);
                        $db->commit();
                    }
                    catch (Exception $e)
                    {
                        $db->rollBack();
                        \Tangent\Logger\Logger::logException($e);
                        $this->_flashMessenger->addMessage(array('error' => sprintf("Manufacturer %s failed to delete.", $this->view->escape($manufacturer->fullname))));
                        $this->redirectToRoute('hardware-library.manufacturers');
                    }

                    $this->_flashMessenger->addMessage(array('success' => sprintf("Manufacturer %s was deleted successfully.", $this->view->escape($manufacturer->fullname))));

                    $this->redirectToRoute('hardware-library.manufacturers');
                }
            }
            else // go back
            {
                $this->redirectToRoute('hardware-library.manufacturers');
            }
        }
        $this->view->form = $form;
    }
}

