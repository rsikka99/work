<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Services\ManageMasterDevicesService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerMasterDeviceAttributeMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\JitCompatibleMasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerConfigMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use My_Model_Abstract;
use Zend_Auth;

/**
 * Class MasterDeviceModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class MasterDeviceModel extends My_Model_Abstract
{
    /*
     * The different device types
     */
    const DEVICE_TYPE_MONO      = 0;
    const DEVICE_TYPE_MONO_MFP  = 1;
    const DEVICE_TYPE_COLOR     = 2;
    const DEVICE_TYPE_COLOR_MFP = 3;

    const LIFE_PAGE_COUNT_MONTHS = 36;

    static $TonerConfigNames = array(
        self::DEVICE_TYPE_MONO      => "Monochrome",
        self::DEVICE_TYPE_MONO_MFP  => "Monochrome MFP",
        self::DEVICE_TYPE_COLOR     => "Color",
        self::DEVICE_TYPE_COLOR_MFP => "Color MFP"
    );


    static $ReportLaborCostPerPage = 0;
    static $ReportPartsCostPerPage = 0;


    /*
     * Database fields
     */

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $manufacturerId;

    /**
     * @var string
     */
    public $modelName;

    /**
     * @var int
     */
    public $tonerConfigId;

    /**
     * @var bool
     */
    public $isCopier;

    /**
     * @var bool
     */
    public $isFax;

    /**
     * @var bool
     */
    public $isDuplex;

    /**
     * @var int
     */
    public $isSystemDevice;

    /**
     * @var bool
     */
    public $isA3;

    /**
     * @var int
     */
    public $maximumRecommendedMonthlyPageVolume;

    /**
     * @var bool
     */
    public $isReplacementDevice;

    /**
     * @var int
     */
    public $wattsPowerNormal;

    /**
     * @var int
     */
    public $wattsPowerIdle;

    /**
     * @var string
     */
    public $launchDate;

    /**
     * @var string
     */
    public $dateCreated;

    /**
     * @var int
     */
    public $ppmBlack;

    /**
     * @var int
     */
    public $ppmColor;

    /**
     * @var bool
     */
    public $isLeased;

    /**
     * @var int
     */
    public $leasedTonerYield;

    /**
     * @var bool
     */
    public $isCapableOfReportingTonerLevels;

    /**
     * @var int
     */
    public $calculatedLaborCostPerPage;

    /**
     * @var int
     */
    public $isUsingDealerLaborCostPerPage;

    /**
     * @var int
     */
    public $isUsingReportLaborCostPerPage;

    /**
     * @var int
     */
    public $calculatedPartsCostPerPage;

    /**
     * @var int
     */
    public $isUsingDealerPartsCostPerPage;

    /**
     * @var int
     */
    public $isUsingReportPartsCostPerPage;

    /*
     * Related Objects
     */
    protected $_toners;
    protected $_manufacturer;
    protected $_tonerConfig;

    /*
     * calculated values and other things
     */
    public    $adminCostPerPage;
    protected $_cachedDeviceCostPerPage;
    protected $_cachedCheapestTonerVendorSet;
    protected $_usingIncompleteBlackTonerData;
    protected $_usingIncompleteColorTonerData;
    protected $_maximumMonthlyPageVolume;
    protected $_hasValidMonoGrossMarginToners;
    protected $_hasValidColorGrossMarginToners;
    protected $_tonersForAssessment;
    protected $_tonersForHealthcheck;
    protected $_tonersForGrossMargin;
    protected $_requiredTonerColors;
    protected $_age;

    protected $_dealerAttributes;

    /**
     * Whether or not this device has a valid set of oem toners
     *
     * @var bool
     */
    protected $_hasValidToners;

    /**
     * @return DealerMasterDeviceAttributeModel
     */
    public function getDealerAttributes ()
    {
        if (!isset($this->_dealerAttributes))
        {

            $where                   = DealerMasterDeviceAttributeMapper::getInstance()->getWhereId(array($this->id, Zend_Auth::getInstance()->getIdentity()->dealerId));
            $this->_dealerAttributes = DealerMasterDeviceAttributeMapper::getInstance()->fetch($where);
        }

        return $this->_dealerAttributes;
    }

    /**
     * The maximum monthly page volume is calculated using the smallest toner yield
     * given the current pricing configuration
     * SPECIAL: Leased devices have a yield set, so we use that
     *
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return int
     */
    public function getMaximumMonthlyPageVolume ($costPerPageSetting)
    {
        if (!isset($this->_maximumMonthlyPageVolume))
        {
            $this->_maximumMonthlyPageVolume = array();
        }

        $cacheKey = $costPerPageSetting->createCacheKey();

        if (!array_key_exists($cacheKey, $this->_maximumMonthlyPageVolume))
        {
            $smallestYield = null;
            if ($this->isLeased)
            {
                $smallestYield = $this->leasedTonerYield;
            }
            else
            {
                $toners = $this->getCheapestTonerSetByVendor($costPerPageSetting);
                foreach ($toners as $toner)
                {
                    if ($toner instanceof TonerModel)
                    {
                        if ($toner->yield < $smallestYield || is_null($smallestYield))
                        {
                            $smallestYield = $toner->yield;
                        }
                    }
                }
            }
            $this->_maximumMonthlyPageVolume[$cacheKey] = $smallestYield;
        }

        return $this->_maximumMonthlyPageVolume[$cacheKey];
    }

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->id) && !is_null($params->id))
        {
            $this->id = $params->id;
        }

        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }

        if (isset($params->manufacturerId) && !is_null($params->manufacturerId))
        {
            $this->manufacturerId = $params->manufacturerId;
        }

        if (isset($params->modelName) && !is_null($params->modelName))
        {
            $this->modelName = $params->modelName;
        }

        if (isset($params->tonerConfigId) && !is_null($params->tonerConfigId))
        {
            $this->tonerConfigId = $params->tonerConfigId;
        }

        if (isset($params->isCopier) && !is_null($params->isCopier))
        {
            $this->isCopier = $params->isCopier;
        }

        if (isset($params->isFax) && !is_null($params->isFax))
        {
            $this->isFax = $params->isFax;
        }

        if (isset($params->isDuplex) && !is_null($params->isDuplex))
        {
            $this->isDuplex = $params->isDuplex;
        }

        if (isset($params->isSystemDevice) && !is_null($params->isSystemDevice))
        {
            $this->isSystemDevice = $params->isSystemDevice;
        }

        if (isset($params->isA3) && !is_null($params->isA3))
        {
            $this->isA3 = $params->isA3;
        }

        if (isset($params->maximumRecommendedMonthlyPageVolume) && !is_null($params->maximumRecommendedMonthlyPageVolume))
        {
            $this->maximumRecommendedMonthlyPageVolume = $params->maximumRecommendedMonthlyPageVolume;
        }

        if (isset($params->isReplacementDevice) && !is_null($params->isReplacementDevice))
        {
            $this->isReplacementDevice = $params->isReplacementDevice;
        }

        if (isset($params->wattsPowerNormal) && !is_null($params->wattsPowerNormal))
        {
            $this->wattsPowerNormal = $params->wattsPowerNormal;
        }

        if (isset($params->wattsPowerIdle) && !is_null($params->wattsPowerIdle))
        {
            $this->wattsPowerIdle = $params->wattsPowerIdle;
        }

        if (isset($params->launchDate) && !is_null($params->launchDate))
        {
            $this->launchDate = $params->launchDate;
        }

        if (isset($params->dateCreated) && !is_null($params->dateCreated))
        {
            $this->dateCreated = $params->dateCreated;
        }

        if (isset($params->ppmBlack) && !is_null($params->ppmBlack))
        {
            $this->ppmBlack = $params->ppmBlack;
        }

        if (isset($params->ppmColor) && !is_null($params->ppmColor))
        {
            $this->ppmColor = $params->ppmColor;
        }

        if (isset($params->isLeased) && !is_null($params->isLeased))
        {
            $this->isLeased = $params->isLeased;
        }

        if (isset($params->leasedTonerYield) && !is_null($params->leasedTonerYield))
        {
            $this->leasedTonerYield = $params->leasedTonerYield;
        }
        if (isset($params->isCapableOfReportingTonerLevels) && !is_null($params->isCapableOfReportingTonerLevels))
        {
            $this->isCapableOfReportingTonerLevels = $params->isCapableOfReportingTonerLevels;
        }

        if (isset($params->calculatedLaborCostPerPage) && !is_null($params->calculatedLaborCostPerPage))
        {
            $this->calculatedLaborCostPerPage = $params->calculatedLaborCostPerPage;
        }

        if (isset($params->isUsingDealerLaborCostPerPage) && !is_null($params->isUsingDealerLaborCostPerPage))
        {
            $this->isUsingDealerLaborCostPerPage = $params->isUsingDealerLaborCostPerPage;
        }

        if (isset($params->isUsingReportLaborCostPerPage) && !is_null($params->isUsingReportLaborCostPerPage))
        {
            $this->isUsingReportLaborCostPerPage = $params->isUsingReportLaborCostPerPage;
        }

        if (isset($params->calculatedPartsCostPerPage) && !is_null($params->calculatedPartsCostPerPage))
        {
            $this->calculatedPartsCostPerPage = $params->calculatedPartsCostPerPage;
        }

        if (isset($params->isUsingDealerPartsCostPerPage) && !is_null($params->isUsingDealerPartsCostPerPage))
        {
            $this->isUsingDealerPartsCostPerPage = $params->isUsingDealerPartsCostPerPage;
        }

        if (isset($params->isUsingReportPartsCostPerPage) && !is_null($params->isUsingReportPartsCostPerPage))
        {
            $this->isUsingReportPartsCostPerPage = $params->isUsingReportPartsCostPerPage;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                                  => $this->id,
            "userId"                              => $this->userId,
            "manufacturerId"                      => $this->manufacturerId,
            "modelName"                           => $this->modelName,
            "tonerConfigId"                       => $this->tonerConfigId,
            "isCopier"                            => $this->isCopier,
            "isFax"                               => $this->isFax,
            "isDuplex"                            => $this->isDuplex,
            "isSystemDevice"                      => $this->isSystemDevice,
            "isA3"                                => $this->isA3,
            "maximumRecommendedMonthlyPageVolume" => $this->maximumRecommendedMonthlyPageVolume,
            "isReplacementDevice"                 => $this->isReplacementDevice,
            "wattsPowerNormal"                    => $this->wattsPowerNormal,
            "wattsPowerIdle"                      => $this->wattsPowerIdle,
            "launchDate"                          => $this->launchDate,
            "dateCreated"                         => $this->dateCreated,
            "ppmBlack"                            => $this->ppmBlack,
            "ppmColor"                            => $this->ppmColor,
            "isLeased"                            => $this->isLeased,
            "leasedTonerYield"                    => $this->leasedTonerYield,
            "isCapableOfReportingTonerLevels"     => $this->isCapableOfReportingTonerLevels,
        );
    }

    /**
     * @return ManufacturerModel
     */
    public function getManufacturer ()
    {
        if (!isset($this->_manufacturer))
        {
            $manufacturerMapper  = ManufacturerMapper::getInstance();
            $this->_manufacturer = $manufacturerMapper->find($this->manufacturerId);
        }

        return $this->_manufacturer;
    }

    /**
     * @param ManufacturerModel $Manufacturer
     *
     * @return MasterDeviceModel
     */
    public function setManufacturer ($Manufacturer)
    {
        $this->_manufacturer = $Manufacturer;

        return $this;
    }

    /**
     * Gets the toner config object for this device
     *
     * @return TonerConfigModel
     */
    public function getTonerConfig ()
    {
        if (!isset($this->_tonerConfig))
        {
            $tonerConfigMapper  = TonerConfigMapper::getInstance();
            $this->_tonerConfig = $tonerConfigMapper->find($this->tonerConfigId);
        }

        return $this->_tonerConfig;
    }

    /**
     * Sets the toner config object for this device
     *
     * @param TonerConfigModel $TonerConfig
     *
     * @return \Proposalgen_Model_MasterDevice
     */
    public function setTonerConfig ($TonerConfig)
    {
        $this->_tonerConfig = $TonerConfig;

        return $this;
    }

    /**
     * @param int $dealerId
     * @param int $clientId
     *
     * @return TonerModel[]
     */
    public function getToners ($dealerId, $clientId = null)
    {
        if (!isset($this->_toners))
        {
            // Get the toners for the device
            $this->_toners = TonerMapper::getInstance()->getReportToners($this->id, $dealerId, $clientId);
        }

        return $this->_toners;
    }

    /**
     * @param TonerModel [][][] $Toners
     *
     * @return MasterDeviceModel
     */
    public function setToners ($Toners)
    {
        $this->_toners = $Toners;

        return $this;
    }

    /**
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return bool
     */
    public function getHasValidMonoGrossMarginToners ($costPerPageSetting)
    {
        if (!isset($this->_hasValidMonoGrossMarginToners))
        {
            $usesAllValidToners      = true;
            $toners                  = $this->getCheapestTonerSetByVendor($costPerPageSetting);
            $oemRank                 = new TonerVendorRankingModel();
            $oemRank->manufacturerId = $this->manufacturerId;

            $preferredMonochromeVendors   = $costPerPageSetting->monochromeTonerRankSet->getRankings();
            $preferredMonochromeVendors[] = $oemRank;
            foreach ($toners as $toner)
            {
                switch ($toner->tonerColorId)
                {
                    case TonerColorModel::BLACK:
                        if ($toner->manufacturerId != $preferredMonochromeVendors[0]->manufacturerId)
                        {
                            $usesAllValidToners = false;
                        }
                        break;
                }
            }


            $this->_hasValidMonoGrossMarginToners = $usesAllValidToners;
        }

        return $this->_hasValidMonoGrossMarginToners;
    }

    /**
     * @param bool $HasValidMonoGrossMarginToners
     *
     * @return MasterDeviceModel
     */
    public function setHasValidMonoGrossMarginToners ($HasValidMonoGrossMarginToners)
    {
        $this->_hasValidMonoGrossMarginToners = $HasValidMonoGrossMarginToners;

        return $this;
    }

    /**
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return bool
     */
    public function getHasValidColorGrossMarginToners ($costPerPageSetting)
    {
        if (!isset($this->_hasValidColorGrossMarginToners))
        {
            $usesAllValidToners           = true;
            $toners                       = $this->getCheapestTonerSetByVendor($costPerPageSetting);
            $oemRank                      = new TonerVendorRankingModel();
            $oemRank->manufacturerId      = $this->manufacturerId;
            $preferredMonochromeVendors   = $costPerPageSetting->colorTonerRankSet->getRankings();
            $preferredMonochromeVendors[] = $oemRank;
            $preferredColorVendors        = $costPerPageSetting->colorTonerRankSet->getRankings();
            $preferredColorVendors[]      = $oemRank;
            foreach ($toners as $toner)
            {
                switch ($toner->tonerColorId)
                {
                    case TonerColorModel::CYAN:
                    case TonerColorModel::MAGENTA:
                    case TonerColorModel::YELLOW:
                    case TonerColorModel::THREE_COLOR:
                        if ($toner->manufacturerId != $preferredColorVendors[0]->manufacturerId)
                        {
                            $usesAllValidToners = false;
                        }
                        break;
                }
            }

            $this->_hasValidColorGrossMarginToners = $usesAllValidToners;
        }

        return $this->_hasValidColorGrossMarginToners;
    }

    /**
     * @param bool $HasValidColorGrossMarginToners
     *
     * @return MasterDeviceModel
     */
    public function setHasValidColorGrossMarginToners ($HasValidColorGrossMarginToners)
    {
        $this->_hasValidColorGrossMarginToners = $HasValidColorGrossMarginToners;

        return $this;
    }

    /**
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return TonerModel[]
     */
    public function getTonersForAssessment ($costPerPageSetting)
    {
        if (!isset($this->_tonersForAssessment))
        {
            $toners                     = $this->getCheapestTonerSetByVendor($costPerPageSetting);
            $this->_tonersForAssessment = $toners;
        }

        return $this->_tonersForAssessment;
    }

    /**
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return TonerModel[]
     */
    public function getTonersForGrossMargin ($costPerPageSetting)
    {
        if (!isset($this->_tonersForGrossMargin))
        {
            $toners                      = $this->getCheapestTonerSetByVendor($costPerPageSetting);
            $this->_tonersForGrossMargin = $toners;
        }

        return $this->_tonersForGrossMargin;
    }

    /**
     * @param TonerModel[] $TonersForAssessment
     *
     * @return MasterDeviceModel
     */
    public function setTonersForAssessment ($TonersForAssessment)
    {
        $this->_tonersForAssessment = $TonersForAssessment;

        return $this;
    }

    /**
     * @param TonerModel[] $TonersForGrossMargin
     *
     * @return MasterDeviceModel
     */
    public function setTonersForGrossMargin ($TonersForGrossMargin)
    {
        $this->_tonersForGrossMargin = $TonersForGrossMargin;

        return $this;
    }

    /**
     * @return array
     */
    public function getRequiredTonerColors ()
    {
        if (!isset($this->_requiredTonerColors))
        {
            $this->_requiredTonerColors = TonerConfigModel::getRequiredTonersForTonerConfig($this->tonerConfigId);
        }

        return $this->_requiredTonerColors;
    }

    /**
     * @param $RequiredTonerColors
     *
     * @return MasterDeviceModel
     */
    public function setRequiredTonerColors ($RequiredTonerColors)
    {
        $this->_requiredTonerColors = $RequiredTonerColors;

        return $this;
    }

    /**
     * Gets the manufacturer display name + printer model
     *
     * @return string
     */
    public function getFullDeviceName ()
    {
        return "{$this->getManufacturer()->displayname} {$this->modelName}";
    }

    /**
     * Returns whether or not a device is color.
     *
     * @return boolean
     */
    public function isColor ()
    {
        return ((int)$this->tonerConfigId !== TonerConfigModel::BLACK_ONLY);
    }

    /**
     * Calculates the cost per page for a master device.
     *
     * @param CostPerPageSettingModel $costPerPageSetting The settings to use when calculating cost per page
     * @param bool                    $isManaged
     *
     * @return DeviceCostPerPageModel
     */
    public function calculateCostPerPage (CostPerPageSettingModel $costPerPageSetting, $isManaged = false)
    {
        /**
         * Caching Array
         */
        if (!isset($this->_cachedDeviceCostPerPage))
        {
            $this->_cachedDeviceCostPerPage = array();
        }

        $cacheKey = $costPerPageSetting->createCacheKey();

        if (!array_key_exists($cacheKey, $this->_cachedDeviceCostPerPage))
        {
            $deviceCostPerPage            = new DeviceCostPerPageModel($this->getCheapestTonerSetByVendor($costPerPageSetting), $costPerPageSetting, $this->calculatedLaborCostPerPage, $this->calculatedPartsCostPerPage);
            $deviceCostPerPage->isManaged = $isManaged;

            $this->_cachedDeviceCostPerPage [$cacheKey] = $deviceCostPerPage;
        }

        return $this->_cachedDeviceCostPerPage [$cacheKey];
    }


    /**
     * Gets a list of toners for the toner vendor id passed
     *
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return TonerModel[]
     */
    public function getCheapestTonerSetByVendor ($costPerPageSetting)
    {
        if (!isset($this->_cachedCheapestTonerVendorSet))
        {
            $this->_cachedCheapestTonerVendorSet = array();
        }

        $cacheKey = $costPerPageSetting->createCacheKey();

        if (!array_key_exists($cacheKey, $this->_cachedCheapestTonerVendorSet))
        {
            $monochromeManufacturerPreference               = implode(',', $costPerPageSetting->monochromeTonerRankSet->getRanksAsArray());
            $colorManufacturerPreference                    = implode(',', $costPerPageSetting->colorTonerRankSet->getRanksAsArray());
            $this->_cachedCheapestTonerVendorSet[$cacheKey] = TonerMapper::getInstance()->getCheapestTonersForDevice($this->id, $costPerPageSetting->dealerId, $monochromeManufacturerPreference, $colorManufacturerPreference, $costPerPageSetting->clientId);
        }

        return $this->_cachedCheapestTonerVendorSet[$cacheKey];
    }

    /**
     * Gets a list of toners for the toner vendor id passed
     *
     * @param $vendorId
     *
     * @return TonerModel[]
     */
    public function getCheapestTonerSetByVendorId ($vendorId)
    {
        $monochromeManufacturerPreference = $vendorId;
        $colorManufacturerPreference      = $vendorId;

        return TonerMapper::getInstance()->getCheapestTonersForDevice($this->id, Zend_Auth::getInstance()->getIdentity()->dealerId, $monochromeManufacturerPreference, $colorManufacturerPreference);
    }

    /**
     * Gets the device type
     *
     * @return number @see Application_Model_MasterDevice for constants
     */
    public function getDeviceType ()
    {
        if (!isset($this->_deviceType))
        {
            if ((int)$this->tonerConfigId === TonerConfigModel::BLACK_ONLY)
            {
                if ($this->isMfp())
                {
                    $this->_deviceType = self::DEVICE_TYPE_MONO_MFP;
                }
                else
                {

                    $this->_deviceType = self::DEVICE_TYPE_MONO;
                }
            }
            else
            {
                if ($this->isMfp())
                {
                    $this->_deviceType = self::DEVICE_TYPE_COLOR_MFP;
                }
                else
                {
                    $this->_deviceType = self::DEVICE_TYPE_COLOR;
                }
            }
        }

        return $this->_deviceType;
    }

    /**
     * The age is the difference between the launch date and today in years.
     * The only special thing here is that the launch date is changed -1 year if
     * it
     * is at least 1 year old. This is to compensate for the fact that most
     * printers
     * aren't deployed in a fleet as soon as they were launched by their
     * manufacturers
     *
     * @return int Calculated device age in years
     */
    public function getAge ()
    {
        if (!isset($this->_age))
        {
            // Get the time difference in seconds
            $launchDate          = time() - strtotime($this->launchDate);
            $correctedLaunchDate = ($launchDate > 31556926) ? ($launchDate - 31556926) : $launchDate;
            $this->_age          = floor($correctedLaunchDate / 31556926);
            if ($this->_age == 0)
            {
                $this->_age = 1;
            }
        }

        return $this->_age;
    }

    /**
     * Returns whether this device is a jitCompatibleMasterDevice for a dealer
     *
     * @param $dealerId
     *
     * @return bool
     */
    public function isJitCompatible ($dealerId)
    {
        return JitCompatibleMasterDeviceMapper::getInstance()->find(array($this->id, $dealerId)) instanceof JitCompatibleMasterDeviceModel;
    }

    /**
     * Checks to see if the master device is a MFP device
     *
     * @return bool
     */
    public function isMfp ()
    {
        return ($this->isCopier);
    }

    /**
     * Calculates the max estimated life count (Defaults to using OEM)
     *
     * @internal param CostPerPageSettingModel $costPerPageSetting Set this if you want to use a different set of toners
     *
     * @return int
     */
    public function calculateEstimatedMaxLifeCount ()
    {
        return $this->maximumRecommendedMonthlyPageVolume * self::LIFE_PAGE_COUNT_MONTHS;
    }

    /**
     * Recalculate the max page volume for the device
     */
    public function recalculateMaximumRecommendedMonthlyPageVolume ()
    {
        $toners = TonerMapper::getInstance()->fetchTonersAssignedToDevice($this->id);

        $highestOEMYield = 0;
        foreach ($toners as $toner)
        {
            if ($toner->manufacturerId == $this->manufacturerId)
            {
                if ($toner->yield > $highestOEMYield)
                {
                    $highestOEMYield = $toner->yield;
                }
            }
        }

        $this->maximumRecommendedMonthlyPageVolume = $highestOEMYield;
    }

    /**
     * Checks to see if a device has valid toners
     *
     * @param int $dealerId
     * @param int $clientId
     *
     * @return int
     */
    public function hasValidToners ($dealerId, $clientId = null)
    {
        if (!isset($this->_hasValidToners))
        {
            $this->_hasValidToners = array();
        }

        $cacheKey = "{$dealerId}_{$clientId}";

        if (!isset($this->_hasValidToners[$cacheKey]))
        {
            $masterDeviceService = new ManageMasterDevicesService($this->id, $dealerId);
            $tonerList           = array();

            foreach ($this->getToners($dealerId, $clientId) as $manufacturerIdList)
            {
                foreach ($manufacturerIdList as $tonerColorIdList)
                {
                    foreach ($tonerColorIdList as $toner)
                    {
                        $tonerList[] = $toner->id;
                    }
                }
            }

            $tonerList       = implode(',', $tonerList);
            $tonerValidation = $masterDeviceService->validateToners($tonerList, $this->tonerConfigId, $this->manufacturerId);

            $this->_hasValidToners[$cacheKey] = ($tonerValidation === true);
        }

        return $this->_hasValidToners[$cacheKey];
    }
}