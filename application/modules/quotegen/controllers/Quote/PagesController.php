<?php

class Quotegen_Quote_PagesController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::PAGES_CONTROLLER);
    }

    /**
     * This function takes care of editing quote settings
     */
    public function indexAction ()
    {
        $form = new Quotegen_Form_Quote_Page($this->_quote);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            // If save button is hit process save
            if (isset($values ['save']))
            {
                // Go through each device and add total pages.
                if ($form->isValid($values))
                {
                    $quoteDeviceGroupDeviceMapper = Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance();
                    foreach ( $this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup )
                    {
                        /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
                        foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
                        {
                            $hasQuantityChanged = false;
                            
                            $newQuantity = $form->getValue("quantity_black_{$quoteDeviceGroupDevice->getQuoteDeviceGroupId()}_{$quoteDeviceGroupDevice->getQuoteDeviceId()}");
                            if ((int)$newQuantity !== (int)$quoteDeviceGroupDevice->getMonochromePagesQuantity())
                            {
                                $quoteDeviceGroupDevice->setMonochromePagesQuantity($newQuantity);
                                $hasQuantityChanged = true;
                            }
                            
                            $newQuantity = $form->getValue("quantity_color_{$quoteDeviceGroupDevice->getQuoteDeviceGroupId()}_{$quoteDeviceGroupDevice->getQuoteDeviceId()}");
                            if ((int)$newQuantity !== (int)$quoteDeviceGroupDevice->getColorPagesQuantity())
                            {
                                $quoteDeviceGroupDevice->setColorPagesQuantity($newQuantity);
                                $hasQuantityChanged = true;
                            }
                            
                            if ($hasQuantityChanged)
                            {
                                $quoteDeviceGroupDeviceMapper->save($quoteDeviceGroupDevice);
                            }
                        }
                    }
                }
            }
            
            if (isset($values ['goBack']))
            {
                $this->_helper->redirector('index', 'quote_groups', null, array (
                        'quoteId' => $this->_quoteId 
                ));
            }
            else if (isset($values ['saveAndContinue']))
            {
                $this->_helper->redirector('index', 'quote_profitability', null, array (
                        'quoteId' => $this->_quoteId 
                ));
            }
        }
        
        $this->view->totalPages = $this->getTotalPages();
        $this->view->form = $form;
    }
}

