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
               
        // Get default leasing schema
        $leasingSchema = $leasingSchemaMapper->find(1);
        $this->view->leasingSchema = $leasingSchema;
    }

    public function edittermAction ()
    {
        $leasingSchemaId = 1;
        $termId = $this->_getParam('id', false);
        
        // Get leasing schema
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema = $leasingSchemaMapper->find($leasingSchemaId);
        
        if (! $leasingSchema)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Leasing schema does not exist.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Get form and pass ranges for this schema
        $leasingSchemaRanges = $leasingSchema->getRanges();
        
        if (! $leasingSchemaRanges)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'No ranges exist.' 
            ));
            $this->_helper->redirector('index');
        }

        $form = new Quotegen_Form_LeasingSchemaTerm($leasingSchemaRanges);
        
        // Postback
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            try
            {
	            // If we cancelled we don't need to validate anything
	            if (! isset($values ['cancel']))
	            {

	                if ($form->isValid($values))
	                {
	                    
	                    // Get post data
	                    $months = $values ['term'];
	                    
	                    // Save new term
	                    if ($termId)
	                    {
	                        try
	                        {
	                            // Save (Edit)
	                            $leasingSchemaTermMapper = new Quotegen_Model_Mapper_LeasingSchemaTerm();
	                            $leasingSchemaTerm = $leasingSchemaTermMapper->fetchAll(array (
	                                    "leasingSchemaId" => $leasingSchemaId,
			                            "months = ?" => $months
			                    ));
                                
	                            // Editing so count should be 1
	                            if ( $leasingSchemaTerm )
	                            {
	                                $leasingSchemaTermModel = new Quotegen_Model_LeasingSchemaTerm();
	                                $leasingSchemaTermModel->setId($termId);
	                                $leasingSchemaTermModel->setLeasingSchemaId($leasingSchemaId);
	                                $leasingSchemaTermModel->setMonths($months);
	                                $leasingSchemaTermMapper->save($leasingSchemaTermModel);
	                                
	                                $leasingSchemaRateMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
	                                $leasingSchemaRateModel = new Quotegen_Model_LeasingSchemaRate();
	                                
	                                // Save rates for range and term
	                                foreach ( $leasingSchemaRanges as $range )
	                                {
	                                    $rangeId = $range->getId();
	                                    $rate = $values ["rate{$rangeId}"];
	                                    
	                                    $leasingSchemaRateModel->setLeasingSchemaTermId($termId);
	                                    $leasingSchemaRateModel->setLeasingSchemaRangeId($rangeId);
	                                    $leasingSchemaRateModel->setRate($rate);
	                                    $leasingSchemaRateId = $leasingSchemaRateMapper->save($leasingSchemaRateModel);
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
	                            $leasingSchemaTermMapper = new Quotegen_Model_Mapper_LeasingSchemaTerm();
	                            $leasingSchemaTerm = $leasingSchemaTermMapper->fetchAll(array (
	                                    "leasingSchemaId" => $leasingSchemaId,
			                            "months = ?" => $months
			                    ));
                                
	                            if ( ! $leasingSchemaTerm )
	                            {
	                            	// Insert (Add)
		                            $leasingSchemaTermModel = new Quotegen_Model_LeasingSchemaTerm();
		                            $leasingSchemaTermModel->setLeasingSchemaId($leasingSchemaId);
		                            $leasingSchemaTermModel->setMonths($months);
		                            
	                                $termId = $leasingSchemaTermMapper->insert($leasingSchemaTermModel);

	                                $leasingSchemaRateMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
	                                $leasingSchemaRateModel = new Quotegen_Model_LeasingSchemaRate();
	                                
	                                // Save rates for range and term
	                                foreach ( $leasingSchemaRanges as $range )
	                                {
	                                    $rangeId = $range->getId();
	                                    $rate = $values ["rate{$rangeId}"];
	                                    
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
	            else 
	            {
	                // User has cancelled. We could do a redirect here if we wanted.
	                $this->_helper->redirector('index');
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
            if ($termId > 0)
            {
                // Get Term
                $leasingSchemaTermMapper = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance();
                $leasingSchemaTerm = $leasingSchemaTermMapper->find($termId);
                
		        if (! $leasingSchemaTerm)
		        {
		            $this->_helper->flashMessenger(array (
		                    'warning' => 'The leasing schema term does not exist.' 
		            ));
		            $this->_helper->redirector('index');
		        }
                
                $form->getElement('term')->setValue($leasingSchemaTerm->getMonths());
                
                // Get Rates for Term
                $leasingSchemaRatesMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                
                // FIXME: Make a function to fetch rates for a term, and have the term model use it to return them. Also see note about SQL injection!
                $leasingSchemaRate = $leasingSchemaRatesMapper->fetchAll('leasingSchemaTermId = ' . $termId);
                
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
                    $months = $term->getMonths();
                    
                    $mapper->delete($term);
                    $this->_helper->flashMessenger(array (
                            'success' => "The Term {$months} months was deleted successfully." 
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

