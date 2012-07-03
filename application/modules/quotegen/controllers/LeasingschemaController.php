<?php

class Quotegen_LeasingschemaController extends Zend_Controller_Action
{

    public function indexAction ()
    {
        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // Get default leasing schema id
        $leasingSchemaId = 1;
        
        // Postback
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            $db->beginTransaction();
            try
            {
                $upload = new Zend_File_Transfer_Adapter_Http();
                $upload->setDestination(Zend_Registry::get('config')->app->uploadPath);
                
                // Limit the extensions to csv files
                $upload->addValidator('Extension', false, 'csv');
                $upload->getValidator('Extension')->setMessage('<span class="warning">*</span> File "' . basename($_FILES ['uploadedfile'] ['name']) . '" has an <em>invalid</em> extension. A <span style="color: red;">.csv</span> is required.');
                
                // Limit the amount of files to maximum 1
                $upload->addValidator('Count', false, 1);
                $upload->getValidator('Count')->setMessage('<span class="warning">*</span> You are only allowed to upload 1 file at a time.');
                
                // Limit the size of all files to be uploaded to maximum 4MB and mimimum 1B
                $upload->addValidator('FilesSize', false, array (
                        'min' => '1B', 
                        'max' => '4MB' 
                ));
                $upload->getValidator('FilesSize')->setMessage('<span class="warning">*</span> File size must be between 1B and 4MB.');
                
                if ($upload->receive())
                {
                    $rangeIds = null;
                    
                    // Get all the lines in the file
                    $lines = file($upload->getFileName(), FILE_IGNORE_NEW_LINES);
                    
                    // Prep mappers
                    $leasingSchemaRateMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                    $leasingSchemaRangeMapper = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance();
                    $leasingSchemaTermMapper = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance();
                    
                    // Prep models
                    $leasingSchemaRateModel = new Quotegen_Model_LeasingSchemaRate();
                    $leasingSchemaRangeModel = new Quotegen_Model_LeasingSchemaRange();
                    $leasingSchemaTermModel = new Quotegen_Model_LeasingSchemaTerm();
                    
                    // Delete existing leasing schema ranges
                    $leasingSchemaRanges = $leasingSchemaRangeMapper->fetchAll(array (
                            'leasingSchemaId' => $leasingSchemaId 
                    ));
                    foreach ( $leasingSchemaRanges as $leasingSchemaRange )
                    {
                        $leasingSchemaRangeMapper->delete($leasingSchemaRange);
                    }
                    
                    // Delete existing leasing schema terms
                    $leasingSchemaTerms = $leasingSchemaTermMapper->fetchAll(array (
                            'leasingSchemaId' => $leasingSchemaId 
                    ));
                    foreach ( $leasingSchemaTerms as $leasingSchemaTerm )
                    {
                        $leasingSchemaTermMapper->delete($leasingSchemaTerm);
                    }
                    
                    // Loop through remaining lines and save terms/rates
                    foreach ( $lines as $key => $value )
                    {
                        if ($key == 0)
                        {
                            // Split value into an array
                            $ranges = explode(",", $value);
                            
                            // Loop through array and save ranges
                            foreach ( $ranges as $rangekey => $range )
                            {
                                if ($rangekey > 0)
                                {
                                    // Make sure range doesn't exist
                                    $rangeExists = $leasingSchemaRangeMapper->fetch(array (
                                            'leasingSchemaId = ?' => $leasingSchemaId, 
                                            'startRange = ?' => $range 
                                    ));
                                    
                                    if (! $rangeExists)
                                    {
                                        // Build array of range id's
                                        $leasingSchemaRangeModel->setLeasingSchemaId($leasingSchemaId);
                                        $leasingSchemaRangeModel->setStartRange($range);
                                        $rangeIds [] = $leasingSchemaRangeMapper->insert($leasingSchemaRangeModel);
                                    }
                                    else
                                    {
                                        // Cancel import
                                        $db->rollback();
                                        $this->_helper->flashMessenger(array (
                                                'error' => "Range of \${$range} has been defined more than once in the file. Please correct it and try again." 
                                        ));
                                        $this->_helper->redirector('index');
                                    }
                                }
                            }
                        }
                        else
                        {
                            $rates = explode(",", $value);
                            foreach ( $rates as $ratekey => $value )
                            {
                                // First column is the term
                                if ($ratekey == 0)
                                {
                                    $months = $value;
                                    
                                    // Make sure range doesn't exist
                                    $termExists = $leasingSchemaTermMapper->fetch(array (
                                            'leasingSchemaId = ?' => $leasingSchemaId, 
                                            'months = ?' => $months 
                                    ));
                                    
                                    if (! $termExists)
                                    {
                                        // Insert term
                                        $leasingSchemaTermModel->setLeasingSchemaId($leasingSchemaId);
                                        $leasingSchemaTermModel->setMonths($months);
                                        $termId = $leasingSchemaTermMapper->insert($leasingSchemaTermModel);
                                    }
                                    else
                                    {
                                        // Cancel import
                                        $db->rollBack();
                                        $this->_helper->flashMessenger(array (
                                                'error' => "The term {$months} months has been defined more than once in the file. Please correct it and try again." 
                                        ));
                                        $this->_helper->redirector('index');
                                    }
                                }
                                else
                                {
                                    // Get Range Id for this column
                                    $rangeId = $rangeIds [$ratekey - 1];
                                    
                                    // Loop through remaining columns and save rates
                                    if ($termId > 0 && $rangeId > 0)
                                    {
                                        // Get rate
                                        $rate = $value;
                                        
                                        // Save Rate
                                        $leasingSchemaRateModel->setLeasingSchemaTermId($termId);
                                        $leasingSchemaRateModel->setLeasingSchemaRangeId($rangeId);
                                        $leasingSchemaRateModel->setRate($rate);
                                        $leasingSchemaRateId = $leasingSchemaRateMapper->insert($leasingSchemaRateModel);
                                    }
                                }
                            }
                        }
                    }
                    
                    // Delete the file we just uploaded
                    unlink($upload->getFileName());
                    
                    // Commit changes to the database
                    $db->commit();
                    
                    // Send success message to the screen
                    $this->_helper->flashMessenger(array (
                            'success' => "The leasing schema import was successful." 
                    ));
                }
                else
                {
                    $db->rollback();
                    if ($upload->getMessages())
                    {
                        // if upload fails, print error message message
                        $this->view->errMessages = $upload->getMessages();
                    }
                    else
                    {
                        // Display errors to screen
                        $this->_helper->flashMessenger(array (
                                'error' => "An error has occurred and the import was not completed. Please double check the format of your file and try again." 
                        ));
                    }
                }
            }
            catch ( Zend_Db_Statement_Mysqli_Exception $e )
            {
                $db->rollback();
                echo $e;
                $this->_helper->flashMessenger(array (
                        'error' => "There was an error saving the leasing schema to the database." 
                ));
            }
            catch ( Exception $e )
            {
                $db->rollback();
                $this->_helper->flashMessenger(array (
                        'error' => "An error has occurred and the import was not completed. Please double check the format of your file and try again." 
                ));
            }
        }
        
