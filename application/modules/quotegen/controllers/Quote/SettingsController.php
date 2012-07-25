<?php

class Quotegen_Quote_SettingsController extends Quotegen_Library_Controller_Quote
{
    public $contexts = array (
            'leasing-schemas' => array (
                    'json' 
            ), 
            'leasing-schema-terms' => array (
                    'json' 
            ) 
    );

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::SETTINGS_CONTROLLER);
        
        $this->_helper->contextSwitch()->initContext();
    }

    /**
     * This function takes care of editing quote settings
     */
    public function indexAction ()
    {
        $this->requireQuote();
        
        // Get the system and user defaults and apply overrides for user settings
        $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
        $userSetting = Quotegen_Model_Mapper_UserQuoteSetting::getInstance()->fetchUserQuoteSetting(Zend_Auth::getInstance()->getIdentity()->id);
        $quoteSetting->applyOverride($userSetting);
        
        $form = new Quotegen_Form_EditQuote();
        $form->populate($this->_quote->toArray());
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ['back']))
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index', 'quote_devices', null, array (
                        'quoteId' => $this->_quoteId 
                ));
            }
            
            try
            {
                if ($form->isValid($values))
                {
                    $formValues = $form->getValues();
                    
                    //  Check the form values to see if user has left text blank, if so get from user / syste defaults 
                    if (strlen($formValues ['pageCoverageMonochrome']) === 0)
                        $formValues ['pageCoverageMonochrome'] = $quoteSetting->getPageCoverageMonochrome();
                    if (strlen($formValues ['pageCoverageColor']) === 0)
                        $formValues ['pageCoverageColor'] = $quoteSetting->getPageCoverageColor();
                    if ($formValues ['pricingConfigId'] === (string)Proposalgen_Model_PricingConfig::NONE)
                        $formValues ['pricingConfigId'] = $quoteSetting->getPricingConfigId();
                        
                        // Update current quote object and save new quote items to database
                    $this->_quote->populate($formValues);
                    $quoteMapper = Quotegen_Model_Mapper_Quote::getInstance()->save($this->_quote, $this->_quote->getId());
                    
                    $this->_helper->flashMessenger(array (
                            'success' => "Quote updated successfully." 
                    ));
                    
                    if (isset($values ['saveAndContinue']))
                    {
                        $this->_helper->redirector('index', 'quote_reports', null, array (
                                'quoteId' => $this->_quoteId 
                        ));
                    }
                    
                    $form->populate($this->_quote->toArray());
                }
            }
            catch ( Exception $e )
            {
                $this->_helper->flashMessenger(array (
                        'danger' => 'Error saving settings.  Please try again.' 
                ));
            }
        }
        
        $this->view->form = $form;
    }

    public function leasingSchemasAction ()
    {
        $leasingSchemas = array ();
        /* @var $leasingSchema Quotegen_Model_LeasingSchema */
        foreach ( Quotegen_Model_Mapper_LeasingSchema::getInstance()->fetchAll() as $leasingSchema )
        {
            $leasingSchemas [] = $leasingSchema->toArray();
        }
        
        $this->view->leasingSchemas = $leasingSchemas;
    }

    public function leasingSchemaTermsAction ()
    {
        $leasingSchemaId = $this->_getParam('leasingSchemaId', FALSE);
        if (! $leasingSchemaId)
        {
            throw new InvalidArgumentException('Leasing Schema Parameter is required!');
        }
        
        $leasingSchema = Quotegen_Model_Mapper_LeasingSchema::getInstance()->find($leasingSchemaId);
        if (! $leasingSchema)
        {
            throw new InvalidArgumentException('Invalid Id!');
        }
        
        $leasingSchemaTerms = array ();
        /* @var $leasingSchemaTerm Quotegen_Model_Mapper_LeasingSchemaTerm */
        foreach ( $leasingSchema->getTerms() as $leasingSchemaTerm )
        {
            $leasingSchemaTerms [] = $leasingSchemaTerm->toArray();
        }
        
        $this->view->leasingSchemaTerms = $leasingSchemaTerms;
    }
}

