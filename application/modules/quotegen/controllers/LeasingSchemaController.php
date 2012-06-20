<?php

class Quotegen_LeasingSchemaController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    public function indexAction ()
    {
        // Display all of the leasing schema rates in a grid
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchemaRangeMapper = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance();
        $leasingSchemaTermMapper = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance();
        $leasingSchemaRateMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
        
        // Get default leasing schema
        $leasingSchema = $leasingSchemaMapper->find(1);
        $this->view->leasingSchema = $leasingSchema;
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            print_r($values);
        }
    }

    public function edittermAction ()
    {
        $id = $this->_getParam('id', false);
        
        // Get leasing schema
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema = $leasingSchemaMapper->find(1);
        
        // Get form and pass ranges for this schema
        $form = new Quotegen_Form_LeasingSchemaTerm($leasingSchema->getRanges());
        
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
                    if ($id)
                    {
                        // Edit
                        echo "Edit<br />";
                    }
                    else
                    {
                        // Add
                        echo "Add<br />";
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
            // Populate form for Editing
            if ($id > 0)
            {
                // Get Term
                $leasingSchemaTermMapper = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance();
                $leasingSchemaTerm = $leasingSchemaTermMapper->find($id);
                $form->getElement('term')->setValue($leasingSchemaTerm->getMonths());
                
                // Get Rates for Term
                $leasingSchemaRatesMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                $leasingSchemaRate = $leasingSchemaRatesMapper->fetchAll('leasingSchemaTermId = ' . $id);
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
                                $leasingSchemaRateId = $leasingSchemaRateMapper->save($leasingSchemaRateModel, array ($termId, $rangeId));
                            }
                            
                            $this->_helper->flashMessenger(array (
                                    'success' => "The range was updated successfully."
                            ));
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
                            
                            if ( ! $exists )
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

    public function deletetermAction ()
    {
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            $id = $values ['hdnId'];
            
            // Validate id
        

            // Confirm delete
            

            print_r($values);
        }
    }

    public function deleterangeAction ()
    {
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            $id = $values ['hdnId'];
            
            // Validate id
        

            // Confirm delete
            

            print_r($values);
        }
    }

}

