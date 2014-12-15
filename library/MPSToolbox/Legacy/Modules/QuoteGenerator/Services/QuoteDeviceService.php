<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Services;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceGroupDeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceConfigurationModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceOptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceOptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel;

/**
 * Class QuoteDeviceService
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Services
 */
class QuoteDeviceService
{
    const DEFAULT_QUANTITY_DEFAULT_GROUP   = 1;
    const DEFAULT_PAGE_COVERAGE_MONOCHROME = 6;
    const DEFAULT_PAGE_COVERAGE_COLOR      = 25;

    /**
     * @var int
     */
    protected $_userId;

    /**
     * @var int
     */
    protected $_quoteId;

    /**
     * @var QuoteModel
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
     * @param int $masterDeviceId
     * @param int $quantity
     *
     * @return QuoteDeviceModel
     */
    public function addDeviceToQuote ($masterDeviceId, $quantity = self::DEFAULT_QUANTITY_DEFAULT_GROUP)
    {
        // Sync the quote device
        $quoteDevice = $this->syncDevice($masterDeviceId);

        // Setup some defaults that don't get synced
        $quoteDevice->quoteId       = $this->getQuote()->id;
        $quoteDevice->margin        = $this->getQuote()->getClient()->getClientSettings()->quoteSettings->defaultDeviceMargin;
        $quoteDevice->packageCost   = $quoteDevice->calculatePackageCost();
        $quoteDevice->packageMarkup = 0;
        $quoteDevice->buyoutValue   = 0;

        // Save our device
        $quoteDeviceId = QuoteDeviceMapper::getInstance()->insert($quoteDevice);

        // Add to default group
        QuoteDeviceGroupDeviceMapper::getInstance()->insertDeviceInDefaultGroup($this->getQuote()->id, (int)$quoteDeviceId, $quantity);

        // Create Link to Device
        $quoteDeviceConfiguration                 = new QuoteDeviceConfigurationModel();
        $quoteDeviceConfiguration->masterDeviceId = $masterDeviceId;
        $quoteDeviceConfiguration->quoteDeviceId  = $quoteDeviceId;
        QuoteDeviceConfigurationMapper::getInstance()->insert($quoteDeviceConfiguration);

        return $quoteDevice;
    }

    /**
     * @return QuoteModel
     */
    protected function getQuote ()
    {
        if (!isset($this->_quote))
        {
            $this->_quote = QuoteMapper::getInstance()->find($this->_quoteId);
        }

        return $this->_quote;
    }

    /**
     * Syncs a quote device with it's master device id record.  This will sync either with a master
     * device id, or a quote device.  Will return false if it cannot resolve the device based on
     * passed parameters
     *
     * @param int|QuoteDeviceModel $object
     *       A master device id, or a quote device
     *
     * @return bool|QuoteDeviceModel
     */
    public function syncDevice ($object)
    {
        if ($object instanceof QuoteDeviceModel)
        {
            // If a quote device is passed we want to preserve and id that isn't going to be overwritten
            $quoteDevice = $object;
            $device      = $object->getDevice();
        }
        else
        {
            // If an id is passed then we want a new quote device
            $quoteDevice = new QuoteDeviceModel();

            $device = DeviceMapper::getInstance()->find(array($object, $this->getQuote()->getClient()->dealerId));
        }

        if (!$device instanceof DeviceModel)
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
     * @param QuoteDeviceModel  $quoteDevice
     * @param MasterDeviceModel $masterDevice
     *
     * @return QuoteDeviceModel
     */
    protected function syncCostPerPageForDevice (QuoteDeviceModel $quoteDevice, MasterDeviceModel $masterDevice)
    {
        $oemCostPerPageSetting                         = new CostPerPageSettingModel();
        $oemCostPerPageSetting->adminCostPerPage       = 0;
        $oemCostPerPageSetting->laborCostPerPage       = 0;
        $oemCostPerPageSetting->partsCostPerPage       = 0;
        $oemCostPerPageSetting->pageCoverageMonochrome = ($this->getQuote()->pageCoverageMonochrome) ? $this->getQuote()->pageCoverageMonochrome : self::DEFAULT_PAGE_COVERAGE_MONOCHROME;
        $oemCostPerPageSetting->pageCoverageColor      = ($this->getQuote()->pageCoverageColor) ? $this->getQuote()->pageCoverageColor : self::DEFAULT_PAGE_COVERAGE_COLOR;
        $oemCostPerPageSetting->monochromeTonerRankSet = $this->getQuote()->getDealerMonochromeRankSet();
        $oemCostPerPageSetting->colorTonerRankSet      = $this->getQuote()->getDealerColorRankSet();

        // Calculate the cost per page
        $costPerPage = $masterDevice->calculateCostPerPage($oemCostPerPageSetting)->getCostOfInkAndTonerPerPage();

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
     * @param $quoteDevice QuoteDeviceModel
     *                     The quote device to sync
     * @param $syncOptions boolean
     *                     If set to true, it will sync the quote options associated with the quote device
     *
     * @return boolean Returns true if the sync was successful. If it was false, chances are it is because there is no
     *         link between the quote device and a device in the system.
     */
    public function performSyncOnQuoteDevice (QuoteDeviceModel $quoteDevice, $syncOptions = true)
    {
        // Sync the device and save
        $quoteDevice = $this->syncDevice($quoteDevice);

        if (!$quoteDevice instanceof QuoteDeviceModel)
        {
            return false;
        }

        // Sync our options
        if ($syncOptions)
        {
            /* @var $quoteDeviceOption QuoteDeviceOptionModel */
            foreach ($quoteDevice->getQuoteDeviceOptions() as $quoteDeviceOption)
            {
                // Only sync options that still have a link back to the master
                $deviceOption = $quoteDeviceOption->getDeviceOption();
                if ($deviceOption)
                {
                    $quoteDeviceOption = $this->syncOption($quoteDeviceOption, $deviceOption);
                    QuoteDeviceOptionMapper::getInstance()->save($quoteDeviceOption);
                }
            }
        }

        $quoteDevice->packageCost = $quoteDevice->calculatePackageCost();

        QuoteDeviceMapper::getInstance()->save($quoteDevice);

        return true;
    }

    /**
     * Syncs a quote device option with an option
     *
     * @param QuoteDeviceOptionModel $quoteDeviceOption The quote device option that will be updated
     * @param DeviceOptionModel      $deviceOption      The option to update the quote device option with
     *
     * @return QuoteDeviceOptionModel The updated quote device option
     */
    public function syncOption (QuoteDeviceOptionModel $quoteDeviceOption, DeviceOptionModel $deviceOption)
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