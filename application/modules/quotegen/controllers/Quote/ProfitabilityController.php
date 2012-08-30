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
                            
                            if ($quoteDeviceHasChanges)
                            {
                                $quoteDeviceMapper->save($quoteDevice);
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

