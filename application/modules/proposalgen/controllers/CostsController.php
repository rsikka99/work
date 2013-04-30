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

        // fill manufacturers drop down
        $manufacturersTable            = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers                 = $manufacturersTable->fetchAll('isDeleted = false', 'fullName');
        $this->view->manufacturer_list = $manufacturers;

        if ($this->_request->isPost())
        {
            $passvalid = 0;
            $formData  = $this->_request->getPost();

            // check post back for update
            $db->beginTransaction();
            try
            {

                // return current drop down states
                // $this->view->company_filter = $formData ['company_filter'];
                $this->view->pricing_filter  = $formData ['pricing_filter'];
                $this->view->search_filter   = $formData ['criteria_filter'];
                $this->view->search_criteria = $formData ['txtCriteria'];
                $this->view->repop_page      = $formData ["hdnPage"];

                if ($formData ['hdnMode'] == "update")
                {
                    //$dealer_company_id = $formData ['company_filter'];
                    //$dealer_company_id = 1;
                    // Save Master Company Pricing Changes
                    if ($formData ['pricing_filter'] == 'toner')
                    {
                        // loop through $result
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
                                else if ($price != '')
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

                            // build repop values
                            $repop_array = '';
                            foreach ($formData as $key => $value)
                            {
                                if (strstr($key, "txtTonerPrice"))
                                {
                                    $toner_id = str_replace("txtTonerPrice", "", $key);
                                    $price    = $formData ['txtTonerPrice' . $toner_id];

                                    // build repop array
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
                            // build repop values
                            $repop_array = '';
                            foreach ($formData as $key => $value)
                            {
                                if (strstr($key, "txtDevicePrice"))
                                {
                                    $master_device_id = str_replace("txtDevicePrice", "", $key);
                                    $price            = $formData ['txtDevicePrice' . $master_device_id];

                                    // build repop array
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

    public function bulkfilepricingAction ()
    {
        Zend_Session::start();
        $this->view->title = "Import & Export Pricing";
        $db                = Zend_Db_Table::getDefaultAdapter();
        $dealerId          = $this->_dealerId;

        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();

            // FIXME: What does this do??!?!?
            $this->view->company_filter = 1;

            /**
             * hdnRole is used when logged in as a dealer to differentiate between if the dealer is on "update company pricing" or "update my pricing"
             */
            if (isset($formData ['hdnMode']))
            {
                // ************************************************************/
                // * Initial Page Load
                // ************************************************************/
            }
            else
            {

                // ************************************************************/
                // * Upload File
                // ************************************************************/
                $upload = new Zend_File_Transfer_Adapter_Http();
                $upload->setDestination($this->_config->app->uploadPath);

                // Limit the extensions to csv files
                $upload->addValidator('Extension', false, 'csv');
                $upload->getValidator('Extension')->setMessage('<span class="warning">*</span> File "' . basename($_FILES ['uploadedfile'] ['name']) . '" has an <em>invalid</em> extension. A <span style="color: red;">.csv</span> is required.');

                // Limit the amount of files to maximum 1
                $upload->addValidator('Count', false, 1);
                $upload->getValidator('Count')->setMessage('<span class="warning">*</span> You are only allowed to upload 1 file at a time.');

                // Limit the size of all files to be uploaded to maximum 4MB and
                // mimimum 100B
                $upload->addValidator('FilesSize', false, array(
                                                               'min' => '100B',
                                                               'max' => '4MB'
                                                          ));

                $upload->getValidator('FilesSize')->setMessage('<span class="warning">*</span> File size must be between 100B and 4MB.');
                if ($upload->receive())
                {
                    $is_valid      = true;
                    $columns       = array();
                    $final_devices = array();
                    $finalDevices  = array();

                    $db->beginTransaction();
                    try
                    {
                        $lines = file($upload->getFileName(), FILE_IGNORE_NEW_LINES);

                        // grab the first row of items(the column headers)
                        $headers = str_getcsv(strtolower($lines [0]));

                        // default column keys
                        $key_manufacturer  = null;
                        $key_part_type     = null;
                        $key_sku           = null;
                        $key_color         = null;
                        $key_yield         = null;
                        $key_new_price     = null;
                        $key_printer_model = null;
                        $key_dealer_sku    = null;
                        $key_parts_cpp     = null;
                        $key_labor_cpp     = null;
                        $key_system_cost   = null;

                        $import_type = false;
                        /**
                         * Finds where each column is located inside the CSV
                         */
                        $array_key = 0;
                        foreach ($headers as $value)
                        {
                            if (strtolower($value) == "toner id")
                            {
                                $import_type = "toner";
                            }
                            else if (strtolower($value) == "manufacturer")
                            {
                                $key_manufacturer = $array_key;
                            }
                            else if (strtolower($value) == "type")
                            {
                                $key_part_type = $array_key;
                            }
                            else if (strtolower($value) == "sku")
                            {
                                $key_sku = $array_key;
                            }
                            else if (strtolower($value) == "color")
                            {
                                $key_color = $array_key;
                            }
                            else if (strtolower($value) == "yield")
                            {
                                $key_yield = $array_key;
                            }
                            else if (strtolower($value) == "dealer price")
                            {
                                $key_new_price = $array_key;
                            }
                            else if (strtolower($value) == "master printer id")
                            {
                                $import_type = "printer";
                            }
                            else if (strtolower($value) == "printer model")
                            {
                                $key_printer_model = $array_key;
                            }
                            else if (strtolower($value) == "dealer sku")
                            {
                                $key_dealer_sku = $array_key;
                            }
                            else if (strtolower($value) == "labor cpp")
                            {
                                $key_labor_cpp = $array_key;
                            }
                            else if (strtolower($value) == "parts cpp")
                            {
                                $key_parts_cpp = $array_key;
                            }
                            else if (strtolower($value) == "system price")
                            {
                                $key_system_cost = $array_key;
                            }
                            $array_key += 1;
                        }

                        if ($is_valid)
                        {
                            // create an associative array of the csv information
                            foreach ($lines as $key => $value)
                            {
                                if ($key > 0)
                                {
                                    $devices [$key] = str_getcsv($value);

                                    // get current pricing
                                    if ($import_type == "printer")
                                    {
                                        $master_device_id = $devices [$key] [0];
                                        $columns [0]      = "Master Printer ID";
                                        $columns [1]      = "Manufacturer";
                                        $columns [2]      = "Printer Model";
                                        $columns [3]      = "Labor CPP";
                                        $columns [4]      = "Parts CPP";


                                        $table   = new Proposalgen_Model_DbTable_MasterDevice();
                                        $where   = $table->getAdapter()->quoteInto('id = ?', $master_device_id, 'INTEGER');
                                        $printer = $table->fetchRow($where);

                                        if (count($printer->toArray()) > 0)
                                        {
                                            // save into array
                                            $final_devices [0] = $master_device_id;
                                            $final_devices [1] = $devices [$key] [$key_manufacturer];
                                            $final_devices [2] = $devices [$key] [$key_printer_model];
                                            $final_devices [3] = $devices [$key] [$key_labor_cpp];
                                            $final_devices [4] = $devices [$key] [$key_parts_cpp];
                                        }

                                    }
                                    else
                                    {
                                        $toner_id    = $devices [$key] [0];
                                        $columns [0] = "Toner ID";
                                        $columns [1] = "Manufacturer";
                                        $columns [2] = "Part Type";
                                        $columns [3] = "SKU";
                                        $columns [4] = "Color";
                                        $columns [5] = "Yield";
                                        $columns [6] = "System Price";
                                        $columns [7] = "Dealer Sku";
                                        $columns [8] = "New Price";

                                        $table = new Proposalgen_Model_DbTable_Toner();
                                        $where = $table->getAdapter()->quoteInto('id = ?', $toner_id, 'INTEGER');
                                        $toner = $table->fetchRow($where);

                                        if (count($toner->toArray()) > 0)
                                        {
                                            // save into array
                                            $final_devices [0] = $toner_id;
                                            $final_devices [1] = $devices [$key] [$key_manufacturer];
                                            $final_devices [2] = $devices [$key] [$key_part_type];
                                            $final_devices [3] = $devices [$key] [$key_sku];
                                            $final_devices [4] = $devices [$key] [$key_color];
                                            $final_devices [5] = $devices [$key] [$key_yield];
                                            $final_devices [6] = $devices [$key] [$key_system_cost];
                                            $final_devices [7] = $devices [$key] [$key_dealer_sku];
                                            $final_devices [8] = $devices [$key] [$key_new_price];
                                        }
                                    }

                                    // combine the column headers and the device
                                    // data into one associative array
                                    $devices [$key] = $final_devices;
                                    if ($devices [$key])
                                    {
                                        $finalDevices [] = array_combine($columns, $devices [$key]);
                                    }
                                }
                            }

                            $results = $finalDevices;
                            $db->beginTransaction();
                            try
                            {
                                // detect file type (printers or toners)
                                $import_type = "printer";

                                foreach ($columns as $value)
                                {
                                    if (strtolower($value) == "toner id")
                                    {
                                        $import_type = "toner";
                                        break;
                                    }
                                }

                                // Loop through the file and save.
                                $inputFilter = new Zend_Filter_Input(array(
                                                                          'cost'             => array(
                                                                              'StringTrim',
                                                                          ),
                                                                          'laborCostPerPage' => array(
                                                                              'StringTrim',
                                                                          ),
                                                                          'partsCostPerPage' => array(
                                                                              'StringTrim',
                                                                          ),
                                                                     ), array(
                                                                             'cost'             => array(
                                                                                 'Float',
                                                                                 array(
                                                                                     'name'    => 'GreaterThan',
                                                                                     'options' => array(
                                                                                         'min' => 0,
                                                                                     )
                                                                                 )
                                                                             ),
                                                                             'laborCostPerPage' => array(
                                                                                 'Float',
                                                                                 array(
                                                                                     'name'    => 'GreaterThan',
                                                                                     'options' => array(
                                                                                         'min' => 0,
                                                                                     )
                                                                                 )
                                                                             ),
                                                                             'partsCostPerPage' => array(
                                                                                 'Float',
                                                                                 array(
                                                                                     'name'    => 'GreaterThan',
                                                                                     'options' => array(
                                                                                         'min' => 0,
                                                                                     )
                                                                                 )
                                                                             ),
                                                                        )
                                );

                                foreach ($results as $value)
                                {
                                    // Update records
                                    if ($import_type == 'printer')
                                    {
                                        $inputFilter->setData(array('laborCostPerPage' => $value ['Labor CPP']));
                                        $importLaborCpp = $inputFilter->laborCostPerPage;
                                        $isLaborValid   = $inputFilter->isValid();

                                        $inputFilter->setData(array('partsCostPerPage' => $value ['Parts CPP']));
                                        $importPartsCpp = $inputFilter->partsCostPerPage;
                                        $isPartsValid   = $inputFilter->isValid();

                                        $masterDeviceId = $value ['Master Printer ID'];

                                        $dataArray = array(
                                            'masterDeviceId'   => $masterDeviceId,
                                            'dealerId'         => $dealerId,
                                            'laborCostPerPage' => $importLaborCpp,
                                            'partsCostPerPage' => $importPartsCpp,
                                        );

                                        // Does the master device exist in our database?
                                        $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($masterDeviceId);
                                        if (count($masterDevice->toArray()) > 0)
                                        {
                                            // Do we have the master device already in this dealer device table
                                            $masterDeviceAttribute = Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->find(array($masterDeviceId, $dealerId));
                                            if ($masterDeviceAttribute)
                                            {
                                                // If we have a master device attribute and the row is empty, delete the row
                                                if (empty($importLaborCpp) && empty($importPartsCpp))
                                                {
                                                    Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->delete($masterDeviceAttribute);
                                                }
                                                else
                                                {
                                                    // Have any values changed
                                                    $hasChanged = false;

                                                    if (!$isLaborValid)
                                                    {
                                                        $importLaborCpp = null;
                                                    }

                                                    if (!$isPartsValid)
                                                    {
                                                        $importPartsCpp = null;
                                                    }

                                                    if ((float)$importPartsCpp !== (float)$masterDeviceAttribute->partsCostPerPage)
                                                    {
                                                        if ($importPartsCpp === null)
                                                        {
                                                            $importPartsCpp = new Zend_Db_Expr('null');
                                                        }
                                                        $masterDeviceAttribute->partsCostPerPage = $importPartsCpp;
                                                        $hasChanged                              = true;
                                                    }

                                                    if ((float)$importLaborCpp !== (float)$masterDeviceAttribute->laborCostPerPage)
                                                    {
                                                        if ($importLaborCpp === null)
                                                        {
                                                            $importLaborCpp = new Zend_Db_Expr('null');
                                                        }
                                                        $masterDeviceAttribute->laborCostPerPage = $importLaborCpp;
                                                        $hasChanged                              = true;
                                                    }

                                                    if ($hasChanged)
                                                    {
                                                        Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->save($masterDeviceAttribute);
                                                    }
                                                }
                                            }
                                            else
                                            {

                                                if ($importLaborCpp > 0 || $importPartsCpp > 0)
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

                                        $inputFilter->setData(array('cost' => $value ['New Price']));
                                        // Filter the data -
                                        $importTonerId   = $value ['Toner ID'];
                                        $importDealerSku = trim($value ['Dealer Sku']);
                                        $importCost      = $inputFilter->cost;

                                        $dataArray = array(
                                            'tonerId'   => $importTonerId,
                                            'dealerSku' => $importDealerSku,
                                            'cost'      => $importCost,
                                            'dealerId'  => $dealerId,
                                        );

                                        $toner = Proposalgen_Model_Mapper_Toner::getInstance()->find($value ['Toner ID']);
                                        if (count($toner->toArray()) > 0)
                                        {

                                            $tonerAttribute = Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->find(array($importTonerId, $dealerId));
                                            // Does the toner attribute exists ?
                                            if ($tonerAttribute)
                                            {
                                                // If cost && sku are empty  or cost = 0 -> delete.
                                                // Delete
                                                if (empty($importCost) && empty($importDealerSku))
                                                {
                                                    // If the attributes are empty after being found, delete them.
                                                    Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->delete($tonerAttribute);
                                                }
                                                else
                                                {
                                                    // Have any values changed
                                                    $hasChanged = false;

                                                    if (!$inputFilter->isValid('cost'))
                                                    {
                                                        $importCost = null;
                                                    }

                                                    if ((float)$importCost !== (float)$tonerAttribute->cost)
                                                    {
                                                        if ($importCost === null)
                                                        {
                                                            $importCost = new Zend_Db_Expr('NULL');
                                                        }
                                                        $tonerAttribute->cost = $importCost;
                                                        $hasChanged           = true;
                                                    }

                                                    if ($tonerAttribute->dealerSku != $importDealerSku)
                                                    {
                                                        if (empty($importDealerSku))
                                                        {
                                                            $importDealerSku = new Zend_Db_Expr('NULL');
                                                        }
                                                        $tonerAttribute->dealerSku = $importDealerSku;
                                                        $hasChanged                = true;
                                                    }

                                                    if ($hasChanged)
                                                    {
                                                        Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->save($tonerAttribute);

                                                    }
                                                }
                                            }
                                            else
                                            {
                                                if ($importCost > 0 || !empty($importDealerSku))
                                                {
                                                    $tonerAttribute = new Proposalgen_Model_Dealer_Toner_Attribute();
                                                    $tonerAttribute->populate($dataArray);
                                                    Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->insert($tonerAttribute);
                                                }
                                            }
                                        }

                                    }
                                }
                                $this->_flashMessenger->addMessage(array("success" => "Your pricing updates have been applied successfully."));
                                $db->commit();
                            }
                            catch
                            (Exception $e)
                            {

                                $db->rollback();
                                $this->_flashMessenger->addMessage(array("error" => "An error has occurred during the update and your changes were not applied. Please review your file and try again."));
                            }
                        }
                    }
                    catch (Exception $e)
                    {
                        $db->rollback();
                        $this->_flashMessenger->addMessage(array("error" => " An error has occurred during the update and your changes were not applied. Please review your file and try again."));
                    }

                    // delete the file we just uploaded
                    unlink($upload->getFileName());
                }
                else
                {
                    // if upload fails, print error message message
                    $this->view->errMessages = $upload->getMessages();
                }
            }
            $this->view->hdnRole = $formData ['hdnRole'];
        }
    }

    public function exportpricingAction ()
    {
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        //$company = $this->_getParam('company', $this->dealer_company_id);
        $pricing = $this->_getParam('pricing', 'printer');


        // filename for CSV file
        $filename = "system_pricing" . "_" . $pricing . "_pricing_" . date('m_d_Y') . ".csv";

        // check post back for update
        $db->beginTransaction();
        try
        {
            // Get device list
            if ($pricing == 'printer')
            {
                $fieldTitles = array(
                    'Master Printer ID',
                    'Manufacturer',
                    'Printer Model',
                    'Labor CPP',
                    'Parts CPP',
                );

                $select = $db->select()
                    ->from(array(
                                'md' => 'master_devices'
                           ), array(
                                   'id AS master_id',
                                   'modelName',
                              ))
                    ->joinLeft(array(
                                    'm' => 'manufacturers'
                               ), 'm.id = md.manufacturerId', array(
                                                                   'fullname'
                                                              ))
                    ->joinLeft(array(
                                    'dmda' => 'dealer_master_device_attributes'
                               ), 'dmda.masterDeviceId = md.id',
                        array(
                             'laborCostPerPage',
                             'partsCostPerPage',
                        ))
                    ->order(array(
                                 'm.fullname',
                                 'md.modelName',
                            ));
                $stmt   = $db->query($select);
                $result = $stmt->fetchAll();
                foreach ($result as $value)
                {
                    $fieldList [] = array(
                        $value ['master_id'],
                        $value ['fullname'],
                        $value ['modelName'],
                        $value ['laborCostPerPage'],
                        $value ['partsCostPerPage'],
                    );
                }

            }
            /* Begin toner export export logic here */
            else
            {
                $fieldTitles = array(
                    'Toner ID',
                    'Manufacturer',
                    'Type',
                    'SKU',
                    'Color',
                    'Yield',
                    'System Price',
                    'Dealer Sku',
                    'Dealer Price',
                );
                $dealerId    = $this->_dealerId;
                // Get Count
                $select = $db->select()
                    ->from(array(
                                't' => 'toners'), array(
                                                       'id AS toners_id', 'sku', 'yield', "systemCost" => "cost"
                                                  )
                    )
                    ->joinLeft(array(
                                    'dt' => 'device_toners'
                               ), 'dt.toner_id = t.id', array(
                                                             'master_device_id'
                                                        ))
                    ->joinLeft(array(
                                    'tm' => 'manufacturers'
                               ), 'tm.id = t.manufacturerId', array(
                                                                   'fullname'
                                                              ))
                    ->joinLeft(array(
                                    'tc' => 'toner_colors'
                               ), 'tc.id = t.tonerColorId', array('name AS toner_color'))
                    ->joinLeft(array(
                                    'pt' => 'part_types'
                               ), 'pt.id = t.partTypeId', 'name AS part_type')
                    ->joinLeft(array(
                                    'dta' => 'dealer_toner_attributes'
                               ), "dta.tonerId = t.id AND dta.dealerId = {$dealerId}", array('cost', 'dealerSku'))
                    ->where("t.id > 0")
                    ->group('t.id')
                    ->order(array(
                                 'tm.fullname'
                            ));
                $stmt   = $db->query($select);
                $result = $stmt->fetchAll();


                foreach ($result as $value)
                {
                    $fieldList [] = array(
                        $value ['toners_id'],
                        $value ['fullname'],
                        $value ['part_type'],
                        $value ['sku'],
                        $value ['toner_color'],
                        $value ['yield'],
                        $value ['systemCost'],
                        $value ['dealerSku'],
                        $value ['cost'],
                    );
                }
            }
        }
        catch (Exception $e)
        {
            throw new Exception("CSV File could not be opened/written for export.", 0, $e);
        }

        $this->view->fieldTitles = implode(",", $fieldTitles);
        $newFieldList            = "";
        foreach ($fieldList as $row)
        {
            $newFieldList .= implode(",", $row);
            $newFieldList .= "\n";
        }
        $this->view->fieldList = $newFieldList;
        Tangent_Functions::setHeadersForDownload($filename);
    }
}