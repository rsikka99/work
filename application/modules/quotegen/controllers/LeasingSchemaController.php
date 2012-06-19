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
        $mode = $this->_getParam('mode', false);
        
        // Get leasing schema
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema = $leasingSchemaMapper->find(1);

        // If id > 0 then find term to edit
        $leasingSchemaTermMapper = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance();
        $leasingSchemaTerm = $leasingSchemaTermMapper->find($id);
        
        // Get form and pass ranges for this schema
        $form = new Quotegen_Form_LeasingSchemaTerm($leasingSchema->getRanges());
        
        // Set default values and attributes
        $form->getElement('hdnId')->setValue($id);
        $form->getElement('hdnMode')->setValue($mode);
        $form->getElement('cancel')->setAttrib('onclick', 'javascript: document.location.href="/quotegen/leasingschema";');
        
        // Postback
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            print_r($values);

            try
            {
	            if ($form->isValid($values))
	            {
					// Do mode specific actions
					switch ($mode) {
						case "add term":
						    break;
						case "edit term":
						    break;
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
        $mode = $this->_getParam('mode', false);
        
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema = $leasingSchemaMapper->find(1);

        $form = new Quotegen_Form_LeasingSchemaRange($leasingSchema->getTerms());
        
        $form->getElement('hdnId')->setValue($id);
        $form->getElement('hdnMode')->setValue($mode);
        $form->getElement('cancel')->setAttrib('onclick', 'javascript: document.location.href="/quotegen/leasingschema";');
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            print_r($values);
            
            if ($form->isValid($values))
            {
				// Do mode specific actions
				switch ($mode) {
					case "add range":
					    break;
					case "edit range":
					    break;
				}
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

