<?php
/**
 * Class Quotegen_Service_QuoteDevice
 */
class Quotegen_Service_QuoteDevice
{
    const DEFAULT_QUANTITY_DEFAULT_GROUP   = 1;
    const DEFAULT_PAGE_COVERAGE_MONOCHROME = 6;
    const DEFAULT_PAGE_COVERAGE_COLOR      = 25;

    /**
     * @var int
     */
    protected $_userId;

    /**
     * @var Quotegen_Model_QuoteSetting
     */
    protected $_quoteSetting;

    /**
     * @var int
     */
    protected $_quoteId;

    /**
     * @var Quotegen_Model_Quote
     */
    protected $_quote;

    /**
     * @param int $userId  id of the user logged in
     * @param int $quoteId if of the quote we want to work with
     */
    public function __construct ($userId, $quoteId)
    {
        $this->_userId  = $userId;
        $this->_quoteId = $quoteId;
    }

    /**
     * Adds a quote device to the system
     *
     * @param      $masterDeviceId
     *
     * @param bool $memjetOptimization
     *
     * @return Quotegen_Model_QuoteDevice
     */
    public function addDeviceToQuote ($masterDeviceId, $memjetOptimization = false)
    {
        // Get the quote settings
        $quoteSetting = $this->getQuoteSetting();

        // Sync the quote device
        $quoteDevice = $this->syncDevice($masterDeviceId);

        // Setup some defaults that don't get synced
        $quoteDevice->quoteId       = $this->getQuote()->id;
        $quoteDevice->margin        = $quoteSetting->deviceMargin;
        $quoteDevice->packageCost   = $quoteDevice->calculatePackageCost();
        $quoteDevice->packageMarkup = 0;
        $quoteDevice->buyoutValue   = 0;

        // Save our device
        $quoteDeviceId = Quotegen_Model_Mapper_QuoteDevice::getInstance()->insert($quoteDevice);

        // Add to default group
        Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance()->insertDeviceInDefaultGroup($this->getQuote()->id, (int)$quoteDeviceId, ($memjetOptimization ? $this->getMemjetMasterDeviceQuantity($masterDeviceId) : $this->getMasterDeviceQuantity($masterDeviceId)));

        // Create Link to Device
        $quoteDeviceConfiguration                 = new Quotegen_Model_QuoteDeviceConfiguration();
        $quoteDeviceConfiguration->masterDeviceId = $masterDeviceId;
        $quoteDeviceConfiguration->quoteDeviceId  = $quoteDeviceId;
        Quotegen_Model_Mapper_QuoteDeviceConfiguration::getInstance()->insert($quoteDeviceConfiguration);

        return $quoteDevice;
    }

    /**
     * Gets the number of master devices, if quote id is found inside hardware_optimization_quotes it will count
     * the master device ids in device_instance_replacement_devices
     *
     * @param $masterDeviceId
     *
     * @return int
     */
    public function getMasterDeviceQuantity ($masterDeviceId)
    {
        $hardwareOptimizationQuoteMapper = Hardwareoptimization_Model_Mapper_Hardware_Optimization_Quote::getInstance();
        $hardwareOptimizationQuote       = $hardwareOptimizationQuoteMapper->find($this->_quoteId);

        if ($hardwareOptimizationQuote instanceof Hardwareoptimization_Model_Hardware_Optimization_Quote)
        {
            $count = Proposalgen_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance()->countReplacementDevicesById($hardwareOptimizationQuote->hardwareOptimizationId, $masterDeviceId);
        }
        else
        {
            $count = self::DEFAULT_QUANTITY_DEFAULT_GROUP;
        }

        return $count;
    }

