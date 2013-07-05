<?php
/**
 * Class Proposalgen_Service_ManageMasterDevices
 */
class Proposalgen_Service_ManageMasterDevices
{
    /**
     * The master device id
     *
     * @var int
     */
    public $masterDeviceId;
    /**
     * The data used to populate the master device
     *
     * @var array
     */
    public $data;

    protected $_suppliesAndServiceForm;
    protected $_deviceAttributesForm;
    protected $_hardwareOptimizationForm;
    protected $_hardwareQuoteForm;
    protected $_availableOptionsForm;
    protected $_availableTonersForm;
    protected $_dealerId;
    protected $_isAllowed;
    public $isQuoteDevice = false;


    /**
     * @param int  $masterDeviceId The id of the Master Device
     * @param int  $dealerId
     * @param bool $isAllowed
     */
    public function __construct ($masterDeviceId, $dealerId, $isAllowed = false)
    {
        $this->masterDeviceId = $masterDeviceId;
        $this->_dealerId      = $dealerId;
        $this->_isAllowed     = $isAllowed;
    }

    /**
     * Shows the forms
     *
     * @param bool $showSupplies
     * @param bool $showDeviceAttributes
     * @param bool $showHardwareOptimization
     * @param bool $showHardwareQuote
     * @param bool $showAvailableOptions
     * @param bool $showHardwareConfigurations
     *
     * @return array
     */
    public function getForms ($showSupplies = true, $showDeviceAttributes = true, $showHardwareOptimization = true, $showHardwareQuote = true, $showAvailableOptions = true, $showHardwareConfigurations = true)
    {
        $formsToShow = array();

        if ($showDeviceAttributes)
        {
            $formsToShow['deviceAttributes'] = $this->getDeviceAttributesForm();
        }

        if ($showHardwareOptimization)
        {
            $formsToShow['hardwareOptimization'] = $this->getHardwareOptimizationForm();
        }

        if ($showHardwareQuote)
        {
            $formsToShow['hardwareQuote'] = $this->getHardwareQuoteForm();
        }

        if ($showAvailableOptions)
        {
            $formsToShow['availableOptions']     = true;
            $formsToShow['availableOptionsForm'] = $this->getAvailableOptionsForm();
        }

        if ($showHardwareConfigurations)
        {
            $formsToShow['hardwareConfigurations'] = true;
        }

        if ($showSupplies)
        {
            $formsToShow['suppliesAndService']  = $this->getSuppliesAndServicesForm();
            $formsToShow['availableTonersForm'] = $this->getAvailableTonersForm();
        }

        $formsToShow['delete'] = new Proposalgen_Form_Delete();

        return $formsToShow;
    }


    /**
     * @param        $form
     * @param string $data []
     * @param string $formName
     *
     * @return array|null
     */
    public function validateData ($form, $data, $formName)
    {
        $json = null;
        $form->populate($data);

        if (!$form->isValid($data))
        {
            $json = array();

            foreach ($form->getMessages() as $errorElementName => $errorElement)
            {
                $count = 0;

                foreach ($errorElement as $errorName => $elementErrorMessage)
                {
                    $count++;
                    $json['errorMessages'][$errorElementName] = $elementErrorMessage;
                    $json['name']                             = $formName;
                }
            }
        }

        return $json;
    }

