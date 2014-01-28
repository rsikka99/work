<?php

/**
 * Class Proposalgen_AdminController
 */
class Proposalgen_CustomerPricingController extends Tangent_Controller_Action
{
    /**
     * @var Zend_Config
     */
    protected $_config;

    /**
     * @var int
     */
    protected $_selectedClientId;

    /**
     * The namespace for our mps application
     *
     * @var Zend_Session_Namespace
     */
    protected $_mpsSession;

    public function init ()
    {
        $this->_config     = Zend_Registry::get('config');
        $this->_mpsSession = new Zend_Session_Namespace('mps-tools');

        if (isset($this->_mpsSession->selectedClientId))
        {
            $client = Quotegen_Model_Mapper_Client::getInstance()->find($this->_mpsSession->selectedClientId);
            // Make sure the selected client is ours!
            if ($client && $client->dealerId == Zend_Auth::getInstance()->getIdentity()->dealerId)
            {
                $this->_selectedClientId      = $this->_mpsSession->selectedClientId;
                $this->view->selectedClientId = $this->_selectedClientId;
            }
        }
    }

    /**
     * Upload customer pricing into the system
     */
    public function indexAction ()
    {
        $this->view->headTitle('Customer Pricing');
        $this->view->headTitle('Upload CSV');

        $db            = Zend_Db_Table::getDefaultAdapter();
        $uploadService = new Proposalgen_Service_Import_Customer_Pricing();
        $form          = $uploadService->getForm();

        if ($this->_request->isPost() && $form->isValid($this->getRequest()->getPost()))
        {
            if (!is_array($uploadService->getValidFile()))
            {
                $db->beginTransaction();
                try
                {
                    if ($uploadService->validatedHeaders())
                    {
                        /**
                         * Fetch all the unique data in the csv
                         */
                        $data = array();
                        while (($value = fgetcsv($uploadService->importFile)) !== false)
                        {
                            $value     = array_combine($uploadService->importHeaders, $value);
                            $validData = $uploadService->processValidation($value);

                            if (!isset($validData['error']) && !isset($data[$uploadService::CSV_HEADER_OEM_PRODUCT_CODE]))
                            {
                                $data[$validData[$uploadService::CSV_HEADER_OEM_PRODUCT_CODE]] = array(
                                    'sku'       => $validData[$uploadService::CSV_HEADER_OEM_PRODUCT_CODE],
                                    'dealerSku' => $validData[$uploadService::CSV_HEADER_DEALER_PRODUCT_CODE],
                                    'clientSku' => $validData[$uploadService::CSV_HEADER_CUSTOMER_PRODUCT_CODE],
                                    'cost'      => $validData[$uploadService::CSV_HEADER_UNIT_PRICE],
                                );
                            }
                        }



                        /**
                         * Add to the database
                         */
                        $clientTonerAttributesMapper = Proposalgen_Model_Mapper_Client_Toner_Attribute::getInstance();
                        $tonerMapper                 = Proposalgen_Model_Mapper_Toner::getInstance();
                        foreach ($data as $oemSku => $pricingData)
                        {
                            $toner = $tonerMapper->fetchBySku($oemSku);
                            if ($toner instanceof Proposalgen_Model_Toner)
                            {
                                $update               = true;
                                $clientTonerAttribute = $clientTonerAttributesMapper->findTonerAttributeByTonerId($toner->id, $this->_selectedClientId);
                                if (!$clientTonerAttribute instanceof Proposalgen_Model_Client_Toner_Attribute)
                                {
                                    $update                         = false;
                                    $clientTonerAttribute           = new Proposalgen_Model_Client_Toner_Attribute();
                                    $clientTonerAttribute->clientId = $this->_selectedClientId;
                                    $clientTonerAttribute->tonerId  = $toner->id;


                                }

                                $clientTonerAttribute->clientSku = $pricingData['clientSku'];
                                $clientTonerAttribute->cost      = $pricingData['cost'];

                                if ($update)
                                {
                                    $clientTonerAttributesMapper->save($clientTonerAttribute);
                                }
                                else
                                {
                                    $clientTonerAttributesMapper->insert($clientTonerAttribute);
                                }
                            }

                        }

                        $this->_flashMessenger->addMessage(array("success" => "Your pricing updates have been applied successfully."));
                        $db->commit();

                        $this->redirector('index', 'costs', 'proposalgen');
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array("error" => "This file headers are in-correct."));
                    }
                }
                catch (Exception $e)
                {
                    $db->rollback();
                    Tangent_Log::logException($e);
                    $this->_flashMessenger->addMessage(array("error" => "An error has occurred during the update and your changes were not applied. Please review your file and try again."));
                }
                $uploadService->closeFiles();
            }
            else
            {
                $this->_flashMessenger->addMessage(array("error" => "An error has occurred during the update and your changes were not applied. Please review your file and try again."));
            }
        }
        $this->view->form = $form;
    }
}