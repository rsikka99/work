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
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema = $leasingSchemaMapper->find(1);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            $id = $values ['hdnId'];
            $mode = $values ['hdnMode'];
            
			$form = new Quotegen_Form_LeasingSchemaTerm($leasingSchema->getRanges());
			$form->getElement('cancel')->setAttrib('onclick', 'javascript: document.location.href="/quotegen/leasingschema";');
			
            // Do mode specific actions
            switch ($mode) {
            	case "add term":
            	    break;
            	case "edit term":
            	    break;
            }

            // add form to page
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
    }
    
    public function editrangeAction ()
    {
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema = $leasingSchemaMapper->find(1);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            $id = $values ['hdnId'];
            $mode = $values ['hdnMode'];
            
			$form = new Quotegen_Form_LeasingSchemaRange($leasingSchema->getTerms());
			$form->getElement('cancel')->setAttrib('onclick', 'javascript: document.location.href="/quotegen/leasingschema";');
            
            // Do mode specific actions
            switch ($mode) {
            	case "add range":
            	    break;
            	case "edit range":
            	    break;
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
    }
    
    public function deletetermAction ()
    {
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            $id = $values ['hdnId'];
            $mode = $values ['hdnMode'];
            
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
            $mode = $values ['hdnMode'];
            
            // Validate id
            
            // Confirm delete
            
            print_r($values);
        }
    }

}