    /**
     * Validates that we have toners
     *
     * @param $tonerList
     * @param $tonerConfigId
     * @param $isLeased
     *
     * @return null|string
     */
    public function validateToners ($tonerList, $tonerConfigId, $isLeased = false)
    {
        $json   = null;
        $toners = [];

        if ($tonerList)
        {
            $toners = Proposalgen_Model_Mapper_Toner::getInstance()->fetchListOfToners($tonerList);
        }

        $toners_valid = false;

        // validate toners against toner_config
        $has_toner   = false;
        $has_black   = false;
        $has_yellow  = false;
        $has_magenta = false;
        $has_cyan    = false;
        $has_3color  = false;
        $has_4color  = false;
        foreach ($toners as $toner)
        {
            if ($toner)
            {
                $has_toner = true;
                $curColor  = strtolower(Proposalgen_Model_Mapper_TonerColor::getInstance()->find($toner['tonerColorId'])->tonerColorName);
                if ($curColor == "black")
                {
                    $has_black = true;
                }
                else if ($curColor == "yellow")
                {
                    $has_yellow = true;
                }
                else if ($curColor == "magenta")
                {
                    $has_magenta = true;
                }
                else if ($curColor == "cyan")
                {
                    $has_cyan = true;
                }
                else if ($curColor == "3 color")
                {
                    $has_3color = true;
                }
                else if ($curColor == "4 color")
                {
                    $has_4color = true;
                }
            }
        }

        $toner_errors       = "";
        $toner_error_colors = "";

        if ($has_toner)
        {
            // Has toners, validate to make sure they match the device
            switch ($tonerConfigId)
            {
                case Proposalgen_Model_TonerConfig::BLACK_ONLY:
                    // BLACK ONLY
                    if ($has_3color || $has_4color || $has_cyan || $has_magenta || $has_yellow)
                    {
                        $toners_valid = false;
                        $toner_errors = "Error: You are trying to add invalid toners to this printer. Only Black Toners are allowed.";
                    }
                    else if ($has_black)
                    {
                        $toners_valid = true;
                    }
                    else
                    {
                        $toner_errors = "Error: Missing a Black Toner. Please add one and try again.";
                    }

                    break;
                case Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED:
                    // 3 COLOR - SEPARATED
                    if ($has_3color || $has_4color)
                    {
                        $toners_valid = false;
                        $toner_errors = "Error: You are trying to add invalid toners to this printer. Only Black, Yellow, Magenta and Cyan Toners are allowed.";
                    }
                    else if ($has_black)
                    {
                        if ($has_yellow)
                        {
                            if ($has_magenta)
                            {
                                if ($has_cyan)
                                {
                                    $toners_valid = true;
                                }
                                else
                                {
                                    $toner_error_colors = "Cyan";
                                }
                            }
                            else
                            {
                                $toner_error_colors = "Magenta";
                            }
                        }
                        else
                        {
                            $toner_error_colors = "Yellow";
                        }
                    }
                    else
                    {
                        $toner_error_colors = "Black";
                    }

                    if ($toner_error_colors != '')
                    {
                        $repop_form   = 1;
                        $toner_errors = "Error: Missing a " . $toner_error_colors . " Toner. Please add one and try again.";
                    }

                    break;
                case Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED:
                    // 3 COLOR - COMBINED
                    if ($has_4color || $has_cyan || $has_magenta || $has_yellow)
                    {
                        $repop_form   = 1;
                        $toners_valid = false;
                        $toner_errors = "Error: You are trying to add invalid toners to this printer. Only 3 Color and Black Toners are allowed.";
                    }
                    else if ($has_black)
                    {
                        if ($has_3color)
                        {
                            $toners_valid = true;
                        }
                        else
                        {
                            $toner_error_colors = "3 Color";
                        }
                    }
                    else
                    {
                        $toner_error_colors = "Black";
                    }

                    if ($toner_error_colors != '')
                    {
                        $toner_errors = "Error: Missing a " . $toner_error_colors . " Toner. Please add one and try again.";
                    }

                    break;

                case Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED:
                    // 4 COLOR - COMBINED
                    if ($has_3color || $has_black || $has_cyan || $has_magenta || $has_yellow)
                    {
                        $toners_valid = false;
                        $toner_errors = "Error: You are trying to add invalid toners to this printer. Only 4 Color Toners are allowed.";
                    }
                    else if ($has_4color)
                    {
                        $toners_valid = true;
                    }
                    else
                    {
                        $toner_errors = "Error: Missing a 4 Color Toner. Please add one and try again.";
                    }

                    break;
            }
        }
        else
        {
            // if leased, then toners not required
            if ($isLeased)
            {
                $toners_valid = true;
            }
            else
            {
                $toners_valid = false;
                $toner_errors = "Error: You must add required toners before saving this device.";
            }
        }

        if ($toners_valid)
        {
            return null;
        }
        else
        {
            return $toner_errors;
        }
    }


