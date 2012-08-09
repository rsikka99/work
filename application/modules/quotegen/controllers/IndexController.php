<?php

class Quotegen_IndexController extends Quotegen_Library_Controller_Quote
{

    public function indexAction ()
    {
        $request = $this->getRequest();
        
        // Get the system and user defaults and apply overrides for user settings
        $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
        $userSetting = Quotegen_Model_Mapper_UserQuoteSetting::getInstance()->fetchUserQuoteSetting(Zend_Auth::getInstance()->getIdentity()->id);
        $quoteSetting->applyOverride($userSetting);
        
        $quoteForm = new Quotegen_Form_Quote();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if ($quoteForm->isValid($values))
            {
                $formValues = $quoteForm->getValues();
                
                //  Check the form values to see if user has left text blank, if so get from user / system defaults
                if (strlen($formValues ['pageCoverageMonochrome']) === 0)
                {
                    $formValues ['pageCoverageMonochrome'] = $quoteSetting->getPageCoverageMonochrome();
                }
                if (strlen($formValues ['pageCoverageColor']) === 0)
                {
                    $formValues ['pageCoverageColor'] = $quoteSetting->getPageCoverageColor();
                }
                if ($formValues ['pricingConfigId'] === (string)Proposalgen_Model_PricingConfig::NONE)
                {
                    $formValues ['pricingConfigId'] = $quoteSetting->getPricingConfigId();
                }
                
                // Update current quote object and save new quote items to database
                $this->_quote->populate($formValues);
                $this->_quote->setDateCreated(date('Y-m-d H:i:s'));
                $this->_quote->setQuoteDate(date('Y-m-d H:i:s'));
                $this->_quote->setUserId($this->_userId);
                
                $quoteId = $this->saveQuote();
                
                $quoteLeaseTerm = new Quotegen_Model_QuoteLeaseTerm();
                $quoteLeaseTerm->setQuoteId($this->_quote->getId());
                $quoteLeaseTerm->setLeasingSchemaTermId($formValues ['leasingSchemaTermId']);
                Quotegen_Model_Mapper_QuoteLeaseTerm::getInstance()->insert($quoteLeaseTerm);
                
                // Create the quote
                // Redirect to the build controller
                $this->_helper->redirector('index', 'quote_devices', null, array (
                        'quoteId' => $quoteId 
                ));
            }
        }
        $this->view->quoteForm = $quoteForm;
    }

    public function existingQuoteAction ()
    {
        $request = $this->getRequest();
        $existingQuoteForm = new Quotegen_Form_SelectQuote();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if (isset($values ['quoteId']))
            {
                // Existing Quote
                if ($existingQuoteForm->isValid($values))
                {
                    // Redirect to the build controller
                    $this->_helper->redirector('index', 'quote_devices', null, array (
                            'quoteId' => $values ['quoteId'] 
                    ));
                }
                else
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => "There was an error selecting your quote. Please try again." 
                    ));
                }
            }
        }
        $this->view->existingQuoteForm = $existingQuoteForm;
    }
}

