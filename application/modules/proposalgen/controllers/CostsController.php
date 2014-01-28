<?php

/**
 * Class Proposalgen_CostsController
 */
class Proposalgen_CostsController extends Tangent_Controller_Action
{
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
     * @var Zend_Config
     */
    protected $_config;

    /**
     * @var int
     */
    protected $_dealerId;

    /**
     * @var int
     */
    protected $_userId;

    /**
     * @var int
     */
    protected $_selectedClientId;

    public function init ()
    {
        $this->_mpsSession = new Zend_Session_Namespace('mps-tools');
        $this->_identity   = Zend_Auth::getInstance()->getIdentity();
        $this->_dealerId   = $this->_identity->dealerId;
        $this->_config     = Zend_Registry::get('config');

        /**
         * FIXME: Is this used anymore?
         */
        $this->view->privilege = array('System Admin');

        /**
         * Old variables
         */
        $this->view->app     = $this->_config->app;
        $this->view->user    = $this->_identity;
        $this->view->user_id = $this->_identity->id;
        $this->_userId       = $this->_identity->id;
        $this->_dealerId     = $this->_identity->dealerId;

        if (isset($this->_mpsSession->selectedClientId))
        {
            $client = Quotegen_Model_Mapper_Client::getInstance()->find($this->_mpsSession->selectedClientId);
            // Make sure the selected client is ours!
            if ($client && $client->dealerId == Zend_Auth::getInstance()->getIdentity()->dealerId)
            {
                $this->_selectedClientId      = $this->_mpsSession->selectedClientId;
                $this->view->selectedClientId = $this->_selectedClientId;
            }
            else
            {
                $this->_flashMessenger->addMessage(array(
                    "error" => "You must select a client before you can access this."
                ));
                $this->redirector('index', 'index', 'default');
            }
        }
        else
        {
            $this->_flashMessenger->addMessage(array(
                "error" => "You must select a client before you can access this."
            ));
            $this->redirector('index', 'index', 'default');
        }
    }

    /**
     * Checks to see if the current user is allowed to approve devices
     *
     * @return boolean
     */
    protected function _canApprove ()
    {
        return $this->view->IsAllowed(Proposalgen_Model_Acl::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE, Application_Model_Acl::PRIVILEGE_ADMIN);
    }