    /**
     * @param      $validatedData
     *
     * @param bool $tonersList
     *
     * @param bool $isAdmin
     *
     * @param bool $approved
     *
     * @return bool|string
     */
    public function saveSuppliesAndDeviceAttributes ($validatedData, $tonersList = false, $isAdmin = false, $approved = false)
    {
        $masterDeviceMapper = Proposalgen_Model_Mapper_MasterDevice::getInstance();
        $masterDevice       = $masterDeviceMapper->find($this->masterDeviceId);

        try
        {

            if ($this->_isAllowed)
            {
                if ($validatedData['isLeased'] == false)
                {
                    $validatedData['leasedTonerYield'] = new Zend_Db_Expr("NULL");
                }

                if ($validatedData['dealerLaborCostPerPage'] == '')
                {
                    $validatedData['dealerLaborCostPerPage'] = new Zend_Db_Expr("NULL");
                }

                if ($validatedData['dealerPartsCostPerPage'] == '')
                {
                    $validatedData['dealerPartsCostPerPage'] = new Zend_Db_Expr("NULL");
                }


                if ($masterDevice instanceof Proposalgen_Model_MasterDevice)
                {
                    if ($isAdmin && $approved)
                    {
                        $validatedData['isSystemDevice'] = 1;
                    }
                    $masterDevice->populate($validatedData);
                    $masterDeviceMapper->save($masterDevice);
                }
                else
                {
                    if ($isAdmin)
                    {
                        $validatedData['isSystemDevice'] = 1;
                    }
                    else
                    {
                        $validatedData['isSystemDevice'] = 0;
                    }

                    $validatedData['dateCreated'] = date('Y-m-d H:i:s');
                    $validatedData['userId']      = Zend_Auth::getInstance()->getIdentity()->id;
                    $this->masterDeviceId         = $masterDeviceMapper->insert(new Proposalgen_Model_MasterDevice($validatedData));
                }

                $tonerIds = explode(',', $tonersList);

                // If for some reason we have a '' without any numbers, lets remove it
                if (count($tonerIds) > 0 && $tonerIds[0] == '')
                {
                    unset($tonerIds[0]);
                }

                $assignedToners = Proposalgen_Model_Mapper_Toner::getInstance()->getTonersForDevice($this->masterDeviceId);

                foreach ($assignedToners as $tonersByManufacturer)
                {
                    foreach ($tonersByManufacturer as $tonersByColor)
                    {
                        foreach ($tonersByColor as $assignedToner)
                        {
                            $shouldDelete = true;

                            foreach ($tonerIds as $tonerId)
                            {
                                if ($assignedToner->id == $tonerId)
                                {
                                    $shouldDelete = false;
                                }
                            }

                            if ($shouldDelete)
                            {
                                Proposalgen_Model_Mapper_DeviceToner::getInstance()->delete(array($assignedToner->id, $this->masterDeviceId));
                            }
                        }
                    }
                }

                foreach ($tonerIds as $tonerId)
                {
                    $alreadyExists = false;

                    foreach ($assignedToners as $assignedToner)
                    {
                        if ($tonerId == $assignedToner->id)
                        {
                            $alreadyExists = true;
                        }
                    }

                    if ($alreadyExists == false)
                    {
                        $deviceToner                 = new Proposalgen_Model_DeviceToner();
                        $deviceToner->masterDeviceId = $this->masterDeviceId;
                        $deviceToner->tonerId        = $tonerId;
                        $deviceTonersMapper          = Proposalgen_Model_Mapper_DeviceToner::getInstance();
                        $deviceTonersMapper->save($deviceToner);
                    }
                }
            }

            $dealerMasterDeviceAttribute                   = new Proposalgen_Model_Dealer_Master_Device_Attribute();
            $dealerMasterDeviceAttribute->dealerId         = $this->_dealerId;
            $dealerMasterDeviceAttribute->masterDeviceId   = $this->masterDeviceId;
            $dealerMasterDeviceAttribute->laborCostPerPage = ($validatedData['dealerLaborCostPerPage'] == '' ? new Zend_Db_Expr("NULL") : $validatedData['dealerLaborCostPerPage']);
            $dealerMasterDeviceAttribute->partsCostPerPage = ($validatedData['dealerPartsCostPerPage'] == '' ? new Zend_Db_Expr("NULL") : $validatedData['dealerPartsCostPerPage']);
            $dealerMasterDeviceAttribute->saveObject();
        }
        catch (Exception $e)
        {
            Tangent_Log::logException($e);

            return false;
        }

        return true;
    }

