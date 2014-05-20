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
    protected $_isAdmin;
    public $isQuoteDevice = false;


    /**
     * @param int  $masterDeviceId The id of the Master Device
     * @param int  $dealerId
     * @param bool $isAllowed
     * @param bool $isAdmin
     */
    public function __construct ($masterDeviceId, $dealerId, $isAllowed = false, $isAdmin = false)
    {
        $this->masterDeviceId = $masterDeviceId;
        $this->_dealerId      = $dealerId;
        $this->_isAllowed     = $isAllowed;
        $this->_isAdmin       = $isAdmin;
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

        $formsToShow['delete'] = new Proposalgen_Form_MasterDeviceManagement_Delete();

        return $formsToShow;
    }


    /**
     * @param Zend_Form $form
     * @param string    $data []
     * @param string    $formName
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

                foreach ($errorElement as $elementErrorMessage)
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
     * @param string $tonerList
     * @param int    $tonerConfigId
     * @param int    $manufacturerId
     *
     * @return null|string
     */
    public function validateToners ($tonerList, $tonerConfigId, $manufacturerId)
    {
        $toners                  = [];
        $validationErrorMessages = [];

        if ($tonerList)
        {
            $toners = Proposalgen_Model_Mapper_Toner::getInstance()->fetchListOfToners($tonerList, $this->masterDeviceId);
        }

        $assignedOemTonerColors = array(
            Proposalgen_Model_TonerColor::BLACK       => false,
            Proposalgen_Model_TonerColor::CYAN        => false,
            Proposalgen_Model_TonerColor::MAGENTA     => false,
            Proposalgen_Model_TonerColor::YELLOW      => false,
            Proposalgen_Model_TonerColor::THREE_COLOR => false,
            Proposalgen_Model_TonerColor::FOUR_COLOR  => false,
        );

        $assignedTonerColors = array(
            Proposalgen_Model_TonerColor::BLACK       => false,
            Proposalgen_Model_TonerColor::CYAN        => false,
            Proposalgen_Model_TonerColor::MAGENTA     => false,
            Proposalgen_Model_TonerColor::YELLOW      => false,
            Proposalgen_Model_TonerColor::THREE_COLOR => false,
            Proposalgen_Model_TonerColor::FOUR_COLOR  => false,
        );

        /**
         * Figure out what color and type of toners we have
         */
        foreach ($toners as $toner)
        {
            $assignedTonerColors[$toner['tonerColorId']] = true;

            /**
             * OEM toners have the same manufacturer id as the device
             */
            if ($toner['manufacturerId'] == $manufacturerId)
            {
                $assignedOemTonerColors[$toner['tonerColorId']] = true;
            }
        }

        $tonerConfigurationColors = Proposalgen_Model_TonerConfig::getRequiredTonersForTonerConfig($tonerConfigId);

        /**
         * Devices require at least one full set of OEM toners for a given color set.
         */
        foreach ($tonerConfigurationColors as $requiredTonerColorId)
        {
            if (!$assignedOemTonerColors[$requiredTonerColorId])
            {
                // Missing a required toner color
                $validationErrorMessages[] = sprintf('Missing %1$s OEM Toner.', Proposalgen_Model_TonerColor::$ColorNames[$requiredTonerColorId]);
            }
        }

        /**
         * Some devices cannot be assigned certain colors (IE Black devices can only have black toners)
         */
        foreach ($assignedTonerColors as $assignedTonerColorId => $isAssigned)
        {
            if ($isAssigned && !in_array($assignedTonerColorId, $tonerConfigurationColors))
            {
                // Invalid Toner Color assigned to the device
                $validationErrorMessages[] = sprintf('%1$s Toners cannot be assigned to this device.', Proposalgen_Model_TonerColor::$ColorNames[$assignedTonerColorId]);
            }
        }

        if (count($validationErrorMessages) > 0)
        {
            return implode(' ', $validationErrorMessages);
        }
        else
        {
            return true;
        }
    }


    /**
     * @param      $validatedData
     *
     * @param bool $tonersList
     *
     * @param bool $approved
     *
     * @return bool|string
     */
    public function saveSuppliesAndDeviceAttributes ($validatedData, $tonersList = false, $approved = false)
    {
        $masterDeviceMapper = Proposalgen_Model_Mapper_MasterDevice::getInstance();
        $deviceTonerMapper  = Proposalgen_Model_Mapper_DeviceToner::getInstance();
        $masterDevice       = $masterDeviceMapper->find($this->masterDeviceId);

        try
        {
            /**
             * If we need to insert the master device, do it here
             */
            if (!$masterDevice instanceof Proposalgen_Model_MasterDevice)
            {
                if ($this->_isAdmin)
                {
                    $validatedData['isSystemDevice'] = 1;
                }
                else
                {
                    $validatedData['isSystemDevice'] = 0;
                }

                $validatedData['dateCreated'] = date('Y-m-d H:i:s');
                $validatedData['userId']      = Zend_Auth::getInstance()->getIdentity()->id;
                $masterDevice                 = new Proposalgen_Model_MasterDevice($validatedData);
                $this->masterDeviceId         = $masterDeviceMapper->insert($masterDevice);
            }

            /**
             * Save Toners
             */
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

                        $deviceToner = $deviceTonerMapper->find(array($assignedToner->id, $this->masterDeviceId));
                        if ($deviceToner instanceof Proposalgen_Model_DeviceToner && ($shouldDelete && ($assignedToner->isSystemDevice == false || $this->_isAllowed || $deviceToner->isSystemDevice == false)))
                        {
                            Proposalgen_Model_Mapper_DeviceToner::getInstance()->delete(array($assignedToner->id, $this->masterDeviceId));
                        }
                    }
                }
            }

            foreach ($tonerIds as $tonerId)
            {
                $alreadyExists = false;

                foreach ($assignedToners as $tonersByManufacturer)
                {
                    foreach ($tonersByManufacturer as $tonersByColor)
                    {
                        foreach ($tonersByColor as $assignedToner)
                        {
                            if ($tonerId === $assignedToner->id)
                            {
                                $alreadyExists = true;
                            }
                        }
                    }
                }

                if ($alreadyExists == false)
                {
                    $deviceToner                   = new Proposalgen_Model_DeviceToner();
                    $deviceToner->master_device_id = (int)$this->masterDeviceId;
                    $deviceToner->toner_id         = (int)$tonerId;
                    $deviceToner->isSystemDevice   = ($this->_isAdmin ? 1 : 0);
                    $deviceToner->userId           = Zend_Auth::getInstance()->getIdentity()->id;

                    // If it doesn't exist in the database then insert it
                    if (!$deviceTonerMapper->find(array($deviceToner->toner_id, $deviceToner->master_device_id)) instanceof Proposalgen_Model_DeviceToner)
                    {
                        $deviceTonerMapper->insert($deviceToner);
                    }
                }
            }

            /**
             * Save Master Device Attributes
             */
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

                if ($validatedData['leaseBuybackPrice'] == '')
                {
                    $validatedData['leaseBuybackPrice'] = new Zend_Db_Expr("NULL");
                }

                if ($validatedData['ppmBlack'] == '')
                {
                    $validatedData['ppmBlack'] = new Zend_Db_Expr("NULL");
                }

                if ($validatedData['ppmColor'] == '')
                {
                    $validatedData['ppmColor'] = new Zend_Db_Expr("NULL");
                }

                if (isset($validatedData['wattsPowerNormal']))
                {
                    $validatedData['wattsPowerNormal'] = ceil($validatedData['wattsPowerNormal']);
                }

                if (isset($validatedData['wattsPowerIdle']))
                {
                    $validatedData['wattsPowerIdle'] = ceil($validatedData['wattsPowerIdle']);
                }


                if ($masterDevice instanceof Proposalgen_Model_MasterDevice)
                {
                    if ($this->_isAdmin && $approved)
                    {
                        $validatedData['isSystemDevice'] = 1;
                    }

                    $masterDevice->populate($validatedData);
                    $masterDevice->recalculateMaximumRecommendedMonthlyPageVolume();
                    $masterDeviceMapper->save($masterDevice);
                }
            }

            // If we are not a JIT compatible device
            if ($masterDevice instanceof Proposalgen_Model_MasterDevice && !$masterDevice->isJitCompatible($this->_dealerId))
            {
                if ($validatedData['jitCompatibleMasterDevice'] === '1')
                {
                    $jitCompatibleMasterDevice                 = new Proposalgen_Model_JitCompatibleMasterDevice();
                    $jitCompatibleMasterDevice->dealerId       = $this->_dealerId;
                    $jitCompatibleMasterDevice->masterDeviceId = $this->masterDeviceId;
                    Proposalgen_Model_Mapper_JitCompatibleMasterDevice::getInstance()->insert($jitCompatibleMasterDevice);
                }
            }
            else if ($validatedData['jitCompatibleMasterDevice'] === '0')
            {
                $jitCompatibleMasterDevice                 = new Proposalgen_Model_JitCompatibleMasterDevice();
                $jitCompatibleMasterDevice->dealerId       = $this->_dealerId;
                $jitCompatibleMasterDevice->masterDeviceId = $this->masterDeviceId;
                Proposalgen_Model_Mapper_JitCompatibleMasterDevice::getInstance()->delete($jitCompatibleMasterDevice);
            }


            $dealerMasterDeviceAttribute                    = new Proposalgen_Model_Dealer_Master_Device_Attribute();
            $dealerMasterDeviceAttribute->dealerId          = $this->_dealerId;
            $dealerMasterDeviceAttribute->masterDeviceId    = $this->masterDeviceId;
            $dealerMasterDeviceAttribute->laborCostPerPage  = ($validatedData['dealerLaborCostPerPage'] == '' ? new Zend_Db_Expr("NULL") : $validatedData['dealerLaborCostPerPage']);
            $dealerMasterDeviceAttribute->partsCostPerPage  = ($validatedData['dealerPartsCostPerPage'] == '' ? new Zend_Db_Expr("NULL") : $validatedData['dealerPartsCostPerPage']);
            $dealerMasterDeviceAttribute->leaseBuybackPrice = ($validatedData['leaseBuybackPrice'] == '' ? new Zend_Db_Expr("NULL") : $validatedData['leaseBuybackPrice']);

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
     * @return Proposalgen_Form_MasterDeviceManagement_SuppliesAndService
     */
    public function getSuppliesAndServicesForm ()
    {
        if (!isset($this->_suppliesAndServiceForm))
        {
            $this->_suppliesAndServiceForm = new Proposalgen_Form_MasterDeviceManagement_SuppliesAndService(null, $this->_isAllowed, $this->isQuoteDevice);
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
                    $this->_suppliesAndServiceForm->populate(array("dealerLaborCostPerPage" => $dealerMasterDeviceAttributes->laborCostPerPage, "dealerPartsCostPerPage" => $dealerMasterDeviceAttributes->partsCostPerPage, 'leaseBuybackPrice' => $dealerMasterDeviceAttributes->leaseBuybackPrice));
                }

                $this->_suppliesAndServiceForm->populate($masterDevice->toArray());
            }
        }

        return $this->_suppliesAndServiceForm;
    }

    /**
     * Gets the Device Attributes Form
     *
     * @return Proposalgen_Form_MasterDeviceManagement_DeviceAttributes
     */
    public function getDeviceAttributesForm ()
    {
        if (!isset($this->_deviceAttributesForm))
        {
            $this->_deviceAttributesForm = new Proposalgen_Form_MasterDeviceManagement_DeviceAttributes(null, $this->_isAllowed);
            $masterDevice                = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($this->masterDeviceId);

            if ($this->data && !$masterDevice)
            {
                $this->_deviceAttributesForm->populate($this->data);
            }
            else if ($masterDevice)
            {
                $this->_deviceAttributesForm->populate($masterDevice->toArray());
            }

            $jitCompatibleMasterDevice = Proposalgen_Model_Mapper_JitCompatibleMasterDevice::getInstance()->find(array($this->masterDeviceId, $this->_dealerId));
            if ($jitCompatibleMasterDevice instanceof Proposalgen_Model_JitCompatibleMasterDevice)
            {
                $this->_deviceAttributesForm->populate(array('jitCompatibleMasterDevice' => true));
            }
        }

        return $this->_deviceAttributesForm;
    }

    /**
     * Gets the Hardware Optimization Form
     *
     * @return Proposalgen_Form_MasterDeviceManagement_HardwareOptimization
     */
    public function getHardwareOptimizationForm ()
    {
        if (!isset($this->_hardwareOptimizationForm))
        {
            $this->_hardwareOptimizationForm = new Proposalgen_Form_MasterDeviceManagement_HardwareOptimization();
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
     * @return Proposalgen_Form_MasterDeviceManagement_HardwareQuote
     */
    public function getHardwareQuoteForm ()
    {
        if (!isset($this->_hardwareQuoteForm))
        {
            $this->_hardwareQuoteForm = new Proposalgen_Form_MasterDeviceManagement_HardwareQuote();
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
     * @return Proposalgen_Form_MasterDeviceManagement_HardwareConfigurations
     */
    public function getHardwareConfigurationsForm ()
    {
        if (!isset($this->_hardwareConfigurationsForm))
        {
            $this->_hardwareConfigurationsForm = new Proposalgen_Form_MasterDeviceManagement_HardwareConfigurations($this->masterDeviceId);
        }

        return $this->_hardwareConfigurationsForm;
    }

    /**
     * Gets the Available Options Form
     *
     * @return Proposalgen_Form_MasterDeviceManagement_AvailableOptions
     */
    public function getAvailableOptionsForm ()
    {
        if (!isset($this->_availableOptionsForm))
        {
            $this->_availableOptionsForm = new Proposalgen_Form_MasterDeviceManagement_AvailableOptions();
        }

        return $this->_availableOptionsForm;
    }

    /**
     * Gets the Available Toners Form
     *
     * @param int $id
     *
     * @return Proposalgen_Form_MasterDeviceManagement_AvailableToners
     */
    public function getAvailableTonersForm ($id = null)
    {
        if (!isset($this->_availableTonersForm))
        {
            $this->_availableTonersForm = new Proposalgen_Form_MasterDeviceManagement_AvailableToners(null, $id);
        }

        return $this->_availableTonersForm;
    }

    /**
     * This creates, saves and deletes for the buttons inside the available toners jqGrid
     *
     * @param bool|array                          $data
     * @param bool|int                            $deleteTonerId
     * @param bool|Proposalgen_Model_MasterDevice $masterDevice
     *
     * @return int
     */
    public function updateAvailableTonersForm ($data = false, $deleteTonerId = false, $masterDevice = false)
    {
        $dealerTonerAttributesMapper = Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance();
        $deviceTonerMapper           = Proposalgen_Model_Mapper_DeviceToner::getInstance();
        $tonerMapper                 = Proposalgen_Model_Mapper_Toner::getInstance();
        $validData                   = array();
        $toner                       = null;

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
                if ($validData['dealerCost'] == '')
                {
                    $validData['dealerCost'] = new Zend_Db_Expr("NULL");
                }
                // If we are adding a new toner
                if ($validData['Id'] == '')
                {
                    $toner = new Proposalgen_Model_Toner();
                    $toner->populate($validData);
                    $toner->sku = $validData['systemSku'];
                    if ($this->_isAdmin)
                    {
                        $toner->cost           = $validData['systemCost'];
                        $toner->isSystemDevice = 1;
                    }
                    else
                    {
                        // We need to apply a random 5 to 10% margin to the system cost they add to our system
                        // Their dealer cost gets set to the cost they chose
                        $validData['dealerCost'] = $validData['systemCost'];
                        $toner->cost             = Proposalgen_Service_Toner::obfuscateTonerCost($validData['systemCost']);
                        $toner->isSystemDevice   = 0;
                    }
                    $toner->tonerColorId = $validData['tonerColorId'];
                    $toner->userId       = Zend_Auth::getInstance()->getIdentity()->id;

                    $validData['Id'] = $tonerMapper->insert($toner);
                    $toner->id       = (int)$validData['Id'];

                    /**
                     * Map The toner
                     */
                    if ($masterDevice instanceof Proposalgen_Model_MasterDevice)
                    {
                        $deviceToner = $deviceTonerMapper->find(array($toner->id, $this->masterDeviceId));
                        if (!$deviceToner instanceof Proposalgen_Model_DeviceToner)
                        {
                            $deviceToner                   = new Proposalgen_Model_DeviceToner();
                            $deviceToner->master_device_id = $masterDevice->id;
                            $deviceToner->toner_id         = $toner->id;

                            $deviceToner->isSystemDevice = $this->_isAdmin;

                            $deviceTonerMapper->insert($deviceToner);
                        }
                    }
                }
                // We are editing a toner
                else if ($validData['Id'] > 0)
                {
                    $toner = $tonerMapper->find($validData['Id']);

                    if ($toner)
                    {
                        if ($this->_isAdmin || $toner->isSystemDevice == 0)
                        {
                            if ($validData['saveAndApproveHdn'] == 1 && $this->_isAdmin)
                            {
                                $toner->isSystemDevice = 1;

                                $deviceToner = $deviceTonerMapper->find(array($toner->id, $this->masterDeviceId));
                                if ($deviceToner instanceof Proposalgen_Model_DeviceToner)
                                {
                                    $deviceToner->isSystemDevice = 1;
                                    $deviceTonerMapper->save($deviceToner);
                                }
                            }

                            $toner->sku            = $validData['systemSku'];
                            $toner->cost           = $validData['systemCost'];
                            $toner->yield          = $validData['yield'];
                            $toner->tonerColorId   = $validData['tonerColorId'];
                            $toner->manufacturerId = $validData['manufacturerId'];
                            $tonerMapper->save($toner);
                        }
                    }
                }

                // Save to dealer Toner Attributes
                $dealerTonerAttributes = $dealerTonerAttributesMapper->findTonerAttributeByTonerId($validData['Id'], $this->_dealerId);

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
                    $dealerTonerAttributes->tonerId   = $validData['Id'];
                    $dealerTonerAttributes->dealerId  = $this->_dealerId;
                    $dealerTonerAttributes->cost      = $validData['dealerCost'];
                    $dealerTonerAttributes->dealerSku = $validData['dealerSku'];
                    $dealerTonerAttributesMapper->insert($dealerTonerAttributes);
                }
            }
            // We are deleting
            else
            {
                $toner = Proposalgen_Model_Mapper_Toner::getInstance()->find($deleteTonerId);
                if ($toner instanceof Proposalgen_Model_Toner)
                {
                    Proposalgen_Model_Mapper_Toner::getInstance()->delete($deleteTonerId);
                    Proposalgen_Model_Mapper_TonerVendorManufacturer::getInstance()->updateTonerVendorByManufacturerId($toner->manufacturerId);
                }
            }

            $db->commit();
            $id = 0;
            if ($toner instanceof Proposalgen_Model_Toner)
            {
                $id = $toner->id;
            }

            return $id;
        }
        catch (Exception $e)
        {
            $db->rollback();
            Tangent_Log::logException($e);

            return false;
        }
    }

    /**
     * This creates, saves and deletes for the buttons inside the available options jqGrid
     *
     * @param bool|array $validatedData
     * @param bool|int   $deleteId
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
     * This creates, saves and deletes for the buttons inside the hardware configurations jqGrid
     *
     * @param bool|array $validatedData
     * @param bool|int   $deleteId
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
                // If we are adding a new optionManufacturerList
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
