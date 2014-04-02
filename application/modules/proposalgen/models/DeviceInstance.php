<?php

/**
 * Class Proposalgen_Model_DeviceInstance
 */
class Proposalgen_Model_DeviceInstance extends My_Model_Abstract
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
    static $RUNNING_HOUR_ARRAY = array(
        500 => 8,
        100 => 4,
        0   => 2
    );

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
    public $rmsDeviceId;

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

    /*
     * ********************************************************************************
     * Related Objects
     * ********************************************************************************
     */
    /**
     * Our Meter
     *
     * @var Proposalgen_Model_DeviceInstanceMeter
     */
    protected $_meter;

    /**
     * Used in determining actions for replacement devices.
     *
     * @var String
     */
    protected $_deviceAction;

    /**
     * @var Proposalgen_Model_MasterDevice
     */
    protected $_masterDevice;

    /**
     * @var Proposalgen_Model_Rms_Upload_Row
     */
    protected $_rmsUploadRow;

    /**
     * @var Proposalgen_Model_Device_Instance_Master_Device
     */
    protected $_deviceInstanceMasterDevice;

    /**
     * @var Proposalgen_Model_MasterDevice
     */
    protected $_replacementMasterDevice;

    /**
     * @var Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance
     */
    protected $_hardwareOptimizationDeviceInstances;

    /**
     * @var Proposalgen_Model_MasterDevice
     */
    protected $_memjetReplacementMasterDevice;

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
     * @var Proposalgen_Model_Rms_Upload_Row
     */
    protected $_uploadDataCollectorRow;

    /**
     * @var Proposalgen_Model_ReplacementDevice
     */
    protected $_replacementDevice;

    /**
     * @var Proposalgen_Model_CostPerPage
     */
    protected $_cachedDeviceCostPerPage;

    /**
     * @var Proposalgen_Model_CostPerPage
     */
    protected $_cachedMonthlyBlackAndWhiteCost;

    /**
     * @var Proposalgen_Model_PageCounts
     */
    protected $_cachedPageCounts;

    /**
     * @var string
     */
    public $_exclusionReason;

    /**
     * The reason why we are replacing a device.
     *
     * @var string
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
     * @var Proposalgen_Model_Toner[]
     */
    static $uniqueTonerArray = array();


    /**
     * Applies overrides to for cost per page
     *
     * @param float    $adminCostPerPage
     * @param bool|int $hardwareOptimizationId
     * @param bool|int $memjetOptimizationId
     */
    public function processOverrides ($adminCostPerPage, $hardwareOptimizationId = false, $memjetOptimizationId = false)
    {
        /**
         * Process any overrides we have on a master device
         */
        $masterDevice = $this->getMasterDevice();
        if ($masterDevice instanceof Proposalgen_Model_MasterDevice)
        {
            $this->getMasterDevice()->processOverrides($adminCostPerPage);
        }

        /*
         * Process any overrides on the replacement master device
         */
        if ($hardwareOptimizationId > 0)
        {
            if ($this->getIsMappedToMasterDevice() || $this->useUserData)
            {
                $hardwareOptimizationDeviceInstance = $this->getHardwareOptimizationDeviceInstance($hardwareOptimizationId);
                if ($hardwareOptimizationDeviceInstance instanceof Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance)
                {
                    if ($hardwareOptimizationDeviceInstance->masterDeviceId > 0)
                    {
                        $hardwareOptimizationDeviceInstance->getMasterDevice()->processOverrides($adminCostPerPage);
                    }
                }
            }
        }

        if ($memjetOptimizationId > 0)
        {
            // TODO lrobert: Process the override for the memjet replacement
        }

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

        if (isset($params->rmsDeviceId) && !is_null($params->rmsDeviceId))
        {
            $this->rmsDeviceId = $params->rmsDeviceId;
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
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                       => $this->id,
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
            "pageCoverageCyan"         => $this->pageCoverageCyan,
            "pageCoverageMagenta"      => $this->pageCoverageMagenta,
            "pageCoverageYellow"       => $this->pageCoverageYellow,
            "isManaged"                => $this->isManaged,
            "rmsDeviceId"              => $this->rmsDeviceId,
            "deviceSwapReasonId"       => $this->deviceSwapReasonId,
            "rawDeviceName"            => $this->rawDeviceName,
            "compatibleWithJitProgram" => $this->compatibleWithJitProgram,
        );
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
     * @return Proposalgen_Model_DeviceInstanceMeter
     */
    public function getMeter ()
    {
        if (!isset($this->_meter))
        {
            $this->_meter = Proposalgen_Model_Mapper_DeviceInstanceMeter::getInstance()->fetchForDeviceInstance($this->id);
        }

        return $this->_meter;
    }

    /**
     * @param Proposalgen_Model_DeviceInstanceMeter $Meters
     *
     * @return Proposalgen_Model_DeviceInstance
     */
    public function setMeters ($Meters)
    {
        $this->_meters = $Meters;

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
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
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
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
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
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
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
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
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
     * @return Proposalgen_Model_DeviceInstance
     */
    public function setDeviceName ($DeviceName)
    {
        $this->_deviceName = $DeviceName;

        return $this;
    }

    /**
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return float
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
     * @internal param \Proposalgen_Model_CostPerPageSetting $costPerPageSetting
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
     * @return Proposalgen_Model_DeviceInstance
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
     * @return Proposalgen_Model_DeviceInstance
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
     * @return Proposalgen_Model_DeviceInstance
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
        if (!isset(Proposalgen_Model_DeviceInstance::$KWH_Cost))
        {

            Proposalgen_Model_DeviceInstance::$KWH_Cost = null;
        }

        return Proposalgen_Model_DeviceInstance::$KWH_Cost;
    }

    /**
     * @return Proposalgen_Model_Rms_Upload_Row
     */
    public function getUploadDataCollectorRow ()
    {
        if (!isset($this->_uploadDataCollectorRow))
        {
            $this->_uploadDataCollectorRow = Proposalgen_Model_Mapper_Rms_Upload_Row::getInstance()->find($this->rmsUploadRowId);
        }

        return $this->_uploadDataCollectorRow;
    }

    /**
     * @param Proposalgen_Model_Rms_Upload_Row $UploadDataCollector
     *
     * @return Proposalgen_Model_DeviceInstance
     */
    public function setUploadDataCollectorRow ($UploadDataCollector)
    {
        $this->_uploadDataCollectorRow = $UploadDataCollector;

        return $this;
    }

    /**
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return float
     */
    public function getMonthlyBlackAndWhiteCost ($costPerPageSetting)
    {
        // Make sure our array is initialized
        if (!isset($this->_cachedMonthlyBlackAndWhiteCost))
        {
            $this->_cachedMonthlyBlackAndWhiteCost = array();
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
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
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
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
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

        if (!isset(Proposalgen_Model_DeviceInstance::$ITCostPerPage))
        {
            Proposalgen_Model_DeviceInstance::$ITCostPerPage = 0;
        }

        return Proposalgen_Model_DeviceInstance::$ITCostPerPage;
    }

    /**
     * @param float $ITCostPerPage
     */
    public static function setITCostPerPage ($ITCostPerPage)
    {
        Proposalgen_Model_DeviceInstance::$ITCostPerPage = $ITCostPerPage;
    }

    /**
     * Gets the master device
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function getMasterDevice ()
    {
        if (!isset($this->_masterDevice))
        {
            if ($this->getIsMappedToMasterDevice())
            {
                $deviceInstanceMasterDevice = $this->getDeviceInstanceMasterDevice();
                $dealerId                   = Zend_Auth::getInstance()->getIdentity()->dealerId;
                $this->_masterDevice        = Proposalgen_Model_Mapper_MasterDevice::getInstance()->findForReports($deviceInstanceMasterDevice->masterDeviceId, $dealerId, Proposalgen_Model_MasterDevice::$ReportLaborCostPerPage, Proposalgen_Model_MasterDevice::$ReportPartsCostPerPage);
            }
            else
            {
                if ($this->useUserData && $this->getRmsUploadRow()->hasCompleteInformation)
                {
                    $this->_masterDevice = Proposalgen_Model_Mapper_Rms_Upload_Row::getInstance()->convertUploadRowToMasterDevice($this->getRmsUploadRow());
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
     * @param Proposalgen_Model_MasterDevice $MasterDevice
     *
     * @return Proposalgen_Model_DeviceInstance
     */
    public function setMasterDevice ($MasterDevice)
    {
        $this->_masterDevice = $MasterDevice;

        return $this;
    }

    /**
     * Gets the RMS upload row
     *
     * @return Proposalgen_Model_Rms_Upload_Row
     */
    public function getRmsUploadRow ()
    {
        if (!isset($this->_rmsUploadRow))
        {
            $this->_rmsUploadRow = Proposalgen_Model_Mapper_Rms_Upload_Row::getInstance()->find($this->rmsUploadRowId);
        }

        return $this->_rmsUploadRow;
    }

    /**
     * Sets the RMS upload row
     *
     * @param Proposalgen_Model_Rms_Upload_Row $rmsUploadRow
     *
     * @return Proposalgen_Model_DeviceInstance
     */
    public function setRmsUploadRow ($rmsUploadRow)
    {
        $this->_rmsUploadRow = $rmsUploadRow;

        return $this;
    }

    /**
     * Gets the device instance master device
     *
     * @return Proposalgen_Model_Device_Instance_Master_Device
     */
    public function getDeviceInstanceMasterDevice ()
    {
        if (!isset($this->_deviceInstanceMasterDevice))
        {
            $this->_deviceInstanceMasterDevice = Proposalgen_Model_Mapper_Device_Instance_Master_Device::getInstance()->find($this->id);
        }

        return $this->_deviceInstanceMasterDevice;
    }

    /**
     * Sets the device instance master device
     *
     * @param Proposalgen_Model_Device_Instance_Master_Device $deviceInstanceMasterDevice
     *
     * @return Proposalgen_Model_DeviceInstance
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
        return (!$this->useUserData && $this->getDeviceInstanceMasterDevice() instanceof Proposalgen_Model_Device_Instance_Master_Device);
    }

    /**
     * Gets an associated hardware optimization device instance
     *
     * @param int $hardwareOptimizationId The hardware optimization id to use when searching for the hardware optimization device instance
     *
     * @return \Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance
     */
    public function getHardwareOptimizationDeviceInstance ($hardwareOptimizationId)
    {
        if (!isset($this->_hardwareOptimizationDeviceInstances))
        {
            $this->_hardwareOptimizationDeviceInstances = array();
        }

        if ($hardwareOptimizationId > 0 && !array_key_exists($hardwareOptimizationId, $this->_hardwareOptimizationDeviceInstances))
        {
            $hardwareOptimizationDeviceInstance = Hardwareoptimization_Model_Mapper_Hardware_Optimization_DeviceInstance::getInstance()->find(array($this->id, $hardwareOptimizationId));
            if (!$hardwareOptimizationDeviceInstance instanceof Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance)
            {
                $hardwareOptimizationDeviceInstance                         = new Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance();
                $hardwareOptimizationDeviceInstance->deviceInstanceId       = $this->id;
                $hardwareOptimizationDeviceInstance->hardwareOptimizationId = $hardwareOptimizationId;
                $hardwareOptimizationDeviceInstance->action                 = Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_KEEP;

                try
                {
                    Hardwareoptimization_Model_Mapper_Hardware_Optimization_DeviceInstance::getInstance()->insert($hardwareOptimizationDeviceInstance);
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
     * @param Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance $hardwareOptimizationDeviceInstance
     *
     * @return $this
     */
    public function setHardwareOptimizationDeviceInstance ($hardwareOptimizationDeviceInstance)
    {
        if (!isset($this->_hardwareOptimizationDeviceInstances))
        {
            $this->_hardwareOptimizationDeviceInstances = array();
        }

        $this->_hardwareOptimizationDeviceInstances[$hardwareOptimizationDeviceInstance->hardwareOptimizationId] = $hardwareOptimizationDeviceInstance;

        return $this;
    }

    /**
     * Gets the replacement master device
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function getReplacementMasterDevice ()
    {
        if (!isset($this->_replacementMasterDevice))
        {
            $deviceInstanceReplacementMasterDevice = Proposalgen_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance()->find($this->id);
            if ($deviceInstanceReplacementMasterDevice)
            {
                $this->_replacementMasterDevice = $deviceInstanceReplacementMasterDevice->getMasterDevice();
            }
        }

        return $this->_replacementMasterDevice;
    }

    /**
     * Gets the replacement master device
     *
     * @param int $memjetOptimizationId
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function getMemjetReplacementMasterDevice ($memjetOptimizationId)
    {
        if (!isset($this->_memjetReplacementMasterDevice))
        {
            $deviceInstanceReplacementMasterDevice = Memjetoptimization_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance()->find($this->id, $memjetOptimizationId);
            if ($deviceInstanceReplacementMasterDevice)
            {
                $this->_memjetReplacementMasterDevice = $deviceInstanceReplacementMasterDevice->getMasterDevice();
            }
        }

        return $this->_memjetReplacementMasterDevice;
    }

    /**
     * @param $hardwareOptimizationId
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function getReplacementMasterDeviceForHardwareOptimization ($hardwareOptimizationId)
    {
        if (!isset($this->_replacementMasterDevice))
        {
            $this->_replacementMasterDevice = array();
        }

        if (!array_key_exists($hardwareOptimizationId, $this->_replacementMasterDevice))
        {
            $hardwareOptimizationDeviceInstance = $this->getHardwareOptimizationDeviceInstance($hardwareOptimizationId);
            if ($hardwareOptimizationDeviceInstance instanceof Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance && $hardwareOptimizationDeviceInstance->action === Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_REPLACE)
            {
                $this->_replacementMasterDevice[$hardwareOptimizationId] = $hardwareOptimizationDeviceInstance->getMasterDevice();
            }
        }

        return $this->_replacementMasterDevice[$hardwareOptimizationId];
    }

    /**
     * @param $memjetOptimizationId
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function getReplacementMasterDeviceForMemjetOptimization ($memjetOptimizationId)
    {
        if (!isset($this->_memjetReplacementMasterDevice))
        {
            $deviceInstanceReplacementMasterDevice = Memjetoptimization_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance()->find(array($this->id, $memjetOptimizationId));
            $memjetOptimization                    = Memjetoptimization_Model_Mapper_Memjet_Optimization::getInstance()->find($memjetOptimizationId);
            if ($deviceInstanceReplacementMasterDevice)
            {
                $this->_memjetReplacementMasterDevice = $deviceInstanceReplacementMasterDevice->getMasterDeviceForReports($memjetOptimization->dealerId, $memjetOptimization->getMemjetOptimizationSetting()->laborCostPerPage, $memjetOptimization->getMemjetOptimizationSetting()->partsCostPerPage);
            }
        }

        return $this->_memjetReplacementMasterDevice;
    }

    /**
     *Sets the replacement master device
     *
     * @param Proposalgen_Model_MasterDevice $replacementMasterDevice
     *
     * @return \Proposalgen_Model_DeviceInstance
     */
    public function setReplacementMasterDevice ($replacementMasterDevice)
    {
        $this->_replacementMasterDevice = $replacementMasterDevice;

        return $this;
    }

    /**
     * The action of the device
     *
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
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
                $this->_deviceAction = Proposalgen_Model_DeviceInstance::ACTION_RETIRE;
            }
            /**
             * Replacement (Do Not Repair)
             * - Device must be printing more than Y pages
             *
             * - Must also match ONE of the following:
             *      - Not capable of reporting toner levels
             *      - Over it's max life usage
             *      - Over X years old
             */
            else if (
                ($this->getMasterDevice()->getAge() > self::REPLACEMENT_AGE || $this->getLifeUsage() > 1 || !$this->isCapableOfReportingTonerLevels())
                && $this->getPageCounts()->getCombinedPageCount()->getMonthly() >= self::REPLACEMENT_MIN_PAGE_COUNT
            )
            {
                $this->_deviceAction = Proposalgen_Model_DeviceInstance::ACTION_REPLACE;
            }
            else
            {
                $this->_deviceAction = Proposalgen_Model_DeviceInstance::ACTION_KEEP;
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
            $deviceSwapReasonId = Hardwareoptimization_Model_Mapper_Device_Instance_Device_Swap_Reason::getInstance()->find(array($hardwareoptimizationId, $this->id))->deviceSwapReasonId;
            $this->_reason      = Hardwareoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->find($deviceSwapReasonId);
        }

        return $this->_reason->reason;
    }

    /**
     * Getter for $_reason
     *
     * @param $memjetOptimizationId
     *
     * @return string
     */
    public function getMemjetReason ($memjetOptimizationId)
    {
        if (!isset($this->_reason))
        {
            $deviceSwapReasonId = Memjetoptimization_Model_Mapper_Device_Instance_Device_Swap_Reason::getInstance()->find(array($memjetOptimizationId, $this->id))->deviceSwapReasonId;
            $this->_reason      = Memjetoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->find($deviceSwapReasonId);
        }

        return $this->_reason->reason;
    }

    /**
     * Gets this device instance's page counts
     *
     * @param int $blackToColorRatio The amount of mono pages to convert to color pages. Defaults to 0%
     *
     * @return Proposalgen_Model_PageCounts
     */
    public function getPageCounts ($blackToColorRatio = 0)
    {
        // Make sure our array is initialized
        if (!isset($this->_cachedPageCounts))
        {
            $this->_cachedPageCounts = array();
        }

        $cacheKey = "{$blackToColorRatio}";
        if (!array_key_exists($cacheKey, $this->_cachedPageCounts))
        {
            $pageCounts = new Proposalgen_Model_PageCounts();
            $meter      = $this->getMeter();

            if ($meter instanceof Proposalgen_Model_DeviceInstanceMeter)
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

        if ($hardwareOptimizationDeviceInstance->action == Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_REPLACE)
        {
            $categoryId = Hardwareoptimization_Model_Device_Swap_Reason_Category::HAS_REPLACEMENT;
        }
        else if ($hardwareOptimizationDeviceInstance->action == Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_DNR)
        {
            $categoryId = Hardwareoptimization_Model_Device_Swap_Reason_Category::FLAGGED;
        }

        return $categoryId;
    }

    /**
     * @param int                                  $memjetOptimizationId
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return int
     */
    public function getDefaultMemjetDeviceSwapReasonCategoryId ($memjetOptimizationId, $costPerPageSetting)
    {
        $categoryId        = 0;
        $replacementDevice = $this->getReplacementMasterDeviceForMemjetOptimization($memjetOptimizationId);
        if ($replacementDevice)
        {
            if (($replacementDevice->isColor() && $this->getMasterDevice()->isColor() == false) || ($replacementDevice->isMfp() && $this->getMasterDevice()->isMfp() == false))
            {
                $categoryId = Memjetoptimization_Model_Device_Swap_Reason_Category::FUNCTIONALITY_UPGRADE;
            }
            else
            {
                $categoryId = Memjetoptimization_Model_Device_Swap_Reason_Category::HAS_REPLACEMENT;
            }
        }
        else if ($this->getAction($costPerPageSetting) === Proposalgen_Model_DeviceInstance::ACTION_REPLACE)
        {
            $categoryId = Memjetoptimization_Model_Device_Swap_Reason_Category::FLAGGED;
        }

        return $categoryId;
    }

    /*****************************************************
     ***************Device Calculations*******************
     *****************************************************/
    /**
     * Calculates the cost per page for a master device.
     *
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *            The settings to use when calculating cost per page
     * @param Proposalgen_Model_MasterDevice       $masterDevice
     *            The master device to use
     *
     * @throws InvalidArgumentException
     * @return Proposalgen_Model_DeviceCostPerPage
     */
    public function calculateCostPerPage ($costPerPageSetting, $masterDevice = null)
    {
        /**
         * Caching Array
         */
        if (!isset($this->_cachedDeviceCostPerPage))
        {
            $this->_cachedDeviceCostPerPage = array();
        }

        // If master device isn't passed, get the master device
        if (!$masterDevice instanceof Proposalgen_Model_MasterDevice)
        {
            $masterDevice = $this->getMasterDevice();
        }

        $masterDeviceId = ($masterDevice instanceof Proposalgen_Model_MasterDevice) ? (int)$masterDevice->id : 0;

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
            if ($masterDevice instanceof Proposalgen_Model_MasterDevice)
            {
                $deviceCostPerPage = $masterDevice->calculateCostPerPage($costPerPageSetting, $this->isManaged);
            }
            else
            {
                // Create fake instance
                $deviceCostPerPage            = new Proposalgen_Model_DeviceCostPerPage(array(), $costPerPageSetting);
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
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *            The settings to use when calculating cost per page
     * @param Proposalgen_Model_MasterDevice       $masterDevice
     *            The master device to use
     * @param int                                  $blackToColorRatio
     *
     * @return number
     */
    public function calculateMonthlyCost (Proposalgen_Model_CostPerPageSetting $costPerPageSetting, $masterDevice = null, $blackToColorRatio = null)
    {
        return $this->calculateMonthlyMonoCost($costPerPageSetting, $masterDevice, $blackToColorRatio) + $this->calculateMonthlyColorCost($costPerPageSetting, $masterDevice, $blackToColorRatio);
    }

    /**
     * Calculates the monthly cost for monochrome printing
     *
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *            The setting used to calculate cost per page
     * @param Proposalgen_Model_MasterDevice       $masterDevice
     *            the master device to us
     *
     * @param int                                  $blackToColorRatio
     *
     * @return number
     */
    public function calculateMonthlyMonoCost (Proposalgen_Model_CostPerPageSetting $costPerPageSetting, $masterDevice = null, $blackToColorRatio = null)
    {
        $monoCostPerPage = $this->calculateCostPerPage($costPerPageSetting, $masterDevice)->getCostPerPage()->monochromeCostPerPage;
        if ($blackToColorRatio != null)
        {
            return $monoCostPerPage * $this->getPageCounts($blackToColorRatio)->getBlackPageCount()->getMonthly();
        }

        return $monoCostPerPage * $this->getPageCounts()->getBlackPageCount()->getMonthly();
    }

    /**
     * Calculates the monthly cost for color printing
     *
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *            the setting used to calculate cost per page
     * @param Proposalgen_Model_MasterDevice       $masterDevice
     *            the master device to use, or null for current instance of device
     *
     * @param int                                  $blackToColorRatio
     *
     * @return number
     */
    public function calculateMonthlyColorCost (Proposalgen_Model_CostPerPageSetting $costPerPageSetting, $masterDevice = null, $blackToColorRatio = null)
    {
        $colorCostPerPage = $this->calculateCostPerPage($costPerPageSetting, $masterDevice)->getCostPerPage()->colorCostPerPage;
        if ($blackToColorRatio != null)
        {
            return $colorCostPerPage * $this->getPageCounts($blackToColorRatio)->getColorPageCount()->getMonthly();
        }

        return $colorCostPerPage * $this->getPageCounts()->getColorPageCount()->getMonthly();
    }

    /**
     * Returns percent of maximum recommended print volume they are printing.
     * If their recommended max is 1000, and they print 2000. This returns 200 (Without % Sign)
     *
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
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
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *            The settings to use when calculating cost per page
     *
     * @param  int                                 $hardwareOptimizationId
     *
     * @return Proposalgen_Model_CostPerPage
     */
    public function calculateCostPerPageWithReplacement (Proposalgen_Model_CostPerPageSetting $costPerPageSetting, $hardwareOptimizationId)
    {
        return $this->calculateCostPerPage($costPerPageSetting, $this->getReplacementMasterDeviceForHardwareOptimization($hardwareOptimizationId))->getCostPerPage();
    }

    /**
     * Calculates a cost per page for a Memjet replacement device
     *
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *            The settings to use when calculating cost per page
     *
     * @param                                      $memjetOptimizationId
     *
     * @return Proposalgen_Model_CostPerPage
     */
    public function calculateMemjetCostPerPageWithReplacement (Proposalgen_Model_CostPerPageSetting $costPerPageSetting, $memjetOptimizationId)
    {
        return $this->calculateCostPerPage($costPerPageSetting, $this->getReplacementMasterDeviceForMemjetOptimization($memjetOptimizationId))->getCostPerPage();
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
        $masterDevice        = $this->getMasterDevice();
        $masterDeviceService = new Proposalgen_Service_ManageMasterDevices($masterDevice->id, $dealerId);
        $first               = true;
        $tonersList          = "";

        foreach ($masterDevice->getToners($dealerId, $clientId) as $manufacturerIdList)
        {
            foreach ($manufacturerIdList as $tonerColorIdList)
            {
                foreach ($tonerColorIdList as $toner)
                {
                    if (!$first)
                    {
                        $tonersList .= ",";
                    }
                    else
                    {
                        $first = false;
                    }

                    $tonersList .= $toner->id;
                }
            }
        }

        if ($masterDeviceService->validateToners($tonersList, $masterDevice->getTonerConfig()->tonerConfigId, $masterDevice->manufacturerId, false) == null)
        {
            return 1;
        }

        return 0;
    }
}