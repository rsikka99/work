<?php
use MPSToolbox\Legacy\Mappers\DealerMapper;
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\ProposalgenAclModel;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Services\TonerService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\ManufacturerDbTable;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerMasterDeviceAttributeMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerTonerAttributeMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceTonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\JitCompatibleMasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DealerMasterDeviceAttributeModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DealerTonerAttributeModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceTonerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\JitCompatibleMasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import\DeviceFeaturesImportService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import\DevicePricingImportService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import\TonerPricingImportService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import\TonerMatchupImportService;
use Tangent\Controller\Action;
use Tangent\Functions;

/**
 * Class Proposalgen_CostsController
 */
class Proposalgen_CostsController extends Action
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


    }

    /**
     * Checks to see if the current user is allowed to approve devices
     *
     * @return boolean
     */
    protected function _canApprove ()
    {
        return $this->view->IsAllowed(ProposalgenAclModel::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE, AppAclModel::PRIVILEGE_ADMIN);
    }

    /**
     * Handles bulk device pricing in a jqgrid (Such description. I know right?)
     */
    public function bulkdevicepricingAction ()
    {
        $this->_pageTitle        = array('Bulk Hardware/Pricing Updates');
        $this->view->parts_list  = array();
        $this->view->device_list = array();
        $db                      = Zend_Db_Table::getDefaultAdapter();

        // Fill manufacturers drop down
        $manufacturersTable            = new ManufacturerDbTable();
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
                                    $tonerAttribute = DealerTonerAttributeMapper::getInstance()->findTonerAttributeByTonerId($toner_id, $this->_dealerId);
                                    if ($tonerAttribute)
                                    {
                                        $tonerAttribute->cost = $price;

                                        DealerTonerAttributeMapper::getInstance()->save($tonerAttribute);
                                    }
                                    else
                                    {

                                        $tonerAttribute           = new DealerTonerAttributeModel();
                                        $tonerAttribute->dealerId = $this->_dealerId;
                                        $tonerAttribute->tonerId  = $toner_id;
                                        $tonerAttribute->cost     = $price;
                                        DealerTonerAttributeMapper::getInstance()->insert($tonerAttribute);

                                    }
                                }
                            }
                            else if (strstr($key, "txtNewDealerSku"))
                            {
                                $toner_id = str_replace("txtNewDealerSku", "", $key);
                                $newSku   = $formData ['txtNewDealerSku' . $toner_id];

                                if ($newSku != '')
                                {
                                    $tonerAttribute = DealerTonerAttributeMapper::getInstance()->findTonerAttributeByTonerId($toner_id, $this->_dealerId);
                                    if ($tonerAttribute)
                                    {

                                        $tonerAttribute->dealerSku = $newSku;

                                        DealerTonerAttributeMapper::getInstance()->save($tonerAttribute);
                                    }
                                    else
                                    {

                                        $tonerAttribute            = new DealerTonerAttributeModel();
                                        $tonerAttribute->dealerId  = $this->_dealerId;
                                        $tonerAttribute->tonerId   = $toner_id;
                                        $tonerAttribute->dealerSku = $newSku;
                                        DealerTonerAttributeMapper::getInstance()->insert($tonerAttribute);

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
                        /* @var $dealerMasterDeviceAttribute DealerMasterDeviceAttributeModel [] */

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
                                            $dealerMasterDeviceAttribute[$masterDeviceId]                   = new DealerMasterDeviceAttributeModel();
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
                                            $dealerMasterDeviceAttribute[$masterDeviceId]                   = new DealerMasterDeviceAttributeModel();
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
                            $masterAttribute = DealerMasterDeviceAttributeMapper::getInstance()->find(array($key, $this->_dealerId));
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
                                DealerMasterDeviceAttributeMapper::getInstance()->save($masterAttribute);
                            }
                            else
                            {
                                DealerMasterDeviceAttributeMapper::getInstance()->insert($value);
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
                \Tangent\Logger\Logger::logException($e);
                $this->_flashMessenger->addMessage(array("error" => "Error: The updates were not saved. Reference #: " . \Tangent\Logger\Logger::getUniqueId()));
            }
        }
    }

    /**
     * Handles device pricing in a csv format
     */
    public function bulkFileDeviceFeaturesAction ()
    {
        $db                    = Zend_Db_Table::getDefaultAdapter();
        $errorMessages         = array();
        $deviceFeaturesService = new DeviceFeaturesImportService();

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

                                $masterDevice = MasterDeviceMapper::getInstance()->find($validData['Master Printer ID']);

                                if ($masterDevice instanceof MasterDeviceModel)
                                {
                                    if ($this->_compareData($dataArray, $masterDevice))
                                    {
                                        MasterDeviceMapper::getInstance()->save($masterDevice);
                                    }

                                    $jitCompatibleMasterDevice                 = new JitCompatibleMasterDeviceModel();
                                    $jitCompatibleMasterDevice->dealerId       = $this->_dealerId;
                                    $jitCompatibleMasterDevice->masterDeviceId = $masterDevice->id;

                                    // If we are not a JIT compatible device
                                    if (!$masterDevice->isJitCompatible($this->_dealerId))
                                    {
                                        if ($validData[$deviceFeaturesService::DEVICE_FEATURES_JIT_COMPATIBILITY] === '1')
                                        {
                                            JitCompatibleMasterDeviceMapper::getInstance()->insert($jitCompatibleMasterDevice);
                                        }
                                    }
                                    else if ($validData[$deviceFeaturesService::DEVICE_FEATURES_JIT_COMPATIBILITY] === '0')
                                    {
                                        JitCompatibleMasterDeviceMapper::getInstance()->delete($jitCompatibleMasterDevice);
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
                        $this->_flashMessenger->addMessage(array("error" => "This file headers are incorrect please verify headers against export file."));
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

    /**
     * Handles bulk toner pricing updates in a csv format
     */
    public function bulkFileTonerPricingAction ()
    {
        $db                  = Zend_Db_Table::getDefaultAdapter();
        $errorMessages       = array();
        $tonerPricingService = new TonerPricingImportService();

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

                                $toner = TonerMapper::getInstance()->find($validData ['Toner ID']);
                                if ($toner instanceof TonerModel)
                                {
                                    $tonerAttribute = DealerTonerAttributeMapper::getInstance()->find(array($dataArray['tonerId'], $this->_dealerId));
                                    // Does the toner attribute exists ?
                                    if ($tonerAttribute instanceof DealerTonerAttributeModel)
                                    {
                                        // If cost && SKU are empty  or cost = 0 -> delete.
                                        // Delete
                                        if (empty($dataArray['cost']) && empty($dataArray['dealerSku']))
                                        {
                                            // If the attributes are empty after being found, delete them.
                                            DealerTonerAttributeMapper::getInstance()->delete($tonerAttribute);
                                        }
                                        else
                                        {
                                            if ($this->_compareData($dataArray, $tonerAttribute))
                                            {
                                                DealerTonerAttributeMapper::getInstance()->save($tonerAttribute);
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if ($dataArray['cost'] > 0 || !empty($dataArray['dealerSku']))
                                        {
                                            $tonerAttribute = new DealerTonerAttributeModel();
                                            $tonerAttribute->populate($dataArray);
                                            DealerTonerAttributeMapper::getInstance()->insert($tonerAttribute);
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
                        $this->_flashMessenger->addMessage(array("error" => "This file headers are incorrect please verify headers against export file."));
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

    /**
     * Handles bringing in new compatible toners
     */
    public function bulkFileTonerMatchupAction ()
    {
        $db             = Zend_Db_Table::getDefaultAdapter();
        $errorMessages  = array();
        $matchupService = new TonerMatchupImportService();

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
                            $tonerId   = false;

                            if (!isset($validData['error']))
                            {
                                // Did we find the compatible toner inside our system
                                if (isset($validData['parsedToners']['comp']['id']))
                                {
                                    $tonerId = $validData['parsedToners']['comp']['id'];
                                }
                                else
                                {
                                    if (isset($validData['parsedToners']['oem']['tonerColorId']))
                                    {
                                        $validData['parsedToners']['comp']['tonerColorId'] = $validData['parsedToners']['oem']['tonerColorId'];

                                        /**
                                         * Insert a new compatible toner.
                                         *
                                         * When inserting a new one we need to obfuscate the dealers cost
                                         */
                                        $toner                 = new TonerModel($validData['parsedToners']['comp']);
                                        $toner->cost           = TonerService::obfuscateTonerCost($toner->cost);
                                        $toner->isSystemDevice = $canApprove;
                                        $toner->userId         = $this->_userId;
                                        $tonerId               = TonerMapper::getInstance()->insert($toner);
                                    }
                                    else
                                    {
                                        $errorMessages [$lineCounter] = array('Toner Color' => 'No OEM Toner found and Toner Color not specified!');
                                    }
                                }

                                if ($tonerId !== false)
                                {


                                    /**
                                     * Dealer Toner Attributes
                                     */
                                    $dealerTonerAttribute = DealerTonerAttributeMapper::getInstance()->find(array($tonerId, $this->_dealerId));
                                    if ($dealerTonerAttribute instanceof DealerTonerAttributeModel)
                                    {
                                        $dealerTonerAttribute->cost = $validData['parsedToners']['comp']['cost'];
                                        DealerTonerAttributeMapper::getInstance()->save($dealerTonerAttribute);
                                    }
                                    else
                                    {
                                        $dealerTonerAttribute           = new DealerTonerAttributeModel();
                                        $dealerTonerAttribute->tonerId  = $tonerId;
                                        $dealerTonerAttribute->dealerId = $this->_dealerId;
                                        $dealerTonerAttribute->cost     = $validData['parsedToners']['comp']['cost'];

                                        if (strlen($validData['parsedToners']['comp']['dealerSku']) > 0)
                                        {
                                            $dealerTonerAttribute->dealerSku = $validData['parsedToners']['comp']['dealerSku'];
                                        }

                                        DealerTonerAttributeMapper::getInstance()->insert($dealerTonerAttribute);
                                    }

                                    /**
                                     * Have we found the OEM toner data based on the OEM Toner SKU?
                                     * Attempt to link device toners to existing toner id
                                     */
                                    if (isset($validData['parsedToners']['oem']['id']))
                                    {
                                        /**
                                         * Map our compatible to the same devices that our OEM toner is mapped to.
                                         */
                                        $existingDeviceToners = DeviceTonerMapper::getInstance()->fetchDeviceTonersByTonerId($validData['parsedToners']['oem']['id']);
                                        foreach ($existingDeviceToners as $existingDeviceToner)
                                        {
                                            if (!DeviceTonerMapper::getInstance()->find(array($tonerId, $existingDeviceToner->master_device_id)) instanceof DeviceTonerModel)
                                            {
                                                $deviceToner                   = new DeviceTonerModel();
                                                $deviceToner->toner_id         = $tonerId;
                                                $deviceToner->master_device_id = $existingDeviceToner->master_device_id;
                                                DeviceTonerMapper::getInstance()->insert($deviceToner);
                                            }
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
                        $this->_flashMessenger->addMessage(array("error" => "This file headers are incorrect please verify headers against export file."));
                    }
                }
                catch (Exception $e)
                {
                    $db->rollback();
                    \Tangent\Logger\Logger::logException($e);
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

    /**
     * Handles updating device pricing by csv
     */
    public function bulkFileDevicePricingAction ()
    {
        $db                   = Zend_Db_Table::getDefaultAdapter();
        $errorMessages        = array();
        $devicePricingService = new DevicePricingImportService();

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
                                $masterDevice = MasterDeviceMapper::getInstance()->find($masterDeviceId);
                                if ($masterDevice instanceof MasterDeviceModel)
                                {
                                    // Do we have the master device already in this dealer device table
                                    $masterDeviceAttribute = DealerMasterDeviceAttributeMapper::getInstance()->find(array($masterDeviceId, $this->_dealerId));
                                    if ($masterDeviceAttribute instanceof DealerMasterDeviceAttributeModel)
                                    {
                                        // If we have a master device attribute and the row is empty, delete the row
                                        if (empty($dataArray['laborCostPerPage']) && empty($dataArray['partsCostPerPage']))
                                        {
                                            DealerMasterDeviceAttributeMapper::getInstance()->delete($masterDeviceAttribute);
                                        }
                                        else
                                        {
                                            if ($this->_compareData($dataArray, $masterDeviceAttribute))
                                            {
                                                DealerMasterDeviceAttributeMapper::getInstance()->save($masterDeviceAttribute);
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if ($dataArray['laborCostPerPage'] > 0 || $dataArray['partsCostPerPage'] > 0)
                                        {
                                            $masterDeviceAttribute = new DealerMasterDeviceAttributeModel();
                                            $masterDeviceAttribute->populate($dataArray);
                                            DealerMasterDeviceAttributeMapper::getInstance()->insert($masterDeviceAttribute);
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
                        $this->_flashMessenger->addMessage(array("error" => "This file headers are incorrect please verify headers against export file."));
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
        $this->view->manufacturers = ManufacturerMapper::getInstance()->fetchAll();
        $this->view->errorMessages = $errorMessages;
    }

    /**
     * Handles checking to see if data has changed or if it should be nullified
     *
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

    /**
     * Exports pricing for various items.
     *
     * @throws Exception
     */
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
                $devicePricingService = new DevicePricingImportService();
                $fieldTitles          = $devicePricingService->csvHeaders;
                $fieldList            = MasterDeviceMapper::getInstance()->getPrinterPricingForExport($manufacturerId, $this->_dealerId);
            }
            else if ($importType == 'features')
            {
                $filename             = "system_printer_features_" . date('m_d_Y') . ".csv";
                $deviceFeatureService = new DeviceFeaturesImportService();
                $fieldTitles          = $deviceFeatureService->csvHeaders;
                $fieldList            = MasterDeviceMapper::getInstance()->getPrinterFeaturesForExport($this->_dealerId);
            }
            else if ($importType == 'toner')
            {
                $manufacturerId      = $this->_getParam('manufacturer', false);
                $filename            = "system_toner_pricing_" . date('m_d_Y') . ".csv";
                $tonerPricingService = new TonerPricingImportService();
                $fieldTitles         = $tonerPricingService->csvHeaders;
                $fieldList           = TonerMapper::getInstance()->getTonerPricingForExport($manufacturerId, $this->_dealerId);
            }
            else if ($importType == "matchup")
            {
                $filename            = "system_toner_matchup.csv";
                $tonerMatchupService = new TonerMatchupImportService();
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

        Functions::setHeadersForDownload($filename);

        $this->view->fieldTitles = implode(",", $fieldTitles);
        $this->view->fieldList   = $newFieldList;
    }
}