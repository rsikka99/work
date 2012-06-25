<?php

class Quotegen_LeasingschemaController extends Zend_Controller_Action
{

    public function init ()
    {
        // FIXME: Delete unused functions
    }

    public function indexAction ()
    {
        // Display all of the leasing schema rates in a grid
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        
        // FIXME: Delete unused variables?
        $leasingSchemaRangeMapper = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance();
        $leasingSchemaTermMapper = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance();
        $leasingSchemaRateMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
        
        // Get default leasing schema
        $leasingSchema = $leasingSchemaMapper->find(1);
        $this->view->leasingSchema = $leasingSchema;
        
        // FIXME: Delete debug data
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            print_r($values);
        }
    }

    public function edittermAction ()
    {
        $leasingSchemaId = 1;
        $id = $this->_getParam('id', false);
        
        // Get leasing schema
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema = $leasingSchemaMapper->find($leasingSchemaId);
        
        // FIXME: What happens if the leasing schema id is invalid?
        

        // Get form and pass ranges for this schema
        $leasingSchemaRanges = $leasingSchema->getRanges();
        
        // FIXME: What happens if there are no ranges?
        

        $form = new Quotegen_Form_LeasingSchemaTerm($leasingSchemaRanges);
        
        // Set default values and attributes
        // FIXME: Why set a form value? You already have it coming back via the parameter. 
        // Also for naming conventions, it should be named according to the field it is related to. If it is id, then it should be named id.
        

        $form->getElement('hdnId')->setValue($id);
        
        // FIXME: This will break when the module moves. This should be handled via a submit button that posts back, and then use redirector if it was a cancel action
        $form->getElement('cancel')->setAttrib('onclick', 'javascript: document.location.href="/quotegen/leasingschema";');
        
        // Postback
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            try
            {
                // FIXME: For the previou comment, you can check if $values['cancel'] is set and then send the user somewhere if it is.
                

                if ($form->isValid($values))
                {
                    // Get post data
                    $termId = $values ['hdnId'];
                    $months = $values ['term'];
                    
                    // FIXME: Did you move the logic into the mapper?
                    // TODO: Move Logic Below to Mapper
                    

                    // Save new term
                    if ($termId)
                    {
                        try
                        {
                            // Save (Edit)
                            $leasingSchemaTermMapper = new Quotegen_Model_Mapper_LeasingSchemaTerm();
                            
                            // FIXME: The fetch could be directly in the if statement if you're not using the return value. Otherwise the variable name is a bit confusing once used later on.
                            // FIXME: Also, this should be in the mapper, the function could verify if a term exists already. Don't forget to escape user data. Right now we could inject sql! 
                            // (Note: Escaping is by using the months = ? syntax within the mapper)
                            // Validate term doesn't exist
                            $exists = $leasingSchemaTermMapper->fetch('months = ' . $months);
                            
                            if (! $exists)
                            {
                                $leasingSchemaTermModel = new Quotegen_Model_LeasingSchemaTerm();
                                $leasingSchemaTermModel->setId($termId);
                                $leasingSchemaTermModel->setLeasingSchemaId($leasingSchemaId);
                                $leasingSchemaTermModel->setMonths($months);
                                
                                // FIXME: Is the id going to change? If not, don't include the primary key parameter
                                $leasingSchemaTermMapper->save($leasingSchemaTermModel, $termId);
                                
                                // Save rates for range and term
                                foreach ( $leasingSchemaRanges as $range )
                                {
                                    $rangeId = $range->getId();
                                    $rate = $values ["rate{$rangeId}"];
                                    
                                    // FIXME: Use mapper::getInstance() instead of instantiating a new one. Also, do this outside of the loop as we don't need to fetch an instance N times, just once.
                                    $leasingSchemaRateMapper = new Quotegen_Model_Mapper_LeasingSchemaRate();
                                    
                                    // FIXME: TIP: Create the model outside the loop and set all data that doesn't change each loop. Then change the data that does change and save. (This can really save on memory)
                                    $leasingSchemaRateModel = new Quotegen_Model_LeasingSchemaRate();
                                    $leasingSchemaRateModel->setLeasingSchemaTermId($termId);
                                    $leasingSchemaRateModel->setLeasingSchemaRangeId($rangeId);
                                    $leasingSchemaRateModel->setRate($rate);
                                    
                                    // FIXME: Is this updating? Will it change the primary key of a row? If yes, then this is correct, otherwise you don't need to pass the primary key in.
                                    $leasingSchemaRateId = $leasingSchemaRateMapper->save($leasingSchemaRateModel, array (
                                            $termId, 
                                            $rangeId 
                                    ));
                                }
                                
                                $this->_helper->flashMessenger(array (
                                        'success' => "The term was updated successfully." 
                                ));
                            }
                            else
                            {
                                $this->_helper->flashMessenger(array (
                                        'danger' => "The term {$months} months already exists." 
                                ));
                            }
                        }
                        catch ( Exception $e )
                        {
                            // Save Error
                            $this->_helper->flashMessenger(array (
                                    'danger' => 'There was an error processing the update.  Please try again.' 
                            ));
                        }
                    }
                    else
                    {
                        try
                        {
                            // Insert (Add)
                            $leasingSchemaTermMapper = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance();
                            $leasingSchemaTermModel = new Quotegen_Model_LeasingSchemaTerm();
                            $leasingSchemaTermModel->setLeasingSchemaId($leasingSchemaId);
                            $leasingSchemaTermModel->setMonths($months);
                            
                            // FIXME: See note about the same statement above!
                            // Validate term doesn't exist
                            $exists = $leasingSchemaTermMapper->fetch('months = ' . $months);
                            
                            if (! $exists)
                            {
                                $termId = $leasingSchemaTermMapper->insert($leasingSchemaTermModel);
                                
                                // Save rates for range and term
                                foreach ( $leasingSchemaRanges as $range )
                                {
                                    $rangeId = $range->getId();
                                    $rate = $values ["rate{$rangeId}"];
                                    
                                    // FIXME: Mapper should not be in the loop since we only need to declare it once.
                                    $leasingSchemaRateMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                                    // FIXME: See note about creating the model outside the loop!
                                    $leasingSchemaRateModel = new Quotegen_Model_LeasingSchemaRate();
                                    $leasingSchemaRateModel->setLeasingSchemaRangeId($rangeId);
                                    $leasingSchemaRateModel->setLeasingSchemaTermId($termId);
                                    $leasingSchemaRateModel->setRate($rate);
                                    $leasingSchemaRateMapper->insert($leasingSchemaRateModel);
                                }
                                
                                $this->_helper->flashMessenger(array (
                                        'success' => "The term {$months} months was added successfully." 
                                ));
                            }
                            else
                            {
                                $this->_helper->flashMessenger(array (
                                        'danger' => "The term {$months} months already exists." 
                                ));
                            }
                        }
                        catch ( Exception $e )
                        {
                            // Insert Error
                            $this->_helper->flashMessenger(array (
                                    'danger' => 'There was an error processing the insert.  Please try again.' 
                            ));
                        }
                    }
                }
                else
                {
                    $this->_helper->flashMessenger(array (
                            'error' => "Please review and complete all required fields." 
                    ));
                }
            }
            catch ( Zend_Validate_Exception $e )
            {
                $form->buildBootstrapErrorDecorators();
            }
        }
        else
        {
            // Populate form for Editing
            if ($id > 0)
            {
                // Get Term
                $leasingSchemaTermMapper = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance();
                $leasingSchemaTerm = $leasingSchemaTermMapper->find($id);
                // FIXME: What happens here if the leasing term doesn't exist?
                
                
                // FIXME: Naming convention, field names should be the same as the database names/toArray names. This way you can use $form->populate($model->toArray());
                $form->getElement('term')->setValue($leasingSchemaTerm->getMonths());
                
                // Get Rates for Term
                $leasingSchemaRatesMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                
                // FIXME: Make a function to fetch rates for a term, and have the term model use it to return them. Also see note about SQL injection!
                $leasingSchemaRate = $leasingSchemaRatesMapper->fetchAll('leasingSchemaTermId = ' . $id);
                
                // FIXME: For populating the form, you could pass it in the term id and let it use the mapper to fetch the term model. I can help explain this further if needed.
                foreach ( $leasingSchemaRate as $rate )
                {
                    $rangeid = $rate->getLeasingSchemaRangeId();
                    $amount = $rate->getRate();
                    
                    if ($form->getElement("rate{$rangeid}"))
                    {
                        $form->getElement("rate{$rangeid}")->setValue($amount);
                    }
                }
            }
        }
        
        // FIXME: Do we need the ranges here? Also this should be set within the form, and the viewscript should be under leasingschema/forms/...phtml to keep things a bit cleaner
        // Add form to page
        $form->setDecorators(array (
                array (
                        'ViewScript', 
                        array (
                                'viewScript' => 'forms/leasingSchemaTerm.phtml', 
                                'leasingSchemaRanges' => $leasingSchema->getRanges() 
                        ) 
                ) 
        ));
        $this->view->leasingSchemaTerm = $form;
    }

    public function deletetermAction ()
    {
        $leasingSchemaId = 1;
        $termId = $this->_getParam('id', false);
        
        if (! $termId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a term to delete first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // FIXME: My bad if I put this example there, but otherwise you can use the delete function as it returns how many rows are returned.
        $mapper = new Quotegen_Model_Mapper_LeasingSchemaTerm();
        $term = $mapper->find($termId);
        
        if (! $termId)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the term to delete.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Make sure this isn't the last term for this schema
        $valid = true;
        
        $leasingSchemaTerms = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance()->fetchAll('leasingSchemaId = ' . $leasingSchemaId);
        if (count($leasingSchemaTerms) <= 1)
        {
            $valid = false;
            $message = "You cannot delete term {$term->getMonths()} months as it is the last term for this Leasing Schema.";
        }
        else
        {
            $message = "Are you sure you want to delete term {$term->getMonths()} months?";
        }
        $form = new Application_Form_Delete($message);
        
        if (! $valid)
        {
            // Setting Style to None instead of removing the element as removing messed up the forms layout
            $form->getElement('submit')->setAttrib('style', 'display: none;');
        }
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                // delete client from database
                if ($form->isValid($values))
                {
                    $mapper->delete($term);
                    $this->_helper->flashMessenger(array (
                            'success' => "The Term {$this->view->escape ( $term->getMonths() )} months was deleted successfully." 
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

    public function editrangeAction ()
    {
        $leasingSchemaId = 1;
        $id = $this->_getParam('id', false);
        
        // Get leasing schema
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema = $leasingSchemaMapper->find($leasingSchemaId);
        
        // Get form and pass terms for this schema
        $leasingSchemaTerms = $leasingSchema->getTerms();
        $form = new Quotegen_Form_LeasingSchemaRange($leasingSchemaTerms);
        
        // Set default values and attributes
        $form->getElement('hdnId')->setValue($id);
        $form->getElement('cancel')->setAttrib('onclick', 'javascript: document.location.href="/quotegen/leasingschema";');
        
        // Postback
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            try
            {
                if ($form->isValid($values))
                {
                    // Get post data
                    $rangeId = $values ['hdnId'];
                    $startRange = $values ['range'];
                    
                    // TODO: Move Logic Below to Mapper
                    // Save new range
                    if ($rangeId)
                    {
                        try
                        {
                            // Save (Edit)
                            $leasingSchemaRangeMapper = new Quotegen_Model_Mapper_LeasingSchemaRange();
                            
                            // Validate Range doesn't exist
                            $exists = $leasingSchemaRangeMapper->fetch('startRange = ' . $startRange);
                            
                            if (! $exists)
                            {
                                $leasingSchemaRangeModel = new Quotegen_Model_LeasingSchemaRange();
                                $leasingSchemaRangeModel->setId($rangeId);
                                $leasingSchemaRangeModel->setLeasingSchemaId($leasingSchemaId);
                                $leasingSchemaRangeModel->setStartRange($startRange);
                                $leasingSchemaRangeMapper->save($leasingSchemaRangeModel, $rangeId);
                                
                                // Save rates for range and term
                                foreach ( $leasingSchemaTerms as $term )
                                {
                                    $termId = $term->getId();
                                    $rate = $values ["rate{$termId}"];
                                    
                                    $leasingSchemaRateMapper = new Quotegen_Model_Mapper_LeasingSchemaRate();
                                    $leasingSchemaRateModel = new Quotegen_Model_LeasingSchemaRate();
                                    $leasingSchemaRateModel->setLeasingSchemaTermId($termId);
                                    $leasingSchemaRateModel->setLeasingSchemaRangeId($rangeId);
                                    $leasingSchemaRateModel->setRate($rate);
                                    $leasingSchemaRateId = $leasingSchemaRateMapper->save($leasingSchemaRateModel, array (
                                            $termId, 
                                            $rangeId 
                                    ));
                                }
                                
                                $this->_helper->flashMessenger(array (
                                        'success' => "The range was updated successfully." 
                                ));
                            }
                            else
                            {
                                $this->_helper->flashMessenger(array (
                                        'danger' => "The range \${$startRange} already exists." 
                                ));
                            }
                        }
                        catch ( Exception $e )
                        {
                            // Save Error
                            $this->_helper->flashMessenger(array (
                                    'danger' => 'There was an error processing the update.  Please try again.' 
                            ));
                        }
                    }
                    else
                    {
                        try
                        {
                            // Insert (Add)
                            $leasingSchemaRangeMapper = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance();
                            $leasingSchemaRangeModel = new Quotegen_Model_LeasingSchemaRange();
                            $leasingSchemaRangeModel->setLeasingSchemaId($leasingSchemaId);
                            $leasingSchemaRangeModel->setStartRange($startRange);
                            
                            // Validate Range doesn't exist
                            $exists = $leasingSchemaRangeMapper->fetch('startRange = ' . $startRange);
                            
                            if (! $exists)
                            {
                                $rangeId = $leasingSchemaRangeMapper->insert($leasingSchemaRangeModel);
                                
                                // Save rates for range and term
                                foreach ( $leasingSchemaTerms as $term )
                                {
                                    $termId = $term->getId();
                                    $rate = $values ["rate{$termId}"];
                                    
                                    $leasingSchemaRateMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                                    $leasingSchemaRateModel = new Quotegen_Model_LeasingSchemaRate();
                                    $leasingSchemaRateModel->setLeasingSchemaRangeId($rangeId);
                                    $leasingSchemaRateModel->setLeasingSchemaTermId($termId);
                                    $leasingSchemaRateModel->setRate($rate);
                                    $leasingSchemaRateMapper->insert($leasingSchemaRateModel);
                                }
                                
                                $this->_helper->flashMessenger(array (
                                        'success' => "The range \${$startRange} was added successfully." 
                                ));
                            }
                            else
                            {
                                $this->_helper->flashMessenger(array (
                                        'danger' => "The range \${$startRange} already exists." 
                                ));
                            }
                        }
                        catch ( Exception $e )
                        {
                            // Insert Error
                            $this->_helper->flashMessenger(array (
                                    'danger' => 'There was an error processing the insert.  Please try again.' 
                            ));
                        }
                    }
                }
                else
                {
                    $this->_helper->flashMessenger(array (
                            'error' => "Please review and complete all required fields." 
                    ));
                }
            }
            catch ( Zend_Validate_Exception $e )
            {
                $form->buildBootstrapErrorDecorators();
            }
        }
        else
        {
            // Populate form for Editing
            if ($id > 0)
            {
                // Get Range
                $leasingSchemaRangeMapper = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance();
                $leasingSchemaRange = $leasingSchemaRangeMapper->find($id);
                $form->getElement('range')->setValue($leasingSchemaRange->getStartRange());
                
                // Get Rates for Range
                $leasingSchemaRatesMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                $leasingSchemaRate = $leasingSchemaRatesMapper->fetchAll('leasingSchemaRangeId = ' . $id);
                foreach ( $leasingSchemaRate as $rate )
                {
                    $termid = $rate->getLeasingSchemaTermId();
                    $amount = $rate->getRate();
                    
                    if ($form->getElement("rate{$termid}"))
                    {
                        $form->getElement("rate{$termid}")->setValue($amount);
                    }
                }
            }
        }
        
        // Add form to page
        $form->setDecorators(array (
                array (
                        'ViewScript', 
                        array (
                                'viewScript' => 'forms/leasingSchemaRange.phtml', 
                                'leasingSchemaTerms' => $leasingSchema->getTerms() 
                        ) 
                ) 
        ));
        $this->view->leasingSchemaRange = $form;
    }

    public function deleterangeAction ()
    {
        $leasingSchemaId = 1;
        $rangeId = $this->_getParam('id', false);
        
        if (! $rangeId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a range to delete first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $mapper = new Quotegen_Model_Mapper_LeasingSchemaRange();
        $range = $mapper->find($rangeId);
        
        if (! $rangeId)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the range to delete.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Make sure this isn't the last range for this schema
        $valid = true;
        
        $leasingSchemaRanges = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance()->fetchAll('leasingSchemaId = ' . $leasingSchemaId);
        if (count($leasingSchemaRanges) <= 1)
        {
            $valid = false;
            $message = "You cannot delete the range \${$range->getStartRange()}  as it is the last range for this Leasing Schema.";
        }
        else
        {
            $message = "Are you sure you want to delete the range \${$range->getStartRange()}?";
        }
        $form = new Application_Form_Delete($message);
        
        if (! $valid)
        {
            // Setting Style to None instead of removing the element as removing messed up the forms layout
            $form->getElement('submit')->setAttrib('style', 'display: none;');
        }
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                // delete client from database
                if ($form->isValid($values))
                {
                    $mapper->delete($range);
                    $this->_helper->flashMessenger(array (
                            'success' => "The Range \${$this->view->escape ( $range->getStartRange() )} was deleted successfully." 
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
}

