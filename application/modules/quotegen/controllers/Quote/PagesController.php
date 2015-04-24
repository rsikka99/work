<?php
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorRankingMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorRankingSetMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\QuotePageForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceGroupDeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceGroupDeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceModel;

/**
 * Class Quotegen_Quote_PagesController
 */
class Quotegen_Quote_PagesController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        $this->_navigation->setActiveStep(\MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteStepsModel::STEP_ADD_PAGES);
    }

    /**
     * This function takes care of editing quote settings
     */
    public function indexAction ()
    {
        $this->_pageTitle = ['Quote', 'Pages'];
        $form             = new QuotePageForm($this->_quote);

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
                    $quoteDeviceGroupDeviceMapper = QuoteDeviceGroupDeviceMapper::getInstance();
                    foreach ($this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup)
                    {
                        /* @var $quoteDeviceGroupDevice QuoteDeviceGroupDeviceModel */
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

                    // This updates the values of the settings on the page
                    $this->_quote->populate($values);

                    // Save the toner ranks
                    $rankingSetMapper = TonerVendorRankingSetMapper::getInstance();

                    if (isset($values['dealerMonochromeRankSetArray']))
                    {
                        $this->_quote->dealerMonochromeRankSetId = $rankingSetMapper->saveRankingSets($this->_quote->dealerMonochromeRankSetId, $values['dealerMonochromeRankSetArray']);
                    }
                    else
                    {
                        TonerVendorRankingMapper::getInstance()->deleteByTonerVendorRankingId($this->_quote->dealerMonochromeRankSetId);
                    }

                    if (isset($values['dealerColorRankSetArray']))
                    {
                        $this->_quote->dealerColorRankSetId = $rankingSetMapper->saveRankingSets($this->_quote->dealerColorRankSetId, $values['dealerColorRankSetArray']);
                    }
                    else
                    {
                        TonerVendorRankingMapper::getInstance()->deleteByTonerVendorRankingId($this->_quote->dealerColorRankSetId);
                    }

                    // If we have a difference in page coverage we need to recalculate CPP rates for devices
                    if ($quotePageCoverageMono !== (float)$this->_quote->pageCoverageMonochrome
                        || $quotePageCoverageColor !== (float)$this->_quote->pageCoverageColor
                        || $quoteAdminCostPerPage !== (float)$this->_quote->adminCostPerPage
                    )
                    {
                        /* @var $quoteDevice QuoteDeviceModel */
                        foreach ($this->_quote->getQuoteDevices() as $quoteDevice)
                        {
                            $device = $quoteDevice->getDevice();
                            if ($device)
                            {
                                $masterDevice = $device->getMasterDevice();
                                if ($masterDevice)
                                {
                                    $quoteDevice = $quoteDeviceService->syncDevice($quoteDevice);
                                    QuoteDeviceMapper::getInstance()->save($quoteDevice);
                                }
                            }
                        }
                    }

                    $this->saveQuote();
                    QuoteMapper::getInstance()->save($this->_quote);
                    $form->populate($this->_quote->toArray());

                    // saveAndContinue is clicked : to to quote_profitability
                    if (isset($values ['saveAndContinue']))
                    {
                        $this->updateQuoteStepName();
                        $this->saveQuote();
                        $this->redirectToRoute('quotes.hardware-financing', ['quoteId' => $this->_quoteId]);
                    }
                    else
                    {
                        // Refresh the page
                        $this->redirectToRoute(null, ['quoteId' => $this->_quoteId]);
                    }
                }
                // form invalid : show error messages 
                else
                {
                    $this->_flashMessenger->addMessage([
                        'danger' => 'Please correct the errors below.'
                    ]);
                }
            }
            // Go back button is clicked : got back to quote_groups
            else
            {
                $this->redirectToRoute('quotes.group-devices', ['quoteId' => $this->_quoteId]);
            }
        }

        $this->view->form = $form;
    }
}