    /**
     * @param $validatedData
     *
     * @return bool
     */
    public function saveHardwareOptimization ($validatedData)
    {
        $deviceSwapMapper = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance();
        try
        {
            if ($validatedData['isDeviceSwap'] == 1 && $validatedData['isSelling'] == 1)
            {
                $deviceSwap = new Hardwareoptimization_Model_Device_Swap(array('masterDeviceId' => $this->masterDeviceId, 'dealerId' => $this->_dealerId));

                $deviceSwap->saveObject($validatedData);
            }
            else
            {
                $deviceSwap = $deviceSwapMapper->find(array($this->masterDeviceId, $this->_dealerId));

                if ($deviceSwap)
                {
                    $deviceSwapMapper->delete($deviceSwap);
                }
            }
        }
        catch (Exception $e)
        {
            Tangent_Log::logException($e);

            return false;
        }

        return true;
    }

    /**
     * This saves a hardware quote
     *
     * @param $validatedData
     *
     * @return boolean
     */
    public function saveHardwareQuote ($validatedData)
    {
        $deviceMapper = Quotegen_Model_Mapper_Device::getInstance();
        try
        {
            if ($validatedData['isSelling'] == 1)
            {
                $device = new Quotegen_Model_Device(array('masterDeviceId' => $this->masterDeviceId, 'dealerId' => $this->_dealerId));
                $device->populate($validatedData);

                $device->saveObject();
            }
            else
            {
                $device = $deviceMapper->find(array($this->masterDeviceId, $this->_dealerId));

                if ($device)
                {
                    $deviceMapper->delete($device);
                }
            }
        }
        catch (Exception $e)
        {
            Tangent_Log::logException($e);

            return false;
        }

        return true;
    }


    /**
     * Gets the Supplies And Services Form
     *
     * @return Proposalgen_Form_SuppliesAndService
     */
    public function getSuppliesAndServicesForm ()
    {
        if (!isset($this->_suppliesAndServiceForm))
        {
            $this->_suppliesAndServiceForm = new Proposalgen_Form_SuppliesAndService(null, $this->_isAllowed, $this->isQuoteDevice);
            $masterDevice                  = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($this->masterDeviceId);

            if ($this->data && !$masterDevice)
            {
                $this->_suppliesAndServiceForm->populate($this->data);
            }
            else if ($masterDevice)
            {
                $dealerMasterDeviceAttributes = Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->find(array($this->masterDeviceId, $this->_dealerId));

                if ($dealerMasterDeviceAttributes)
                {
                    $this->_suppliesAndServiceForm->populate(array("dealerLaborCostPerPage" => $dealerMasterDeviceAttributes->laborCostPerPage, "dealerPartsCostPerPage" => $dealerMasterDeviceAttributes->partsCostPerPage));
                }
                $this->_suppliesAndServiceForm->populate($masterDevice->toArray());
            }
        }

        return $this->_suppliesAndServiceForm;
    }

