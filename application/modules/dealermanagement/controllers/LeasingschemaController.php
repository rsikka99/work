<?php
use MPSToolbox\Legacy\Forms\DeleteConfirmationForm;
use MPSToolbox\Legacy\Modules\Admin\Forms\LeasingSchemaForm;
use MPSToolbox\Legacy\Modules\DealerManagement\Forms\ImportLeaseCsvForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\LeasingSchemaTermForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\LeasingSchemaRangeForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\LeasingSchemaTermMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\LeasingSchemaRateMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\LeasingSchemaRangeMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\LeasingSchemaMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaTermModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaRateModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaRangeModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaModel;
use Tangent\Controller\Action;

/**
 * Class Dealermanagement_LeasingschemaController
 */
class Dealermanagement_LeasingschemaController extends Action
{

    public function indexAction ()
    {
        $this->_pageTitle = ['Your Lease Rates', 'Company'];
        // Display all of the leasing schema rates in a grid
        $leasingSchemas             = LeasingSchemaMapper::getInstance()->getSchemasForDealer(Zend_Auth::getInstance()->getIdentity()->dealerId);
        $this->view->leasingSchemas = $leasingSchemas;
    }

    public function viewAction ()
    {
        $this->_pageTitle = ['Manage Lease Rate', 'Your Lease Rates', 'Company'];
        $leasingSchemaId  = $this->_getParam('leasingSchemaId', false);
        if (!$leasingSchemaId)
        {
            $this->_flashMessenger->addMessage(['error' => 'That schema does not exist']);
            $this->redirectToRoute('company.leasing-schema');
        }
        $leasingSchema = LeasingSchemaMapper::getInstance()->find($leasingSchemaId);
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(['danger' => 'Insufficient Privilege: You cannot view this leasing schema.']);
            $this->redirectToRoute('company.leasing-schema');
        }
        // Display all of the leasing schema rates in a grid
        $leasingSchemaMapper       = LeasingSchemaMapper::getInstance();
        $leasingSchema             = $leasingSchemaMapper->find($leasingSchemaId);
        $this->view->leasingSchema = $leasingSchema;
    }

    public function editAction ()
    {
        $this->_pageTitle = ['Lease Rate Card Name', 'Your Lease Rates', 'Company'];
        $leasingSchemaId  = $this->_getParam('leasingSchemaId', false);
        if (!$leasingSchemaId)
        {
            $this->_flashMessenger->addMessage(['error' => 'That schema does not exist']);
            $this->redirectToRoute('company.leasing-schema');
        }
        $leasingSchema = LeasingSchemaMapper::getInstance()->find($leasingSchemaId);
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(['danger' => 'Insufficient Privilege: You cannot view this leasing schema.']);
            $this->redirectToRoute('company.leasing-schema');
        }
        $form             = new  LeasingSchemaForm(true);
        $this->view->form = $form;
        // Postback
        $request = $this->getRequest();
        if ($request->isPost())
        {

            $values = $request->getPost();
            $db     = Zend_Db_Table::getDefaultAdapter();
            // If we cancelled we don't need to validate anything
            if (!isset($values ['cancel']))
            {
                if ($form->isValid($request->getParams()))
                {
                    $db->beginTransaction();
                    try
                    {
                        $leasingSchemaMapper = LeasingSchemaMapper::getInstance();
                        $leasingSchema->populate($values);
                        $leasingSchemaMapper->save($leasingSchema);
                        $db->commit();
                        $this->_flashMessenger->addMessage(['success' => "{$leasingSchema->name} has been saved successfully."]);
                        $this->redirectToRoute('company.leasing-schema');
                    }
                    catch (InvalidArgumentException $e)
                    {
                        $db->rollback();
                        $this->_flashMessenger->addMessage(['danger' => $e->getMessage()]);
                    }
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirectToRoute('company.leasing-schema');
            }
        }
        $form->populate($leasingSchema->toArray());
    }


    public function createAction ()
    {
        $this->_pageTitle = ['Create New Rate Card', 'Your Lease Rates', 'Company'];
        $form             = new  LeasingSchemaForm(true);
        $this->view->form = $form;
        // Postback
        $request = $this->getRequest();
        if ($request->isPost())
        {

            $values = $request->getPost();
            $db     = Zend_Db_Table::getDefaultAdapter();
            // If we cancelled we don't need to validate anything
            if (!isset($values ['cancel']))
            {
                if ($form->isValid($request->getParams()))
                {
                    $db->beginTransaction();
                    try
                    {
                        $leasingSchemaMapper     = LeasingSchemaMapper::getInstance();
                        $leasingSchema           = new LeasingSchemaModel($values);
                        $leasingSchema->dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
                        $leasingSchemaMapper->insert($leasingSchema);
                        // Reset the grid to default values
                        $months = "12";
                        $range  = "0";
                        $rate   = "0.0750";

                        // Prep mappers
                        $leasingSchemaRateMapper  = LeasingSchemaRateMapper::getInstance();
                        $leasingSchemaRangeMapper = LeasingSchemaRangeMapper::getInstance();
                        $leasingSchemaTermMapper  = LeasingSchemaTermMapper::getInstance();

                        // Prep models
                        $leasingSchemaRateModel  = new LeasingSchemaRateModel();
                        $leasingSchemaRangeModel = new LeasingSchemaRangeModel();
                        $leasingSchemaTermModel  = new LeasingSchemaTermModel();
                        // Save Term
                        $leasingSchemaTermModel->leasingSchemaId = $leasingSchema->id;
                        $leasingSchemaTermModel->months          = $months;
                        $termId                                  = $leasingSchemaTermMapper->insert($leasingSchemaTermModel);

                        // Save Range
                        $leasingSchemaRangeModel->leasingSchemaId = $leasingSchema->id;
                        $leasingSchemaRangeModel->startRange      = $range;
                        $rangeId                                  = $leasingSchemaRangeMapper->insert($leasingSchemaRangeModel);

                        // Save Rate
                        $leasingSchemaRateModel->leasingSchemaTermId  = $termId;
                        $leasingSchemaRateModel->leasingSchemaRangeId = $rangeId;
                        $leasingSchemaRateModel->rate                 = $rate;
                        $leasingSchemaRateMapper->insert($leasingSchemaRateModel);

                        $db->commit();
                        $this->_flashMessenger->addMessage(['success' => sprintf('The "%s" rate card has been created successfully.', $leasingSchema->name)]);
                        $this->redirectToRoute('company.leasing-schema');
                    }
                    catch (InvalidArgumentException $e)
                    {
                        $db->rollback();
                        $this->_flashMessenger->addMessage(['danger' => $e->getMessage()]);
                    }
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirectToRoute('company.leasing-schema');
            }
        }
    }

    public function deleteAction ()
    {
        $this->_pageTitle = ['Delete Lease Rate Card', 'Your Lease Rates', 'Company'];
        $leasingSchemaId  = $this->_getParam('leasingSchemaId', false);
        if (!$leasingSchemaId)
        {
            $this->_flashMessenger->addMessage(['warning' => 'Please select a leasing schema to delete first.']);
            $this->redirectToRoute('company.leasing-schema');
        }

        $leasingSchemaMapper = LeasingSchemaMapper::getInstance();
        $leasingSchema       = $leasingSchemaMapper->find($leasingSchemaId);
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(['danger' => 'Insufficient Privilege: You cannot delete this leasing schema.']);
            $this->redirectToRoute('company.leasing-schema');
        }
        $message = sprintf('Are you sure you want to delete the "%s" rate card?', $leasingSchema->name);
        $form    = new DeleteConfirmationForm($message);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                if ($form->isValid($values))
                {
                    try
                    {
                        $leasingSchemaMapper->delete($leasingSchema->id);
                    }
                    catch (Exception $e)
                    {
                        $this->_flashMessenger->addMessage(['danger' => "Failed to delete"]);
                        $this->redirectToRoute('company.leasing-schema');
                    }

                    $this->_flashMessenger->addMessage(['success' => sprintf('The "%s" lease rate card was deleted successfully.', $leasingSchema->name)]);
                    $this->redirectToRoute('company.leasing-schema');
                }
            }
            else // User has selected cancel button, go back.
            {
                $this->redirectToRoute('company.leasing-schema');
            }
        }
        $this->view->form = $form;
    }

    public function addtermAction ()
    {
        $this->_pageTitle = ['Add Term', 'Your Lease Rates', 'Company'];

        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();

        $leasingSchemaId = $this->_getParam('leasingSchemaId', false);

        // Get leasing schema
        $leasingSchemaMapper = LeasingSchemaMapper::getInstance();
        $leasingSchema       = $leasingSchemaMapper->find($leasingSchemaId);

        if (!$leasingSchema)
        {
            $this->_flashMessenger->addMessage(['warning' => 'The leasing schema does not exist.']);
            $this->redirectToRoute('company.leasing-schema');
        }
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(['danger' => 'Insufficient Privilege: You cannot view this leasing schema.']);
            $this->redirectToRoute('company.leasing-schema');
        }

        // Get form and pass ranges for this schema
        $leasingSchemaRanges = $leasingSchema->getRanges();

        if (!$leasingSchemaRanges)
        {
            $this->_flashMessenger->addMessage(['warning' => 'No ranges exist.']);
            $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
        }

        $form = new LeasingSchemaTermForm($leasingSchemaRanges);

        // Postback
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            // If we cancelled we don't need to validate anything
            if (!isset($values ['cancel']))
            {
                $db->beginTransaction();
                try
                {
                    if ($form->isValid($values))
                    {

                        // Get post data
                        $months                  = $values ['term'];
                        $leasingSchemaTermMapper = new LeasingSchemaTermMapper();
                        $leasingSchemaTerm       = $leasingSchemaTermMapper->fetchAll([
                            "leasingSchemaId = ?" => $leasingSchemaId,
                            "months = ?"          => $months
                        ]);
                        if (!$leasingSchemaTerm)
                        {
                            // Insert (Add)
                            $leasingSchemaTermModel                  = new LeasingSchemaTermModel();
                            $leasingSchemaTermModel->leasingSchemaId = $leasingSchemaId;
                            $leasingSchemaTermModel->months          = $months;

                            $termId = $leasingSchemaTermMapper->insert($leasingSchemaTermModel);

                            $leasingSchemaRateMapper = LeasingSchemaRateMapper::getInstance();
                            $leasingSchemaRateModel  = new LeasingSchemaRateModel();

                            // Save rates for range and term
                            foreach ($leasingSchemaRanges as $range)
                            {
                                $rangeId = $range->id;
                                $rate    = $values ["rate{$rangeId}"];

                                $leasingSchemaRateModel->leasingSchemaRangeId = $rangeId;
                                $leasingSchemaRateModel->leasingSchemaTermId  = $termId;
                                $leasingSchemaRateModel->rate                 = $rate;
                                $leasingSchemaRateMapper->insert($leasingSchemaRateModel);
                            }
                            $db->commit();

                            $this->_flashMessenger->addMessage(['success' => "The term {$months} months was added successfully."]);
                        }
                        else
                        {
                            $db->rollBack();
                            $this->_flashMessenger->addMessage(['danger' => "The term {$months} months already exists."]);
                        }
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch (InvalidArgumentException $e)
                {
                    $db->rollback();
                    $this->_flashMessenger->addMessage(['danger' => $e->getMessage()]);
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
            }
        }

        // Add form to page
        $form->setDecorators([['ViewScript', [
            'viewScript'          => 'forms/dealermanagement/leasing-schema-term-form.phtml',
            'leasingSchemaRanges' => $leasingSchema->getRanges()
        ]]]);
        $this->view->leasingSchemaTerm = $form;
    }

    public function edittermAction ()
    {
        $this->_pageTitle = ['Edit Term', 'Your Lease Rates', 'Company'];
        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();

        $leasingSchemaId = $this->_getParam('leasingSchemaId', false);
        $termId          = $this->_getParam('id', false);

        // Get leasing schema
        $leasingSchemaMapper = LeasingSchemaMapper::getInstance();
        $leasingSchema       = $leasingSchemaMapper->find($leasingSchemaId);

        if (!$leasingSchema)
        {
            $this->_flashMessenger->addMessage(['warning' => 'The leasing schema does not exist.']);
            $this->redirectToRoute('company.leasing-schema');
        }
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(['danger' => 'Insufficient Privilege: You cannot view this leasing schema.']);
            $this->redirectToRoute('company.leasing-schema');
        }

        // Get form and pass ranges for this schema
        $leasingSchemaRanges = $leasingSchema->getRanges();

        if (!$leasingSchemaRanges)
        {
            $this->_flashMessenger->addMessage(['warning' => 'No ranges exist.']);
            $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
        }

        $form = new LeasingSchemaTermForm($leasingSchemaRanges);

        // Postback
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            // If we cancelled we don't need to validate anything
            if (!isset($values ['cancel']))
            {
                $db->beginTransaction();
                try
                {
                    if ($form->isValid($values))
                    {
                        // Get post data
                        $months = $values ['term'];

                        // Save new term
                        if ($termId)
                        {
                            // Save (Edit)
                            $leasingSchemaTermMapper = new LeasingSchemaTermMapper();
                            $leasingSchemaTerm       = $leasingSchemaTermMapper->fetchAll([
                                "id != ?"             => $termId,
                                "leasingSchemaId = ?" => $leasingSchemaId,
                                "months = ?"          => $months
                            ]);

                            // Edit
                            if (!$leasingSchemaTerm)
                            {
                                $leasingSchemaTermModel                  = new LeasingSchemaTermModel();
                                $leasingSchemaTermModel->id              = $termId;
                                $leasingSchemaTermModel->leasingSchemaId = $leasingSchemaId;
                                $leasingSchemaTermModel->months          = $months;
                                $leasingSchemaTermMapper->save($leasingSchemaTermModel);

                                $leasingSchemaRateMapper = LeasingSchemaRateMapper::getInstance();
                                $leasingSchemaRateModel  = new LeasingSchemaRateModel();

                                // Save rates for range and term
                                foreach ($leasingSchemaRanges as $range)
                                {
                                    $rangeId = $range->id;
                                    $rate    = $values ["rate{$rangeId}"];

                                    $leasingSchemaRateModel->leasingSchemaTermId  = $termId;
                                    $leasingSchemaRateModel->leasingSchemaRangeId = $rangeId;
                                    $leasingSchemaRateModel->rate                 = $rate;
                                    $leasingSchemaRateMapper->save($leasingSchemaRateModel);
                                }
                                $db->commit();

                                $this->_flashMessenger->addMessage(['success' => "The term {$months} months was updated successfully."]);
                            }
                            else
                            {
                                $db->rollBack();
                                $this->_flashMessenger->addMessage(['danger' => "The term {$months} months already exists."]);
                            }
                        }
                        else
                        {
                            $db->rollBack();
                            $this->_flashMessenger->addMessage(['error' => "No term was selected. Please try again."]);
                        }
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch (Exception $e)
                {
                    // Save Error
                    $db->rollBack();
                    $this->_flashMessenger->addMessage(['danger' => $e->getMessage()]);
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
            }
        }
        else
        {
            // Populate form for Editing
            if ($termId > 0)
            {
                // Get Term
                $leasingSchemaTermMapper = LeasingSchemaTermMapper::getInstance();
                $leasingSchemaTerm       = $leasingSchemaTermMapper->find($termId);

                if (!$leasingSchemaTerm)
                {
                    $this->_flashMessenger->addMessage(['warning' => 'The leasing schema term does not exist.']);
                    $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
                }

                $form->getElement('term')->setValue($leasingSchemaTerm->months);

                // Get Rates for Term
                $leasingSchemaRatesMapper = LeasingSchemaRateMapper::getInstance();

                $leasingSchemaRate = $leasingSchemaRatesMapper->fetchAll([
                    'leasingSchemaTermId = ?' => $termId
                ]);
                /*
                 * @var $rate MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaRateModel
                 */
                foreach ($leasingSchemaRate as $rate)
                {
                    $rangeId = $rate->leasingSchemaRangeId;
                    $amount  = $rate->rate;

                    if ($form->getElement("rate{$rangeId}"))
                    {
                        $form->getElement("rate{$rangeId}")->setValue($amount);
                    }
                }
            }
        }

        // Add form to page
        $form->setDecorators([['ViewScript', [
            'viewScript'          => 'forms/dealermanagement/leasing-schema-term-form.phtml',
            'leasingSchemaRanges' => $leasingSchema->getRanges()
        ]]]);
        $this->view->leasingSchemaTerm = $form;
    }

    public function deletetermAction ()
    {
        $this->_pageTitle = ['Delete Term', 'Your Lease Rates', 'Company'];
        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();

        $leasingSchemaId = $this->_getParam('leasingSchemaId', false);
        $termId          = $this->_getParam('id', false);

        if (!$termId)
        {
            $this->_flashMessenger->addMessage(['warning' => 'Please select a term to delete first.']);
            $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
        }
        $leasingSchemaMapper = LeasingSchemaMapper::getInstance();
        $leasingSchema       = $leasingSchemaMapper->find($leasingSchemaId);
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(['danger' => 'Insufficient Privilege: You cannot view this leasing schema.']);
            $this->redirectToRoute('company.leasing-schema');
        }

        $mapper = new LeasingSchemaTermMapper();
        $term   = $mapper->find($termId);

        if (!$termId)
        {
            $this->_flashMessenger->addMessage(['danger' => 'There was an error selecting the term to delete.']);
            $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
        }

        // Make sure this isn't the last term for this schema
        $leasingSchemaTerms = LeasingSchemaTermMapper::getInstance()->fetchAll('leasingSchemaId = ' . $leasingSchemaId);
        if (count($leasingSchemaTerms) <= 1)
        {
            $this->_flashMessenger->addMessage(['danger' => "You cannot delete term {$term->months} months as it is the last term for this leasing schema."]);
            $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
        }

        $message = "Are you sure you want to delete term {$term->months} months?";
        $form    = new DeleteConfirmationForm($message);
        $request = $this->getRequest();

        if ($request->isPost())
        {
            $db->beginTransaction();
            try
            {
                $values = $request->getPost();
                if (!isset($values ['cancel']))
                {
                    // delete client from database
                    if ($form->isValid($values))
                    {
                        $months = $term->months;
                        $mapper->delete($term);
                        $db->commit();
                        $this->_flashMessenger->addMessage(['success' => "The term {$months} months was deleted successfully."]);
                        $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
                    }
                }
                else // go back
                {
                    $db->rollBack();
                    $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
                }
            }
            catch (Exception $e)
            {
                $db->rollBack();
                $this->_flashMessenger->addMessage(['danger' => 'There was an error selecting the term to delete.']);
                $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
            }
        }
        $this->view->form = $form;
    }

    public function addrangeAction ()
    {
        $this->_pageTitle = ['Add Range', 'Your Lease Rates', 'Company'];
        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();

        $leasingSchemaId = $this->_getParam('leasingSchemaId', false);

        // Get leasing schema
        $leasingSchemaMapper = LeasingSchemaMapper::getInstance();
        $leasingSchema       = $leasingSchemaMapper->find($leasingSchemaId);

        if (!$leasingSchema)
        {
            $this->_flashMessenger->addMessage(['warning' => 'The leasing schema does not exist.']);
            $this->redirectToRoute('company.leasing-schema');
        }
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(['danger' => 'Insufficient Privilege: You cannot view this leasing schema.']);
            $this->redirectToRoute('company.leasing-schema');
        }
        // Get form and pass terms for this schema
        $leasingSchemaTerms = $leasingSchema->getTerms();

        $form = new LeasingSchemaRangeForm($leasingSchemaTerms);

        // Postback
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            // If we cancelled we don't need to validate anything
            if (!isset($values ['cancel']))
            {
                $db->beginTransaction();
                try
                {
                    if ($form->isValid($values))
                    {
                        // Get post data
                        $startRange = $values ['range'];

                        // Insert (Add)
                        $leasingSchemaRangeMapper                 = LeasingSchemaRangeMapper::getInstance();
                        $leasingSchemaRangeModel                  = new LeasingSchemaRangeModel();
                        $leasingSchemaRangeModel->leasingSchemaId = $leasingSchemaId;
                        $leasingSchemaRangeModel->startRange      = $startRange;

                        // Validate Range doesn't exist
                        $leasingSchemaRange = $leasingSchemaRangeMapper->fetch([
                            'leasingSchemaId = ?' => $leasingSchemaId,
                            'startRange = ?'      => $startRange
                        ]);

                        if (!$leasingSchemaRange)
                        {
                            $rangeId = $leasingSchemaRangeMapper->insert($leasingSchemaRangeModel);

                            $leasingSchemaRateMapper = LeasingSchemaRateMapper::getInstance();
                            $leasingSchemaRateModel  = new LeasingSchemaRateModel();

                            // Save rates for range and term
                            foreach ($leasingSchemaTerms as $term)
                            {
                                $termId = $term->id;
                                $rate   = $values ["rate{$termId}"];

                                $leasingSchemaRateModel->leasingSchemaRangeId = $rangeId;
                                $leasingSchemaRateModel->leasingSchemaTermId  = $termId;
                                $leasingSchemaRateModel->rate                 = $rate;
                                $leasingSchemaRateMapper->insert($leasingSchemaRateModel);
                            }
                            $db->commit();

                            $this->_flashMessenger->addMessage(['success' => "The range \${$startRange} was added successfully."]);
                        }
                        else
                        {
                            $db->rollBack();
                            $this->_flashMessenger->addMessage(['danger' => "The range \${$startRange} already exists."]);
                        }
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch (InvalidArgumentException $e)
                {
                    $db->rollBack();
                    $this->_flashMessenger->addMessage(['danger' => $e->getMessage()]);
                }
                catch (Exception $e)
                {
                    // Insert Error
                    $db->rollBack();
                    $this->_flashMessenger->addMessage(['danger' => 'There was an error processing the insert. Please try again.']);
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
            }
        }

        // Add form to page
        $form->setDecorators([['ViewScript', [
            'viewScript'         => 'forms/dealermanagement/leasing-schema-range-form.phtml',
            'leasingSchemaTerms' => $leasingSchema->getTerms()
        ]]]);
        $this->view->leasingSchemaRange = $form;
    }

    public function editrangeAction ()
    {
        $this->_pageTitle = ['Edit Range', 'Your Lease Rates', 'Company'];
        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();

        $leasingSchemaId = $this->_getParam('leasingSchemaId', false);
        $rangeId         = $this->_getParam('id', false);

        // Get leasing schema
        $leasingSchemaMapper = LeasingSchemaMapper::getInstance();
        $leasingSchema       = $leasingSchemaMapper->find($leasingSchemaId);

        if (!$leasingSchema)
        {
            $this->_flashMessenger->addMessage(['warning' => 'The leasing schema does not exist.']);
            $this->redirectToRoute('company.leasing-schema');
        }
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(['danger' => 'Insufficient Privilege: You cannot view this leasing schema.']);
            $this->redirectToRoute('company.leasing-schema');
        }

        // Get form and pass terms for this schema
        $leasingSchemaTerms = $leasingSchema->getTerms();

        $form = new LeasingSchemaRangeForm($leasingSchemaTerms);

        // Postback
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            $db->beginTransaction();
            try
            {
                // If we cancelled we don't need to validate anything
                if (!isset($values ['cancel']))
                {
                    if ($form->isValid($values))
                    {
                        // Get post data
                        $startRange = $values ['range'];

                        // Save new range
                        if ($rangeId)
                        {
                            try
                            {
                                // Save (Edit)
                                $leasingSchemaRangeMapper = LeasingSchemaRangeMapper::getInstance();
                                $leasingSchemaRange       = $leasingSchemaRangeMapper->fetchAll([
                                    'id != ?'             => $rangeId,
                                    'leasingSchemaId = ?' => $leasingSchemaId,
                                    'startRange = ?'      => $startRange
                                ]);

                                if (!$leasingSchemaRange)
                                {
                                    $leasingSchemaRangeModel                  = new LeasingSchemaRangeModel();
                                    $leasingSchemaRangeModel->id              = $rangeId;
                                    $leasingSchemaRangeModel->leasingSchemaId = $leasingSchemaId;
                                    $leasingSchemaRangeModel->startRange      = $startRange;
                                    $leasingSchemaRangeMapper->save($leasingSchemaRangeModel);

                                    $leasingSchemaRateMapper = LeasingSchemaRateMapper::getInstance();
                                    $leasingSchemaRateModel  = new LeasingSchemaRateModel();

                                    // Save rates for range and term
                                    foreach ($leasingSchemaTerms as $term)
                                    {
                                        $termId = $term->id;
                                        $rate   = $values ["rate{$termId}"];

                                        $leasingSchemaRateModel->leasingSchemaTermId  = $termId;
                                        $leasingSchemaRateModel->leasingSchemaRangeId = $rangeId;
                                        $leasingSchemaRateModel->rate                 = $rate;
                                        $leasingSchemaRateMapper->save($leasingSchemaRateModel);
                                    }
                                    $db->commit();

                                    $this->_flashMessenger->addMessage(['success' => "The range was updated successfully."]);
                                }
                                else
                                {
                                    $db->rollBack();
                                    $this->_flashMessenger->addMessage(['danger' => "The range \${$startRange} already exists."]);
                                }
                            }
                            catch (Exception $e)
                            {
                                // Save Error
                                $db->rollBack();
                                $this->_flashMessenger->addMessage(['danger' => 'There was an error processing the update.  Please try again.']);
                            }
                        }
                    }
                    else
                    {
                        $db->rollBack();
                        $this->_flashMessenger->addMessage(['error' => "Please review and complete all required fields."]);
                    }
                }
                else
                {
                    // User has cancelled. We could do a redirect here if we wanted.
                    $db->rollBack();
                    $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
                }
            }
            catch (Zend_Validate_Exception $e)
            {
                $db->rollBack();
                $form->buildBootstrapErrorDecorators();
            }
        }
        else
        {
            // Populate form for Editing
            if ($rangeId > 0)
            {
                // Get Range
                $leasingSchemaRangeMapper = LeasingSchemaRangeMapper::getInstance();
                $leasingSchemaRange       = $leasingSchemaRangeMapper->find($rangeId);

                if (!$leasingSchemaRange)
                {
                    $this->_flashMessenger->addMessage(['warning' => 'The leasing schema range does not exist.']);
                    $this->redirectToRoute('company.leasing-schema');
                }

                $form->getElement('range')->setValue($leasingSchemaRange->startRange);

                // Get Rates for Range
                $leasingSchemaRatesMapper = LeasingSchemaRateMapper::getInstance();

                $leasingSchemaRate = $leasingSchemaRatesMapper->fetchAll([
                    'leasingSchemaRangeId = ?' => $rangeId
                ]);
                /**
                 * @var $rate LeasingSchemaRateModel
                 */
                foreach ($leasingSchemaRate as $rate)
                {
                    $termId = $rate->leasingSchemaTermId;
                    $amount = $rate->rate;

                    if ($form->getElement("rate{$termId}"))
                    {
                        $form->getElement("rate{$termId}")->setValue($amount);
                    }
                }
            }
        }

        // Add form to page
        $form->setDecorators([['ViewScript', [
            'viewScript'         => 'forms/dealermanagement/leasing-schema-range-form.phtml',
            'leasingSchemaTerms' => $leasingSchema->getTerms()
        ]]]);
        $this->view->leasingSchemaRange = $form;
    }

    public function deleterangeAction ()
    {
        $this->_pageTitle = ['Delete Range', 'Your Lease Rates', 'Company'];
        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();

        $leasingSchemaId = $this->_getParam('leasingSchemaId', false);
        $rangeId         = $this->_getParam('id', false);

        if (!$rangeId)
        {
            $this->_flashMessenger->addMessage(['warning' => 'Please select a range to delete first.']);
            $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
        }
        $leasingSchemaMapper = LeasingSchemaMapper::getInstance();
        $leasingSchema       = $leasingSchemaMapper->find($leasingSchemaId);
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(['danger' => 'Insufficient Privilege: You cannot view this leasing schema.']);
            $this->redirectToRoute('company.leasing-schema');
        }

        $mapper = new LeasingSchemaRangeMapper();
        $range  = $mapper->find($rangeId);

        if (!$rangeId)
        {
            $this->_flashMessenger->addMessage(['danger' => 'There was an error selecting the range to delete.']);
            $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
        }

        // Make sure this isn't the last range for this schema
        $leasingSchemaRanges = LeasingSchemaRangeMapper::getInstance()->fetchAll('leasingSchemaId = ' . $leasingSchemaId);
        if (count($leasingSchemaRanges) <= 1)
        {
            $this->_flashMessenger->addMessage(['danger' => "You cannot delete the range \${$range->startRange}  as it is the last range for this Leasing Schema."]);
            $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
        }
        else
        {
            $message = "Are you sure you want to delete the range \${$range->startRange}?";
        }
        $form = new DeleteConfirmationForm($message);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            $db->beginTransaction();
            try
            {
                if (!isset($values ['cancel']))
                {
                    // delete client from database
                    if ($form->isValid($values))
                    {
                        $mapper->delete($range);
                        $db->commit();
                        $this->_flashMessenger->addMessage(['success' => "The range \${$this->view->escape($range->startRange)} was deleted successfully."]);
                        $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
                    }
                }
                else // go back
                {
                    $db->rollBack();
                    $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
                }
            }
            catch (Exception $e)
            {
                $db->rollBack();
                $this->_flashMessenger->addMessage(['danger' => 'There was an error selecting the term to delete.']);
                $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
            }
        }
        $this->view->form = $form;
    }

    public function importAction ()
    {
        $this->_pageTitle = ['Import Card', 'Your Lease Rates', 'Company'];
        $leasingSchemaId  = $this->_getParam('leasingSchemaId', false);
        $leasingSchema    = null;
        // Get db adapter
        $db   = Zend_Db_Table::getDefaultAdapter();
        $form = new ImportLeaseCsvForm(['csv'], "1B", "8MB");

        // Postback
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ['cancel']))
            {
                if ($leasingSchemaId > 0) // We need to return the user to their previous view
                {
                    $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
                }
                else
                {
                    $this->redirectToRoute('company.leasing-schema');
                }
            }
            $db->beginTransaction();
            try
            {
                // Prep mappers
                $leasingSchemaMapper      = LeasingSchemaMapper::getInstance();
                $leasingSchemaRateMapper  = LeasingSchemaRateMapper::getInstance();
                $leasingSchemaRangeMapper = LeasingSchemaRangeMapper::getInstance();
                $leasingSchemaTermMapper  = LeasingSchemaTermMapper::getInstance();

                // Prep models
                $leasingSchemaRateModel  = new LeasingSchemaRateModel();
                $leasingSchemaRangeModel = new LeasingSchemaRangeModel();
                $leasingSchemaTermModel  = new LeasingSchemaTermModel();

                if ($form->isValid($values) && $form->getUploadedFilename() !== false)
                {
                    $rangeIds = null;
                    // Get all the lines in the file
                    $lines = file($form->getUploadedFilename(), FILE_IGNORE_NEW_LINES);

                    // If we are trying to import for an existing leasing schema
                    if ($leasingSchemaId > 0)
                    {
                        $leasingSchema = $leasingSchemaMapper->find($leasingSchemaId);
                    }

                    if (!$leasingSchema instanceof LeasingSchemaModel)
                    {
                        $leasingSchema           = new LeasingSchemaModel();
                        $leasingSchema->name     = basename($form->getUploadedFilename());
                        $leasingSchema->dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
                        $leasingSchemaId         = $leasingSchemaMapper->insert($leasingSchema);
                    }
                    else
                    {
                        $this->emptySchema($leasingSchemaId);
                    }

                    // Loop through remaining lines and save terms/rates
                    foreach ($lines as $key => $value)
                    {
                        if ($key == 0)
                        {
                            // Split value into an array
                            $ranges = explode(",", $value);

                            // Loop through array and save ranges
                            foreach ($ranges as $rangekey => $range)
                            {
                                if ($rangekey > 0)
                                {
                                    if (!is_numeric($range))
                                    {
                                        throw new Exception("Passing exception up the chain.", 0, null);
                                    }
                                    // Build array of range id's
                                    $leasingSchemaRangeModel->leasingSchemaId = $leasingSchemaId;
                                    $leasingSchemaRangeModel->startRange      = $range;
                                    $rangeIds []                              = $leasingSchemaRangeMapper->insert($leasingSchemaRangeModel);
                                }
                            }
                        }
                        else
                        {
                            $rates = explode(",", $value);
                            foreach ($rates as $ratekey => $ratevalue)
                            {

                                // First column is the term
                                if ($ratekey == 0)
                                {
                                    // If its not a valid month, ERROR OUT!
                                    if (!is_numeric($ratevalue) || $ratevalue <= 0)
                                    {
                                        throw new Exception("Passing exception up the chain.", 0, null);
                                    }

                                    $months = $ratevalue;

                                    // Insert term
                                    $leasingSchemaTermModel->leasingSchemaId = $leasingSchemaId;
                                    $leasingSchemaTermModel->months          = $months;
                                    $termId                                  = $leasingSchemaTermMapper->insert($leasingSchemaTermModel);
                                }
                                else
                                {
                                    // Get Range Id for this column
                                    $rangeId = $rangeIds [$ratekey - 1];

                                    // Loop through remaining columns and save rates
                                    if ($termId > 0 && $rangeId > 0)
                                    {
                                        if (!is_numeric($ratevalue) || $ratevalue > 1)
                                        {
                                            throw new Exception("Passing exception up the chain.", 0, null);
                                        }
                                        // Get rate
                                        $rate = $ratevalue;

                                        // Save Rate
                                        $leasingSchemaRateModel->leasingSchemaTermId  = $termId;
                                        $leasingSchemaRateModel->leasingSchemaRangeId = $rangeId;
                                        $leasingSchemaRateModel->rate                 = $rate;
                                        $leasingSchemaRateId                          = $leasingSchemaRateMapper->insert($leasingSchemaRateModel);
                                    }
                                }
                            }
                        }
                    }

                    // Delete the file we just uploaded
                    unlink($form->getUploadedFilename());

                    // Commit changes to the database
                    $db->commit();

                    // Send success message to the screen
                    $this->_flashMessenger->addMessage(['success' => "The leasing schema import was completed successfully."]);
                    $this->redirectToRoute('company.leasing-schema.view', ['leasingSchemaId' => $leasingSchemaId]);
                }
                else
                {
                    $db->rollback();
                    if ($form->getErrorMessages())
                    {
                        // if upload fails, print error message message
                        $this->view->errMessages = $form->getErrorMessages();
                    }
                    else
                    {
                        // Display errors to screen
                        $this->_flashMessenger->addMessage(['error' => "An error has occurred and the import was not completed. Please double check the format of your file and try again."]);
                    }
                }
            }
            catch (Zend_Db_Statement_Mysqli_Exception $e)
            {
                $db->rollback();
                $this->_flashMessenger->addMessage(['error' => "There was an error saving the leasing schema to the database."]);
            }
            catch (Exception $e)
            {
                $db->rollback();
                $this->_flashMessenger->addMessage(['error' => "An error has occurred and the updates were not saved."]);
            }

        }
        $this->view->form = $form;
    }

    public function resetschemaAction ()
    {
        $this->_pageTitle = ['Clear Card', 'Your Lease Rates', 'Company'];
        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();

        // Get default leasing schema id
        $leasingSchemaId     = $this->_getParam('leasingSchemaId', false);
        $leasingSchemaMapper = LeasingSchemaMapper::getInstance();
        $leasingSchema       = $leasingSchemaMapper->find($leasingSchemaId);
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(['danger' => 'Insufficient Privilege: You cannot view this leasing schema.']);
            $this->redirectToRoute('company.leasing-schema');
        }
        // Reset the grid to default values
        $months = "12";
        $range  = "0";
        $rate   = "0.0750";

        // Prep mappers
        $leasingSchemaRateMapper  = LeasingSchemaRateMapper::getInstance();
        $leasingSchemaRangeMapper = LeasingSchemaRangeMapper::getInstance();
        $leasingSchemaTermMapper  = LeasingSchemaTermMapper::getInstance();

        // Prep models
        $leasingSchemaRateModel  = new LeasingSchemaRateModel();
        $leasingSchemaRangeModel = new LeasingSchemaRangeModel();
        $leasingSchemaTermModel  = new LeasingSchemaTermModel();

        $message = "Are you sure you want to reset the current schema back to it's default? This action cannot be undone.";
        $form    = new DeleteConfirmationForm($message);


        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            $db->beginTransaction();
            try
            {
                if (!isset($values ['cancel']))
                {
                    // Delete Existing Terms and Ranges
                    if ($this->emptySchema($leasingSchemaId))
                    {
                        // Save Term
                        $leasingSchemaTermModel->leasingSchemaId = $leasingSchemaId;
                        $leasingSchemaTermModel->months          = $months;
                        $termId                                  = $leasingSchemaTermMapper->insert($leasingSchemaTermModel);

                        // Save Range
                        $leasingSchemaRangeModel->leasingSchemaId = $leasingSchemaId;
                        $leasingSchemaRangeModel->startRange      = $range;
                        $rangeId                                  = $leasingSchemaRangeMapper->insert($leasingSchemaRangeModel);

                        // Save Rate
                        $leasingSchemaRateModel->leasingSchemaTermId  = $termId;
                        $leasingSchemaRateModel->leasingSchemaRangeId = $rangeId;
                        $leasingSchemaRateModel->rate                 = $rate;
                        $leasingSchemaRateMapper->insert($leasingSchemaRateModel);

                        // Commit changes
                        $db->commit();

                        // Send success message to the screen
                        $this->_flashMessenger->addMessage(['success' => "The leasing schema has been successfully reset."]);
                    }
                    else
                    {
                        // Delete current failed
                        $db->rollBack();
                        $this->_flashMessenger->addMessage(['error' => "An error occurred while deleting the current schema."]);
                    }
                }
                else
                {
                    // Cancel action
                    $db->rollBack();
                    $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
                }
            }
            catch (Exception $e)
            {
                // Delete current failed
                $db->rollBack();
                $this->_flashMessenger->addMessage(['error' => "An error occurred while deleting the current schema."]);
            }

            // Always redirect back to index
            $this->redirectToRoute('company.leasing-schema.view', ["leasingSchemaId" => $leasingSchemaId]);
        }
        $this->view->form = $form;
    }

    /**
     * @param $leasingSchemaId
     *
     * @return bool
     */
    public function emptySchema ($leasingSchemaId)
    {
        // Prep mappers
        $leasingSchemaRangeMapper = LeasingSchemaRangeMapper::getInstance();
        $leasingSchemaTermMapper  = LeasingSchemaTermMapper::getInstance();

        try
        {
            // Delete existing leasing schema ranges
            $leasingSchemaRanges = $leasingSchemaRangeMapper->fetchAll([
                'leasingSchemaId = ?' => $leasingSchemaId
            ]);
            foreach ($leasingSchemaRanges as $leasingSchemaRange)
            {
                $leasingSchemaRangeMapper->delete($leasingSchemaRange);
            }

            // Delete existing leasing schema terms
            $leasingSchemaTerms = $leasingSchemaTermMapper->fetchAll([
                'leasingSchemaId = ?' => $leasingSchemaId
            ]);
            foreach ($leasingSchemaTerms as $leasingSchemaTerm)
            {
                $leasingSchemaTermMapper->delete($leasingSchemaTerm);
            }
        }
        catch (Exception $e)
        {
            Return false;
        }

        Return true;
    }
}

