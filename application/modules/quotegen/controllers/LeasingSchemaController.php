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
        
        //*****************************************
        // TODO: MOVE ALL THIS INTO A FUNCTION INSIDE THE MAPPER
        //*****************************************
        

        // Get default leasing schema
        $leasingSchema = $leasingSchemaMapper->find(1);
        
        // Get list of ranges
        $leasingSchemaRanges = $leasingSchemaRangeMapper->fetchAll('leasingSchemaId = ' . $leasingSchema->getId());
        $this->view->ranges = $leasingSchemaRanges;
        
        // Get list of terms
        $leasingSchemaTerms = $leasingSchemaTermMapper->fetchAll('leasingSchemaId = ' . $leasingSchema->getId());
        
        // Build array to pass to view
        $terms = array ();
        
        // Loop through Terms
        foreach ( $leasingSchemaTerms as $leasingSchemaTerm )
        {
            $leasingSchemaTermId = $leasingSchemaTerm->getId();
            $leasingSchemaTermMonths = $leasingSchemaTerm->getMonths();

            // Get rate for term/range
            $leasingSchemaRates = $leasingSchemaRateMapper->fetchAll('leasingSchemaTermId = ' . $leasingSchemaTermId);
            
            $rates = array ();
            foreach ( $leasingSchemaRates as $leasingSchemaRate ) {
                $leasingSchemaRangeId = $leasingSchemaRate->getLeasingSchemaRangeId();
	            $leasingSchemaRateRate = $leasingSchemaRate->getRate();
		                
		        // Append to array
		        $rates [] = array (
		                'rangeid' => $leasingSchemaRangeId,
		                'rate' => $leasingSchemaRateRate
		                );
            }

            $terms [] = array (
                    'termid' => $leasingSchemaTermId,
                    'term' => $leasingSchemaTermMonths,
                    'rates' => $rates
            );
            
        
        }
        // print_r($terms); die;
        $this->view->terms = $terms;
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            print_r($values);
        }
    }

}