    /**
     * Gets the number of master devices, if quote id is found inside memjet_optimization_quotes it will count
     * the master device ids in memjet_device_instance_replacement_devices
     *
     * @param $masterDeviceId
     *
     * @return int
     */
    public function getMemjetMasterDeviceQuantity ($masterDeviceId)
    {
        $memjetOptimizationQuoteMapper = Memjetoptimization_Model_Mapper_Memjet_Optimization_Quote::getInstance();
        $memjetOptimizationQuote       = $memjetOptimizationQuoteMapper->find($this->_quoteId);

        if ($memjetOptimizationQuote instanceof Memjetoptimization_Model_Memjet_Optimization_Quote)
        {
            $count = Memjetoptimization_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance()->countReplacementDevicesById($memjetOptimizationQuote->memjetOptimizationId, $masterDeviceId);
        }
        else
        {
            $count = self::DEFAULT_QUANTITY_DEFAULT_GROUP;
        }

        return $count;
    }

    /**
     * Gets the quote setting that has been overridden by user settings based on the userId in the class
     *
     * @return Quotegen_Model_QuoteSetting
     */
    protected function getQuoteSetting ()
    {
        if (!isset($this->_quoteSetting))
        {
            $this->_quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
            $this->_quoteSetting->applyOverride(Preferences_Model_Mapper_User_Setting::getInstance()->find($this->_userId)->getQuoteSettings());
        }

        return $this->_quoteSetting;
    }

    /**
     * @return Quotegen_Model_Quote
     */
    protected function getQuote ()
    {
        if (!isset($this->_quote))
        {
            $this->_quote = Quotegen_Model_Mapper_Quote::getInstance()->find($this->_quoteId);
        }

        return $this->_quote;
    }

    /**
     * Syncs a quote device with it's master device id record.  This will sync either with a master
     * device id, or a quote device.  Will return false if it cannot resolve the device based on
     * passed parameters
     *
     * @param int|Quotegen_Model_QuoteDevice $object
     *       A master device id, or a quote device
     *
     * @return bool|\Quotegen_Model_QuoteDevice
     */
    public function syncDevice ($object)
    {
        if ($object instanceof Quotegen_Model_QuoteDevice)
        {
            // If a quote device is passed we want to preserve and id that isn't going to be overwritten
            $quoteDevice = $object;
            $device      = $object->getDevice();
        }
        else
        {
            // If an id is passed then we want a new quote device
            $quoteDevice = new Quotegen_Model_QuoteDevice();

            $device = Quotegen_Model_Mapper_Device::getInstance()->find(array($object, $this->getQuote()->getClient()->dealerId));
        }

        if (!$device instanceof Quotegen_Model_Device)
        {
            return false;
        }

        // Get the master device
        $masterDevice = $device->getMasterDevice();

        // Sync the settings
        $quoteDevice->name          = $masterDevice->getFullDeviceName();
        $quoteDevice->oemSku        = $device->oemSku;
        $quoteDevice->dealerSku     = $device->dealerSku;
        $quoteDevice->tonerConfigId = $masterDevice->tonerConfigId;
        $quoteDevice->cost          = $device->cost;
        $quoteDevice                = $this->syncCostPerPageForDevice($quoteDevice, $masterDevice);

        // Sync our cost per page and return it
        return $quoteDevice;
    }

