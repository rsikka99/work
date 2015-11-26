<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use DateInterval;
use Exception;
use InvalidArgumentException;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceInstanceDeviceSwapReasonMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapReasonMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\HardwareOptimizationDeviceInstanceMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapReasonCategoryModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapReasonModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationDeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMeterMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceReplacementMasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsUploadRowMapper;
use My_Model_Abstract;
use Zend_Auth;

/**
 * Class DeviceInstanceModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class DeviceInstanceModel extends My_Model_Abstract
{
    /**
     * Constants for replacement actions for the solution
     */
    const ACTION_KEEP    = 'Keep';
    const ACTION_REPLACE = 'Flagged';
    const ACTION_RETIRE  = 'Retire';

    /**
     * These constants help determine whether or not to retire a device
     */
    const RETIREMENT_AGE             = 12;
    const RETIREMENT_MAX_PAGE_COUNT  = 200;
    const REPLACEMENT_AGE            = 12;
    const REPLACEMENT_MIN_PAGE_COUNT = 200;


    /**
     * An array used to determine how many hours a device is running based on its average volume per day
     *
     * @var array
     */
    static $RUNNING_HOUR_ARRAY = [
        500 => 8,
        100 => 4,
        0   => 2,
    ];

    /**
     * The cost of electricity
     *
     * @var float
     */
    static $KWH_Cost = 0;

    /**
     * The IT cost per page
     *
     * @var float
     */
    static $ITCostPerPage = 0;

    /*
     * ********************************************************************************
     * Database Fields
     * ********************************************************************************
     */

    /**
     * @var int
     */
    public $id;

    /** @var  int */
    public $rmsDeviceInstanceId;

    /**
     * @var int
     */
    public $rmsUploadId;

    /**
     * @var int
     */
    public $rmsUploadRowId;

    /**
     * @var int
     */
    public $ipAddress;

    /**
     * @var int
     */
    public $isExcluded;

    /**
     * @var int
     */
    public $mpsDiscoveryDate;

    /**
     * @var int
     */
    public $isCapableOfReportingTonerLevels;

    /**
     * @var int
     */
    public $serialNumber;

    /**
     * @var int
     */
    public $useUserData;

    /**
     * @var float
     */
    public $pageCoverageMonochrome;

    /**
     * @var float
     */
    public $pageCoverageColor;

    /**
     * @var float
     */
    public $pageCoverageCyan;
    /**
     * @var float
     */
    public $pageCoverageMagenta;
    /**
     * @var float
     */
    public $pageCoverageYellow;

    /**
     * @var string|int
     */
    public $assetId;

    /**
     * @var bool
     */
    public $isManaged;

    /**
     * @var bool
     */
    public $isLeased;

    /**
     * @var int
     */
    public $deviceSwapReasonId;

    /**
     * @var bool
     */
    public $compatibleWithJitProgram;

    /**
     * @var string
     */
    public $rawDeviceName;

    /**
     * @var string
     */
    public $location;

    /*
     * ********************************************************************************
     * Related Objects
     * ********************************************************************************
     */
    /**
     * Our Meter
     *
     * @var DeviceInstanceMeterModel
     */
    protected $_meter;

    /**
     * Used in determining actions for replacement devices.
     *
     * @var String
     */
    protected $_deviceAction;

    /**
     * @var MasterDeviceModel
     */
    protected $_masterDevice;

    /**
     * @var RmsUploadRowModel
     */
    protected $_rmsUploadRow;

    /**
     * @var DeviceInstanceMasterDeviceModel
     */
    protected $_deviceInstanceMasterDevice;

    /**
     * @var MasterDeviceModel
     */
    protected $_replacementMasterDevice;

    /**
     * @var HardwareOptimizationDeviceInstanceModel
     */
    protected $_hardwareOptimizationDeviceInstances;

    /*
     * ********************************************************************************
     * Calculated fields
     * ********************************************************************************
     */

    /**
     * @var float
     */
    protected $_age;

    /**
     * @var DateInterval
     */
    protected $_mpsMonitorInterval;

    /**
     * @var float
     */
    protected $_averageDailyPowerConsumption;

    /**
     * @var float
     */
    protected $_averageMonthlyPowerConsumption;

    /**
     * @var int
     */
    protected $lifePageCount;

    /**
     * @var int
     */
    protected $_lifeBlackAndWhitePageCount;

    /**
     * @var int
     */
    protected $_lifeColorPageCount;

    /**
     * @var float
     */
    protected $_costOfInkAndToner;

    /**
     * @var float
     */
    protected $_costOfBlackAndWhiteInkAndToner;

    /**
     * @var float
     */
    protected $_costOfColorInkAndToner;

    /**
     * @var float
     */
    protected $_usage;

    /**
     * @var float
     */
    protected $_lifeUsage;

    /**
     * @var string
     */
    protected $_deviceName;

    /**
     * @var float
     */
    protected $_monthlyRate;

    /**
     * @var float
     */
    protected $_averageMonthlyPowerCost;

    /**
     * @var float
     */
    protected $_averageDailyPowerCost;

    /**
     * @var float
     */
    protected $_combinedMonthlyPageCount;

    /**
     * @var float
     */
    protected $_blackMonthlyPageCount;

    /**
     * @var float
     */
    protected $_colorMonthlyPageCount;

    /*
     * ********************************************************************************
     * Non calculated fields
     * ********************************************************************************
     */

    /**
     * @var float
     */
    protected $_ageRank;

    /**
     * @var float
     */
    protected $_lifeUsageRank;

    /**
     * @var float
     */
    protected $_riskRank;

    /**
     * @var RmsUploadRowModel
     */
    protected $_uploadDataCollectorRow;

    /**
     * @var CostPerPageModel
     */
    protected $_cachedDeviceCostPerPage;

    /**
     * @var CostPerPageModel
     */
    protected $_cachedMonthlyBlackAndWhiteCost;

    /**
     * @var PageCountsModel
     */
    protected $_cachedPageCounts;

    /**
     * @var string
     */
    public $_exclusionReason;

    /**
     * The reason why we are replacing a device.
     *
     * @var DeviceSwapReasonModel
     */
    public $_reason;

    /**
     * The reason why we are replacing a device based on a customer report.
     *
     * @var string
     */
    public $_customerReason;

    /**
     * @var bool
     */
    public $isUnknown = false;

    /**
     * @var TonerModel[]
     */
    static $uniqueTonerArray = [];

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

        if (isset($params->rmsDeviceInstanceId) && !is_null($params->rmsDeviceInstanceId))
        {
            $this->rmsDeviceInstanceId = $params->rmsDeviceInstanceId;
        }

        if (isset($params->rmsUploadId) && !is_null($params->rmsUploadId))
        {
            $this->rmsUploadId = $params->rmsUploadId;
        }

        if (isset($params->rmsUploadRowId) && !is_null($params->rmsUploadRowId))
        {
            $this->rmsUploadRowId = $params->rmsUploadRowId;
        }

        if (isset($params->ipAddress) && !is_null($params->ipAddress))
        {
            $this->ipAddress = $params->ipAddress;
        }

        if (isset($params->isExcluded) && !is_null($params->isExcluded))
        {
            $this->isExcluded = $params->isExcluded;
        }

        if (isset($params->isLeased) && !is_null($params->isLeased))
        {
            $this->isLeased = $params->isLeased;
        }

        if (isset($params->mpsDiscoveryDate) && !is_null($params->mpsDiscoveryDate))
        {
            $this->mpsDiscoveryDate = $params->mpsDiscoveryDate;
        }

        if (isset($params->reportsTonerLevels) && !is_null($params->reportsTonerLevels))
        {
            $this->isCapableOfReportingTonerLevels = $params->reportsTonerLevels;
        }

        if (isset($params->serialNumber) && !is_null($params->serialNumber))
        {
            $this->serialNumber = $params->serialNumber;
        }

        if (isset($params->useUserData) && !is_null($params->useUserData))
        {
            $this->useUserData = $params->useUserData;
        }

        if (isset($params->pageCoverageMonochrome) && !is_null($params->pageCoverageMonochrome))
        {
            $this->pageCoverageMonochrome = $params->pageCoverageMonochrome;
        }

        if (isset($params->pageCoverageColor) && !is_null($params->pageCoverageColor))
        {
            $this->pageCoverageColor = $params->pageCoverageColor;
        }

        if (isset($params->pageCoverageCyan) && !is_null($params->pageCoverageCyan))
        {
            $this->pageCoverageCyan = $params->pageCoverageCyan;
        }

        if (isset($params->pageCoverageMagenta) && !is_null($params->pageCoverageMagenta))
        {
            $this->pageCoverageMagenta = $params->pageCoverageMagenta;
        }

        if (isset($params->pageCoverageYellow) && !is_null($params->pageCoverageYellow))
        {
            $this->pageCoverageYellow = $params->pageCoverageYellow;
        }

        if (isset($params->isManaged) && !is_null($params->isManaged))
        {
            $this->isManaged = $params->isManaged;
        }

        if (isset($params->assetId) && !is_null($params->assetId))
        {
            $this->assetId = $params->assetId;
        }

        if (isset($params->deviceSwapReasonId) && !is_null($params->deviceSwapReasonId))
        {
            $this->deviceSwapReasonId = $params->deviceSwapReasonId;
        }

        if (isset($params->rawDeviceName) && !is_null($params->rawDeviceName))
        {
            $this->rawDeviceName = $params->rawDeviceName;
        }

        if (isset($params->compatibleWithJitProgram) && !is_null($params->compatibleWithJitProgram))
        {
            $this->compatibleWithJitProgram = $params->compatibleWithJitProgram;
        }

        if (isset($params->location) && !is_null($params->location))
        {
            $this->location = $params->location;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "id"                       => $this->id,
            "rmsDeviceInstanceId"      => $this->rmsDeviceInstanceId,
            "rmsUploadId"              => $this->rmsUploadId,
            "rmsUploadRowId"           => $this->rmsUploadRowId,
            "ipAddress"                => $this->ipAddress,
            "isExcluded"               => $this->isExcluded,
            "isLeased"                 => $this->isLeased,
            "mpsDiscoveryDate"         => $this->mpsDiscoveryDate,
            "reportsTonerLevels"       => $this->isCapableOfReportingTonerLevels,
            "serialNumber"             => $this->serialNumber,
            "useUserData"              => $this->useUserData,
            "pageCoverageMonochrome"   => $this->pageCoverageMonochrome,
            "pageCoverageColor"        => $this->pageCoverageColor,
            "pageCoverageCyan"         => $this->pageCoverageCyan,
            "pageCoverageMagenta"      => $this->pageCoverageMagenta,
            "pageCoverageYellow"       => $this->pageCoverageYellow,
            "isManaged"                => $this->isManaged,
            "assetId"                  => $this->assetId,
            "deviceSwapReasonId"       => $this->deviceSwapReasonId,
            "rawDeviceName"            => $this->rawDeviceName,
            "compatibleWithJitProgram" => $this->compatibleWithJitProgram,
            "location"                 => $this->location,
        ];
    }

    /**
     * Gets the average power consumption for the device
     *
     * @return int
     */
    public function getAverageMonthlyPowerConsumption ()
    {
        if (!isset($this->_averageMonthlyPowerConsumption))
        {
            $this->_averageMonthlyPowerConsumption = $this->getAverageDailyPowerConsumption() * 30;
        }

        return $this->_averageMonthlyPowerConsumption;
    }

    /**
     * The average daily power consumption for a device is calculated based on a
     * running hour basis
     * If a printer prints over x amount of pages per day, then it is likely to
     * be operating for y hours
     * You can check the running hour array to see the specific values
     *
     * @return float $AverageDailyPowerConsumption in kWh
     */
    public function getAverageDailyPowerConsumption ()
    {
        if (!isset($this->_averageDailyPowerConsumption))
        {
            $powerUsage   = 0;
            $runningHours = 0;

            foreach (self::$RUNNING_HOUR_ARRAY as $pages => $runningHours)
            {
                if ($this->getPageCounts()->getCombinedPageCount()->getDaily() >= $pages)
                {
                    break;
                }
            }

            $idleHours = 24 - $runningHours;
            $powerUsage += $idleHours * $this->getMasterDevice()->wattsPowerIdle;
            $powerUsage += $runningHours * $this->getMasterDevice()->wattsPowerNormal;
            $powerUsage                          = $powerUsage / 1000;
            $this->_averageDailyPowerConsumption = $powerUsage;
        }

        return $this->_averageDailyPowerConsumption;
    }

    /**
     * @return float
     */
    public function getAge ()
    {
        if (!isset($this->_age))
        {
            // Get the time difference in seconds
            $launchDate          = time() - strtotime($this->getMasterDevice()->launchDate);
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
     * @return DeviceInstanceMeterModel
     */
    public function getMeter ()
    {
        if (!isset($this->_meter))
        {
            $this->_meter = DeviceInstanceMeterMapper::getInstance()->fetchForDeviceInstance($this->id);
        }

        return $this->_meter;
    }

    /**
     * @param DeviceInstanceMeterModel $Meters
     *
     * @return DeviceInstanceModel
     */
    public function setMeter(DeviceInstanceMeterModel $meter)
    {
        $this->_meter = $meter;
        return $this;
    }


    /**
     * @return float
     */
    public function getAverageMonthlyPowerCost ()
    {
        if (!isset($this->_averageMonthlyPowerCost))
        {
            $this->_averageMonthlyPowerCost = $this->getAverageDailyPowerCost() * 30;
        }

        return $this->_averageMonthlyPowerCost;
    }

    /**
     * @return float
     */
    public function getAverageDailyPowerCost ()
    {
        if (!isset($this->_averageDailyPowerCost))
        {
            $this->_averageDailyPowerCost = $this->getAverageDailyPowerConsumption() * self::$KWH_Cost;
        }

        return $this->_averageDailyPowerCost;
    }

    /**
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return float
     */
    public function getCostOfInkAndToner ($costPerPageSetting)
    {
        if (!isset($this->_costOfInkAndToner))
        {
            $this->_costOfInkAndToner = $this->getCostOfBlackAndWhiteInkAndToner($costPerPageSetting) + $this->getCostOfColorInkAndToner($costPerPageSetting);
        }

        return $this->_costOfInkAndToner;
    }

    /**
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return float
     */
    public function getCostOfBlackAndWhiteInkAndToner ($costPerPageSetting)
    {
        if (!isset($this->_costOfBlackAndWhiteInkAndToner))
        {
            $this->_costOfBlackAndWhiteInkAndToner = $this->calculateCostPerPage($costPerPageSetting)->getCostOfInkAndTonerPerPage()->monochromeCostPerPage * $this->getPageCounts()->getBlackPageCount()->getMonthly();
        }

        return $this->_costOfBlackAndWhiteInkAndToner;
    }

    /**
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return float
     */
    public function getCostOfColorInkAndToner ($costPerPageSetting)
    {
        if (!isset($this->_costOfColorInkAndToner))
        {
            $this->_costOfColorInkAndToner = $this->calculateCostPerPage($costPerPageSetting)->getCostOfInkAndTonerPerPage()->colorCostPerPage * $this->getPageCounts()->getColorPageCount()->getMonthly();
        }

        return $this->_costOfColorInkAndToner;
    }

    /**
     * Gets the monochrome CPP with a margin
     *
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return float
     */
    public function getColorCostPerPageWithMargin ($costPerPageSetting)
    {
        return $this->calculateCostPerPage($costPerPageSetting)->getCostOfInkAndTonerPerPage()->colorCostPerPage;
    }

    /**
     * @return string
     */
    public function getDeviceName ()
    {
        if (!isset($this->_deviceName))
        {

            if ($this->getIsMappedToMasterDevice())
            {
                $this->_deviceName = $this->getMasterDevice()->getManufacturer()->fullname . " " . $this->getMasterDevice()->modelName;
            }
            else
            {
                $this->_deviceName = $this->getRmsUploadRow()->manufacturer . ' ' . $this->getRmsUploadRow()->modelName;
            }

        }

        return $this->_deviceName;
    }

    /**
     * @param string $DeviceName
     *
     * @return DeviceInstanceModel
     */
    public function setDeviceName ($DeviceName)
    {
        $this->_deviceName = $DeviceName;

        return $this;
    }

    /**
     * @param CostPerPageSettingModel $costPerPageSetting
     * @return float
     * @todo why is $costPerPageSetting not used here?
     */
    public function getUsage ($costPerPageSetting)
    {
        if (!isset($this->_usage))
        {
            // Calculate device usage by dividing it's current monthly volume by its maximum
            if ($this->getMasterDevice()->maximumRecommendedMonthlyPageVolume > 0)
            {
                $this->_usage = $this->getPageCounts()->getCombinedPageCount()->getMonthly() / $this->getMasterDevice()->maximumRecommendedMonthlyPageVolume;
            }
        }

        return $this->_usage;
    }

    /**
     * @internal param CostPerPageSettingModel $costPerPageSetting
     *
     * @return float
     */
    public function getLifeUsage ()
    {
        if (!isset($this->_lifeUsage))
        {
            $this->_lifeUsage = 0;

            // Calculate device life usage by dividing it's current life count
            // by it's estimated max life page count (maximum monthly page
            // volume * LIFE_PAGE_COUNT_MONTHS)
            $maximumLifeCount = $this->getMasterDevice()->calculateEstimatedMaxLifeCount();
            if ($maximumLifeCount > 0 && $this->getMeter()->endMeterLife > 0)
            {
                $this->_lifeUsage = $this->getMeter()->endMeterLife / $maximumLifeCount;
            }
        }

        return $this->_lifeUsage;
    }

    /**
     * @return float
     */
    public function getAgeRank ()
    {
        if (!isset($this->_ageRank))
        {

            $this->_ageRank = null;
        }

        return $this->_ageRank;
    }

    /**
     * @param float $AgeRank
     *
     * @return DeviceInstanceModel
     */
    public function setAgeRank ($AgeRank)
    {
        $this->_ageRank = $AgeRank;

        return $this;
    }

    /**
     * @return float
     */
    public function getLifeUsageRank ()
    {
        if (!isset($this->_lifeUsageRank))
        {
            $this->_lifeUsageRank = null;
        }

        return $this->_lifeUsageRank;
    }

    /**
     * @param float $LifeUsageRank
     *
     * @return DeviceInstanceModel
     */
    public function setLifeUsageRank ($LifeUsageRank)
    {
        $this->_lifeUsageRank = $LifeUsageRank;

        return $this;
    }

    /**
     * @return float
     */
    public function getRiskRank ()
    {
        if (!isset($this->_riskRank))
        {

            $this->_riskRank = null;
        }

        return $this->_riskRank;
    }

    /**
     * @param float $RiskRank
     *
     * @return DeviceInstanceModel
     */
    public function setRiskRank ($RiskRank)
    {
        $this->_riskRank = $RiskRank;

        return $this;
    }

    /**
     * @return float
     */
    public static function getKWH_Cost ()
    {
        if (!isset(DeviceInstanceModel::$KWH_Cost))
        {

            DeviceInstanceModel::$KWH_Cost = null;
        }

        return DeviceInstanceModel::$KWH_Cost;
    }

    /**
     * @return RmsUploadRowModel
     */
    public function getUploadDataCollectorRow ()
    {
        if (!isset($this->_uploadDataCollectorRow))
        {
            $this->_uploadDataCollectorRow = RmsUploadRowMapper::getInstance()->find($this->rmsUploadRowId);
        }

        return $this->_uploadDataCollectorRow;
    }

    /**
     * @param RmsUploadRowModel $UploadDataCollector
     *
     * @return DeviceInstanceModel
     */
    public function setUploadDataCollectorRow ($UploadDataCollector)
    {
        $this->_uploadDataCollectorRow = $UploadDataCollector;

        return $this;
    }

    /**
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return float
     */
    public function getMonthlyBlackAndWhiteCost ($costPerPageSetting)
    {
        // Make sure our array is initialized
        if (!isset($this->_cachedMonthlyBlackAndWhiteCost))
        {
            $this->_cachedMonthlyBlackAndWhiteCost = [];
        }
        $cacheKey = $costPerPageSetting->createCacheKey() . '_device_instance' . $this->id;
        if (!array_key_exists($cacheKey, $this->_cachedMonthlyBlackAndWhiteCost))
        {
            $this->_cachedMonthlyBlackAndWhiteCost [$cacheKey] = ($this->calculateCostPerPage($costPerPageSetting)->getCostPerPage()->monochromeCostPerPage * $this->getPageCounts()->getBlackPageCount()->getMonthly());
        }

        return $this->_cachedMonthlyBlackAndWhiteCost [$cacheKey];
    }

    /**
     * Calculates the cost of the device on a monthly basis to compare with
     * replacement devices
     *
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return float
     */
    public function getMonthlyRate ($costPerPageSetting)
    {
        if (!isset($this->_monthlyRate))
        {
            $this->_monthlyRate = 0;
            $this->_monthlyRate += $this->getCostOfBlackAndWhiteInkAndToner($costPerPageSetting);
            $this->_monthlyRate += $this->getCostOfColorInkAndToner($costPerPageSetting);
            $this->_monthlyRate += ($this->getPageCounts()->getCombinedPageCount()->getMonthly() * self::getITCostPerPage());
        }

        return $this->_monthlyRate;
    }

    /**
     * @param                                      $monthlyTotalCost
     *
     * @param CostPerPageSettingModel              $costPerPageSetting
     *
     * @return float
     */
    public function getMonthlyRatePercentage ($monthlyTotalCost, $costPerPageSetting)
    {
        return ($this->getMonthlyRate($costPerPageSetting) / $monthlyTotalCost) * 100;
    }

    /**
     * @param $monthlyLeasePayment
     * @param $monochromeCostPerPage
     * @param $colorCostPerPage
     *
     * @return float
     */
    public function getLeasedMonthlyRate ($monthlyLeasePayment, $monochromeCostPerPage, $colorCostPerPage)
    {
        return $monthlyLeasePayment + ($monochromeCostPerPage * $this->getPageCounts()->getBlackPageCount()->getMonthly()) + ($colorCostPerPage * $this->getPageCounts()->getBlackPageCount()->getMonthly());
    }

    /**
     * @param $monthlyLeasePayment
     * @param $monochromeCostPerPage
     * @param $colorCostPerPage
     * @param $totalMonthlyCost
     *
     * @return float
     */
    public function getLeasedMonthlyRatePercentage ($monthlyLeasePayment, $monochromeCostPerPage, $colorCostPerPage, $totalMonthlyCost)
    {
        return ($this->getLeasedMonthlyRate($monthlyLeasePayment, $monochromeCostPerPage, $colorCostPerPage) / $totalMonthlyCost) * 100;
    }

    /**
     * @return float
     */
    public static function getITCostPerPage ()
    {

        if (!isset(DeviceInstanceModel::$ITCostPerPage))
        {
            DeviceInstanceModel::$ITCostPerPage = 0;
        }

        return DeviceInstanceModel::$ITCostPerPage;
    }

    /**
     * @param float $ITCostPerPage
     */
    public static function setITCostPerPage ($ITCostPerPage)
    {
        DeviceInstanceModel::$ITCostPerPage = $ITCostPerPage;
    }

    /**
     * Gets the master device
     *
     * @return MasterDeviceModel
     */
    public function getMasterDevice ()
    {
        if (!isset($this->_masterDevice))
        {
            if ($this->getIsMappedToMasterDevice())
            {
                $deviceInstanceMasterDevice = $this->getDeviceInstanceMasterDevice();
                $dealerId                   = self::getAuthDealerId();
                $this->_masterDevice        = MasterDeviceMapper::getInstance()->findForReports($deviceInstanceMasterDevice->masterDeviceId, $dealerId);
            }
            else
            {
                if ($this->useUserData && $this->getRmsUploadRow()->hasCompleteInformation)
                {
                    $this->_masterDevice = RmsUploadRowMapper::getInstance()->convertUploadRowToMasterDevice($this->getRmsUploadRow());
                }
                else
                {
                    $this->_masterDevice = false;
                }
            }
        }

        return $this->_masterDevice;
    }

    /**
     * @param MasterDeviceModel $MasterDevice
     *
     * @return DeviceInstanceModel
     */
    public function setMasterDevice ($MasterDevice)
    {
        $this->_masterDevice = $MasterDevice;

        return $this;
    }

    /**
     * Gets the RMS upload row
     *
     * @return RmsUploadRowModel
     */
    public function getRmsUploadRow ()
    {
        if (!isset($this->_rmsUploadRow))
        {
            $this->_rmsUploadRow = RmsUploadRowMapper::getInstance()->find($this->rmsUploadRowId);
        }

        return $this->_rmsUploadRow;
    }

    /**
     * Sets the RMS upload row
     *
     * @param RmsUploadRowModel $rmsUploadRow
     *
     * @return DeviceInstanceModel
     */
    public function setRmsUploadRow ($rmsUploadRow)
    {
        $this->_rmsUploadRow = $rmsUploadRow;

        return $this;
    }

    /**
     * Gets the device instance master device
     *
     * @return DeviceInstanceMasterDeviceModel
     */
    public function getDeviceInstanceMasterDevice ()
    {
        if (!isset($this->_deviceInstanceMasterDevice))
        {
            $this->_deviceInstanceMasterDevice = DeviceInstanceMasterDeviceMapper::getInstance()->find($this->id);
        }

        return $this->_deviceInstanceMasterDevice;
    }

    /**
     * Sets the device instance master device
     *
     * @param DeviceInstanceMasterDeviceModel $deviceInstanceMasterDevice
     *
     * @return DeviceInstanceModel
     */
    public function setDeviceInstanceMasterDevice ($deviceInstanceMasterDevice)
    {
        $this->_deviceInstanceMasterDevice = $deviceInstanceMasterDevice;

        return $this;
    }

    /**
     * True is the device instance is mapped to a master device
     *
     * @return bool
     */
    public function getIsMappedToMasterDevice ()
    {
        return (!$this->useUserData && $this->getDeviceInstanceMasterDevice() instanceof DeviceInstanceMasterDeviceModel);
    }

    /**
     * Gets an associated hardware optimization device instance
     *
     * @param int $hardwareOptimizationId The hardware optimization id to use when searching for the hardware optimization device instance
     *
     * @return HardwareOptimizationDeviceInstanceModel
     */
    public function getHardwareOptimizationDeviceInstance ($hardwareOptimizationId)
    {
        if (!isset($this->_hardwareOptimizationDeviceInstances))
        {
            $this->_hardwareOptimizationDeviceInstances = [];
        }

        if ($hardwareOptimizationId > 0 && !array_key_exists($hardwareOptimizationId, $this->_hardwareOptimizationDeviceInstances))
        {
            $hardwareOptimizationDeviceInstance = HardwareOptimizationDeviceInstanceMapper::getInstance()->find([$this->id, $hardwareOptimizationId]);
            if (!$hardwareOptimizationDeviceInstance instanceof HardwareOptimizationDeviceInstanceModel)
            {
                $hardwareOptimizationDeviceInstance                         = new HardwareOptimizationDeviceInstanceModel();
                $hardwareOptimizationDeviceInstance->deviceInstanceId       = $this->id;
                $hardwareOptimizationDeviceInstance->hardwareOptimizationId = $hardwareOptimizationId;
                $hardwareOptimizationDeviceInstance->action                 = HardwareOptimizationDeviceInstanceModel::ACTION_KEEP;

                try
                {
                    HardwareOptimizationDeviceInstanceMapper::getInstance()->insert($hardwareOptimizationDeviceInstance);
                }
                catch (Exception $e)
                {
                    $hardwareOptimizationDeviceInstance = false;
                }
            }

            $this->_hardwareOptimizationDeviceInstances[$hardwareOptimizationId] = $hardwareOptimizationDeviceInstance;
        }

        return $this->_hardwareOptimizationDeviceInstances[$hardwareOptimizationId];
    }

    /**
     * Sets the hardware optimization device instance
     *
     * @param HardwareOptimizationDeviceInstanceModel $hardwareOptimizationDeviceInstance
     *
     * @return $this
     */
    public function setHardwareOptimizationDeviceInstance ($hardwareOptimizationDeviceInstance)
    {
        if (!isset($this->_hardwareOptimizationDeviceInstances))
        {
            $this->_hardwareOptimizationDeviceInstances = [];
        }

        $this->_hardwareOptimizationDeviceInstances[$hardwareOptimizationDeviceInstance->hardwareOptimizationId] = $hardwareOptimizationDeviceInstance;

        return $this;
    }

    /**
     * Gets the replacement master device
     *
     * @return MasterDeviceModel
     */
    public function getReplacementMasterDevice ()
    {
        if (!isset($this->_replacementMasterDevice))
        {
            $deviceInstanceReplacementMasterDevice = DeviceInstanceReplacementMasterDeviceMapper::getInstance()->find($this->id);
            if ($deviceInstanceReplacementMasterDevice)
            {
                $this->_replacementMasterDevice = $deviceInstanceReplacementMasterDevice->getMasterDevice();
            }
        }

        return $this->_replacementMasterDevice;
    }

    /**
     * @param $hardwareOptimizationId
     *
     * @return MasterDeviceModel
     */
    public function getReplacementMasterDeviceForHardwareOptimization ($hardwareOptimizationId)
    {
        if (!isset($this->_replacementMasterDevice))
        {
            $this->_replacementMasterDevice = [];
        }

        if (!array_key_exists($hardwareOptimizationId, $this->_replacementMasterDevice))
        {
            $hardwareOptimizationDeviceInstance = $this->getHardwareOptimizationDeviceInstance($hardwareOptimizationId);
            if ($hardwareOptimizationDeviceInstance instanceof HardwareOptimizationDeviceInstanceModel && ($hardwareOptimizationDeviceInstance->action === HardwareOptimizationDeviceInstanceModel::ACTION_REPLACE || $hardwareOptimizationDeviceInstance->action === HardwareOptimizationDeviceInstanceModel::ACTION_UPGRADE))
            {
                $this->_replacementMasterDevice[$hardwareOptimizationId] = $hardwareOptimizationDeviceInstance->getMasterDevice();
            }
            else
            {
                $this->_replacementMasterDevice[$hardwareOptimizationId] = false;
            }
        }

        return $this->_replacementMasterDevice[$hardwareOptimizationId];
    }

    /**
     *Sets the replacement master device
     *
     * @param MasterDeviceModel $replacementMasterDevice
     *
     * @return $this
     */
    public function setReplacementMasterDevice ($replacementMasterDevice)
    {
        $this->_replacementMasterDevice = $replacementMasterDevice;

        return $this;
    }

    /**
     * @return String
     */
    public function getDeviceAction()
    {
        return $this->_deviceAction;
    }

    /**
     * @param String $deviceAction
     */
    public function setDeviceAction($deviceAction)
    {
        $this->_deviceAction = $deviceAction;
    }


    /**
     * The action of the device
     *
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return String $Action
     */
    public function getAction ($costPerPageSetting)
    {
        if (!isset($this->_deviceAction))
        {
            /**
             * Retirement
             * - Device must be printing less than Y pages
             */
            if ($this->getPageCounts()->getCombinedPageCount()->getMonthly() <= self::RETIREMENT_MAX_PAGE_COUNT)
            {
                $this->_deviceAction = DeviceInstanceModel::ACTION_RETIRE;
            }
            /**
             * Replacement (Do Not Repair)
             * - Device must be printing more than Y pages
             *
             * - Must also match ONE of the following:
             *      - Not capable of reporting toner levels
             *      - Over its max life usage
             *      - Over X years old
             */
            else if (
                ($this->getMasterDevice()->getAge() > self::REPLACEMENT_AGE || $this->getLifeUsage() > 1 || !$this->isCapableOfReportingTonerLevels())
                && $this->getPageCounts()->getCombinedPageCount()->getMonthly() >= self::REPLACEMENT_MIN_PAGE_COUNT
            )
            {
                $this->_deviceAction = DeviceInstanceModel::ACTION_REPLACE;
            }
            else
            {
                $this->_deviceAction = DeviceInstanceModel::ACTION_KEEP;
            }
        }

        return $this->_deviceAction;
    }

    /**
     * Getter for $_reason
     *
     * @param $hardwareoptimizationId
     *
     * @return string
     */
    public function getReason ($hardwareoptimizationId)
    {
        if (!isset($this->_reason))
        {
            $deviceSwapReasonId = DeviceInstanceDeviceSwapReasonMapper::getInstance()->find([$hardwareoptimizationId, $this->id])->deviceSwapReasonId;
            $this->_reason      = DeviceSwapReasonMapper::getInstance()->find($deviceSwapReasonId);
        }

        return $this->_reason->reason;
    }

    public function getCombinedMonthlyPageCount() {
        if (!isset($this->_combinedMonthlyPageCount)) {
            $this->_combinedMonthlyPageCount = $this->getPageCounts()->getCombinedPageCount()->getMonthly();
        }
        return $this->_combinedMonthlyPageCount;
    }

    /**
     * @param int $combinedMonthlyPageCount
     */
    public function setCombinedMonthlyPageCount($combinedMonthlyPageCount)
    {
        $this->_combinedMonthlyPageCount = $combinedMonthlyPageCount;
    }

    /**
     * @return float
     */
    public function getColorMonthlyPageCount()
    {
        if (!isset($this->_colorMonthlyPageCount)) {
            $this->_colorMonthlyPageCount = $this->getPageCounts()->getColorPageCount()->getMonthly();
        }
        return $this->_colorMonthlyPageCount;
    }

    /**
     * @param float $colorMonthlyPageCount
     */
    public function setColorMonthlyPageCount($colorMonthlyPageCount)
    {
        $this->_colorMonthlyPageCount = $colorMonthlyPageCount;
    }

    /**
     * @return float
     */
    public function getBlackMonthlyPageCount()
    {
        if (!isset($this->_blackMonthlyPageCount)) {
            $this->_blackMonthlyPageCount = $this->getPageCounts()->getBlackPageCount()->getMonthly();
        }
        return $this->_blackMonthlyPageCount;
    }

    /**
     * @param float $blackMonthlyPageCount
     */
    public function setBlackMonthlyPageCount($blackMonthlyPageCount)
    {
        $this->_blackMonthlyPageCount = $blackMonthlyPageCount;
    }




    /**
     * Gets this device instance's page counts
     *
     * @param int $blackToColorRatio The amount of mono pages to convert to color pages. Defaults to 0%
     *
     * @return PageCountsModel
     */
    public function getPageCounts ($blackToColorRatio = 0)
    {
        // Make sure our array is initialized
        if (!isset($this->_cachedPageCounts))
        {
            $this->_cachedPageCounts = [];
        }

        $cacheKey = "{$blackToColorRatio}";
        if (!array_key_exists($cacheKey, $this->_cachedPageCounts))
        {
            $pageCounts = new PageCountsModel();
            $meter      = $this->getMeter();

            if ($meter instanceof DeviceInstanceMeterModel)
            {
                $pageCounts->getBlackPageCount()->add($meter->getBlackPageCount());
                $pageCounts->getColorPageCount()->add($meter->getColorPageCount());
                $pageCounts->getCopyBlackPageCount()->add($meter->getCopyBlackPageCount());
                $pageCounts->getCopyColorPageCount()->add($meter->getCopyColorPageCount());
                $pageCounts->getFaxPageCount()->add($meter->getFaxPageCount());
                $pageCounts->getPrintA3BlackPageCount()->add($meter->getPrintA3BlackPageCount());
                $pageCounts->getPrintA3ColorPageCount()->add($meter->getPrintA3ColorPageCount());
                $pageCounts->getScanPageCount()->add($meter->getScanPageCount());
                $pageCounts->getLifePageCount()->add($meter->getLifePageCount());

                if ($blackToColorRatio > 0)
                {
                    $pageCounts->processPageRatio($blackToColorRatio);
                }
            }

            $this->_cachedPageCounts[$cacheKey] = $pageCounts;
        }

        return $this->_cachedPageCounts[$cacheKey];
    }

    /**
     * @param $hardwareOptimizationId
     *
     * @return int
     */
    public function getDefaultDeviceSwapReasonCategoryId ($hardwareOptimizationId)
    {
        $categoryId = 0;

        $hardwareOptimizationDeviceInstance = $this->getHardwareOptimizationDeviceInstance($hardwareOptimizationId);

        if ($hardwareOptimizationDeviceInstance->action == HardwareOptimizationDeviceInstanceModel::ACTION_REPLACE)
        {
            $categoryId = DeviceSwapReasonCategoryModel::HAS_REPLACEMENT;
        }
        else if ($hardwareOptimizationDeviceInstance->action == HardwareOptimizationDeviceInstanceModel::ACTION_UPGRADE)
        {
            $categoryId = DeviceSwapReasonCategoryModel::HAS_FUNCTIONALITY_REPLACEMENT;
        }
        else if ($hardwareOptimizationDeviceInstance->action == HardwareOptimizationDeviceInstanceModel::ACTION_DNR)
        {
            $categoryId = DeviceSwapReasonCategoryModel::FLAGGED;
        }

        return $categoryId;
    }

    /*****************************************************
     ***************Device Calculations*******************
     *****************************************************/
    /**
     * Calculates the cost per page for a master device.
     *
     * @param CostPerPageSettingModel $costPerPageSetting
     *            The settings to use when calculating cost per page
     * @param MasterDeviceModel       $masterDevice
     *            The master device to use
     *
     * @throws InvalidArgumentException
     * @return DeviceCostPerPageModel
     */
    public function calculateCostPerPage ($costPerPageSetting, $masterDevice = null)
    {
        /**
         * Caching Array
         */
        if (!isset($this->_cachedDeviceCostPerPage))
        {
            $this->_cachedDeviceCostPerPage = [];
        }

        // If master device isn't passed, get the master device
        if (!$masterDevice instanceof MasterDeviceModel)
        {
            $masterDevice = $this->getMasterDevice();
        }

        $masterDeviceId = ($masterDevice instanceof MasterDeviceModel) ? (int)$masterDevice->id : 0;

        $cacheKey = $costPerPageSetting->createCacheKey() . "_master_device" . $masterDeviceId . "_device_instance" . (int)$this->id;

        if (!array_key_exists($cacheKey, $this->_cachedDeviceCostPerPage))
        {
            /**
             * Set our page coverages if the  device has it's specific coverages
             */
            if ($costPerPageSetting->useDevicePageCoverages)
            {
                // Clone to make sure we don't modify global page coverages
                $costPerPageSetting = clone $costPerPageSetting;

                /**
                 * Monochrome page coverage should be used from the device
                 */
                if ($this->pageCoverageMonochrome > 0)
                {
                    $costPerPageSetting->pageCoverageMonochrome = $this->pageCoverageMonochrome;
                }

                /**
                 * Color page coverage is added here. It should be noted that in our settings for "pageCoverageColor" that it already contains
                 */
                if ($this->pageCoverageCyan > 0 || $this->pageCoverageMagenta > 0 || $this->pageCoverageYellow > 0)
                {
                    $oldColorCoverage                      = $costPerPageSetting->pageCoverageColor / 4;
                    $costPerPageSetting->pageCoverageColor = ($this->pageCoverageMonochrome > 0) ? $this->pageCoverageMonochrome : $oldColorCoverage; // Mono compensation
                    $costPerPageSetting->pageCoverageColor += ($this->pageCoverageCyan > 0) ? $this->pageCoverageCyan : $oldColorCoverage;
                    $costPerPageSetting->pageCoverageColor += ($this->pageCoverageMagenta > 0) ? $this->pageCoverageMagenta : $oldColorCoverage;
                    $costPerPageSetting->pageCoverageColor += ($this->pageCoverageYellow > 0) ? $this->pageCoverageYellow : $oldColorCoverage;
                }
            }

            /**
             * Get our device cost per page or create a blank one
             */
            if ($masterDevice instanceof MasterDeviceModel)
            {
                $deviceCostPerPage = $masterDevice->calculateCostPerPage($costPerPageSetting, $this->isManaged);
            }
            else
            {
                // Create fake instance
                $deviceCostPerPage            = new DeviceCostPerPageModel([], $costPerPageSetting);
                $deviceCostPerPage->isManaged = $this->isManaged;
            }


            $this->_cachedDeviceCostPerPage [$cacheKey] = $deviceCostPerPage;
        }

        return $this->_cachedDeviceCostPerPage [$cacheKey];
    }

    /**
     * Figures out if the device can report toner levels
     *
     * @return bool|int
     */
    public function isCapableOfReportingTonerLevels ()
    {
        /**
         * Always use the master device record.
         * This way administrators can control which devices can and can not report toner levels.
         */
        return (!$this->getMasterDevice()) ? false : $this->getMasterDevice()->isCapableOfReportingTonerLevels;
    }

    /**
     * Calculates the monthly cost for this instance
     *
     * @param CostPerPageSettingModel $costPerPageSetting
     *            The settings to use when calculating cost per page
     * @param MasterDeviceModel       $masterDevice
     *            The master device to use
     * @param int                     $blackToColorRatio
     *
     * @return number
     */
    public function calculateMonthlyCost (CostPerPageSettingModel $costPerPageSetting, $masterDevice = null, $blackToColorRatio = null)
    {
        return $this->calculateMonthlyMonoCost($costPerPageSetting, $masterDevice, $blackToColorRatio) + $this->calculateMonthlyColorCost($costPerPageSetting, $masterDevice, $blackToColorRatio);
    }

    /**
     * Calculates the monthly cost for monochrome printing
     *
     * @param CostPerPageSettingModel $costPerPageSetting
     *            The setting used to calculate cost per page
     * @param MasterDeviceModel       $masterDevice
     *            the master device to us
     *
     * @param int                     $blackToColorRatio
     *
     * @return number
     */
    public function calculateMonthlyMonoCost (CostPerPageSettingModel $costPerPageSetting, $masterDevice = null, $blackToColorRatio = 0)
    {
        $monoCostPerPage = $this->calculateCostPerPage($costPerPageSetting, $masterDevice)->getCostPerPage()->monochromeCostPerPage;
        if ($blackToColorRatio) {
            return $monoCostPerPage * $this->getPageCounts($blackToColorRatio)->getBlackPageCount()->getMonthly();
        }
        return $monoCostPerPage * $this->getBlackMonthlyPageCount();
    }

    /**
     * Calculates the monthly cost for color printing
     *
     * @param CostPerPageSettingModel $costPerPageSetting
     *            the setting used to calculate cost per page
     * @param MasterDeviceModel       $masterDevice
     *            the master device to use, or null for current instance of device
     *
     * @param int                     $blackToColorRatio
     *
     * @return number
     */
    public function calculateMonthlyColorCost (CostPerPageSettingModel $costPerPageSetting, $masterDevice = null, $blackToColorRatio = 0)
    {
        $colorCostPerPage = $this->calculateCostPerPage($costPerPageSetting, $masterDevice)->getCostPerPage()->colorCostPerPage;
        if ($blackToColorRatio) {
            return $colorCostPerPage * $this->getPageCounts($blackToColorRatio)->getColorPageCount()->getMonthly();
        }
        return $colorCostPerPage * $this->getColorMonthlyPageCount();
    }

    /**
     * Returns percent of maximum recommended print volume they are printing.
     * If their recommended max is 1000, and they print 2000. This returns 200 (Without % Sign)
     *
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return float
     */
    public function calculatePercentOfMaximumRecommendedMaxVolume ($costPerPageSetting)
    {
        $percent = 0;
        if ($this->getMasterDevice()->maximumRecommendedMonthlyPageVolume > 0)
        {
            $percent = ($this->getPageCounts()->getCombinedPageCount()->getMonthly() / $this->getMasterDevice()->maximumRecommendedMonthlyPageVolume * 100);
        }

        return $percent;
    }

    /**
     * Calculates the percent monthly page volume of total page volume
     *
     * @param int $totalPageVolume
     *              The Total Page Volume
     *
     * @return float
     */
    public function calculateMonthlyPercentOfTotalVolume ($totalPageVolume)
    {
        return $this->getPageCounts()->getCombinedPageCount()->getMonthly() / $totalPageVolume * 100;
    }

    /**
     * Calculates a cost per page for a replacement device
     *
     * @param CostPerPageSettingModel $costPerPageSetting
     *            The settings to use when calculating cost per page
     *
     * @param  int                    $hardwareOptimizationId
     *
     * @return CostPerPageModel
     */
    public function calculateCostPerPageWithReplacement (CostPerPageSettingModel $costPerPageSetting, $hardwareOptimizationId)
    {
        return $this->calculateCostPerPage($costPerPageSetting, $this->getReplacementMasterDeviceForHardwareOptimization($hardwareOptimizationId))->getCostPerPage();
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
        $device = $this->getMasterDevice();
        if (empty($device)) return false;
        return $device->hasValidToners($dealerId, $clientId);
    }
}