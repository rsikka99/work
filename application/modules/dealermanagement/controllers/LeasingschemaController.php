<?php

/**
 * Class Dealermanagement_LeasingschemaController
 */
class Dealermanagement_LeasingschemaController extends Tangent_Controller_Action
{

    public function indexAction ()
    {
        // Display all of the leasing schema rates in a grid
        $leasingSchemas             = Quotegen_Model_Mapper_LeasingSchema::getInstance()->getSchemasForDealer(Zend_Auth::getInstance()->getIdentity()->dealerId);
        $this->view->leasingSchemas = $leasingSchemas;
    }

    public function viewAction ()
    {
        $leasingSchemaId = $this->_getParam('leasingSchemaId', false);
        if (!$leasingSchemaId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'error' => 'That schema does not exist'
                                               ));
            $this->redirector('index');
        }
        $leasingSchema = Quotegen_Model_Mapper_LeasingSchema::getInstance()->find($leasingSchemaId);
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'Insufficient Privilege: You cannot view this leasing schema.'
                                               ));
            $this->redirector('index');
        }
        // Display all of the leasing schema rates in a grid
        $leasingSchemaMapper       = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema             = $leasingSchemaMapper->find($leasingSchemaId);
        $this->view->leasingSchema = $leasingSchema;
    }

    public function editAction ()
    {
        $leasingSchemaId = $this->_getParam('leasingSchemaId', false);
        if (!$leasingSchemaId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'error' => 'That schema does not exist'
                                               ));
            $this->redirector('index');
        }
        $leasingSchema = Quotegen_Model_Mapper_LeasingSchema::getInstance()->find($leasingSchemaId);
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'Insufficient Privilege: You cannot view this leasing schema.'
                                               ));
            $this->redirector('index');
        }
        $form             = new  Admin_Form_LeasingSchema(true);
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
                        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
                        $leasingSchema->populate($values);
                        $leasingSchemaMapper->save($leasingSchema);
                        $db->commit();
                        $this->_flashMessenger->addMessage(array(
                                                                'success' => "{$leasingSchema->name} has been saved successfully."
                                                           ));
                        $this->redirector('index');
                    }
                    catch (InvalidArgumentException $e)
                    {
                        $db->rollback();
                        $this->_flashMessenger->addMessage(array(
                                                                'danger' => $e->getMessage()
                                                           ));
                    }
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $db->rollBack();
                $this->redirector('index');
            }
        }
        $form->populate($leasingSchema->toArray());
    }


    public function createAction ()
    {
        $form             = new  Admin_Form_LeasingSchema(true);
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
                        $leasingSchemaMapper     = Quotegen_Model_Mapper_LeasingSchema::getInstance();
                        $leasingSchema           = new Quotegen_Model_LeasingSchema($values);
                        $leasingSchema->dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
                        $leasingSchemaMapper->insert($leasingSchema);
                        // Reset the grid to default values
                        $months = "12";
                        $range  = "0";
                        $rate   = "0.0750";

                        // Prep mappers
                        $leasingSchemaRateMapper  = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                        $leasingSchemaRangeMapper = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance();
                        $leasingSchemaTermMapper  = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance();

                        // Prep models
                        $leasingSchemaRateModel  = new Quotegen_Model_LeasingSchemaRate();
                        $leasingSchemaRangeModel = new Quotegen_Model_LeasingSchemaRange();
                        $leasingSchemaTermModel  = new Quotegen_Model_LeasingSchemaTerm();
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
                        $this->_flashMessenger->addMessage(array(
                                                                'success' => "{$leasingSchema->name} has been created successfully."
                                                           ));
                        $this->redirector('index');
                    }
                    catch (InvalidArgumentException $e)
                    {
                        $db->rollback();
                        $this->_flashMessenger->addMessage(array(
                                                                'danger' => $e->getMessage()
                                                           ));
                    }
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $db->rollBack();
                $this->redirector('index');
            }
        }
    }

    public function deleteAction ()
    {
        $leasingSchemaId = $this->_getParam('leasingSchemaId', false);
        if (!$leasingSchemaId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'warning' => 'Please select a leasing schema to delete first.'
                                               ));
            $this->redirector('index');
        }

        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema       = $leasingSchemaMapper->find($leasingSchemaId);
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'Insufficient Privilege: You cannot delete this leasing schema.'
                                               ));
            $this->redirector('index');
        }
        $message = "Are you sure you want to completely delete {$leasingSchema->name}?";
        $form    = new Application_Form_Delete($message);

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
                        $this->_flashMessenger->addMessage(array(
                                                                'danger' => "Failed to delete"
                                                           ));
                        $this->redirector('index');
                    }

                    $this->_flashMessenger->addMessage(array(
                                                            'success' => "Leasing Schema  {$leasingSchema->name} was deleted successfully."
                                                       ));
                    $this->redirector('index');
                }
            }
            else // User has selected cancel button, go back.
            {
                $this->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    public function addtermAction ()
    {
        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();

        $leasingSchemaId = $this->_getParam('leasingSchemaId', false);

        // Get leasing schema
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema       = $leasingSchemaMapper->find($leasingSchemaId);

        if (!$leasingSchema)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'warning' => 'The leasing schema does not exist.'
                                               ));
            $this->redirector('index');
        }
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'Insufficient Privilege: You cannot view this leasing schema.'
                                               ));
            $this->redirector('index');
        }

        // Get form and pass ranges for this schema
        $leasingSchemaRanges = $leasingSchema->getRanges();

        if (!$leasingSchemaRanges)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'warning' => 'No ranges exist.'
                                               ));
            $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
        }

        $form = new Quotegen_Form_LeasingSchemaTerm($leasingSchemaRanges);

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
                        $leasingSchemaTermMapper = new Quotegen_Model_Mapper_LeasingSchemaTerm();
                        $leasingSchemaTerm       = $leasingSchemaTermMapper->fetchAll(array(
                                                                                           "leasingSchemaId = ?" => $leasingSchemaId,
                                                                                           "months = ?"          => $months
                                                                                      ));
                        if (!$leasingSchemaTerm)
                        {
                            // Insert (Add)
                            $leasingSchemaTermModel                  = new Quotegen_Model_LeasingSchemaTerm();
                            $leasingSchemaTermModel->leasingSchemaId = $leasingSchemaId;
                            $leasingSchemaTermModel->months          = $months;

                            $termId = $leasingSchemaTermMapper->insert($leasingSchemaTermModel);

                            $leasingSchemaRateMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                            $leasingSchemaRateModel  = new Quotegen_Model_LeasingSchemaRate();

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

                            $this->_flashMessenger->addMessage(array(
                                                                    'success' => "The term {$months} months was added successfully."
                                                               ));
                        }
                        else
                        {
                            $db->rollBack();
                            $this->_flashMessenger->addMessage(array(
                                                                    'danger' => "The term {$months} months already exists."
                                                               ));
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
                    $this->_flashMessenger->addMessage(array(
                                                            'danger' => $e->getMessage()
                                                       ));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $db->rollBack();
                $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
            }
        }

        // Add form to page
        $form->setDecorators(array(
                                  array(
                                      'ViewScript',
                                      array(
                                          'viewScript'          => 'leasingschema/forms/leasingSchemaTerm.phtml',
                                          'leasingSchemaRanges' => $leasingSchema->getRanges()
                                      )
                                  )
                             ));
        $this->view->leasingSchemaTerm = $form;
    }

    public function edittermAction ()
    {
        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();

        $leasingSchemaId = $this->_getParam('leasingSchemaId', false);
        $termId          = $this->_getParam('id', false);

        // Get leasing schema
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema       = $leasingSchemaMapper->find($leasingSchemaId);

        if (!$leasingSchema)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'warning' => 'The leasing schema does not exist.'
                                               ));
            $this->redirector('index');
        }
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'Insufficient Privilege: You cannot view this leasing schema.'
                                               ));
            $this->redirector('index');
        }

        // Get form and pass ranges for this schema
        $leasingSchemaRanges = $leasingSchema->getRanges();

        if (!$leasingSchemaRanges)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'warning' => 'No ranges exist.'
                                               ));
            $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
        }

        $form = new Quotegen_Form_LeasingSchemaTerm($leasingSchemaRanges);

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
                            $leasingSchemaTermMapper = new Quotegen_Model_Mapper_LeasingSchemaTerm();
                            $leasingSchemaTerm       = $leasingSchemaTermMapper->fetchAll(array(
                                                                                               "id != ?"             => $termId,
                                                                                               "leasingSchemaId = ?" => $leasingSchemaId,
                                                                                               "months = ?"          => $months
                                                                                          ));

                            // Edit
                            if (!$leasingSchemaTerm)
                            {
                                $leasingSchemaTermModel                  = new Quotegen_Model_LeasingSchemaTerm();
                                $leasingSchemaTermModel->id              = $termId;
                                $leasingSchemaTermModel->leasingSchemaId = $leasingSchemaId;
                                $leasingSchemaTermModel->months          = $months;
                                $leasingSchemaTermMapper->save($leasingSchemaTermModel);

                                $leasingSchemaRateMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                                $leasingSchemaRateModel  = new Quotegen_Model_LeasingSchemaRate();

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

                                $this->_flashMessenger->addMessage(array(
                                                                        'success' => "The term {$months} months was updated successfully."
                                                                   ));
                            }
                            else
                            {
                                $db->rollBack();
                                $this->_flashMessenger->addMessage(array(
                                                                        'danger' => "The term {$months} months already exists."
                                                                   ));
                            }
                        }
                        else
                        {
                            $db->rollBack();
                            $this->_flashMessenger->addMessage(array(
                                                                    'error' => "No term was selected. Please try again."
                                                               ));
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
                    $this->_flashMessenger->addMessage(array(
                                                            'danger' => $e->getMessage()
                                                       ));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $db->rollBack();
                $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
            }
        }
        else
        {
            // Populate form for Editing
            if ($termId > 0)
            {
                // Get Term
                $leasingSchemaTermMapper = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance();
                $leasingSchemaTerm       = $leasingSchemaTermMapper->find($termId);

                if (!$leasingSchemaTerm)
                {
                    $this->_flashMessenger->addMessage(array(
                                                            'warning' => 'The leasing schema term does not exist.'
                                                       ));
                    $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
                }

                $form->getElement('term')->setValue($leasingSchemaTerm->months);

                // Get Rates for Term
                $leasingSchemaRatesMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();

                $leasingSchemaRate = $leasingSchemaRatesMapper->fetchAll(array(
                                                                              'leasingSchemaTermId = ?' => $termId
                                                                         ));
                /*
                 * @var $rate Quotegen_Model_LeasingSchemaRate
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
        $form->setDecorators(array(
                                  array(
                                      'ViewScript',
                                      array(
                                          'viewScript'          => 'leasingschema/forms/leasingSchemaTerm.phtml',
                                          'leasingSchemaRanges' => $leasingSchema->getRanges()
                                      )
                                  )
                             ));
        $this->view->leasingSchemaTerm = $form;
    }

    public function deletetermAction ()
    {
        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();

        $leasingSchemaId = $this->_getParam('leasingSchemaId', false);
        $termId          = $this->_getParam('id', false);

        if (!$termId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'warning' => 'Please select a term to delete first.'
                                               ));
            $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
        }
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema       = $leasingSchemaMapper->find($leasingSchemaId);
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'Insufficient Privilege: You cannot view this leasing schema.'
                                               ));
            $this->redirector('index');
        }

        $mapper = new Quotegen_Model_Mapper_LeasingSchemaTerm();
        $term   = $mapper->find($termId);

        if (!$termId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'There was an error selecting the term to delete.'
                                               ));
            $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
        }

        // Make sure this isn't the last term for this schema
        $leasingSchemaTerms = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance()->fetchAll('leasingSchemaId = ' . $leasingSchemaId);
        if (count($leasingSchemaTerms) <= 1)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => "You cannot delete term {$term->months} months as it is the last term for this leasing schema."
                                               ));
            $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
        }

        $message = "Are you sure you want to delete term {$term->months} months?";
        $form    = new Application_Form_Delete($message);
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
                        $this->_flashMessenger->addMessage(array(
                                                                'success' => "The term {$months} months was deleted successfully."
                                                           ));
                        $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
                    }
                }
                else // go back
                {
                    $db->rollBack();
                    $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
                }
            }
            catch (Exception $e)
            {
                $db->rollBack();
                $this->_flashMessenger->addMessage(array(
                                                        'danger' => 'There was an error selecting the term to delete.'
                                                   ));
                $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
            }
        }
        $this->view->form = $form;
    }

    public function addrangeAction ()
    {
        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();

        $leasingSchemaId = $this->_getParam('leasingSchemaId', false);

        // Get leasing schema
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema       = $leasingSchemaMapper->find($leasingSchemaId);

        if (!$leasingSchema)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'warning' => 'The leasing schema does not exist.'
                                               ));
            $this->redirector('index');
        }
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'Insufficient Privilege: You cannot view this leasing schema.'
                                               ));
            $this->redirector('index');
        }
        // Get form and pass terms for this schema
        $leasingSchemaTerms = $leasingSchema->getTerms();

        $form = new Quotegen_Form_LeasingSchemaRange($leasingSchemaTerms);

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
                        $leasingSchemaRangeMapper                 = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance();
                        $leasingSchemaRangeModel                  = new Quotegen_Model_LeasingSchemaRange();
                        $leasingSchemaRangeModel->leasingSchemaId = $leasingSchemaId;
                        $leasingSchemaRangeModel->startRange      = $startRange;

                        // Validate Range doesn't exist
                        $leasingSchemaRange = $leasingSchemaRangeMapper->fetch(array(
                                                                                    'leasingSchemaId = ?' => $leasingSchemaId,
                                                                                    'startRange = ?'      => $startRange
                                                                               ));

                        if (!$leasingSchemaRange)
                        {
                            $rangeId = $leasingSchemaRangeMapper->insert($leasingSchemaRangeModel);

                            $leasingSchemaRateMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                            $leasingSchemaRateModel  = new Quotegen_Model_LeasingSchemaRate();

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

                            $this->_flashMessenger->addMessage(array(
                                                                    'success' => "The range \${$startRange} was added successfully."
                                                               ));
                        }
                        else
                        {
                            $db->rollBack();
                            $this->_flashMessenger->addMessage(array(
                                                                    'danger' => "The range \${$startRange} already exists."
                                                               ));
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
                    $this->_flashMessenger->addMessage(array(
                                                            'danger' => $e->getMessage()
                                                       ));
                }
                catch (Exception $e)
                {
                    // Insert Error
                    $db->rollBack();
                    $this->_flashMessenger->addMessage(array(
                                                            'danger' => 'There was an error processing the insert. Please try again.'
                                                       ));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $db->rollBack();
                $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
            }
        }

        // Add form to page
        $form->setDecorators(array(
                                  array(
                                      'ViewScript',
                                      array(
                                          'viewScript'         => 'leasingschema/forms/leasingSchemaRange.phtml',
                                          'leasingSchemaTerms' => $leasingSchema->getTerms()
                                      )
                                  )
                             ));
        $this->view->leasingSchemaRange = $form;
    }

    public function editrangeAction ()
    {
        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();

        $leasingSchemaId = $this->_getParam('leasingSchemaId', false);
        $rangeId         = $this->_getParam('id', false);

        // Get leasing schema
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema       = $leasingSchemaMapper->find($leasingSchemaId);

        if (!$leasingSchema)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'warning' => 'The leasing schema does not exist.'
                                               ));
            $this->redirector('index');
        }
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'Insufficient Privilege: You cannot view this leasing schema.'
                                               ));
            $this->redirector('index');
        }

        // Get form and pass terms for this schema
        $leasingSchemaTerms = $leasingSchema->getTerms();

        $form = new Quotegen_Form_LeasingSchemaRange($leasingSchemaTerms);

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
                                $leasingSchemaRangeMapper = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance();
                                $leasingSchemaRange       = $leasingSchemaRangeMapper->fetchAll(array(
                                                                                                     'id != ?'             => $rangeId,
                                                                                                     'leasingSchemaId = ?' => $leasingSchemaId,
                                                                                                     'startRange = ?'      => $startRange
                                                                                                ));

                                if (!$leasingSchemaRange)
                                {
                                    $leasingSchemaRangeModel                  = new Quotegen_Model_LeasingSchemaRange();
                                    $leasingSchemaRangeModel->id              = $rangeId;
                                    $leasingSchemaRangeModel->leasingSchemaId = $leasingSchemaId;
                                    $leasingSchemaRangeModel->startRange      = $startRange;
                                    $leasingSchemaRangeMapper->save($leasingSchemaRangeModel);

                                    $leasingSchemaRateMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                                    $leasingSchemaRateModel  = new Quotegen_Model_LeasingSchemaRate();

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

                                    $this->_flashMessenger->addMessage(array(
                                                                            'success' => "The range was updated successfully."
                                                                       ));
                                }
                                else
                                {
                                    $db->rollBack();
                                    $this->_flashMessenger->addMessage(array(
                                                                            'danger' => "The range \${$startRange} already exists."
                                                                       ));
                                }
                            }
                            catch (Exception $e)
                            {
                                // Save Error
                                $db->rollBack();
                                $this->_flashMessenger->addMessage(array(
                                                                        'danger' => 'There was an error processing the update.  Please try again.'
                                                                   ));
                            }
                        }
                    }
                    else
                    {
                        $db->rollBack();
                        $this->_flashMessenger->addMessage(array(
                                                                'error' => "Please review and complete all required fields."
                                                           ));
                    }
                }
                else
                {
                    // User has cancelled. We could do a redirect here if we wanted.
                    $db->rollBack();
                    $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
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
                $leasingSchemaRangeMapper = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance();
                $leasingSchemaRange       = $leasingSchemaRangeMapper->find($rangeId);

                if (!$leasingSchemaRange)
                {
                    $this->_flashMessenger->addMessage(array(
                                                            'warning' => 'The leasing schema range does not exist.'
                                                       ));
                    $this->redirector('index');
                }

                $form->getElement('range')->setValue($leasingSchemaRange->startRange);

                // Get Rates for Range
                $leasingSchemaRatesMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();

                $leasingSchemaRate = $leasingSchemaRatesMapper->fetchAll(array(
                                                                              'leasingSchemaRangeId = ?' => $rangeId
                                                                         ));
                /**
                 * @var $rate Quotegen_Model_LeasingSchemaRate
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
        $form->setDecorators(array(
                                  array(
                                      'ViewScript',
                                      array(
                                          'viewScript'         => 'leasingschema/forms/leasingSchemaRange.phtml',
                                          'leasingSchemaTerms' => $leasingSchema->getTerms()
                                      )
                                  )
                             ));
        $this->view->leasingSchemaRange = $form;
    }

    public function deleterangeAction ()
    {
        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();

        $leasingSchemaId = $this->_getParam('leasingSchemaId', false);
        $rangeId         = $this->_getParam('id', false);

        if (!$rangeId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'warning' => 'Please select a range to delete first.'
                                               ));
            $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
        }
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema       = $leasingSchemaMapper->find($leasingSchemaId);
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'Insufficient Privilege: You cannot view this leasing schema.'
                                               ));
            $this->redirector('index');
        }

        $mapper = new Quotegen_Model_Mapper_LeasingSchemaRange();
        $range  = $mapper->find($rangeId);

        if (!$rangeId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'There was an error selecting the range to delete.'
                                               ));
            $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
        }

        // Make sure this isn't the last range for this schema
        $leasingSchemaRanges = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance()->fetchAll('leasingSchemaId = ' . $leasingSchemaId);
        if (count($leasingSchemaRanges) <= 1)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => "You cannot delete the range \${$range->startRange}  as it is the last range for this Leasing Schema."
                                               ));
            $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
        }
        else
        {
            $message = "Are you sure you want to delete the range \${$range->startRange}?";
        }
        $form = new Application_Form_Delete($message);

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
                        $this->_flashMessenger->addMessage(array(
                                                                'success' => "The range \${$this->view->escape($range->startRange)} was deleted successfully."
                                                           ));
                        $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
                    }
                }
                else // go back
                {
                    $db->rollBack();
                    $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
                }
            }
            catch (Exception $e)
            {
                $db->rollBack();
                $this->_flashMessenger->addMessage(array(
                                                        'danger' => 'There was an error selecting the term to delete.'
                                                   ));
                $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
            }
        }
        $this->view->form = $form;
    }

    public function importAction ()
    {
        $leasingSchemaId = $this->_getParam('leasingSchemaId', false);
        $leasingSchema   = null;
        // Get db adapter
        $db   = Zend_Db_Table::getDefaultAdapter();
        $form = new Dealermanagement_Form_ImportLeaseCsv(array('csv'), "1B", "8MB");

        // Postback
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ['cancel']))
            {
                $this->redirector("index");
            }
            $db->beginTransaction();
            try
            {
                // Prep mappers
                $leasingSchemaMapper      = Quotegen_Model_Mapper_LeasingSchema::getInstance();
                $leasingSchemaRateMapper  = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                $leasingSchemaRangeMapper = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance();
                $leasingSchemaTermMapper  = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance();

                // Prep models
                $leasingSchemaRateModel  = new Quotegen_Model_LeasingSchemaRate();
                $leasingSchemaRangeModel = new Quotegen_Model_LeasingSchemaRange();
                $leasingSchemaTermModel  = new Quotegen_Model_LeasingSchemaTerm();

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

                    if (!$leasingSchema instanceof Quotegen_Model_LeasingSchema)
                    {
                        $leasingSchema           = new Quotegen_Model_LeasingSchema();
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
                            foreach ($rates as $ratekey => $value)
                            {

                                // First column is the term
                                if ($ratekey == 0)
                                {
                                    // If its not a valid month, ERROR OUT!
                                    if (!is_numeric($value) || $value <= 0)
                                    {
                                        throw new Exception("Passing exception up the chain.", 0, null);
                                    }

                                    $months = $value;

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
                                        if (!is_numeric($value) || $value > 1)
                                        {
                                            throw new Exception("Passing exception up the chain.", 0, null);
                                        }
                                        // Get rate
                                        $rate = $value;

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
                    $this->_flashMessenger->addMessage(array(
                                                            'success' => "The leasing schema import was completed successfully."
                                                       ));
                    $this->redirector(array('action' => 'index'));
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
                        $this->_flashMessenger->addMessage(array(
                                                                'error' => "An error has occurred and the import was not completed. Please double check the format of your file and try again."
                                                           ));
                    }
                }
            }
            catch (Zend_Db_Statement_Mysqli_Exception $e)
            {
                $db->rollback();
                $this->_flashMessenger->addMessage(array(
                                                        'error' => "There was an error saving the leasing schema to the database."
                                                   ));
            }
            catch (Exception $e)
            {
                $db->rollback();
                $this->_flashMessenger->addMessage(array(
                                                        'error' => "An error has occurred and the updates were not saved."
                                                   ));
            }

        }
        $this->view->form = $form;
    }

    public function resetschemaAction ()
    {
        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();

        // Get default leasing schema id
        $leasingSchemaId     = $this->_getParam('leasingSchemaId', false);
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema       = $leasingSchemaMapper->find($leasingSchemaId);
        if ($leasingSchema && $leasingSchema->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'Insufficient Privilege: You cannot view this leasing schema.'
                                               ));
            $this->redirector('index');
        }
        // Reset the grid to default values
        $months = "12";
        $range  = "0";
        $rate   = "0.0750";

        // Prep mappers
        $leasingSchemaRateMapper  = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
        $leasingSchemaRangeMapper = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance();
        $leasingSchemaTermMapper  = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance();

        // Prep models
        $leasingSchemaRateModel  = new Quotegen_Model_LeasingSchemaRate();
        $leasingSchemaRangeModel = new Quotegen_Model_LeasingSchemaRange();
        $leasingSchemaTermModel  = new Quotegen_Model_LeasingSchemaTerm();

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
                        $this->_flashMessenger->addMessage(array(
                                                                'success' => "The leasing schema has been successfully reset."
                                                           ));
                    }
                    else
                    {
                        // Delete current failed
                        $db->rollBack();
                        $this->_flashMessenger->addMessage(array(
                                                                'error' => "An error occured while deleting the current schema."
                                                           ));
                    }
                }
                else
                {
                    // Cancel action
                    $db->rollBack();
                }
            }
            catch (Exception $e)
            {
                // Delete current failed
                $db->rollBack();
                $this->_flashMessenger->addMessage(array(
                                                        'error' => "An error occured while deleting the current schema."
                                                   ));
            }

            // Always redirect back to index
            $this->redirector('view', null, null, array("leasingSchemaId" => $leasingSchemaId));
        }
    }

    /**
     * @param $leasingSchemaId
     *
     * @return bool
     */
    public function emptySchema ($leasingSchemaId)
    {
        // Prep mappers
        $leasingSchemaRangeMapper = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance();
        $leasingSchemaTermMapper  = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance();

        try
        {
            // Delete existing leasing schema ranges
            $leasingSchemaRanges = $leasingSchemaRangeMapper->fetchAll(array(
                                                                            'leasingSchemaId = ?' => $leasingSchemaId
                                                                       ));
            foreach ($leasingSchemaRanges as $leasingSchemaRange)
            {
                $leasingSchemaRangeMapper->delete($leasingSchemaRange);
            }

            // Delete existing leasing schema terms
            $leasingSchemaTerms = $leasingSchemaTermMapper->fetchAll(array(
                                                                          'leasingSchemaId = ?' => $leasingSchemaId
                                                                     ));
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

