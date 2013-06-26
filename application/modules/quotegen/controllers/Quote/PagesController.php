<?php

/**
 * Class Quotegen_Quote_PagesController
 */
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
            if (!isset($values ['goBack']))
            {
                // Go through each device and add total pages.
                if ($form->isValid($values))
                {

                    $quoteDeviceGroupDeviceMapper = Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance();
                    foreach ($this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup)
                    {
                        /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
                        foreach ($quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice)
                        {
                            // Checks to see if the quantity has been changed per device
                            $hasQuantityChanged = false;

                            $newQuantity = $form->getValue("quantity_monochrome_{$quoteDeviceGroupDevice->quoteDeviceGroupId}_{$quoteDeviceGroupDevice->quoteDeviceId}");
                            if ((int)$newQuantity !== (int)$quoteDeviceGroupDevice->monochromePagesQuantity)
                            {
                                $quoteDeviceGroupDevice->monochromePagesQuantity = $newQuantity;
                                $hasQuantityChanged                              = true;
                            }

                            // If device is color capable
                            if ($quoteDeviceGroupDevice->getQuoteDevice()->isColorCapable())
                            {
                                $newQuantity = $form->getValue("quantity_color_{$quoteDeviceGroupDevice->quoteDeviceGroupId}_{$quoteDeviceGroupDevice->quoteDeviceId}");
                                if ((int)$newQuantity !== (int)$quoteDeviceGroupDevice->colorPagesQuantity)
                                {
                                    $quoteDeviceGroupDevice->colorPagesQuantity = $newQuantity;
                                    $hasQuantityChanged                         = true;
                                }
                            }

                            if ($hasQuantityChanged)
                            {
                                $quoteDeviceGroupDeviceMapper->save($quoteDeviceGroupDevice);
                            }
                        }
                    }

                    $quoteDeviceService = $this->getQuoteDeviceService();

                    // Here we need to check to see if page coverage has changed.
                    $quotePageCoverageMono  = (float)$this->_quote->pageCoverageMonochrome;
                    $quotePageCoverageColor = (float)$this->_quote->pageCoverageColor;
                    $quoteAdminCostPerPage  = (float)$this->_quote->adminCostPerPage;
                    $quotePricingConfigId   = (float)$this->_quote->pricingConfigId;

                    $this->_quote->populate($values);

                    // If we have a difference in page coverage we need to recalculate cpp rates for devices
                    if ($quotePageCoverageMono !== (float)$this->_quote->pageCoverageMonochrome
                        || $quotePageCoverageColor !== (float)$this->_quote->pageCoverageColor
                        || $quoteAdminCostPerPage !== (float)$this->_quote->adminCostPerPage
                        || $quotePricingConfigId !== (float)$this->_quote->pricingConfigId
                    )
                    {
                        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
                        foreach ($this->_quote->getQuoteDevices() as $quoteDevice)
                        {
                            $device = $quoteDevice->getDevice();
                            if ($device)
                            {
                                $masterDevice = $device->getMasterDevice();
                                if ($masterDevice)
                                {
                                    $quoteDevice = $quoteDeviceService->syncDevice($quoteDevice);
                                    Quotegen_Model_Mapper_QuoteDevice::getInstance()->save($quoteDevice);
                                }
                            }
                        }
                    }

                    $this->saveQuote();
                    Quotegen_Model_Mapper_Quote::getInstance()->save($this->_quote);
                    $form->populate($this->_quote->toArray());

                    // saveAndContinue is clicked : to to quote_profitability
                    if (isset($values ['saveAndContinue']))
                    {
                        $this->redirector('index', 'quote_profitability', null, array(
                                                                                     'quoteId' => $this->_quoteId
                                                                                ));
                    }
                    else
                    {
                        // Refresh the page
                        $this->redirector(null, null, null, array(
                                                                 'quoteId' => $this->_quoteId
                                                            ));
                    }
                }
                // form invalid : show error messages 
                else
                {
                    $this->_flashMessenger->addMessage(array(
                                                            'danger' => 'Please correct the errors below.'
                                                       ));
                }
            }
            // Go back button is clicked : got back to qoute_groups
            else
            {
                $this->redirector('index', 'quote_groups', null, array(
                                                                      'quoteId' => $this->_quoteId
                                                                 ));
            }
        }

        $this->view->form = $form;
    }
}