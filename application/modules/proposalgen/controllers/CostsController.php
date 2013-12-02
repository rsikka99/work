<?php
/**
 * Class Proposalgen_CostsController
 */
class Proposalgen_CostsController extends Tangent_Controller_Action
{
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
        $this->_identity = Zend_Auth::getInstance()->getIdentity();
        $this->_dealerId = $this->_identity->dealerId;
        $this->_config   = Zend_Registry::get('config');

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

    public function indexAction ()
    {
        // Nothing to do here
    }

    public function bulkdevicepricingAction ()
    {
        $this->view->title       = "Update Pricing";
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
            $passvalid = 0;
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
                                    $passvalid = 1;
                                    $this->_flashMessenger->addMessage(array(
                                                                            "error" => "All values must be greater than 0. Please correct it and try again."
                                                                       ));
                                    break;
                                }
                                else if ($price != '' && !is_numeric($price))
                                {
                                    $passvalid = 1;
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

                        if ($passvalid == 0)
                        {
                            $this->_flashMessenger->addMessage(array(
                                                                    "success" => "The toner pricing updates have been applied successfully."
                                                               ));
                        }
                        else
                        {
                            $db->rollBack();

                            // Build repop values
                            $repop_array = '';
                            foreach ($formData as $key => $value)
                            {
                                if (strstr($key, "txtTonerPrice"))
                                {
                                    $toner_id = str_replace("txtTonerPrice", "", $key);
                                    $price    = $formData ['txtTonerPrice' . $toner_id];

                                    // Build repop array
                                    if ($repop_array != '')
                                    {
                                        $repop_array .= ',';
                                    }
                                    $repop_array .= $toner_id . ':' . $price;
                                }
                            }
                            $this->view->repop_array = $repop_array;
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

                        if ($passvalid == 0)
                        {
                            $this->_flashMessenger->addMessage(array(
                                                                    "success" => "The device pricing updates have been applied successfully."
                                                               ));
                        }
                        else
                        {
                            $db->rollBack();
                            // Build repop values
                            $repop_array = '';
                            foreach ($formData as $key => $value)
                            {
                                if (strstr($key, "txtDevicePrice"))
                                {
                                    $master_device_id = str_replace("txtDevicePrice", "", $key);
                                    $price            = $formData ['txtDevicePrice' . $master_device_id];

                                    // Build repop array
                                    if ($repop_array != '')
                                    {
                                        $repop_array .= ',';
                                    }
                                    $repop_array .= $master_device_id . ':' . $price;
                                }
                            }
                            $this->view->repop_array = $repop_array;
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
        Zend_Session::start();
        $db                    = Zend_Db_Table::getDefaultAdapter();
        $errorMessages         = array();
        $canApprove            = $this->view->IsAllowed(Proposalgen_Model_Acl::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE, Application_Model_Acl::PRIVILEGE_ADMIN);
        $deviceFeaturesService = new Proposalgen_Service_Import_Device_Features();

        if ($this->_request->isPost())
        {
            if (!is_array($deviceFeaturesService->getValidFile($this->_config)) && $canApprove)
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
                                    'dutyCycle'          => $validData[$deviceFeaturesService::DEVICE_FEATURES_DUTY_CYCLE],
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
        $this->view->canApprove    = $canApprove;
        $this->view->errorMessages = $errorMessages;
    }

    public function bulkFileTonerPricingAction ()
    {
        Zend_Session::start();
        $db                  = Zend_Db_Table::getDefaultAdapter();
        $errorMessages       = array();
        $canApprove          = $this->view->IsAllowed(Proposalgen_Model_Acl::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE, Application_Model_Acl::PRIVILEGE_ADMIN);
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
        $this->view->canApprove    = $canApprove;
        $this->view->errorMessages = $errorMessages;
    }

    public function bulkFileTonerMatchupAction ()
    {
        Zend_Session::start();
        $db             = Zend_Db_Table::getDefaultAdapter();
        $errorMessages  = array();
        $canApprove     = $this->view->IsAllowed(Proposalgen_Model_Acl::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE, Application_Model_Acl::PRIVILEGE_ADMIN);
        $matchupService = new Proposalgen_Service_Import_Toner_Matchup();

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
        $this->view->canApprove    = $canApprove;
        $this->view->errorMessages = $errorMessages;
    }

    public function bulkFileDevicePricingAction ()
    {
        Zend_Session::start();
        $db                   = Zend_Db_Table::getDefaultAdapter();
        $errorMessages        = array();
        $canApprove           = $this->view->IsAllowed(Proposalgen_Model_Acl::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE, Application_Model_Acl::PRIVILEGE_ADMIN);
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
        $this->view->canApprove    = $canApprove;
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
                $fieldList            = Proposalgen_Model_Mapper_MasterDevice::getInstance()->getPrinterFeaturesForExport();
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