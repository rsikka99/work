<?php

class Quotegen_Quote_GroupsController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::GROUPS_CONTROLLER);
    }

    /**
     * This function takes care of editing quote settings
     */
    public function indexAction ()
    {
        $form = new Quotegen_Form_Quote_Group($this->_quote);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if (isset($values ['goBack']))
            {
                $this->_helper->redirector('index', 'quote_devices', null, array (
                        'quoteId' => $this->_quoteId 
                ));
            }
            else
            {
                // We're saving quantities here
                if ($form->isValid($values))
                {
                    $db = Zend_Db_Table::getDefaultAdapter();
                    try
                    {
                        $db->beginTransaction();
                        $quoteDeviceGroupDeviceMapper = Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance();
                        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
                        foreach ( $this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup )
                        {
                            /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
                            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
                            {
                                $quoteDeviceGroupDevice->setQuantity($form->getValue("quantity_{$quoteDeviceGroupDevice->getQuoteDeviceGroupId()}_{$quoteDeviceGroupDevice->getQuoteDeviceId()}"));
                                $quoteDeviceGroupDeviceMapper->save($quoteDeviceGroupDevice);
                            }
                        }
                        
                        $this->saveQuote();
                        
                        $db->commit();
                        
                        $this->_helper->flashMessenger(array (
                                'success' => 'Your changes have been saved.' 
                        ));
                        
                        
                        // Time to move on?
                        if (isset($values ['saveAndContinue']))
                        {
                            
                            $this->_helper->redirector('index', 'quote_pages', null, array (
                                    'quoteId' => $this->_quoteId 
                            ));
                        }
                    }
                    catch ( Exception $e )
                    {
                        $db->rollBack();
                        
                        // Log the error
                        My_Log::logException($e);
                        
                        $this->_helper->flashMessenger(array (
                                'danger' => 'There was an error saving your changes. Please try again or contact your system administrator.' 
                        ));
                    }
                }
            }
        }
        
        $this->view->form = $form;
    }
}