    /**
     * Syncs a quote device's cost per page to be up to date with the latest master device cost per page
     *
     * @param Quotegen_Model_QuoteDevice     $quoteDevice
     * @param Proposalgen_Model_MasterDevice $masterDevice
     *
     * @return \Quotegen_Model_QuoteDevice
     */
    protected function syncCostPerPageForDevice (Quotegen_Model_QuoteDevice $quoteDevice, Proposalgen_Model_MasterDevice $masterDevice)
    {
        $oemCostPerPageSetting                         = new Proposalgen_Model_CostPerPageSetting();
        $oemCostPerPageSetting->adminCostPerPage       = 0;
        $oemCostPerPageSetting->laborCostPerPage       = 0;
        $oemCostPerPageSetting->partsCostPerPage       = 0;
        $oemCostPerPageSetting->pageCoverageMonochrome = ($this->getQuote()->pageCoverageMonochrome) ? $this->getQuote()->pageCoverageMonochrome : self::DEFAULT_PAGE_COVERAGE_MONOCHROME;
        $oemCostPerPageSetting->pageCoverageColor      = ($this->getQuote()->pageCoverageColor) ? $this->getQuote()->pageCoverageColor : self::DEFAULT_PAGE_COVERAGE_COLOR;
        $oemCostPerPageSetting->monochromeTonerRankSet = $this->getQuote()->getDealerMonochromeRankSet();
        $oemCostPerPageSetting->colorTonerRankSet      = $this->getQuote()->getDealerColorRankSet();

        // Calculate the cost per page
        $costPerPage = $masterDevice->calculateCostPerPage($oemCostPerPageSetting);

        // Set our mono cost per page
        $quoteDevice->costPerPageMonochrome = $costPerPage->monochromeCostPerPage;

        // Only set our color if the device is color
        $quoteDevice->costPerPageColor = 0;

        if ($masterDevice->isColor())
        {
            $quoteDevice->costPerPageColor = $costPerPage->colorCostPerPage;
        }

        return $quoteDevice;
    }

    /**
     * Syncs a device configuration into a quote device for a quote.
     * If a device does not exist for the current quote it will create it for you.
     *
     * @param $quoteDevice Quotegen_Model_QuoteDevice
     *                     The quote device to sync
     * @param $syncOptions boolean
     *                     If set to true, it will sync the quote options associated with the quote device
     *
     * @return boolean Returns true if the sync was successful. If it was false, chances are it is because there is no
     *         link between the quote device and a device in the system.
     */
    public function performSyncOnQuoteDevice (Quotegen_Model_QuoteDevice $quoteDevice, $syncOptions = true)
    {
        // Sync the device and save
        $quoteDevice = $this->syncDevice($quoteDevice);

        if (!$quoteDevice instanceof Quotegen_Model_QuoteDevice)
        {
            return false;
        }

        // Sync our options
        if ($syncOptions)
        {
            /* @var $quoteDeviceOption Quotegen_Model_QuoteDeviceOption */
            foreach ($quoteDevice->getQuoteDeviceOptions() as $quoteDeviceOption)
            {
                // Only sync options that still have a link back to the master
                $deviceOption = $quoteDeviceOption->getDeviceOption();
                if ($deviceOption)
                {
                    $quoteDeviceOption = $this->syncOption($quoteDeviceOption, $deviceOption);
                    Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->save($quoteDeviceOption);
                }
            }
        }

        $quoteDevice->packageCost = $quoteDevice->calculatePackageCost();

        Quotegen_Model_Mapper_QuoteDevice::getInstance()->save($quoteDevice);

        return true;
    }

    /**
     * Syncs a quote device option with an option
     *
     * @param Quotegen_Model_QuoteDeviceOption $quoteDeviceOption The quote device option that will be updated
     * @param Quotegen_Model_DeviceOption      $deviceOption      The option to update the quote device option with
     *
     * @return Quotegen_Model_QuoteDeviceOption The updated quote device option
     */
    public function syncOption (Quotegen_Model_QuoteDeviceOption $quoteDeviceOption, Quotegen_Model_DeviceOption $deviceOption)
    {
        // Copy the option
        $quoteDeviceOption->oemSku           = $deviceOption->getOption()->oemSku;
        $quoteDeviceOption->dealerSku        = $deviceOption->getOption()->dealerSku;
        $quoteDeviceOption->name             = $deviceOption->getOption()->name;
        $quoteDeviceOption->description      = $deviceOption->getOption()->description;
        $quoteDeviceOption->cost             = $deviceOption->getOption()->cost;
        $quoteDeviceOption->includedQuantity = $deviceOption->includedQuantity;

        return $quoteDeviceOption;
    }
}