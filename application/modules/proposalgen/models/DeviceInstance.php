<?php
class Proposalgen_Model_DeviceInstance extends My_Model_Abstract
{
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
    public $reportsTonerLevels;

    /**
     * @var int
     */
    public $serialNumber;

    /**
     * @var int
     */
    public $useUserData;


    /*
     * ********************************************************************************
     * Related Objects
     * ********************************************************************************
     */
    /**
     * An array of all the meters
     *
     * @var Proposalgen_Model_DeviceInstanceMeter[]
     */
    protected $_meters;

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
     * @var int
     */
    protected $_averageDailyBlackAndWhitePageCount;

    /**
     * @var int
     */
    protected $_averageDailyColorPageCount;

    /**
     * @var int
     */
    protected $_averageDailyPageCount;

    /**
     * @var int
     */
    protected $_averageMonthlyBlackAndWhitePageCount;

    /**
     * @var int
     */
    protected $_averageMonthlyColorPageCount;

    /**
     * @var int
     */
    protected $_averageMonthlyPageCount;

    /**
     * @var int
     */
    protected $_averageYearlyBlackAndWhitePageCount;

    /**
     * @var int
     */
    protected $_averageYearlyColorPageCount;

    /**
     * @var int
     */
    protected $_averageYearlyPageCount;

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
    protected $_grossMarginMonthlyBlackAndWhiteCost;

    /**
     * @var float
     */
    protected $_grossMarginMonthlyColorCost;

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
     * @var string
     */
    public $_exclusionReason;

    /**
     * @var bool
     */
    public $isUnknown = false;

    /**
     * @var Proposalgen_Model_Toner[]
     */
    static $uniqueTonerArray = array();


