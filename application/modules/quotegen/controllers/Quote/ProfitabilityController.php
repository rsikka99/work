<?php

/**
 * Class Quotegen_Quote_ProfitabilityController
 */
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

        $selectedLeasingSchemaId = $this->_getParam('leasingSchemaId', null);
        $form                    = new Quotegen_Form_Quote_Profitability($this->_quote, $selectedLeasingSchemaId);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();

            if (isset($values ['goBack']))
            {
                $this->redirector('index', 'quote_pages', null, array(
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

                        $changesMade       = false;
                        $quoteDeviceMapper = Quotegen_Model_Mapper_QuoteDevice::getInstance();

                        // Save the devices
                        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
                        foreach ($this->_quote->getQuoteDevices() as $quoteDevice)
                        {
                            $quoteDeviceHasChanges = false;

                            $packageMarkup = $form->getValue("packageMarkup_{$quoteDevice->id}");
                            $margin        = $form->getValue("margin_{$quoteDevice->id}");

                            // Has the package markup changed?
                            if ((float)$quoteDevice->packageMarkup !== (float)$packageMarkup)
                            {
                                $quoteDevice->packageMarkup = $packageMarkup;
                                $quoteDeviceHasChanges      = true;
                            }

                            // Has the margin changed?
                            if ((float)$quoteDevice->margin !== (float)$margin)
                            {
                                $quoteDevice->margin   = $margin;
                                $quoteDeviceHasChanges = true;
                            }

                            // Leased quote only
                            if ($this->_quote->isLeased())
                            {
                                $residual = $form->getValue("residual_{$quoteDevice->id}");

                                // Has the residual changed?
                                if ((float)$quoteDevice->residual !== (float)$residual)
                                {
                                    $quoteDevice->residual = $residual;
                                    $quoteDeviceHasChanges = true;
                                }
                            }

                            // Save changes to the device if anything changed.
                            if ($quoteDeviceHasChanges)
                            {
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
                            if (!$leasingSchemaTerm || (int)$form->getValue('leasingSchemaTermId') != (int)$leasingSchemaTerm->id)
                            {

                                $quoteLeaseTerm          = new Quotegen_Model_QuoteLeaseTerm();
                                $quoteLeaseTerm->quoteId = $this->_quote->id;

                                $quoteLeaseTerm->leasingSchemaTermId = $form->getValue('leasingSchemaTermId');

                                if ($leasingSchemaTerm)
                                {
                                    Quotegen_Model_Mapper_QuoteLeaseTerm::getInstance()->save($quoteLeaseTerm);
                                }
                                else
                                {
                                    Quotegen_Model_Mapper_QuoteLeaseTerm::getInstance()->insert($quoteLeaseTerm);
                                }

                                // Reset the leasing schema term for the quote since it has changed.
                                $this->_quote->setLeasingSchemaTerm(null);

                                $changesMade = true;
                            }
                        }

                        $db->commit();

                        // Only show a message when we've made changes.
                        if ($changesMade)
                        {
                            $this->saveQuote();
                            $this->_flashMessenger->addMessage(array(
                                                                    'success' => 'Changes saved successfully.'
                                                               ));
                        }

                        if (!$changesMade && isset($values ['save']))
                        {
                            $this->_flashMessenger->addMessage(array(
                                                                    'info' => 'There were no changes to save.'
                                                               ));
                        }

                        if (isset($values ['saveAndContinue']))
                        {
                            $this->redirector('index', 'quote_reports', null, array(
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
                    catch (Exception $e)
                    {
                        $db->rollBack();
                        Tangent_Log::logException($e);
                    }
                }
                else
                {
                    if (!isset($values['leasingSchemaId']))
                    {
                        $this->_flashMessenger->addMessage(array(
                                                                'danger' => 'Select a Lease Term'
                                                           ));
                    }
                    else
                    {

                        $this->_flashMessenger->addMessage(array(
                                                                'danger' => 'Please correct the errors below.'
                                                           ));
                    }
                }
            }
        }

        $this->view->form = $form;
    }

    /**
     * The leasingdetailsAction accepts a leaseId as a GET parameter, and
     * returns
     * information about the corresponding lease in json format.
     * Jquery code
     * requesting data from this action is located in on the main layout page.
     */
    public function leasingdetailsAction ()
    {
        // Disable the default layout
        $this->_helper->layout->disableLayout();
        $leasingSchemaId = $this->_getParam('schemaId', false);

        try
        {
            if ($leasingSchemaId > 0)
            {
                $leasingSchema      = Quotegen_Model_Mapper_LeasingSchema::getInstance()->find($leasingSchemaId);
                if ($leasingSchema)
                {
                    $formData = new stdClass();
                    /* @var $leasingSchemaTerm Quotegen_Model_LeasingSchemaTerm */
                    $i = 0;
                    foreach ($leasingSchema->getTerms() as $leasingSchemaTerm)
                    {
                        $formData->$i = array(
                            $leasingSchemaTerm->id,
                            number_format($leasingSchemaTerm->months) . " months"
                        );
                        $i++;
                    }
                    $formData->length = $i;
                }
            }
            else
            {
                $formData = array();
            }
        }
        catch (Exception $e)
        {
            // CRITICAL EXCEPTION
            Throw new exception("Critical Error: Unable to find company.", 0, $e);
        } // end catch


        // Encode company data to return to the client:
        $this->sendJson($formData);
    }
}