    /**
     * Gets the Device Attributes Form
     *
     * @return Proposalgen_Form_DeviceAttributes
     */
    public function getDeviceAttributesForm ()
    {
        if (!isset($this->_deviceAttributesForm))
        {
            $this->_deviceAttributesForm = new Proposalgen_Form_DeviceAttributes(null, $this->_isAllowed);
            $masterDevice                = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($this->masterDeviceId);

            if ($this->data && !$masterDevice)
            {
                $this->_deviceAttributesForm->populate($this->data);
            }
            else if ($masterDevice)
            {
                $this->_deviceAttributesForm->populate($masterDevice->toArray());
            }
        }

        return $this->_deviceAttributesForm;
    }

    /**
     * Gets the Hardware Optimization Form
     *
     * @return Proposalgen_Form_HardwareOptimization
     */
    public function getHardwareOptimizationForm ()
    {
        if (!isset($this->_hardwareOptimizationForm))
        {
            $this->_hardwareOptimizationForm = new Proposalgen_Form_HardwareOptimization();
            $deviceSwap                      = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance()->find(array($this->masterDeviceId, $this->_dealerId));

            if ($deviceSwap)
            {
                $this->_hardwareOptimizationForm->populate($deviceSwap->toArray());
                $this->_hardwareOptimizationForm->populate(array('isDeviceSwap' => true));
            }
        }

        return $this->_hardwareOptimizationForm;
    }

    /**
     * Gets the Hardware Quote Form
     *
     * @return Proposalgen_Form_HardwareQuote
     */
    public function getHardwareQuoteForm ()
    {
        if (!isset($this->_hardwareQuoteForm))
        {
            $this->_hardwareQuoteForm = new Proposalgen_Form_HardwareQuote();
            $device                   = Quotegen_Model_Mapper_Device::getInstance()->find(array($this->masterDeviceId, $this->_dealerId));

            if ($device)
            {
                $this->_hardwareQuoteForm->populate($device->toArray());
                $this->_hardwareQuoteForm->populate(array('isSelling' => true));
                $this->isQuoteDevice = true;
            }
        }

        return $this->_hardwareQuoteForm;
    }

    /**
     * Gets the Hardware Configuration Form
     *
     * @return Proposalgen_Form_HardwareConfigurations
     */
    public function getHardwareConfigurationsForm ()
    {
        if (!isset($this->_hardwareConfigurationsForm))
        {
            $this->_hardwareConfigurationsForm = new Proposalgen_Form_HardwareConfigurations($this->masterDeviceId);
        }

        return $this->_hardwareConfigurationsForm;
    }

    /**
     * Gets the Available Options Form
     *
     * @return Proposalgen_Form_AvailableOptions
     */
    public function getAvailableOptionsForm ()
    {
        if (!isset($this->_availableOptionsForm))
        {
            $this->_availableOptionsForm = new Proposalgen_Form_AvailableOptions();
        }

        return $this->_availableOptionsForm;
    }

    /**
     * Gets the Available Toners Form
     *
     * @return Proposalgen_Form_AvailableToners
     */
    public function getAvailableTonersForm ()
    {
        if (!isset($this->_availableTonersForm))
        {
            $this->_availableTonersForm = new Proposalgen_Form_AvailableToners(null,$this->_isAllowed);
        }

        return $this->_availableTonersForm;
    }

