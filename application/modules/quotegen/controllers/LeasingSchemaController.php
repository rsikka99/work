<?php

class Quotegen_LeasingSchemaController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    public function indexAction ()
    {
        // Display all of the clients
        $leasingSchemaMapper = Quotegen_Model_Mapper_LeasingSchema::getInstance();
        $leasingSchemaRangeMapper = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance();
        $leasingSchemaTermMapper = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance();
        $leasingSchemaRateMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();

        // Get default leasing schema
        $leasingSchema = $leasingSchemaMapper->find(1);
        
        // Get list of ranges
        $leasingSchemaRanges = $leasingSchemaRangeMapper->find('leasingSchemaId = 1');

        // Get list of terms
        $leasingSchemaTerms = $leasingSchemaTermMapper->find('leasingSchemaId = 1');
        
        // Build array to pass to view
        // Loop through Terms
        foreach ( $leasingSchemaTerms as $leasingSchemaTerm ) {
	        // Get list of rates for term
	        $leasingSchemaRates = $leasingSchemaRateMapper->find('leasingSchemaTermId = ' . $leasingSchemaTerm->getId());
	        
	        // Loop through Rates
	        foreach ( $leasingSchemaRates as $leasingSchemaRate ) {
	        	// Loop through Ranges
	        }
        }

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            print_r($values);
        }
    }

}

