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
        if ( $request->isPost() )
        {
            $values = $request->getPost();
            try
            {
	            if ( $form->isValid( $values ) )
	            {
					if ($id) {
					    // Edit
					    echo "Edit<br />";
					} else {
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
	        if ( $id > 0 )
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
	        	    
	        	    if ( $form->getElement("rate{$rangeid}") ) 
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
        $id = $this->_getParam('id', false);
        
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema = $leasingSchemaMapper->find(1);

        $form = new Quotegen_Form_LeasingSchemaRange($leasingSchema->getTerms());
        
        $form->getElement('hdnId')->setValue($id);
        $form->getElement('cancel')->setAttrib('onclick', 'javascript: document.location.href="/quotegen/leasingschema";');
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            try
            {
                if ($form->isValid($values))
                {
                    if ($id) {
                        // Edit
                        echo "Edit<br />";
                    } else {
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

        // add form to page
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