    /**
     * This creates, saves and deletes for the buttons inside the available toners jqGrid
     *
     * @param array|bool $data
     * @param bool       $deleteTonerId
     * @param bool       $isAdmin
     *
     * @return bool
     */
    public function updateAvailableTonersForm ($isAdmin, $data = false, $deleteTonerId = false)
    {
        $dealerTonerAttributesMapper = Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance();
        $tonerMapper                 = Proposalgen_Model_Mapper_Toner::getInstance();
        $validData                   = array();

        // We need to remove the prefix on the formValues
        foreach ($data as $key => $value)
        {
            $validData[substr($key, strlen("availableToners"))] = $data[$key];
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try
        {
            // If we are not deleting a toner
            if ($deleteTonerId == 0)
            {
                // If we are adding a new toner
                if ($validData['id'] == '' && $isAdmin)
                {
                    $toner = new Proposalgen_Model_Toner();
                    $toner->populate($validData);
                    $toner->sku            = $validData['systemSku'];
                    $toner->cost           = $validData['systemCost'];
                    $toner->tonerColorId   = $validData['tonerColorId'];
                    $toner->isSystemDevice = 1;
                    $toner->userId         = Zend_Auth::getInstance()->getIdentity()->id;

                    $validData['id'] = $tonerMapper->insert($toner);
                }
                // We are editing a toner
                else if ($validData['id'] > 0)
                {
                    // If we have admin privileges save to the toners table
                    if ($isAdmin)
                    {
                        $toner                 = new Proposalgen_Model_Toner();
                        $toner->id             = $validData['id'];
                        $toner->sku            = $validData['systemSku'];
                        $toner->cost           = $validData['systemCost'];
                        $toner->yield          = $validData['yield'];
                        $toner->tonerColorId   = $validData['tonerColorId'];
                        $toner->manufacturerId = $validData['manufacturerId'];

                        $tonerMapper->save($toner);
                    }
                }

                // Save to dealer Toner Attributes
                $dealerTonerAttributes = $dealerTonerAttributesMapper->findTonerAttributeByTonerId($validData['id'], $this->_dealerId);

                if ($dealerTonerAttributes)
                {
                    // This allows null to be saved to the database.
                    $dealerTonerAttributes->cost      = ($validData['dealerCost'] == '' ? new Zend_Db_Expr("NULL") : $validData['dealerCost']);
                    $dealerTonerAttributes->dealerSku = ($validData['dealerSku'] == '' ? new Zend_Db_Expr("NULL") : $validData['dealerSku']);

                    // If these are NULL we want to remove it from the database
                    if ($dealerTonerAttributes->cost == new Zend_Db_Expr("NULL") && $dealerTonerAttributes->dealerSku == new Zend_Db_Expr("NULL"))
                    {
                        $dealerTonerAttributesMapper->delete($dealerTonerAttributes);
                    }
                    // At least one is not null, lets save it
                    else
                    {
                        $dealerTonerAttributesMapper->save($dealerTonerAttributes);
                    }
                }
                else
                {
                    $dealerTonerAttributes            = new Proposalgen_Model_Dealer_Toner_Attribute();
                    $dealerTonerAttributes->tonerId   = $validData['id'];
                    $dealerTonerAttributes->dealerId  = $this->_dealerId;
                    $dealerTonerAttributes->cost      = $validData['dealerCost'];
                    $dealerTonerAttributes->dealerSku = $validData['dealerSku'];
                    $dealerTonerAttributesMapper->insert($dealerTonerAttributes);
                }
            }
            // We are deleting
            else
            {
                Proposalgen_Model_Mapper_Toner::getInstance()->delete($deleteTonerId);
                Proposalgen_Model_Mapper_TonerVendorManufacturer::getInstance()->updateTonerVendorByManufacturerId(Proposalgen_Model_Mapper_Toner::getInstance()->find($deleteTonerId)->manufacturerId);
            }

            $db->commit();

            return true;
        }
        catch (Exception $e)
        {
            $db->rollback();
            Tangent_Log::logException($e);

            return false;
        }

        return true;
    }

    /**
     * This creates, saves and deletes for the buttons inside the available options jqgrid
     *
     * @param array|bool $validatedData
     * @param bool       $deleteId
     *
     * @return bool
     */
    public function updateAvailableOptionsForm ($validatedData, $deleteId = false)
    {
        $optionMapper = Quotegen_Model_Mapper_Option::getInstance();

        $newData = array();

        //We need to remove the prefix on the formValues
        foreach ($validatedData as $key => $value)
        {
            $newData[substr($key, strlen("availableOptions"))] = $validatedData[$key];
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try
        {
            // If we are not deleting
            if (!$deleteId)
            {
                // If we are adding a new option
                if ($newData['id'] == 0)
                {
                    $option = new Quotegen_Model_Option();
                    $option->populate($newData);
                    $option->dealerId = $this->_dealerId;
                    $optionMapper->insert($option);
                }
                // We are editing
                else if ($newData['id'] > 0)
                {
                    $option = new Quotegen_Model_Option();
                    $option->populate($newData);
                    $option->dealerId = $this->_dealerId;
                    $optionMapper->save($option);
                    // We are deleting
                }
            }
            // We are deleting
            else
            {
                $optionMapper->delete($deleteId);
                $deviceOptionMapper = Quotegen_Model_Mapper_DeviceOption::getInstance();
                $deviceOptionMapper->deleteOptionsByDeviceId($deleteId);
            }

            $db->commit();

            return true;
        }
        catch (Exception $e)
        {
            $db->rollback();
            Tangent_Log::logException($e);

            return false;
        }
    }

    /**
     * This creates, saves and deletes for the buttons inside the hardware configurations jqgrid
     *
     * @param bool $validatedData
     * @param bool $deleteId
     *
     * @return bool
     */
    public function updateHardwareConfigurationsForm ($validatedData = false, $deleteId = false)
    {
        $configurationMapper = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();
        $newData             = array();

        //We need to remove the prefix on the formValues
        foreach ($validatedData as $key => $value)
        {
            $newData[substr($key, strlen("hardwareConfigurations"))] = $validatedData[$key];
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try
        {
            // If we are not deleting
            if (!$deleteId)
            {
                // If we are adding a new optionmanufacturerList
                if ($newData['id'] == 0)
                {
                    $deviceConfiguration = new Quotegen_Model_DeviceConfiguration();
                    $deviceConfiguration->populate($newData);
                    $deviceConfiguration->dealerId       = $this->_dealerId;
                    $deviceConfiguration->masterDeviceId = $this->masterDeviceId;
                    $newData['id']                       = $configurationMapper->insert($deviceConfiguration);
                }
                // We are editing
                else if ($newData['id'] > 0)
                {
                    $deviceConfiguration = new Quotegen_Model_DeviceConfiguration();
                    $deviceConfiguration->populate($newData);
                    $deviceConfiguration->dealerId       = $this->_dealerId;
                    $deviceConfiguration->masterDeviceId = $this->masterDeviceId;
                    $configurationMapper->save($deviceConfiguration);
                }

                // Save all the deviceConfigurationOptions
                if ($newData['id'] >= 0)
                {
                    $deviceConfigurationOptionMapper = Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance();
                    $deviceOptions                   = Quotegen_Model_Mapper_DeviceOption::getInstance()->fetchDeviceOptionListForDealerAndDevice($this->masterDeviceId, $this->_dealerId);

                    // Save Options
                    foreach ($deviceOptions as $option)
                    {
                        $deviceConfigurationOption = new Quotegen_Model_DeviceConfigurationOption();
                        $optionId                  = $option->optionId;
                        $quantity                  = $newData ["option{$optionId}"];

                        if ($quantity > 0)
                        {
                            //Save or Insert device options
                            $deviceConfigurationOption->deviceConfigurationId = $newData['id'];
                            $deviceConfigurationOption->optionId              = $optionId;
                            $deviceConfigurationOption->quantity              = $quantity;
                            $deviceConfigurationOption->saveObject();
                        }
                        else
                        {
                            // Delete existing device options
                            $deviceConfigurationOption = $deviceConfigurationOptionMapper->delete($deviceConfigurationOptionMapper->fetch($deviceConfigurationOptionMapper->getWhereId(array($newData['id'], $optionId))));
                            $deviceConfigurationOptionMapper->delete($deviceConfigurationOption);
                        }
                    }
                }
            }
            // We are deleting
            else
            {
                $configurationMapper = Quotegen_Model_Mapper_DeviceConfiguration::getInstance();
                $configurationMapper->delete($deleteId);
            }

            $db->commit();

            return true;
        }
        catch (Exception $e)
        {
            $db->rollback();
            Tangent_Log::logException($e);

            return false;
        }
    }

    /**
     * Sets the data to be used when populating forms
     * Must be called before getForms or else it will have no affect
     *
     * @param array $data
     */
    public function populate ($data)
    {
        $this->data = $data;
    }
}