    /**
     * Applies overrides to settings, device prices, toner prices
     *
     * @param Proposalgen_Model_Assessment $report
     */
    public function processOverrides ($report)
    {
        /**
         * Process any overrides we have on a master device
         */
        $masterDevice = $this->getMasterDevice();
        if ($masterDevice instanceof Proposalgen_Model_MasterDevice)
        {
            $this->getMasterDevice()->processOverrides($report);
        }

        /*
         * Process any overrides on the replacement master device
         */
        if ($this->getIsMappedToMasterDevice() || $this->useUserData)
        {
            $replacementMasterDevice = $this->getReplacementMasterDevice();
            if ($replacementMasterDevice instanceof Proposalgen_Model_MasterDevice)
            {
                $replacementMasterDevice->processOverrides($report);
            }
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

        if (isset($params->mpsDiscoveryDate) && !is_null($params->mpsDiscoveryDate))
        {
            $this->mpsDiscoveryDate = $params->mpsDiscoveryDate;
        }

        if (isset($params->reportsTonerLevels) && !is_null($params->reportsTonerLevels))
        {
            $this->reportsTonerLevels = $params->reportsTonerLevels;
        }

        if (isset($params->serialNumber) && !is_null($params->serialNumber))
        {
            $this->serialNumber = $params->serialNumber;
        }

        if (isset($params->useUserData) && !is_null($params->useUserData))
        {
            $this->useUserData = $params->useUserData;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                 => $this->id,
            "rmsUploadId"        => $this->rmsUploadId,
            "rmsUploadRowId"     => $this->rmsUploadRowId,
            "ipAddress"          => $this->ipAddress,
            "isExcluded"         => $this->isExcluded,
            "mpsDiscoveryDate"   => $this->mpsDiscoveryDate,
            "reportsTonerLevels" => $this->reportsTonerLevels,
            "serialNumber"       => $this->serialNumber,
            "useUserData"        => $this->useUserData,
        );
    }

    /**
     * @return float
     */
    public function getAverageDailyPageCount ()
    {
        if (!isset($this->_averageDailyPageCount))
        {
            $this->_averageDailyPageCount = $this->getAverageDailyBlackAndWhitePageCount() + $this->getAverageDailyColorPageCount();
        }

        return $this->_averageDailyPageCount;
    }

    /**
     * Gets the average monthly page count
     *
     * @return float
     */
    public function getAverageMonthlyPageCount ()
    {
        if (!isset($this->_averageMonthlyPageCount))
        {
            $this->_averageMonthlyPageCount = $this->getAverageMonthlyBlackAndWhitePageCount() + $this->getAverageMonthlyColorPageCount();
        }

        return $this->_averageMonthlyPageCount;
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
     * @return float $AverageDailyPowerConsumption in KWH
     */
    public function getAverageDailyPowerConsumption ()
    {
        if (!isset($this->_averageDailyPowerConsumption))
        {
            $powerUsage   = 0;
            $runningHours = 0;

            foreach (self::$RUNNING_HOUR_ARRAY as $pages => $runningHours)
            {
                if ($this->getAverageDailyPageCount() >= $pages)
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
     * @return int
     */
    public function getLifePageCount ()
    {
        if (!isset($this->lifePageCount))
        {
            $lifeCount = 0;
            $lifeCount += $this->getLifeBlackAndWhitePageCount();
            $lifeCount += $this->getLifeColorPageCount();
            $this->lifePageCount = $lifeCount;
        }

        return $this->lifePageCount;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getLifeBlackAndWhitePageCount ()
    {
        if (!isset($this->_lifeBlackAndWhitePageCount))
        {
            $meters = $this->getMeters();
            if (!isset($meters [Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_BLACK]))
            {
                // if no life or color then throw exception
                throw new Exception("Device does not have a BLACK meter! " . $this->id);
            }

            $pageCount = $meters [Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_BLACK]->endMeter;

            $this->_lifeBlackAndWhitePageCount = $pageCount;
        }

        return $this->_lifeBlackAndWhitePageCount;
    }

    /**
     * @return int
     */
    public function getLifeColorPageCount ()
    {
        if (!isset($this->_lifeColorPageCount))
        {
            $meters    = $this->getMeters();
            $pageCount = 0;
            if (isset($meters [Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_COLOR]))
            {
                $pageCount = $meters [Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_COLOR]->endMeter;
            }
            $this->_lifeColorPageCount = $pageCount;
        }

        return $this->_lifeColorPageCount;
    }

    /**
     * @return float
     * @throws Exception
     */
    public function getAverageDailyBlackAndWhitePageCount ()
    {
        if (!isset($this->_averageDailyBlackAndWhitePageCount))
        {
            $meters = $this->getMeters();
            if (!isset($meters [Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_BLACK]))
            {
                // If we do not have a black meter, throw an exception
                throw new Exception("Device does not have a BLACK meter! " . $this->id);
            }

            /* @var $meter Proposalgen_Model_DeviceInstanceMeter */
            $meter                                     = $meters [Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_BLACK];
            $this->_averageDailyBlackAndWhitePageCount = $meter->calculateAverageDailyPageVolume();
        }

        return $this->_averageDailyBlackAndWhitePageCount;
    }

    /**
     * @return float
     */
    public function getAverageMonthlyBlackAndWhitePageCount ()
    {
        if (!isset($this->_averageMonthlyBlackAndWhitePageCount))
        {
            $this->_averageMonthlyBlackAndWhitePageCount = $this->getAverageDailyBlackAndWhitePageCount() * 30;
        }

        return $this->_averageMonthlyBlackAndWhitePageCount;
    }

    /**
     * @return float
     */
    public function getAverageDailyColorPageCount ()
    {
        if (!isset($this->_averageDailyColorPageCount))
        {
            $meters       = $this->getMeters();
            $avgPageCount = 0.0;

            if (isset($meters [Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_COLOR]) && $this->getMasterDevice()->tonerConfigId !== Proposalgen_Model_TonerConfig::BLACK_ONLY)
            {
                /* @var $meter Proposalgen_Model_DeviceInstanceMeter */
                $meter        = $meters [Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_COLOR];
                $avgPageCount = $meter->calculateAverageDailyPageVolume();

            }
            $this->_averageDailyColorPageCount = $avgPageCount;
        }

        return $this->_averageDailyColorPageCount;
    }

    /**
     * @return float
     */
    public function getAverageMonthlyColorPageCount ()
    {
        if (!isset($this->_averageMonthlyColorPageCount))
        {
            $this->_averageMonthlyColorPageCount = $this->getAverageDailyColorPageCount() * 30;
        }

        return $this->_averageMonthlyColorPageCount;
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
     * @return Proposalgen_Model_DeviceInstanceMeter[]
     */
    public function getMeters ()
    {
        if (!isset($this->_meters))
        {
            $meters = Proposalgen_Model_Mapper_DeviceInstanceMeter::getInstance()->fetchAllForDeviceInstance($this->id);

            // If we do not have a BLACK meter, then we should try and calculate
            // it
            if (!isset($meters [Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_BLACK]))
            {
                /**
                 * BLACK METER Calculation:
                 * StartMeterBLACK = StartMeterLife - StartMeterCOLOR
                 * EndMeterBLACK = EndMeterLIFE - EndMeterCOLOR
                 *
                 * To calculate the BLACK METER we need to have a LIFE meter AND
                 * a COLOR Meter
                 */
                if (isset($meters [Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_LIFE]) && isset($meters [Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_COLOR]))
                {
                    $startMeter                                                       = $meters [Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_LIFE]->startMeter - $meters [Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_COLOR]->startMeter;
                    $endMeter                                                         = $meters [Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_LIFE]->endMeter - $meters [Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_COLOR]->endMeter;
                    $newBlackMeter                                                    = new Proposalgen_Model_DeviceInstanceMeter();
                    $newBlackMeter->startMeter                                        = $startMeter;
                    $newBlackMeter->endMeter                                          = $endMeter;
                    $newBlackMeter->meterType                                         = Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_BLACK;
                    $newBlackMeter->deviceInstanceId                                  = $this->id;
                    $newBlackMeter->generatedBySystem                                 = true;
                    $meters [Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_BLACK] = $newBlackMeter;
                }
            }
            $this->_meters = $meters;
        }

        return $this->_meters;
    }

    /**
     * @param Proposalgen_Model_DeviceInstanceMeter[] $Meters
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
     * @return float
     */
    public function getCostOfInkAndToner ()
    {
        if (!isset($this->_costOfInkAndToner))
        {
            $this->_costOfInkAndToner = $this->getCostOfBlackAndWhiteInkAndToner() + $this->getCostOfColorInkAndToner();
        }

        return $this->_costOfInkAndToner;
    }

    /**
     * @return float
     */
    public function getCostOfBlackAndWhiteInkAndToner ()
    {
        if (!isset($this->_costOfBlackAndWhiteInkAndToner))
        {
            $this->_costOfBlackAndWhiteInkAndToner = $this->getMasterDevice()->getCostPerPage()->Estimated->BasePlusMargin->BlackAndWhite * $this->getAverageMonthlyBlackAndWhitePageCount();
        }

        return $this->_costOfBlackAndWhiteInkAndToner;
    }

    /**
     * @return float
     */
    public function getCostOfColorInkAndToner ()
    {
        if (!isset($this->_costOfColorInkAndToner))
        {
            $this->_costOfColorInkAndToner = $this->getMasterDevice()->getCostPerPage()->Estimated->BasePlusMargin->Color * $this->getAverageMonthlyColorPageCount();
        }

        return $this->_costOfColorInkAndToner;
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
     * @return float
     */
    public function getUsage ()
    {
        if (!isset($this->_usage))
        {
            // Calculate device usage by dividing it's current monthly volume by its maximum
            if ($this->getMasterDevice()->getMaximumMonthlyPageVolume() > 0)
            {
                $this->_usage = $this->getAverageMonthlyPageCount() / $this->getMasterDevice()->getMaximumMonthlyPageVolume();
            }
        }

        return $this->_usage;
    }

    /**
     * @return float
     */
    public function getLifeUsage ()
    {
        if (!isset($this->_lifeUsage))
        {
            // Calculate device life usage by dividing it's current life count
            // by it's estimated max life page count (maximum monthly page
            // volume * 36 months)
            $maximumLifeCount = $this->getMasterDevice()->getMaximumMonthlyPageVolume() * 36;
            if ($maximumLifeCount > 0)
            {
                $this->_lifeUsage = $this->getLifePageCount() / $maximumLifeCount;
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
     * @return float
     */
    public function getAverageYearlyBlackAndWhitePageCount ()
    {
        if (!isset($this->_averageYearlyBlackAndWhitePageCount))
        {
            $this->_averageYearlyBlackAndWhitePageCount = $this->getAverageMonthlyBlackAndWhitePageCount() * 12;
        }

        return $this->_averageYearlyBlackAndWhitePageCount;
    }

    /**
     * @return float
     */
    public function getAverageYearlyColorPageCount ()
    {
        if (!isset($this->_averageYearlyColorPageCount))
        {
            $this->_averageYearlyColorPageCount = $this->getAverageMonthlyColorPageCount() * 12;
        }

        return $this->_averageYearlyColorPageCount;
    }

    /**
     * @return float
     */
    public function getAverageYearlyPageCount ()
    {
        if (!isset($this->_averageYearlyPageCount))
        {
            $this->_averageYearlyPageCount = $this->getAverageYearlyBlackAndWhitePageCount() + $this->getAverageYearlyColorPageCount();
        }

        return $this->_averageYearlyPageCount;
    }

    /**
     * @return Proposalgen_Model_ReplacementDevice
     */
    public function getReplacementDevice ()
    {
        if (!isset($this->_replacementDevice))
        {

            $this->_replacementDevice = null;
        }

        return $this->_replacementDevice;
    }

    /**
     * @param Proposalgen_Model_ReplacementDevice $ReplacementDevice
     *
     * @return Proposalgen_Model_DeviceInstance
     */
    public function setReplacementDevice ($ReplacementDevice)
    {
        $this->_replacementDevice = $ReplacementDevice;

        return $this;
    }

    /**
     * @return float
     */
    public function getGrossMarginMonthlyBlackAndWhiteCost ()
    {
        if (!isset($this->_grossMarginMonthlyBlackAndWhiteCost))
        {
            $this->_grossMarginMonthlyBlackAndWhiteCost = ($this->getMasterDevice()->getCostPerPage()->Actual->BasePlusService->BlackAndWhite * $this->getAverageMonthlyBlackAndWhitePageCount());
        }

        return $this->_grossMarginMonthlyBlackAndWhiteCost;
    }

    /**
     * @return float
     */
    public function getGrossMarginMonthlyColorCost ()
    {
        if (!isset($this->_grossMarginMonthlyColorCost))
        {
            $this->_grossMarginMonthlyColorCost = ($this->getMasterDevice()->getCostPerPage()->Actual->BasePlusService->Color * $this->getAverageMonthlyColorPageCount());
        }

        return $this->_grossMarginMonthlyColorCost;
    }

    /**
     * Calculates the cost of the device on a monthly basis to compare with
     * replacement devices
     *
     * @return float
     */
    public function getMonthlyRate ()
    {
        if (!isset($this->_monthlyRate))
        {
            $this->_monthlyRate = 0;
            $this->_monthlyRate += ($this->getMasterDevice()->getCostPerPage()->Estimated->BasePlusMargin->BlackAndWhite * $this->getAverageMonthlyBlackAndWhitePageCount());
            $this->_monthlyRate += ($this->getMasterDevice()->getCostPerPage()->Estimated->BasePlusMargin->Color * $this->getAverageMonthlyColorPageCount());
            $this->_monthlyRate += ($this->getAverageMonthlyPageCount() * self::getITCostPerPage());
        }

        return $this->_monthlyRate;
    }

    /**
     * @param $monthlyTotalCost
     *
     * @return float
     */
    public function getMonthlyRatePercentage ($monthlyTotalCost)
    {
        return ($this->getMonthlyRate() / $monthlyTotalCost) * 100;
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
        return $monthlyLeasePayment + ($monochromeCostPerPage * $this->getAverageMonthlyBlackAndWhitePageCount()) + ($colorCostPerPage * $this->getAverageMonthlyColorPageCount());
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
                $masterDeviceMapper         = Proposalgen_Model_Mapper_MasterDevice::getInstance();
                $deviceInstanceMasterDevice = $this->getDeviceInstanceMasterDevice();
                $this->_masterDevice        = $masterDeviceMapper->find($deviceInstanceMasterDevice->masterDeviceId);
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
     * Gets the rms upload row
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
     * Sets the rms upload row
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
     * Calculates the cost per page for a master device.
     *
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *            The settings to use when calculating cost per page
     * @param Proposalgen_Model_MasterDevice       $masterDevice
     *            The master device to use
     *
     * @throws InvalidArgumentException
     * @return Proposalgen_Model_CostPerPage
     */
    public function calculateCostPerPage (Proposalgen_Model_CostPerPageSetting $costPerPageSetting, $masterDevice = null)
    {
        // Make sure our array is initialized
        if (!isset($this->_cachedCostPerPage))
        {
            $this->_cachedCostPerPage = array();
        }

        // If master device isn't passed, get the master device
        if (!$masterDevice instanceof Proposalgen_Model_MasterDevice)
        {
            $masterDevice = $this->getMasterDevice();
        }

        $cacheKey = $costPerPageSetting->createCacheKey() . "_" . (int)$masterDevice->id;

        if (!array_key_exists($cacheKey, $this->_cachedCostPerPage))
        {
            $costPerPage = new Proposalgen_Model_CostPerPage();

            // DO MATH
            if ($masterDevice instanceof Proposalgen_Model_MasterDevice)
            {

                $costPerPage->add($masterDevice->calculateCostPerPage($costPerPageSetting));

                $costPerPage->monochromeCostPerPage = $costPerPage->monochromeCostPerPage + $masterDevice->getDealerAttributes()->laborCostPerPage + $masterDevice->getDealerAttributes()->partsCostPerPage + $costPerPageSetting->adminCostPerPage;
                if ($masterDevice->isColor())
                {
                    $costPerPage->colorCostPerPage = $costPerPage->monochromeCostPerPage + $costPerPage->colorCostPerPage;
                }
            }

            $this->_cachedCostPerPage [$cacheKey] = $costPerPage;
        }

        return $this->_cachedCostPerPage [$cacheKey];
    }

    /**
     * Gets whether or not the device is leased
     *
     * @return bool
     */
    public function getIsLeased ()
    {
        if ($this->getIsMappedToMasterDevice())
        {
            $isLeased = $this->getMasterDevice()->isLeased;
        }
        else
        {
            $isLeased = $this->getRmsUploadRow()->isLeased;
        }

        return $isLeased;
    }


    /**
     * Figures out if the device can report toner levels
     *
     * @return bool|int
     */
    public function isCapableOfReportingTonerLevels ()
    {
        // Always use the master device record. This way administrators can control which devices can and can not report toner levels.
        $reportsTonerLevels = $this->getMasterDevice()->reportsTonerLevels;

        return $reportsTonerLevels;
    }

    /**
     * Calculates the monthly cost for this instance
     *
     * @param Application_Model_CostPerPageSetting $costPerPageSetting
     *            The settings to use when calculating cost per page
     * @param Application_Model_MasterDevice       $masterDevice
     *            The master device to use
     *
     * @return number
     */
    public function calculateMonthlyCost (Proposalgen_Model_CostPerPageSetting $costPerPageSetting, $masterDevice = null)
    {
        return $this->calculateMonthlyMonoCost($costPerPageSetting, $masterDevice) + $this->calculateMonthlyColorCost($costPerPageSetting, $masterDevice);
    }

    /**
     * Calculates the monthly cost for monochrome printing
     *
     * @param Application_Model_CostPerPageSetting $costPerPageSetting
     *            The setting used to calculate cost per page
     * @param Application_Model_MasterDevice       $masterDevice
     *            the master device to us
     *
     * @return number
     */
    public function calculateMonthlyMonoCost (Proposalgen_Model_CostPerPageSetting $costPerPageSetting, $masterDevice = null)
    {
        $monoCostPerPage = $this->calculateCostPerPage($costPerPageSetting, $masterDevice)->monochromeCostPerPage;

        return $monoCostPerPage * $this->getAverageMonthlyBlackAndWhitePageCount();
    }

    /**
     * Calculates the monthly cost for color printing
     *
     * @param Application_Model_CostPerPageSetting $costPerPageSetting
     *            the setting used to calculate cost per page
     * @param Application_Model_MasterDevice       $masterDevice
     *            the master device to use, or null for current instance of device
     *
     * @return number
     */
    public function calculateMonthlyColorCost (Proposalgen_Model_CostPerPageSetting $costPerPageSetting, $masterDevice = null)
    {
        $colorCostPerPage = $this->calculateCostPerPage($costPerPageSetting, $masterDevice)->colorCostPerPage;

        return $colorCostPerPage * $this->getAverageMonthlyColorPageCount();
    }

    /**
     * Returns percent of maximum recommended print volume they are printing.
     * If their recommended max is 1000, and they print 2000. This returns 200 (Without % Sign)
     *
     * @return float
     */
    public function calculatePercentOfMaximumRecommendedMaxVolume ()
    {
        $percent = 0;
        if ($this->getMasterDevice()->getMaximumMonthlyPageVolume() > 0)
        {
            $percent = ($this->getAverageMonthlyPageCount() / $this->getMasterDevice()->getMaximumMonthlyPageVolume() * 100);
        }

        return $percent;
    }
}