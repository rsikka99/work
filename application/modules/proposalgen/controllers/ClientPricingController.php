<?php

/**
 * Class Proposalgen_AdminController
 */
class Proposalgen_ClientPricingController extends Tangent_Controller_Action
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

    /**
     * @var stdClass
     */
    protected $_identity;

    /**
     * @var int
     */
    protected $_dealerId;

    /**
     * @var int
     */
    protected $_userId;

    public function init ()
    {
        $this->_config     = Zend_Registry::get('config');
        $this->_mpsSession = new Zend_Session_Namespace('mps-tools');
        $this->_identity   = Zend_Auth::getInstance()->getIdentity();
        $this->_userId     = $this->_identity->id;
        $this->_dealerId   = $this->_identity->dealerId;

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

    public function indexAction ()
    {
        $this->view->clientTonersForm = new Proposalgen_Form_ClientPricing_ClientToner();
    }

    /**
     * Creates a list of client toners to show in jqgrid
     */
    public function clientTonersListAction ()
    {
        $jqGridService    = new Tangent_Service_JQGrid();
        $filter           = $this->_getParam('filter', false);
        $criteria         = $this->_getParam('criteria', false);
        $jqGridParameters = array(
            'sidx' => $this->_getParam('sidx', 'id'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10),
        );
        $sortColumns      = array(
            'oemSku',
            'dealerSku',
            'clientSku',
            'cost',
            'replacementOemSku',
            'replacementDealerSku',
            'replacementCost',
            'replacementSavings',
        );

        $jqGridService->setValidSortColumns($sortColumns);
        if ($jqGridService->sortingIsValid())
        {
            $clientTonerAttributeMapper = Proposalgen_Model_Mapper_Client_Toner_Order::getInstance();

            $jqGridService->parseJQGridPagingRequest($jqGridParameters);

            $sortOrder = array();
            if ($jqGridService->hasColumns())
            {
                $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
            }

            $jqGridService->setRecordCount($clientTonerAttributeMapper->jqgridFetchAllForClient($this->_selectedClientId, $this->_dealerId, $sortOrder, 10000, 0, $filter, $criteria, true));

            // Validate current page number since we don't want to be out of bounds
            if ($jqGridService->getCurrentPage() < 1)
            {
                $jqGridService->setCurrentPage(1);
            }
            else if ($jqGridService->getCurrentPage() > $jqGridService->calculateTotalPages())
            {
                $jqGridService->setCurrentPage($jqGridService->calculateTotalPages());
            }

            // Return a small subset of the results based on the jqGrid parameters
            $startRecord = $jqGridService->getRecordsPerPage() * ($jqGridService->getCurrentPage() - 1);

            if ($startRecord < 0)
            {
                $startRecord = 0;
            }

            $jqGridService->setRows($clientTonerAttributeMapper->jqgridFetchAllForClient($this->_selectedClientId, $this->_dealerId, $sortOrder, $jqGridService->getRecordsPerPage(), $startRecord, $filter, $criteria));

            // Send back jqGrid JSON data
            $this->sendJson($jqGridService->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson(array(
                'error' => 'Sorting parameters are invalid'
            ));
        }
    }

    /**
     * Gets all the client toner replacements and puts them into an array to be used in the editing of a client toner order on the form
     */
    public function clientTonerReplacementsAction ()
    {
        $tonerId         = $this->_getParam('tonerId', false);
        $replacementData = array();

        if ($tonerId)
        {
            $toners = Proposalgen_Model_Mapper_Toner::getInstance()->getCompatibleToners($tonerId, $this->_selectedClientId);
            foreach ($toners as $toner)
            {
                $replacementData[$toner->id] = $toner->getManufacturer()->displayname . " - " . $toner->sku . " - " . $this->view->currency($toner->calculatedCost / $toner->yield, array('precision' => 4));
            }
        }

        $this->sendJson($replacementData);
    }

    public function saveClientPricingAction ()
    {
        $id                 = $this->_getParam('id', null);
        $clientSku          = $this->_getParam('clientSku', null);
        $clientCost         = $this->_getParam('cost', null);
        $replacementTonerId = $this->_getParam('replacementTonerId', null);

        $form = new Proposalgen_Form_ClientPricing_ClientToner();
        if ($form->isValid(array('clientSku' => $clientSku, 'cost' => $clientCost)))
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try
            {
                $clientTonerOrderMapper = Proposalgen_Model_Mapper_Client_Toner_Order::getInstance();
                $clientTonerOrder       = $clientTonerOrderMapper->find($id);
                if ($clientTonerOrder instanceof Proposalgen_Model_Client_Toner_Order)
                {
                    if (empty($clientSku))
                    {
                        $clientSku = new Zend_Db_Expr('NULL');
                    }

                    if (empty($clientCost))
                    {
                        $clientCost = new Zend_Db_Expr('NULL');
                    }

                    if (empty($replacementTonerId) || $replacementTonerId == 0)
                    {
                        $replacementTonerId = new Zend_Db_Expr('NULL');
                    }

                    $clientTonerOrder->populate(array('clientSku' => $clientSku, 'cost' => $clientCost, 'replacementTonerId' => $replacementTonerId));
                    $clientTonerOrderMapper->save($clientTonerOrder);
                    $db->commit();
                    $this->sendJson(array(
                        'success' => 'Successfully saved client toner attributes'
                    ));
                }
                else
                {
                    $this->sendJson(array(
                        'error' => 'failed to save client toner attributes'
                    ));
                }
            }
            catch (Exception $e)
            {
                $db->rollback();
                Tangent_Log::logException($e);
                $this->sendJson(array(
                    'error' => 'failed to save client toner attributes'
                ));
            }
        }
        else
        {
            $json = $form->getErrors();
            unset($json['tonerId']);
            unset($json['systemSku']);
            unset($json['dealerSku']);
            unset($json['tonerId']);
            unset($json['replacementTonerId']);
            if (count($json['clientSku']) == 0)
            {
                unset($json['clientSku']);
            }

            $this->sendJsonError($json);
        }
    }

    public function deleteClientPricingAction ()
    {
        $tonerId = $this->_getParam('deleteTonerId', null);
        if ($tonerId)
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try
            {
                $clientId                   = $this->_selectedClientId;
                $clientTonerAttributeMapper = Proposalgen_Model_Mapper_Client_Toner_Order::getInstance();
                $clientTonerAttribute       = $clientTonerAttributeMapper->find(array($tonerId, $clientId));
                if ($clientTonerAttribute instanceof Proposalgen_Model_Client_Toner_Order)
                {
                    $clientTonerAttributeMapper->delete($clientTonerAttribute);
                    $db->commit();
                    $this->sendJson(array('success' => 'Successfully deleted client toner attribute'));
                }
            }
            catch (Exception $e)
            {
                $db->rollback();
                Tangent_Log::logException($e);
            }
        }

        $this->sendJsonError('failed to delete client toner attribute');
    }

    /**
     * Deletes all client pricing
     */
    public function deleteAllClientPricingAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try
        {
            $clientId                   = $this->_selectedClientId;
            $clientTonerAttributeMapper = Proposalgen_Model_Mapper_Client_Toner_Order::getInstance();
            $clientTonerAttributeMapper->deleteAllForClient($clientId);
            $db->commit();
            $this->sendJson(array('success' => 'successfully deleted all client pricing'));
        }
        catch (Exception $e)
        {
            $db->rollback();
            Tangent_Log::logException($e);
            $this->sendJsonError('failed to delete all client toner attribute');
        }
    }

    /**
     * Upload client pricing into the system
     */
    public function uploadAction ()
    {
        $this->view->headTitle('Client Pricing');
        $this->view->headTitle('Upload CSV');

        $db            = Zend_Db_Table::getDefaultAdapter();
        $uploadService = new Proposalgen_Service_Import_Client_Pricing();
        $form          = $uploadService->getForm();

        if ($this->_request->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['goBack']))
            {
                $this->redirector('index');
            }
            else
            {
                if ($form->isValid($postData))
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
                                    $value = array_combine($uploadService->importHeaders, $value);
                                    if ($value !== false && trim($value[$uploadService::CSV_HEADER_ORDER_NUMBER]) != '')
                                    {
                                        $validData = $uploadService->processValidation($value);

                                        if (!isset($validData['error']) && !isset($data[$uploadService::CSV_HEADER_OEM_PRODUCT_CODE]))
                                        {
                                            $data[$validData[$uploadService::CSV_HEADER_OEM_PRODUCT_CODE]] = array(
                                                'oemSku'         => $validData[$uploadService::CSV_HEADER_OEM_PRODUCT_CODE],
                                                'dealerSku'      => $validData[$uploadService::CSV_HEADER_DEALER_PRODUCT_CODE],
                                                'clientSku'      => $validData[$uploadService::CSV_HEADER_CUSTOMER_PRODUCT_CODE],
                                                'cost'           => $validData[$uploadService::CSV_HEADER_UNIT_PRICE],
                                                'orderNumber'    => $validData[$uploadService::CSV_HEADER_ORDER_NUMBER],
                                                'dateOrdered'    => $validData[$uploadService::CSV_HEADER_DATE_ORDERED],
                                                'dateShipped'    => $validData[$uploadService::CSV_HEADER_DATE_SHIPPED],
                                                'dateReconciled' => $validData[$uploadService::CSV_HEADER_DATE_RECONCILED],
                                                'quantity'       => $validData[$uploadService::CSV_HEADER_QUANTITY],
                                            );
                                        }
                                    }
                                }


                                /**
                                 * Add to the database
                                 */
                                $clientTonerOrderMapper = Proposalgen_Model_Mapper_Client_Toner_Order::getInstance();
                                $tonerMapper            = Proposalgen_Model_Mapper_Toner::getInstance();

                                foreach ($data as $oemSku => $pricingData)
                                {
                                    /**
                                     * It makes sense to update rows that have the same oemSku and same orderNumber
                                     */
                                    $update           = true;
                                    $clientTonerOrder = $clientTonerOrderMapper->findTonerOrder($this->_selectedClientId, $pricingData['oemSku'], $pricingData['orderNumber']);
                                    if (!$clientTonerOrder instanceof Proposalgen_Model_Client_Toner_Order)
                                    {
                                        $update                     = false;
                                        $clientTonerOrder           = new Proposalgen_Model_Client_Toner_Order();
                                        $clientTonerOrder->clientId = $this->_selectedClientId;
                                    }

                                    /**
                                     * General updates
                                     */
                                    $clientTonerOrder->oemSku         = $pricingData['oemSku'];
                                    $clientTonerOrder->dealerSku      = $pricingData['dealerSku'];
                                    $clientTonerOrder->clientSku      = $pricingData['clientSku'];
                                    $clientTonerOrder->quantity       = $pricingData['quantity'];
                                    $clientTonerOrder->cost           = $pricingData['cost'];
                                    $clientTonerOrder->orderNumber    = $pricingData['orderNumber'];
                                    $clientTonerOrder->dateOrdered    = (new \Carbon\Carbon($pricingData['dateOrdered']))->toDateString();
                                    $clientTonerOrder->dateShipped    = (new \Carbon\Carbon($pricingData['dateShipped']))->toDateString();
                                    $clientTonerOrder->dateReconciled = (new \Carbon\Carbon($pricingData['dateReconciled']))->toDateString();

                                    /**
                                     * Map the toners
                                     */
                                    if ($clientTonerOrder->getToner() instanceof Proposalgen_Model_Toner)
                                    {
                                        if (strcasecmp($clientTonerOrder->getToner()->sku, $clientTonerOrder->oemSku) !== 0)
                                        {
                                            $toner = $tonerMapper->fetchBySku($clientTonerOrder->oemSku);
                                            if ($toner instanceof Proposalgen_Model_Toner)
                                            {
                                                $clientTonerOrder->tonerId = $toner->id;
                                                $clientTonerOrder->setToner($toner);
                                            }
                                            else
                                            {
                                                $clientTonerOrder->tonerId = new Zend_Db_Expr("NULL");
                                            }
                                        }

                                        // Make sure we haven't cleared out the mapping
                                        if ($clientTonerOrder->tonerId > 0)
                                        {
                                            if (!$clientTonerOrder->replacementTonerId > 0)
                                            {
                                                $replacementToner = $this->findTonerReplacement($clientTonerOrder->getToner());
                                                if ($replacementToner instanceof Proposalgen_Model_Toner)
                                                {
                                                    $clientTonerOrder->replacementTonerId = $replacementToner->id;
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $toner = $tonerMapper->fetchBySku($oemSku);
                                        if ($toner instanceof Proposalgen_Model_Toner)
                                        {
                                            $clientTonerOrder->tonerId = $toner->id;
                                            $clientTonerOrder->setToner($toner);

                                            if (!$clientTonerOrder->replacementTonerId > 0)
                                            {
                                                $replacementToner = $this->findTonerReplacement($toner);
                                                if ($replacementToner instanceof Proposalgen_Model_Toner)
                                                {
                                                    $clientTonerOrder->replacementTonerId = $replacementToner->id;
                                                }
                                            }
                                        }
                                    }

                                    /**
                                     * Save our work
                                     */
                                    if ($update)
                                    {
                                        $clientTonerOrderMapper->save($clientTonerOrder);
                                    }
                                    else
                                    {
                                        $clientTonerOrderMapper->insert($clientTonerOrder);
                                    }
                                }

                                $this->_flashMessenger->addMessage(array("success" => "Your pricing updates have been applied successfully."));
                                $db->commit();

                                $this->redirector('index');
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
                            $this->_flashMessenger->addMessage(array("error" => "An error has occurred during the update and your changes were not applied. Please review your file and try again. #" . Tangent_Log::getUniqueId()));
                        }
                        $uploadService->closeFiles();
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array("error" => "An error has occurred during the update and your changes were not applied. Please review your file and try again."));
                    }
                }
            }
        }
        $this->view->form = $form;
    }

    /**
     * Finds a suitable compatible replacement for a toner
     *
     * @param Proposalgen_Model_Toner $originalToner
     *
     * @return bool|Proposalgen_Model_Toner
     */
    public function findTonerReplacement ($originalToner)
    {
        $toner = false;
        if (!$originalToner->isCompatible())
        {
            $compatibleToners = $originalToner->getCompatibleToners($this->_selectedClientId, $this->_dealerId);
            if (count($compatibleToners) > 0)
            {
                $toner = $compatibleToners[0];
            }
        }

        return $toner;
    }
}