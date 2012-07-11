<?php

class Quotegen_Quote_SettingsController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::SETTINGS_CONTROLLER);
    }

    /**
     * This function takes care of editing quote settings
     */
    public function indexAction ()
    {
        if (! $this->_quote->getId())
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Error you must select a quote first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Get the system and user defaults and apply overrides for user settings
        $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
        $userSetting = Quotegen_Model_Mapper_UserQuoteSetting::getInstance()->fetchUserQuoteSetting(Zend_Auth::getInstance()->getIdentity()->id);
        $quoteSetting->applyOverride($userSetting);
        
        $form = new Quotegen_Form_EditQuote();
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ['back']))
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index', 'quote_pricing', null, array (
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
                }
                else
                    throw new InvalidArgumentException('Please correct the error messages below');
            }
            catch ( Exception $e )
            {
                throw new Exception('Error saving to the database', 0, $e);
                $this->_helper->flashMessenger(array (
                        'danger' => $e->getMessage() 
                ));
            }
        }
        $form->populate($this->_quote->toArray());
        $this->view->form = $form;
    }
}