        // Display all of the leasing schema rates in a grid
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema = $leasingSchemaMapper->find($leasingSchemaId);
        $this->view->leasingSchema = $leasingSchema;
    }

    public function edittermAction ()
    {
        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $leasingSchemaId = 1;
        $termId = $this->_getParam('id', false);
        
        // Get leasing schema
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema = $leasingSchemaMapper->find($leasingSchemaId);
        
        if (! $leasingSchema)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'The leasing schema does not exist.' 
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
            $db->beginTransaction();
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
                                
                                // Edit
                                if ($leasingSchemaTerm)
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
                                    $db->commit();
                                    
                                    $this->_helper->flashMessenger(array (
                                            'success' => "The term was updated successfully." 
                                    ));
                                }
                                else
                                {
                                    $db->rollBack();
                                    $this->_helper->flashMessenger(array (
                                            'danger' => "The term {$months} months already exists." 
                                    ));
                                }
                            }
                            catch ( Exception $e )
                            {
                                // Save Error
                                $db->rollBack();
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
                                        'id != ?' => $termId, 
                                        "leasingSchemaId" => $leasingSchemaId, 
                                        "months = ?" => $months 
                                ));
                                
                                if (! $leasingSchemaTerm)
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
                                    $db->commit();
                                    
                                    $this->_helper->flashMessenger(array (
                                            'success' => "The term {$months} months was added successfully." 
                                    ));
                                }
                                else
                                {
                                    $db->rollBack();
                                    $this->_helper->flashMessenger(array (
                                            'danger' => "The term {$months} months already exists." 
                                    ));
                                }
                            }
                            catch ( Exception $e )
                            {
                                // Insert Error
                                $db->rollBack();
                                $this->_helper->flashMessenger(array (
                                        'danger' => 'There was an error processing the insert.  Please try again.' 
                                ));
                            }
                        }
                    }
                    else
                    {
                        $db->rollBack();
                        $this->_helper->flashMessenger(array (
                                'error' => "Please review and complete all required fields." 
                        ));
                    }
                }
                else
                {
                    // User has cancelled. We could do a redirect here if we wanted.
                    $db->rollBack();
                    $this->_helper->redirector('index');
                }
            }
            catch ( Zend_Validate_Exception $e )
            {
                $db->rollBack();
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
                
                // FIXME: Make a function to fetch rates for a term, and have the term model use it to return them.
                $leasingSchemaRate = $leasingSchemaRatesMapper->fetchAll(array (
                        'leasingSchemaTermId = ?' => $termId 
                ));
                
                // FIXME: For populating the form, you could pass it in the term id and let it use the mapper to fetch the term model.
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
                                'viewScript' => 'leasingschema/forms/leasingSchemaTerm.phtml', 
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
        
        $leasingSchemaId = 1;
        $termId = $this->_getParam('id', false);
        
        if (! $termId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a term to delete first.' 
            ));
            $this->_helper->redirector('index');
        }
        
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
        $leasingSchemaTerms = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance()->fetchAll('leasingSchemaId = ' . $leasingSchemaId);
        if (count($leasingSchemaTerms) <= 1)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => "You cannot delete term {$term->getMonths()} months as it is the last term for this leasing schema." 
            ));
            $this->_helper->redirector('index');
        }
        else
        {
            $message = "Are you sure you want to delete term {$term->getMonths()} months?";
        }
        $form = new Application_Form_Delete($message);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $db->beginTransaction();
            try 
            {
	            $values = $request->getPost();
	            if (! isset($values ['cancel']))
	            {
	                // delete client from database
	                if ($form->isValid($values))
	                {
	                    $months = $term->getMonths();
	                    $mapper->delete($term);
	                    $db->commit();
	                    $this->_helper->flashMessenger(array (
	                            'success' => "The term {$months} months was deleted successfully." 
	                    ));
	                    $this->_helper->redirector('index');
	                }
	            }
	            else // go back
	            {
	                $db->rollBack();
	                $this->_helper->redirector('index');
	            }
            }
            catch (Exception $e)
            {
                $db->rollBack();
	            $this->_helper->flashMessenger(array (
	                    'danger' => 'There was an error selecting the term to delete.' 
	            ));
	            $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    public function editrangeAction ()
    {
        // Get db adapter
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $leasingSchemaId = 1;
        $rangeId = $this->_getParam('id', false);
        
        // Get leasing schema
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchema = $leasingSchemaMapper->find($leasingSchemaId);
        
        if (! $leasingSchema)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'The leasing schema does not exist.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Get form and pass terms for this schema
        $leasingSchemaTerms = $leasingSchema->getTerms();
        
        // FIXME: Terms can be access from the form itself, although this is the lowest priority
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
                if (! isset($values ['cancel']))
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
                                $leasingSchemaRange = $leasingSchemaRangeMapper->fetchAll(array (
                                        'id != ?' => $rangeId, 
                                        'leasingSchemaId = ?' => $leasingSchemaId, 
                                        'startRange = ?' => $startRange 
                                ));
                                
                                if (! $leasingSchemaRange)
                                {
                                    $leasingSchemaRangeModel = new Quotegen_Model_LeasingSchemaRange();
                                    $leasingSchemaRangeModel->setId($rangeId);
                                    $leasingSchemaRangeModel->setLeasingSchemaId($leasingSchemaId);
                                    $leasingSchemaRangeModel->setStartRange($startRange);
                                    $leasingSchemaRangeMapper->save($leasingSchemaRangeModel);
                                    
                                    $leasingSchemaRateMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                                    $leasingSchemaRateModel = new Quotegen_Model_LeasingSchemaRate();
                                    
                                    // Save rates for range and term
                                    foreach ( $leasingSchemaTerms as $term )
                                    {
                                        $termId = $term->getId();
                                        $rate = $values ["rate{$termId}"];
                                        
                                        $leasingSchemaRateModel->setLeasingSchemaTermId($termId);
                                        $leasingSchemaRateModel->setLeasingSchemaRangeId($rangeId);
                                        $leasingSchemaRateModel->setRate($rate);
                                        $leasingSchemaRateId = $leasingSchemaRateMapper->save($leasingSchemaRateModel);
                                    }
                                    $db->commit();
                                    
                                    $this->_helper->flashMessenger(array (
                                            'success' => "The range was updated successfully." 
                                    ));
                                }
                                else
                                {
                                    $db->rollBack();
                                    $this->_helper->flashMessenger(array (
                                            'danger' => "The range \${$startRange} already exists." 
                                    ));
                                }
                            }
                            catch ( Exception $e )
                            {
                                // Save Error
                                $db->rollBack();
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
                                $leasingSchemaRange = $leasingSchemaRangeMapper->fetch(array (
                                        'startRange = ?' => $startRange 
                                ));
                                
                                if (! $leasingSchemaRange)
                                {
                                    $rangeId = $leasingSchemaRangeMapper->insert($leasingSchemaRangeModel);
                                    
                                    $leasingSchemaRateMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                                    $leasingSchemaRateModel = new Quotegen_Model_LeasingSchemaRate();
                                    
                                    // Save rates for range and term
                                    foreach ( $leasingSchemaTerms as $term )
                                    {
                                        $termId = $term->getId();
                                        $rate = $values ["rate{$termId}"];
                                        
                                        $leasingSchemaRateModel->setLeasingSchemaRangeId($rangeId);
                                        $leasingSchemaRateModel->setLeasingSchemaTermId($termId);
                                        $leasingSchemaRateModel->setRate($rate);
                                        $leasingSchemaRateMapper->insert($leasingSchemaRateModel);
                                    }
                                    $db->commit();
                                    
                                    $this->_helper->flashMessenger(array (
                                            'success' => "The range \${$startRange} was added successfully." 
                                    ));
                                }
                                else
                                {
                                    $db->rollBack();
                                    $this->_helper->flashMessenger(array (
                                            'danger' => "The range \${$startRange} already exists." 
                                    ));
                                }
                            }
                            catch ( Exception $e )
                            {
                                // Insert Error
                                $db->rollBack();
                                $this->_helper->flashMessenger(array (
                                        'danger' => 'There was an error processing the insert.  Please try again.' 
                                ));
                            }
                        }
                    }
                    else
                    {
                        $db->rollBack();
                        $this->_helper->flashMessenger(array (
                                'error' => "Please review and complete all required fields." 
                        ));
                    }
                }
                else
                {
                    // User has cancelled. We could do a redirect here if we wanted.
                    $db->rollBack();
                    $this->_helper->redirector('index');
                }
            }
            catch ( Zend_Validate_Exception $e )
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
                $leasingSchemaRange = $leasingSchemaRangeMapper->find($rangeId);
                
                if (! $leasingSchemaRange)
                {
                    $this->_helper->flashMessenger(array (
                            'warning' => 'The leasing schema range does not exist.' 
                    ));
                    $this->_helper->redirector('index');
                }
                
                $form->getElement('range')->setValue($leasingSchemaRange->getStartRange());
                
                // Get Rates for Range
                $leasingSchemaRatesMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                
                $leasingSchemaRate = $leasingSchemaRatesMapper->fetchAll(array (
                        'leasingSchemaRangeId = ?' => $rangeId 
                ));
                
                // FIXME: You could populate these elements by using the model within the mapper.
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
                                'viewScript' => 'leasingschema/forms/leasingSchemaRange.phtml', 
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
        $leasingSchemaRanges = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance()->fetchAll('leasingSchemaId = ' . $leasingSchemaId);
        if (count($leasingSchemaRanges) <= 1)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => "You cannot delete the range \${$range->getStartRange()}  as it is the last range for this Leasing Schema." 
            ));
            $this->_helper->redirector('index');
        }
        else
        {
            $message = "Are you sure you want to delete the range \${$range->getStartRange()}?";
        }
        $form = new Application_Form_Delete($message);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            $db->beginTransaction();
            try
            {
	            if (! isset($values ['cancel']))
	            {
	                // delete client from database
	                if ($form->isValid($values))
	                {
	                    $mapper->delete($range);
	                    $db->commit();
	                    $this->_helper->flashMessenger(array (
	                            'success' => "The range \${$this->view->escape ( $range->getStartRange() )} was deleted successfully." 
	                    ));
	                    $this->_helper->redirector('index');
	                }
	            }
	            else // go back
	            {
	                $db->rollBack();
	                $this->_helper->redirector('index');
	            }
            }
            catch (Exception $e)
            {
	        	$db->rollBack();
	            $this->_helper->flashMessenger(array (
	                    'danger' => 'There was an error selecting the term to delete.' 
	            ));
	            $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    public function importAction ()
    {
        // Import currently handled by indexAction. 
        // Kept this in case we want to change it so import is handled on it's own page
    }

}