    public function indexAction ()
    {

        $this->view->clientTonersForm = new Proposalgen_Form_Costs_ClientToner();
    }

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
        );
        $jqGridService->setValidSortColumns($sortColumns);
        if ($jqGridService->sortingIsValid())
        {
            $clientTonerAttributeMapper = Proposalgen_Model_Mapper_Client_Toner_Attribute::getInstance();

            $jqGridService->parseJQGridPagingRequest($jqGridParameters);

            $sortOrder = array();
            if ($jqGridService->hasColumns())
            {
                $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
            }

            $jqGridService->setRecordCount($clientTonerAttributeMapper->fetchAllForClient($this->_selectedClientId, $this->_dealerId, $sortOrder, 10000, 0, $filter, $criteria, true));

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

            $jqGridService->setRows($clientTonerAttributeMapper->fetchAllForClient($this->_selectedClientId, $this->_dealerId, $sortOrder, $jqGridService->getRecordsPerPage(), $startRecord, $filter, $criteria));

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

    public function saveClientPricingAction ()
    {
        $tonerId    = $this->_getParam('tonerId', null);
        $clientId   = $this->_selectedClientId;
        $clientSku  = $this->_getParam('clientSku', null);
        $clientCost = $this->_getParam('cost', null);

        $form = new Proposalgen_Form_Costs_ClientToner();
        if ($form->isValid(array('clientSku' => $clientSku, 'cost' => $clientCost)))
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try
            {
                $clientTonerAttributeMapper = Proposalgen_Model_Mapper_Client_Toner_Attribute::getInstance();
                $clientTonerAttribute       = $clientTonerAttributeMapper->find(array($tonerId, $clientId));
                if ($clientTonerAttribute instanceof Proposalgen_Model_Client_Toner_Attribute)
                {
                    if (empty($clientSku))
                    {
                        $clientSku = new Zend_Db_Expr('NULL');
                    }
                    if (empty($clientCost))
                    {
                        $clientCost = new Zend_Db_Expr('NULL');
                    }
                    $clientTonerAttribute->populate(array('clientSku' => $clientSku, 'cost' => $clientCost));
                    $clientTonerAttributeMapper->save($clientTonerAttribute);
                    $db->commit();
                    $this->sendJson(array(
                        'success' => 'Successfully saved client toner attributes'
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
                $clientTonerAttributeMapper = Proposalgen_Model_Mapper_Client_Toner_Attribute::getInstance();
                $clientTonerAttribute       = $clientTonerAttributeMapper->find(array($tonerId, $clientId));
                if ($clientTonerAttribute instanceof Proposalgen_Model_Client_Toner_Attribute)
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

    public function deleteAllClientPricingAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try
        {
            $clientId                   = $this->_selectedClientId;
            $clientTonerAttributeMapper = Proposalgen_Model_Mapper_Client_Toner_Attribute::getInstance();
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

    public function bulkdevicepricingAction ()
    {
        $this->view->headTitle('Bulk Hardware/Pricing Updates');
        $this->view->parts_list  = array();
        $this->view->device_list = array();
        $db                      = Zend_Db_Table::getDefaultAdapter();

        $dealer         = Admin_Model_Mapper_Dealer::getInstance()->find($this->_dealerId);
        $dealerSettings = $dealer->getDealerSettings();


        $this->view->default_labor = $dealerSettings->getAssessmentSettings()->laborCostPerPage;
        $this->view->default_parts = $dealerSettings->getAssessmentSettings()->partsCostPerPage;

        // Fill manufacturers drop down
        $manufacturersTable            = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers                 = $manufacturersTable->fetchAll('isDeleted = false', 'fullName');
        $this->view->manufacturer_list = $manufacturers;

        if ($this->_request->isPost())
        {
            $hasErrors = 0;
            $formData  = $this->_request->getPost();

            // Check post back for update
            $db->beginTransaction();
            try
            {

                // Return current drop down states
                // $this->view->company_filter = $formData ['company_filter'];
                $this->view->pricing_filter  = $formData ['pricing_filter'];
                $this->view->search_filter   = $formData ['criteria_filter'];
                $this->view->search_criteria = $formData ['txtCriteria'];
                $this->view->repop_page      = $formData ["hdnPage"];

                if ($formData ['hdnMode'] == "update")
                {
                    // $dealer_company_id = $formData ['company_filter'];
                    // $dealer_company_id = 1;
                    // Save Master Company Pricing Changes
                    if ($formData ['pricing_filter'] == 'toner')
                    {
                        // Loop through $result
                        foreach ($formData as $key => $value)
                        {
                            if (strstr($key, "txtTonerPrice"))
                            {
                                $toner_id = str_replace("txtTonerPrice", "", $key);
                                $price    = $formData ['txtTonerPrice' . $toner_id];
                                // check if new price is populated.
                                if ($price == "0")
                                {
                                    $hasErrors = 1;
                                    $this->_flashMessenger->addMessage(array(
                                        "error" => "All values must be greater than 0. Please correct it and try again."
                                    ));
                                    break;
                                }
                                else if ($price != '' && !is_numeric($price))
                                {
                                    $hasErrors = 1;
                                    $this->_flashMessenger->addMessage(array(
                                        "error" => "All values must be numeric. Please correct it and try again."
                                    ));
                                    break;
                                }
                                else if ($price != '' && $price > 0)
                                {
                                    $tonerAttribute = Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->findTonerAttributeByTonerId($toner_id, $this->_dealerId);
                                    if ($tonerAttribute)
                                    {
                                        $tonerAttribute->cost = $price;

                                        Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->save($tonerAttribute);
                                    }
                                    else
                                    {

                                        $tonerAttribute           = new Proposalgen_Model_Dealer_Toner_Attribute();
                                        $tonerAttribute->dealerId = $this->_dealerId;
                                        $tonerAttribute->tonerId  = $toner_id;
                                        $tonerAttribute->cost     = $price;
                                        Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->insert($tonerAttribute);

                                    }
                                }
                            }
                            else if (strstr($key, "txtNewDealerSku"))
                            {
                                $toner_id = str_replace("txtNewDealerSku", "", $key);
                                $newSku   = $formData ['txtNewDealerSku' . $toner_id];

                                if ($newSku != '')
                                {
                                    $tonerAttribute = Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->findTonerAttributeByTonerId($toner_id, $this->_dealerId);
                                    if ($tonerAttribute)
                                    {

                                        $tonerAttribute->dealerSku = $newSku;

                                        Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->save($tonerAttribute);
                                    }
                                    else
                                    {

                                        $tonerAttribute            = new Proposalgen_Model_Dealer_Toner_Attribute();
                                        $tonerAttribute->dealerId  = $this->_dealerId;
                                        $tonerAttribute->tonerId   = $toner_id;
                                        $tonerAttribute->dealerSku = $newSku;
                                        Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->insert($tonerAttribute);

                                    }
                                }
                            }
                        }

                        if ($hasErrors == 0)
                        {
                            $this->_flashMessenger->addMessage(array(
                                "success" => "The toner pricing updates have been applied successfully."
                            ));
                        }
                        else
                        {
                            $db->rollBack();

                            // Build repopulate values
                            $repopulateArray = '';
                            foreach ($formData as $key => $value)
                            {
                                if (strstr($key, "txtTonerPrice"))
                                {
                                    $toner_id = str_replace("txtTonerPrice", "", $key);
                                    $price    = $formData ['txtTonerPrice' . $toner_id];

                                    // Build repopulate array
                                    if ($repopulateArray != '')
                                    {
                                        $repopulateArray .= ',';
                                    }
                                    $repopulateArray .= $toner_id . ':' . $price;
                                }
                            }
                            $this->view->repop_array = $repopulateArray;
                        }
                    }
                    else
                    {
                        /* @var $dealerMasterDeviceAttribute Proposalgen_Model_Dealer_Master_Device_Attribute [] */

                        $dealerMasterDeviceAttribute = array();
                        $dealerId                    = $this->_dealerId;
                        foreach ($formData as $key => $value)
                        {

                            // This can either be partsCostPerPage or laborCostPerPage.
                            // Regardless we can get the mater device it from the end of the element

                            // Find out the cost we are dealing with
                            if (strstr($key, "laborCostPerPage"))
                            {
                                $masterDeviceId = str_replace("laborCostPerPage", "", $key);
                                $price          = $value;

                                if ($price != '' && !is_numeric($price))
                                {
                                    $this->_flashMessenger->addMessage(array("error" => "All values must be numeric. Please correct it and try again."));
                                    break;
                                }
                                else if ($price != '')
                                {
                                    if ($price == 0)
                                    {
                                        $price = new Zend_Db_Expr('NULL');
                                    }

                                    if ($value > 0)
                                    {
                                        if (isset($dealerMasterDeviceAttribute [$masterDeviceId]))
                                        {
                                            $dealerMasterDeviceAttribute[$masterDeviceId]->laborCostPerPage = $price;
                                        }
                                        else
                                        {
                                            $dealerMasterDeviceAttribute[$masterDeviceId]                   = new Proposalgen_Model_Dealer_Master_Device_Attribute();
                                            $dealerMasterDeviceAttribute[$masterDeviceId]->laborCostPerPage = $price;
                                            $dealerMasterDeviceAttribute[$masterDeviceId]->dealerId         = $dealerId;
                                            $dealerMasterDeviceAttribute[$masterDeviceId]->masterDeviceId   = $masterDeviceId;
                                        }
                                    }

                                }
                            }
                            else if (strstr($key, "partsCostPerPage"))
                            {
                                $masterDeviceId = str_replace("partsCostPerPage", "", $key);
                                $price          = $value;

                                if ($price != '' && !is_numeric($price))
                                {
                                    $this->_flashMessenger->addMessage(array("error" => "All values must be numeric. Please correct it and try again."));
                                    break;
                                }
                                else if ($price != '')
                                {
                                    if ($price == 0)
                                    {
                                        $price = new Zend_Db_Expr('NULL');
                                    }
                                    if ($value > 0)
                                    {
                                        if (isset($dealerMasterDeviceAttribute [$masterDeviceId]))
                                        {
                                            $dealerMasterDeviceAttribute[$masterDeviceId]->partsCostPerPage = $price;
                                        }
                                        else
                                        {
                                            $dealerMasterDeviceAttribute[$masterDeviceId]                   = new Proposalgen_Model_Dealer_Master_Device_Attribute();
                                            $dealerMasterDeviceAttribute[$masterDeviceId]->partsCostPerPage = $price;
                                            $dealerMasterDeviceAttribute[$masterDeviceId]->dealerId         = $dealerId;
                                            $dealerMasterDeviceAttribute[$masterDeviceId]->masterDeviceId   = $masterDeviceId;
                                        }
                                    }
                                }

                            }
                        }

                        foreach ($dealerMasterDeviceAttribute as $key => $value)
                        {
                            $masterAttribute = Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->find(array($key, $this->_dealerId));
                            if ($masterAttribute)
                            {
                                if (isset($value->laborCostPerPage))
                                {
                                    $masterAttribute->laborCostPerPage = $value->laborCostPerPage;
                                }
                                if (isset($value->partsCostPerPage))
                                {
                                    $masterAttribute->partsCostPerPage = $value->partsCostPerPage;
                                }
                                Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->save($masterAttribute);
                            }
                            else
                            {
                                Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->insert($value);
                            }
                        }

                        if ($hasErrors == 0)
                        {
                            $this->_flashMessenger->addMessage(array(
                                "success" => "The device pricing updates have been applied successfully."
                            ));
                        }
                        else
                        {
                            $db->rollBack();
                            // Build repopulate values
                            $repopulateArray = '';
                            foreach ($formData as $key => $value)
                            {
                                if (strstr($key, "txtDevicePrice"))
                                {
                                    $master_device_id = str_replace("txtDevicePrice", "", $key);
                                    $price            = $formData ['txtDevicePrice' . $master_device_id];

                                    // Build repopulate array
                                    if ($repopulateArray != '')
                                    {
                                        $repopulateArray .= ',';
                                    }
                                    $repopulateArray .= $master_device_id . ':' . $price;
                                }
                            }
                            $this->view->repop_array = $repopulateArray;
                        }
                    }
                }

                $db->commit();
            }
            catch (Exception $e)
            {
                $db->rollback();
                Tangent_Log::logException($e);
                $this->_flashMessenger->addMessage(array("error" => "Error: The updates were not saved. Reference #: " . Tangent_Log::getUniqueId()));
            }
        }
    }

    public function bulkFileDeviceFeaturesAction ()
    {
        $db                    = Zend_Db_Table::getDefaultAdapter();
        $errorMessages         = array();
        $deviceFeaturesService = new Proposalgen_Service_Import_Device_Features();

        $this->view->canApprove = $this->_canApprove();

        if ($this->_request->isPost())
        {
            if (!is_array($deviceFeaturesService->getValidFile($this->_config)) && $this->view->canApprove)
            {
                $db->beginTransaction();
                try
                {
                    if ($deviceFeaturesService->validatedHeaders())
                    {
                        $lineCounter = 2;
                        while (($value = fgetcsv($deviceFeaturesService->importFile)) !== false)
                        {
                            $value     = array_combine($deviceFeaturesService->importHeaders, $value);
                            $validData = $deviceFeaturesService->processValidation($value);

                            if (!isset($validData['error']))
                            {
                                $dataArray = array(
                                    'isDuplex'           => $validData[$deviceFeaturesService::DEVICE_FEATURES_DUPLEX],
                                    'isCopier'           => $validData[$deviceFeaturesService::DEVICE_FEATURES_SCAN],
                                    'reportsTonerLevels' => $validData[$deviceFeaturesService::DEVICE_FEATURES_REPORTS_TONER_LEVELS],
                                    'ppmBlack'           => $validData[$deviceFeaturesService::DEVICE_FEATURES_PPM_MONOCHROME],
                                    'ppmColor'           => $validData[$deviceFeaturesService::DEVICE_FEATURES_PPM_COLOR],
                                    'wattsPowerNormal'   => $validData[$deviceFeaturesService::DEVICE_FEATURES_OPERATING_WATTAGE],
                                    'wattsPowerIdle'     => $validData[$deviceFeaturesService::DEVICE_FEATURES_IDLE_WATTAGE],
                                );

                                $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($validData['Master Printer ID']);

                                if ($masterDevice instanceof Proposalgen_Model_MasterDevice)
                                {
                                    if ($this->_compareData($dataArray, $masterDevice))
                                    {
                                        Proposalgen_Model_Mapper_MasterDevice::getInstance()->save($masterDevice);
                                    }

                                    $jitCompatibleMasterDevice                 = new Proposalgen_Model_JitCompatibleMasterDevice();
                                    $jitCompatibleMasterDevice->dealerId       = $this->_dealerId;
                                    $jitCompatibleMasterDevice->masterDeviceId = $masterDevice->id;

                                    // If we are not a JIT compatible device
                                    if (!$masterDevice->isJitCompatible($this->_dealerId))
                                    {
                                        if ($validData[$deviceFeaturesService::DEVICE_FEATURES_JIT_COMPATIBILITY] === '1')
                                        {
                                            Proposalgen_Model_Mapper_JitCompatibleMasterDevice::getInstance()->insert($jitCompatibleMasterDevice);
                                        }
                                    }
                                    else if ($validData[$deviceFeaturesService::DEVICE_FEATURES_JIT_COMPATIBILITY] === '0')
                                    {
                                        Proposalgen_Model_Mapper_JitCompatibleMasterDevice::getInstance()->delete($jitCompatibleMasterDevice);
                                    }
                                }
                            }
                            else
                            {
                                $errorMessages [$lineCounter] = $validData['error'];
                            }
                            $lineCounter++;
                        }
                        $this->_flashMessenger->addMessage(array("success" => "Your pricing updates have been applied successfully."));
                        $db->commit();
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array("error" => "This file headers are in-correct please verify headers against export file."));
                    }
                }
                catch (Exception $e)
                {
                    $db->rollback();
                    $this->_flashMessenger->addMessage(array("error" => "An error has occurred during the update and your changes were not applied. Please review your file and try again."));
                }
                $deviceFeaturesService->closeFiles();
            }
            else
            {
                $this->_flashMessenger->addMessage(array("error" => "An error has occurred during the update and your changes were not applied. Please review your file and try again."));
            }
        }
        $this->view->errorMessages = $errorMessages;
    }

    public function bulkFileTonerPricingAction ()
    {
        $db                  = Zend_Db_Table::getDefaultAdapter();
        $errorMessages       = array();
        $tonerPricingService = new Proposalgen_Service_Import_Toner_Pricing();

        if ($this->_request->isPost())
        {
            if (!is_array($tonerPricingService->getValidFile($this->_config)))
            {
                $db->beginTransaction();
                try
                {
                    if ($tonerPricingService->validatedHeaders())
                    {
                        $lineCounter = 2;
                        while (($value = fgetcsv($tonerPricingService->importFile)) !== false)
                        {
                            $value     = array_combine($tonerPricingService->importHeaders, $value);
                            $validData = $tonerPricingService->processValidation($value);

                            if (!isset($validData['error']))
                            {
                                $dataArray = array(
                                    'tonerId'   => $validData[$tonerPricingService::TONER_PRICING_TONER_ID],
                                    'dealerSku' => $validData[$tonerPricingService::TONER_PRICING_DEALER_SKU],
                                    'cost'      => $validData[$tonerPricingService::TONER_PRICING_NEW_PRICE],
                                    'dealerId'  => $this->_dealerId,
                                );

                                $toner = Proposalgen_Model_Mapper_Toner::getInstance()->find($validData ['Toner ID']);
                                if ($toner instanceof Proposalgen_Model_Toner)
                                {
                                    $tonerAttribute = Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->find(array($dataArray['tonerId'], $this->_dealerId));
                                    // Does the toner attribute exists ?
                                    if ($tonerAttribute instanceof Proposalgen_Model_Dealer_Toner_Attribute)
                                    {
                                        // If cost && SKU are empty  or cost = 0 -> delete.
                                        // Delete
                                        if (empty($importCost) && empty($importDealerSku))
                                        {
                                            // If the attributes are empty after being found, delete them.
                                            Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->delete($tonerAttribute);
                                        }
                                        else
                                        {
                                            if ($this->_compareData($dataArray, $tonerAttribute))
                                            {
                                                Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->save($tonerAttribute);
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if ($dataArray['cost'] > 0 || !empty($importDealerSku))
                                        {
                                            $tonerAttribute = new Proposalgen_Model_Dealer_Toner_Attribute();
                                            $tonerAttribute->populate($dataArray);
                                            Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->insert($tonerAttribute);
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $errorMessages [$lineCounter] = $validData['error'];
                            }
                            $lineCounter++;
                        }
                        $this->_flashMessenger->addMessage(array("success" => "Your pricing updates have been applied successfully."));
                        $db->commit();
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array("error" => "This file headers are in-correct please verify headers against export file."));
                    }
                }
                catch (Exception $e)
                {
                    $db->rollback();
                    $this->_flashMessenger->addMessage(array("error" => "An error has occurred during the update and your changes were not applied. Please review your file and try again."));
                }
                $tonerPricingService->closeFiles();
            }
            else
            {
                $this->_flashMessenger->addMessage(array("error" => "An error has occurred during the update and your changes were not applied. Please review your file and try again."));
            }
        }

        $this->view->canApprove    = $this->_canApprove();
        $this->view->errorMessages = $errorMessages;
    }

    public function bulkFileTonerMatchupAction ()
    {
        $db             = Zend_Db_Table::getDefaultAdapter();
        $errorMessages  = array();
        $matchupService = new Proposalgen_Service_Import_Toner_Matchup();

        $canApprove             = $this->_canApprove();
        $this->view->canApprove = $canApprove;

        if ($this->_request->isPost())
        {
            if (!is_array($matchupService->getValidFile($this->_config)))
            {
                $db->beginTransaction();
                try
                {
                    if ($matchupService->validatedHeaders())
                    {
                        $lineCounter = 2;
                        while (($value = fgetcsv($matchupService->importFile)) !== false)
                        {
                            $value     = array_combine($matchupService->importHeaders, $value);
                            $validData = $matchupService->processValidation($value);

                            if (!isset($validData['error']))
                            {
                                // Did we find the compatible toner inside our system
                                if (isset($validData['parsedToners']['comp']['id']))
                                {
                                    $tonerId = $validData['parsedToners']['comp']['id'];
                                }
                                else
                                {
                                    // Insert
                                    // If we insert then we want to use the dealer price as the base price
                                    $toner                 = new Proposalgen_Model_Toner($validData['parsedToners']['comp']);
                                    $toner->cost           = Proposalgen_Service_Toner::obfuscateTonerCost($toner->cost);
                                    $toner->isSystemDevice = $canApprove;
                                    $toner->userId         = $this->_userId;
                                    $tonerId               = Proposalgen_Model_Mapper_Toner::getInstance()->insert($toner);
                                }

                                $dealerTonerAttribute = Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->find(array($tonerId, $this->_dealerId));
                                if ($dealerTonerAttribute instanceof Proposalgen_Model_Dealer_Toner_Attribute)
                                {
                                    // Update
                                    $dealerTonerAttribute->cost = $validData['parsedToners']['comp']['cost'];
                                    Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->save($dealerTonerAttribute);
                                }
                                else
                                {
                                    // Insert
                                    $dealerTonerAttribute            = new Proposalgen_Model_Dealer_Toner_Attribute();
                                    $dealerTonerAttribute->tonerId   = $tonerId;
                                    $dealerTonerAttribute->dealerId  = $this->_dealerId;
                                    $dealerTonerAttribute->cost      = $validData['parsedToners']['comp']['cost'];
                                    $dealerTonerAttribute->dealerSku = $validData['parsedToners']['comp']['dealerSku'];

                                    Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->insert($dealerTonerAttribute);
                                }

                                // Have we found the OEM toner data based on the OEM Toner SKU?
                                // Attempt to link device toners to existing toner id
                                if (isset($validData['parsedToners']['oem']['id']))
                                {
                                    // Insert
                                    // Find the master devices that we have assigned for this toner.
                                    $existingDeviceToners = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchDeviceTonersByTonerId($validData['parsedToners']['oem']['id']);
                                    foreach ($existingDeviceToners as $existingDeviceToner)
                                    {
                                        if (!Proposalgen_Model_Mapper_DeviceToner::getInstance()->find(array($tonerId, $existingDeviceToner->master_device_id)) instanceof Proposalgen_Model_DeviceToner)
                                        {
                                            $deviceToner                   = new Proposalgen_Model_DeviceToner();
                                            $deviceToner->toner_id         = $tonerId;
                                            $deviceToner->master_device_id = $existingDeviceToner->master_device_id;
                                            Proposalgen_Model_Mapper_DeviceToner::getInstance()->insert($deviceToner);
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $errorMessages [$lineCounter] = $validData['error'];
                            }
                            $lineCounter++;
                        }
                        $this->_flashMessenger->addMessage(array("success" => "Your pricing updates have been applied successfully."));
                        $db->commit();
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array("error" => "This file headers are in-correct please verify headers against export file."));
                    }
                }
                catch (Exception $e)
                {
                    $db->rollback();
                    $this->_flashMessenger->addMessage(array("error" => "An error has occurred during the update and your changes were not applied. Please review your file and try again."));
                }
                $matchupService->closeFiles();
            }
            else
            {
                $this->_flashMessenger->addMessage(array("error" => "An error has occurred during the update and your changes were not applied. Please review your file and try again."));
            }
        }
        $this->view->errorMessages = $errorMessages;
    }

    public function bulkFileDevicePricingAction ()
    {
        $db                   = Zend_Db_Table::getDefaultAdapter();
        $errorMessages        = array();
        $devicePricingService = new Proposalgen_Service_Import_Device_Pricing();

        if ($this->_request->isPost())
        {
            if (!is_array($devicePricingService->getValidFile($this->_config)))
            {
                $db->beginTransaction();
                try
                {
                    if ($devicePricingService->validatedHeaders())
                    {
                        $lineCounter = 2;
                        while (($value = fgetcsv($devicePricingService->importFile)) !== false)
                        {
                            $value     = array_combine($devicePricingService->importHeaders, $value);
                            $validData = $devicePricingService->processValidation($value);

                            if (!isset($validData['error']))
                            {
                                $masterDeviceId = $validData ['Master Printer ID'];

                                $dataArray = array(
                                    'masterDeviceId'   => $masterDeviceId,
                                    'dealerId'         => $this->_dealerId,
                                    'laborCostPerPage' => $validData[$devicePricingService::DEVICE_PRICING_LABOR_CPP],
                                    'partsCostPerPage' => $validData[$devicePricingService::DEVICE_PRICING_PARTS_CPP],
                                );

                                // Does the master device exist in our database?
                                $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($masterDeviceId);
                                if ($masterDevice instanceof Proposalgen_Model_MasterDevice)
                                {
                                    // Do we have the master device already in this dealer device table
                                    $masterDeviceAttribute = Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->find(array($masterDeviceId, $this->_dealerId));
                                    if ($masterDeviceAttribute instanceof Proposalgen_Model_Dealer_Master_Device_Attribute)
                                    {
                                        // If we have a master device attribute and the row is empty, delete the row
                                        if (empty($dataArray['laborCostPerPage']) && empty($dataArray['partsCostPerPage']))
                                        {
                                            Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->delete($masterDeviceAttribute);
                                        }
                                        else
                                        {
                                            if ($this->_compareData($dataArray, $masterDeviceAttribute))
                                            {
                                                Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->save($masterDeviceAttribute);
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if ($dataArray['laborCostPerPage'] > 0 || $dataArray['partsCostPerPage'] > 0)
                                        {
                                            $masterDeviceAttribute = new Proposalgen_Model_Dealer_Master_Device_Attribute();
                                            $masterDeviceAttribute->populate($dataArray);
                                            Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->insert($masterDeviceAttribute);
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $errorMessages [$lineCounter] = $validData['error'];
                            }
                            $lineCounter++;
                        }
                        $this->_flashMessenger->addMessage(array("success" => "Your pricing updates have been applied successfully."));
                        $db->commit();
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array("error" => "This file headers are in-correct please verify headers against export file."));
                    }
                }
                catch (Exception $e)
                {
                    $db->rollback();
                    $this->_flashMessenger->addMessage(array("error" => "An error has occurred during the update and your changes were not applied. Please review your file and try again."));
                }
                $devicePricingService->closeFiles();
            }
            else
            {
                $this->_flashMessenger->addMessage(array("error" => "An error has occurred during the update and your changes were not applied. Please review your file and try again."));
            }
        }
        $this->view->canApprove    = $this->_canApprove();
        $this->view->manufacturers = Proposalgen_Model_Mapper_Manufacturer::getInstance()->fetchAll();
        $this->view->errorMessages = $errorMessages;
    }

    /**
     * @param $importData  array
     * @param $object      mixed
     *
     * @return bool
     */
    private function _compareData ($importData, &$object)
    {
        $hasChanged = false;

        foreach ($importData as $key => $value)
        {
            if ($object->$key != $value)
            {
                if (empty($value))
                {
                    $value = new Zend_Db_Expr('NULL');
                }

                $object->$key = $value;
                $hasChanged   = true;
            }
        }

        return $hasChanged;
    }

    public function exportpricingAction ()
    {
        $this->_helper->layout->disableLayout();

        $importType   = $this->_getParam('type', false);
        $fieldTitles  = array();
        $fieldList    = array();
        $filename     = "";
        $newFieldList = "";

        try
        {
            if ($importType == 'printer')
            {
                $manufacturerId       = $this->_getParam('manufacturer', false);
                $filename             = "system_printer_pricing_" . date('m_d_Y') . ".csv";
                $devicePricingService = new Proposalgen_Service_Import_Device_Pricing();
                $fieldTitles          = $devicePricingService->csvHeaders;
                $fieldList            = Proposalgen_Model_Mapper_MasterDevice::getInstance()->getPrinterPricingForExport($manufacturerId, $this->_dealerId);
            }
            else if ($importType == 'features')
            {
                $filename             = "system_printer_features_" . date('m_d_Y') . ".csv";
                $deviceFeatureService = new Proposalgen_Service_Import_Device_Features();
                $fieldTitles          = $deviceFeatureService->csvHeaders;
                $fieldList            = Proposalgen_Model_Mapper_MasterDevice::getInstance()->getPrinterFeaturesForExport($this->_dealerId);
            }
            else if ($importType == 'toner')
            {
                $manufacturerId      = $this->_getParam('manufacturer', false);
                $filename            = "system_toner_pricing_" . date('m_d_Y') . ".csv";
                $tonerPricingService = new Proposalgen_Service_Import_Toner_Pricing();
                $fieldTitles         = $tonerPricingService->csvHeaders;
                $fieldList           = Proposalgen_Model_Mapper_Toner::getInstance()->getTonerPricingForExport($manufacturerId, $this->_dealerId);
            }
            else if ($importType == "matchup")
            {
                $filename            = "system_toner_matchup.csv";
                $tonerMatchupService = new Proposalgen_Service_Import_Toner_Matchup();
                $fieldTitles         = $tonerMatchupService->csvHeaders;
                $fieldList           = array();
            }
        }
        catch (Exception $e)
        {
            throw new Exception("CSV File could not be opened/written for export.", 0, $e);
        }

        foreach ($fieldList as $row)
        {
            $newFieldList .= implode(",", $row);
            $newFieldList .= "\n";
        }

        Tangent_Functions::setHeadersForDownload($filename);

        $this->view->fieldTitles = implode(",", $fieldTitles);
        $this->view->fieldList   = $newFieldList;
    }
}