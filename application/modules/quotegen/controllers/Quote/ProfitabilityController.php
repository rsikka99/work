<?php

class Quotegen_Quote_ProfitabilityController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::PROFITABILITY_CONTROLLER);
    }

    /**
     * This function takes care of editing quote settings
     */
    public function indexAction ()
    {
        $form = new Quotegen_Form_Quote_Profitability($this->_quote);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if (isset($values ['goBack']))
            {
                $this->_helper->redirector('index', 'quote_pages', null, array (
                        'quoteId' => $this->_quoteId 
                ));
            }
            else
            {
                // Check to see if the form is valid
                if ($form->isValid($values))
                {
                    $db = Zend_Db_Table::getDefaultAdapter();
                    try
                    {
                        $db->beginTransaction();
                        
                        $changesMade = false;
                        $quoteDeviceMapper = Quotegen_Model_Mapper_QuoteDevice::getInstance();
                        
                        // Save the devices
                        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
                        foreach ( $this->_quote->getQuoteDevices() as $quoteDevice )
                        {
                            $quoteDeviceHasChanges = false;
                            
                            $packageMarkup = $form->getValue("packageMarkup_{$quoteDevice->getId()}");
                            $margin = $form->getValue("margin_{$quoteDevice->getId()}");
                            
                            // Has the package markup changed?
                            if ((float)$quoteDevice->getPackageMarkup() !== (float)$packageMarkup)
                            {
                                $quoteDevice->setPackageMarkup($packageMarkup);
                                $quoteDeviceHasChanges = true;
                            }
                            
                            // Has the margin changed?
                            if ((float)$quoteDevice->getMargin() !== (float)$margin)
                            {
                                $quoteDevice->setMargin($margin);
                                $quoteDeviceHasChanges = true;
                            }
                            
                            // Leased quote only
                            if ($this->_quote->isLeased())
                            {
                                $residual = $form->getValue("residual_{$quoteDevice->getId()}");
                                
                                // Has the residual changed?
                                if ((float)$quoteDevice->getResidual() !== (float)$residual)
                                {
                                    $quoteDevice->setResidual($residual);
                                    $quoteDeviceHasChanges = true;
                                }
                            }
                            
                            // Save changes to the device if anything changed.
                            if ($quoteDeviceHasChanges)
                            {
                                // Last minute check to ensure that residual will be never be higher than the package price
                                if ($quoteDevice->getResidual() > 0 && $quoteDevice->getResidual() >= $quoteDevice->calculatePackagePrice())
                                {
                                    $quoteDevice->setResidual(0);
                                }
                                $quoteDeviceMapper->save($quoteDevice);
                                $changesMade = true;
                            }
                        }
                        
                        // Only make changes if the quote is leased.
                        if ($this->_quote->isLeased())
                        {
                            // Get the leasing schema id to have the form populate the select box options properly
                            $leasingSchemaTerm = $this->_quote->getLeasingSchemaTerm();
                            
                            // Save the leasing schema term
                            if (! $leasingSchemaTerm || (int)$form->getValue('leasingSchemaTermId') != (int)$leasingSchemaTerm->getId())
                            {
                                $quoteLeaseTerm = new Quotegen_Model_QuoteLeaseTerm();
                                $quoteLeaseTerm->setQuoteId($this->_quote->getId());
                                
                                $quoteLeaseTerm->setLeasingSchemaTermId($form->getValue('leasingSchemaTermId'));
                                
                                if ($leasingSchemaTerm)
                                {
                                    Quotegen_Model_Mapper_QuoteLeaseTerm::getInstance()->save($quoteLeaseTerm);
                                }
                                else
                                {
                                    Quotegen_Model_Mapper_QuoteLeaseTerm::getInstance()->insert($quoteLeaseTerm);
                                }
                                
                                $changesMade = true;
                            }
                        }
                        
                        $db->commit();
                        
                        // Only show a message when we've made changes.
                        if ($changesMade)
                        {
                            $this->saveQuote();
                            $this->_helper->flashMessenger(array (
                                    'success' => 'Changes saved successfully.' 
                            ));
                        }
                        
                        if (! $changesMade && isset($values ['save']))
                        {
                            $this->_helper->flashMessenger(array (
                                    'info' => 'There were no changes to save.' 
                            ));
                        }
                        
                        if (isset($values ['saveAndContinue']))
                        {
                            $this->_helper->redirector('index', 'quote_reports', null, array (
                                    'quoteId' => $this->_quoteId 
                            ));
                        }
                        else
                        {
                            // Refresh the page
                            $this->_helper->redirector(null, null, null, array (
                                    'quoteId' => $this->_quoteId 
                            ));
                        }
                    }
                    catch ( Exception $e )
                    {
                        $db->rollBack();
                        My_Log::logException($e);
                    }
                }
            }
        }
        
        $this->view->form = $form;
    }
}

