<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Services;

use Exception;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\AvailableOptionsForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\AvailableTonersForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\DeleteForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\DeviceAttributesForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\HardwareConfigurationsForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\HardwareOptimizationForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\HardwareQuoteForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\SuppliesAndServiceForm;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerMasterDeviceAttributeMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerTonerAttributeMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceTonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\JitCompatibleMasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DealerMasterDeviceAttributeModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DealerTonerAttributeModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceTonerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\JitCompatibleMasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationOptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionModel;
use Tangent\Logger\Logger;
use Zend_Auth;
use Zend_Db_Expr;
use Zend_Db_Table;
use Zend_Form;

/**
 * Class ManageMasterDevicesService
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Services
 */
class ManageMasterDevicesService
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
    public    $isQuoteDevice = false;


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
        }

        $formsToShow['delete'] = new DeleteForm();

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

        if ($form->isValid($data))
        {
            $json = $form->getValues();
        }
        else
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
     * Validates a devices toners.
     *
     * Devices must have at least one full set of OEM toners to be added into
     * the system. They also cannot be assigned toners that don't belong with
     * their toner configuration.
     *
     * @param array $tonerIds
     * @param int   $tonerConfigId
     * @param int   $manufacturerId
     * @param int   $masterDeviceId
     *
     * @return null|string
     */
    public function validateToners ($tonerIds, $tonerConfigId, $manufacturerId, $masterDeviceId = false)
    {
        $toners                  = array();
        $currentToners           = array();
        $validationErrorMessages = array();
        $newTonerIds = array();

        if ($masterDeviceId)
        {
            foreach (TonerMapper::getInstance()->fetchTonersAssignedToDevice($masterDeviceId) as $toner)
            {
                $currentToners[(int)$toner->id] = $toner;
            }
        }

        if ($tonerIds)
        {
            foreach (TonerMapper::getInstance()->find($tonerIds) as $toner)
            {
                $newTonerIds[(int)$toner->id] = $toner;
            }

            // Existing Toners
            foreach ($currentToners as $toner)
            {
                if (isset($newTonerIds[(int)$toner->id]))
                {
                    $toners[(int)$toner->id] = $toner;
                }
                else
                {
                    // Slated for deletion
                }
            }

            // Toners being added
            foreach ($newTonerIds as $toner)
            {
                if (!isset($toners[(int)$toner->id]))
                {
                    $toners[(int)$toner->id] = $toner;
                }
            }
        }

        $assignedOemTonerColors = array(
            TonerColorModel::BLACK       => false,
            TonerColorModel::CYAN        => false,
            TonerColorModel::MAGENTA     => false,
            TonerColorModel::YELLOW      => false,
            TonerColorModel::THREE_COLOR => false,
            TonerColorModel::FOUR_COLOR  => false,
        );

        $assignedTonerColors = array(
            TonerColorModel::BLACK       => false,
            TonerColorModel::CYAN        => false,
            TonerColorModel::MAGENTA     => false,
            TonerColorModel::YELLOW      => false,
            TonerColorModel::THREE_COLOR => false,
            TonerColorModel::FOUR_COLOR  => false,
        );

        /**
         * Figure out what color and type of toners we have
         */
        foreach ($toners as $toner)
        {
            $assignedTonerColors[(int)$toner->tonerColorId] = true;

            /**
             * OEM toners have the same manufacturer id as the device
             */
            if ((int)$toner->manufacturerId == (int)$manufacturerId)
            {
                $assignedOemTonerColors[(int)$toner->tonerColorId] = true;
            }
        }

        $tonerConfigurationColors = TonerConfigModel::getRequiredTonersForTonerConfig($tonerConfigId);

        /**
         * Devices require at least one full set of OEM toners for a given color set.
         */
        foreach ($tonerConfigurationColors as $requiredTonerColorId)
        {
            if (!$assignedOemTonerColors[$requiredTonerColorId])
            {
                // Missing a required toner color
                $validationErrorMessages[] = sprintf('Missing %1$s OEM Toner.', TonerColorModel::$ColorNames[$requiredTonerColorId]);
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
                $validationErrorMessages[] = sprintf('%1$s Toners cannot be assigned to this device.', TonerColorModel::$ColorNames[$assignedTonerColorId]);
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
     * @param array $validatedData
     * @param bool  $approved
     *
     * @return bool|string
     */
    public function saveSuppliesAndDeviceAttributes ($validatedData, $approved = false)
    {
        $masterDeviceMapper = MasterDeviceMapper::getInstance();
        $masterDevice       = $masterDeviceMapper->find($this->masterDeviceId);

        try
        {
            /**
             * If we need to insert the master device, do it here
             */
            if (!$masterDevice instanceof MasterDeviceModel)
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
                $masterDevice                 = new MasterDeviceModel($validatedData);
                $this->masterDeviceId         = $masterDeviceMapper->insert($masterDevice);
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

                if ($masterDevice instanceof MasterDeviceModel)
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
            if ($masterDevice instanceof MasterDeviceModel && !$masterDevice->isJitCompatible($this->_dealerId))
            {
                if ($validatedData['jitCompatibleMasterDevice'] === '1')
                {
                    $jitCompatibleMasterDevice                 = new JitCompatibleMasterDeviceModel();
                    $jitCompatibleMasterDevice->dealerId       = $this->_dealerId;
                    $jitCompatibleMasterDevice->masterDeviceId = $this->masterDeviceId;
                    JitCompatibleMasterDeviceMapper::getInstance()->insert($jitCompatibleMasterDevice);
                }
            }
            else if ($validatedData['jitCompatibleMasterDevice'] === '0')
            {
                $jitCompatibleMasterDevice                 = new JitCompatibleMasterDeviceModel();
                $jitCompatibleMasterDevice->dealerId       = $this->_dealerId;
                $jitCompatibleMasterDevice->masterDeviceId = $this->masterDeviceId;
                JitCompatibleMasterDeviceMapper::getInstance()->delete($jitCompatibleMasterDevice);
            }


            $dealerMasterDeviceAttribute                    = new DealerMasterDeviceAttributeModel();
            $dealerMasterDeviceAttribute->dealerId          = $this->_dealerId;
            $dealerMasterDeviceAttribute->masterDeviceId    = $this->masterDeviceId;
            $dealerMasterDeviceAttribute->laborCostPerPage  = ($validatedData['dealerLaborCostPerPage'] == '' ? new Zend_Db_Expr("NULL") : $validatedData['dealerLaborCostPerPage']);
            $dealerMasterDeviceAttribute->partsCostPerPage  = ($validatedData['dealerPartsCostPerPage'] == '' ? new Zend_Db_Expr("NULL") : $validatedData['dealerPartsCostPerPage']);
            $dealerMasterDeviceAttribute->leaseBuybackPrice = ($validatedData['leaseBuybackPrice'] == '' ? new Zend_Db_Expr("NULL") : $validatedData['leaseBuybackPrice']);

            $dealerMasterDeviceAttribute->saveObject();
        }
        catch (Exception $e)
        {
            Logger::logException($e);

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
        $deviceSwapMapper = DeviceSwapMapper::getInstance();
        try
        {
            if ($validatedData['isDeviceSwap'] == 1 && $validatedData['isSelling'] == 1)
            {
                $deviceSwap = new DeviceSwapModel(array('masterDeviceId' => $this->masterDeviceId, 'dealerId' => $this->_dealerId));

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
            Logger::logException($e);

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
        $deviceMapper = DeviceMapper::getInstance();
        try
        {
            if ($validatedData['isSelling'] == 1)
            {
                $device = new DeviceModel(array('masterDeviceId' => $this->masterDeviceId, 'dealerId' => $this->_dealerId));
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
            Logger::logException($e);

            return false;
        }

        return true;
    }


    /**
     * Gets the Supplies And Services Form
     *
     * @return SuppliesAndServiceForm
     */
    public function getSuppliesAndServicesForm ()
    {
        if (!isset($this->_suppliesAndServiceForm))
        {
            $this->_suppliesAndServiceForm = new SuppliesAndServiceForm(null, $this->_isAllowed, $this->isQuoteDevice);
            $masterDevice                  = MasterDeviceMapper::getInstance()->find($this->masterDeviceId);

            if ($this->data && !$masterDevice)
            {
                $this->_suppliesAndServiceForm->populate($this->data);
            }
            else if ($masterDevice)
            {
                $dealerMasterDeviceAttributes = DealerMasterDeviceAttributeMapper::getInstance()->find(array($this->masterDeviceId, $this->_dealerId));

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
     * @return DeviceAttributesForm
     */
    public function getDeviceAttributesForm ()
    {
        if (!isset($this->_deviceAttributesForm))
        {
            $this->_deviceAttributesForm = new DeviceAttributesForm(null, $this->_isAllowed);
            $masterDevice                = MasterDeviceMapper::getInstance()->find($this->masterDeviceId);

            if ($this->data && !$masterDevice)
            {
                $this->_deviceAttributesForm->populate($this->data);
            }
            else if ($masterDevice)
            {
                $this->_deviceAttributesForm->populate($masterDevice->toArray());
            }

            $jitCompatibleMasterDevice = JitCompatibleMasterDeviceMapper::getInstance()->find(array($this->masterDeviceId, $this->_dealerId));
            if ($jitCompatibleMasterDevice instanceof JitCompatibleMasterDeviceModel)
            {
                $this->_deviceAttributesForm->populate(array('jitCompatibleMasterDevice' => true));
            }
        }

        return $this->_deviceAttributesForm;
    }

    /**
     * Gets the Hardware Optimization Form
     *
     * @return HardwareOptimizationForm
     */
    public function getHardwareOptimizationForm ()
    {
        if (!isset($this->_hardwareOptimizationForm))
        {
            $this->_hardwareOptimizationForm = new HardwareOptimizationForm();
            $deviceSwap                      = DeviceSwapMapper::getInstance()->find(array($this->masterDeviceId, $this->_dealerId));

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
     * @return HardwareQuoteForm
     */
    public function getHardwareQuoteForm ()
    {
        if (!isset($this->_hardwareQuoteForm))
        {
            $this->_hardwareQuoteForm = new HardwareQuoteForm();
            $device                   = DeviceMapper::getInstance()->find(array($this->masterDeviceId, $this->_dealerId));

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
     * @return HardwareConfigurationsForm
     */
    public function getHardwareConfigurationsForm ()
    {
        if (!isset($this->_hardwareConfigurationsForm))
        {
            $this->_hardwareConfigurationsForm = new HardwareConfigurationsForm($this->masterDeviceId);
        }

        return $this->_hardwareConfigurationsForm;
    }

    /**
     * Gets the Available Options Form
     *
     * @return AvailableOptionsForm
     */
    public function getAvailableOptionsForm ()
    {
        if (!isset($this->_availableOptionsForm))
        {
            $this->_availableOptionsForm = new AvailableOptionsForm();
        }

        return $this->_availableOptionsForm;
    }

    /**
     * Gets the Available Toners Form
     *
     * @param int $id
     *
     * @return AvailableTonersForm
     */
    public function getAvailableTonersForm ($id = null)
    {
        if (!isset($this->_availableTonersForm))
        {
            $this->_availableTonersForm = new AvailableTonersForm($id, null);
        }

        return $this->_availableTonersForm;
    }

    /**
     * This creates, saves and deletes for the buttons inside the available toners jqGrid
     *
     * @param bool|array             $data
     * @param bool|int               $deleteTonerId
     * @param bool|MasterDeviceModel $masterDevice
     *
     * @return int
     */
    public function updateAvailableTonersForm ($data = false, $deleteTonerId = false, $masterDevice = false)
    {
        $dealerTonerAttributesMapper = DealerTonerAttributeMapper::getInstance();
        $deviceTonerMapper           = DeviceTonerMapper::getInstance();
        $tonerMapper                 = TonerMapper::getInstance();
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
                    $toner = new TonerModel();
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
                        $toner->cost             = TonerService::obfuscateTonerCost($validData['systemCost']);
                        $toner->isSystemDevice   = 0;
                    }
                    $toner->tonerColorId = $validData['tonerColorId'];
                    $toner->userId       = Zend_Auth::getInstance()->getIdentity()->id;

                    $validData['Id'] = $tonerMapper->insert($toner);
                    $toner->id       = (int)$validData['Id'];

                    /**
                     * Map The toner
                     */
                    if ($masterDevice instanceof MasterDeviceModel)
                    {
                        $deviceToner = $deviceTonerMapper->find(array($toner->id, $this->masterDeviceId));
                        if (!$deviceToner instanceof DeviceTonerModel)
                        {
                            $deviceToner                   = new DeviceTonerModel();
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
                                if ($deviceToner instanceof DeviceTonerModel)
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
                    $dealerTonerAttributes            = new DealerTonerAttributeModel();
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
                $toner = TonerMapper::getInstance()->find($deleteTonerId);
                if ($toner instanceof TonerModel)
                {
                    TonerMapper::getInstance()->delete($deleteTonerId);
                    TonerVendorManufacturerMapper::getInstance()->updateTonerVendorByManufacturerId($toner->manufacturerId);
                }
            }

            $db->commit();
            $id = 0;
            if ($toner instanceof TonerModel)
            {
                $id = $toner->id;
            }

            return $id;
        }
        catch (Exception $e)
        {
            $db->rollback();
            Logger::logException($e);

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
        $optionMapper = OptionMapper::getInstance();

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
                    $option = new OptionModel();
                    $option->populate($newData);
                    $option->dealerId = $this->_dealerId;
                    $optionMapper->insert($option);
                }
                // We are editing
                else if ($newData['id'] > 0)
                {
                    $option = new OptionModel();
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
                $deviceOptionMapper = DeviceOptionMapper::getInstance();
                $deviceOptionMapper->deleteOptionsByDeviceId($deleteId);
            }

            $db->commit();

            return true;
        }
        catch (Exception $e)
        {
            $db->rollback();
            Logger::logException($e);

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
        $configurationMapper = DeviceConfigurationMapper::getInstance();
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
                    $deviceConfiguration = new DeviceConfigurationModel();
                    $deviceConfiguration->populate($newData);
                    $deviceConfiguration->dealerId       = $this->_dealerId;
                    $deviceConfiguration->masterDeviceId = $this->masterDeviceId;
                    $newData['id']                       = $configurationMapper->insert($deviceConfiguration);
                }
                // We are editing
                else if ($newData['id'] > 0)
                {
                    $deviceConfiguration = new DeviceConfigurationModel();
                    $deviceConfiguration->populate($newData);
                    $deviceConfiguration->dealerId       = $this->_dealerId;
                    $deviceConfiguration->masterDeviceId = $this->masterDeviceId;
                    $configurationMapper->save($deviceConfiguration);
                }

                // Save all the deviceConfigurationOptions
                if ($newData['id'] >= 0)
                {
                    $deviceConfigurationOptionMapper = DeviceConfigurationOptionMapper::getInstance();
                    $deviceOptions                   = DeviceOptionMapper::getInstance()->fetchDeviceOptionListForDealerAndDevice($this->masterDeviceId, $this->_dealerId);

                    // Save Options
                    foreach ($deviceOptions as $option)
                    {
                        $deviceConfigurationOption = new DeviceConfigurationOptionModel();
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
                $configurationMapper = DeviceConfigurationMapper::getInstance();
                $configurationMapper->delete($deleteId);
            }

            $db->commit();

            return true;
        }
        catch (Exception $e)
        {
            $db->rollback();
            Logger::logException($e);

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

    /**
     * Assigns toners to a master device
     *
     * @param int   $masterDeviceId
     * @param int[] $tonerIds
     * @param bool  $approve
     *
     * @throws \Exception
     * @return bool
     */
    public function addToners ($masterDeviceId, $tonerIds, $approve = false)
    {
        $deviceTonerMapper = DeviceTonerMapper::getInstance();

        $toners         = array();
        $affectedToners = array();

        if ($masterDeviceId)
        {
            foreach (TonerMapper::getInstance()->fetchTonersAssignedToDevice($masterDeviceId) as $toner)
            {
                $toners[(int)$toner->id] = $toner;
            }
        }

        if ($tonerIds)
        {
            $tonersToAdd = TonerMapper::getInstance()->find($tonerIds);
            foreach ($tonersToAdd as $toner)
            {
                if (!isset($toners[(int)$toner->id]))
                {
                    $affectedToners[(int)$toner->id] = $toner;
                }
            }
        }

        $deviceTonerModel                   = new DeviceTonerModel();
        $deviceTonerModel->master_device_id = $masterDeviceId;
        $deviceTonerModel->isSystemDevice   = $approve;
        $deviceTonerModel->userId           = Zend_Auth::getInstance()->getIdentity()->id;
        foreach ($affectedToners as $toner)
        {
            $deviceTonerModel->toner_id = $toner->id;
            $deviceTonerMapper->insert($deviceTonerModel);
        }

        return true;
    }

    /**
     * Unassign toners from a device
     *
     * @param int   $masterDeviceId
     * @param int[] $tonerIds
     *
     * @return bool
     */
    public function removeToners ($masterDeviceId, $tonerIds)
    {
        $deviceTonerMapper = DeviceTonerMapper::getInstance();

        $toners         = array();
        $affectedToners = array();

        if ($masterDeviceId)
        {
            foreach (TonerMapper::getInstance()->fetchTonersAssignedToDevice($masterDeviceId) as $toner)
            {
                $toners[(int)$toner->id] = $toner;
            }
        }

        if ($tonerIds)
        {
            $tonersToDelete = TonerMapper::getInstance()->find($tonerIds);
            foreach ($tonersToDelete as $toner)
            {
                if (isset($toners[(int)$toner->id]))
                {
                    $affectedToners[(int)$toner->id] = $toner;
                }
            }
        }

        $deviceTonerModel                   = new DeviceTonerModel();
        $deviceTonerModel->master_device_id = $masterDeviceId;
        foreach ($affectedToners as $toner)
        {
            $deviceTonerModel->toner_id = $toner->id;
            $deviceTonerMapper->delete($deviceTonerModel);
        }

        return true;
    }
}
