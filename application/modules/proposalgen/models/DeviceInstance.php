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
    public $reportId;

    /**
     * @var int
     */
    public $masterDeviceId;

    /**
     * @var int
     */
    public $uploadDataCollectorId;

    /**
     * @var string
     */
    public $serialNumber;

    /**
     * @var string
     */
    public $mpsMonitorStartDate;

    /**
     * @var string
     */
    public $mpsMonitorEndDate;

    /**
     * @var string
     */
    public $mpsDiscoveryDate;

    /**
     * @var bool
     */
    public $isExcluded;


    /**
     * @var string
     */
    public $ipAddress;

    /**
     * @var bool
     */
    public $jitSuppliesSupported;


    /*
     * ********************************************************************************
     * Related Objects
     * ********************************************************************************
     */
    /**
     * An array of all the meters
     *
     * @var Proposalgen_Model_Meter[]
     */
    protected $_meters;

    /**
     * @var Proposalgen_Model_MasterDevice
     */
    protected $_masterDevice;

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
     * @var Proposalgen_Model_UploadDataCollectorRow
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
     * Applies overrides to device costs and toner costs.
     * Also adds a margin on the costs that were not overridden
     *
     * @param Proposalgen_Model_DeviceInstance $device
     * @param Proposalgen_Model_Report         $report
     * @param float                            $reportMargin
     * @param float                            $companyMargin
     */
    static function processOverrides ($device, $report, $reportMargin, $companyMargin)
    {
//        $dealerDeviceOverrideMapper = Proposalgen_Model_Mapper_DealerDeviceOverride::getInstance();
//        $dealerTonerOverrideMapper  = Proposalgen_Model_Mapper_DealerTonerOverride::getInstance();
        $userDeviceOverrideMapper = Proposalgen_Model_Mapper_UserDeviceOverride::getInstance();
        $userTonerOverrideMapper  = Proposalgen_Model_Mapper_UserTonerOverride::getInstance();
        $deviceOverride           = null;

        // Known Device, override
        if (!$device->isUnknown)
        {
//            // Dealer
//            $deviceOverride = $dealerDeviceOverrideMapper->fetchRow(array (
//                    "master_device_id = ?" => $device->getMasterDeviceId(),
//                    "dealer_company_id = ?" => Proposalgen_Model_DealerCompany::getCurrentUserCompany()->getDealerCompanyId()
//            ));
//            // If no dealer, check user
//            if (! $deviceOverride)
//            {
            $deviceOverride = $userDeviceOverrideMapper->fetchRow(array(
                                                                       "master_device_id = ?" => $device->masterDeviceId,
                                                                       "user_id = ?"          => Proposalgen_Model_User::getCurrentUserId()
                                                                  ));
//            }
        }

        // Apply Company Margin if no overrides
        if ($deviceOverride)
        {
            // Cost
            $device->getMasterDevice()->setCost($deviceOverride->OverrideDevicePrice);
//            $device->getMasterDevice()->setDevicePrice($deviceOverride->OverrideDevicePrice);
        }
        else // If we found a device override, apply it
        {
            $device->getMasterDevice()->setCost($device->getMasterDevice()->getCost() / $companyMargin);
        }

        // Apply Report Margin to the device price
        $device->getMasterDevice()->setCost($device->getMasterDevice()->getCost() / $reportMargin);

        // Toner Overrides + Margin
        foreach ($device->getMasterDevice()->getToners() as $tonersByPartType)
        {
            foreach ($tonersByPartType as $tonersByColor)
            {
                /* @var $toner Proposalgen_Model_Toner */
                foreach ($tonersByColor as $toner)
                {
                    if (!in_array($toner->TonerSKU, self::$uniqueTonerArray))
                    {
                        self::$uniqueTonerArray [] = $toner->TonerSKU;
                        $tonerOverride             = null;
                        // Known Device, override
                        if (!$device->isUnknown)
                        {
                            // Toner Overrides
//                            $tonerOverride = $dealerTonerOverrideMapper->fetchRow(array (
//                                    "toner_id = ?" => $toner->getTonerId(),
//                                    "dealer_company_id = ?" => Proposalgen_Model_DealerCompany::getCurrentUserCompany()->getDealerCompanyId()
//                            ));
//                            if (!$tonerOverride)
//                            {
                            /* @var $tonerOverride Proposalgen_Model_UserTonerOverride */
                            $tonerOverride = $userTonerOverrideMapper->fetchRow(array(
                                                                                     "toner_id = ?" => $toner->id,
                                                                                     "user_id = ?"  => Proposalgen_Model_User::getCurrentUserId()
                                                                                ));
//                            }
                        }

                        // If we found a toner override, apply it
                        if ($tonerOverride)
                        {
                            $toner->cost = $tonerOverride->overrideTonerPrice;
                        }
                        else // Apply Company Margin if no overrides
                        {
                            $toner->cost = $toner->cost / $companyMargin;
                        }
                    }
                } // endforeach
            } // endforeach
        } // endforeach

        // Service Cost Per Page Cost
        if ($device->getMasterDevice()->serviceCostPerPage <= 0)
        {
            $device->getMasterDevice()->serviceCostPerPage = $report->getReportSettings()->serviceCostPerPage;
        }

        // Admin Charge
        $device->getMasterDevice()->adminCostPerPage = $report->getReportSettings()->adminCostPerPage;
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

        if (isset($params->deviceInstanceId) && !is_null($params->deviceInstanceId))
        {
            $this->deviceInstanceId = $params->deviceInstanceId;
        }

        if (isset($params->reportId) && !is_null($params->reportId))
        {
            $this->reportId = $params->reportId;
        }

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->uploadDataCollectorId) && !is_null($params->uploadDataCollectorId))
        {
            $this->uploadDataCollectorId = $params->uploadDataCollectorId;
        }

        if (isset($params->serialNumber) && !is_null($params->serialNumber))
        {
            $this->serialNumber = $params->serialNumber;
        }

        if (isset($params->mpsMonitorStartDate) && !is_null($params->mpsMonitorStartDate))
        {
            $this->mpsMonitorStartDate = $params->mpsMonitorStartDate;
        }

        if (isset($params->mpsMonitorEndDate) && !is_null($params->mpsMonitorEndDate))
        {
            $this->mpsMonitorEndDate = $params->mpsMonitorEndDate;
        }

        if (isset($params->mpsDiscoveryDate) && !is_null($params->mpsDiscoveryDate))
        {
            $this->mpsDiscoveryDate = $params->mpsDiscoveryDate;
        }

        if (isset($params->isExcluded) && !is_null($params->isExcluded))
        {
            $this->isExcluded = $params->isExcluded;
        }

        if (isset($params->ipAddress) && !is_null($params->ipAddress))
        {
            $this->ipAddress = $params->ipAddress;
        }

        if (isset($params->jitSuppliesSupported) && !is_null($params->jitSuppliesSupported))
        {
            $this->jitSuppliesSupported = $params->jitSuppliesSupported;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "deviceInstanceId"      => $this->deviceInstanceId,
            "reportId"              => $this->reportId,
            "masterDeviceId"        => $this->masterDeviceId,
            "uploadDataCollectorId" => $this->uploadDataCollectorId,
            "serialNumber"          => $this->serialNumber,
            "mpsMonitorStartDate"   => $this->mpsMonitorStartDate,
            "mpsMonitorEndDate"     => $this->mpsMonitorEndDate,
            "mpsDiscoveryDate"      => $this->mpsDiscoveryDate,
            "isExcluded"            => $this->isExcluded,
            "ipAddress"             => $this->ipAddress,
            "jitSuppliesSupported"  => $this->jitSuppliesSupported,
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
            $powerUsage += $idleHours * $this->getMasterDevice()->WattsPowerIdle;
            $powerUsage += $runningHours * $this->getMasterDevice()->WattsPowerNormal;
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
            if (!isset($meters [Proposalgen_Model_Meter::METER_TYPE_BLACK]))
            {
                // if no life or color then throw exception
                throw new Exception("Device does not have a BLACK meter! " . $this->id);
            }

            $pageCount = $meters [Proposalgen_Model_Meter::METER_TYPE_BLACK]->endMeter;

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
            if (isset($meters [Proposalgen_Model_Meter::METER_TYPE_COLOR]))
            {
                $pageCount = $meters [Proposalgen_Model_Meter::METER_TYPE_COLOR]->endMeter;
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
            if (!isset($meters [Proposalgen_Model_Meter::METER_TYPE_BLACK]))
            {
                // If we do not have a black meter, throw an exception
                throw new Exception("Device does not have a BLACK meter! " . $this->id);
            }

            $startMeter = $meters [Proposalgen_Model_Meter::METER_TYPE_BLACK]->startMeter;
            $endMeter   = $meters [Proposalgen_Model_Meter::METER_TYPE_BLACK]->endMeter;
            $pageCount  = $endMeter - $startMeter;

            if ($this->getMpsMonitorInterval()->days > 0)
            {
                $this->_averageDailyBlackAndWhitePageCount = $pageCount / $this->getMpsMonitorInterval()->days;
            }
            else
            {
                $this->_averageDailyBlackAndWhitePageCount = 0;
            }
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
            $avgPageCount = 0;

            if (!isset($this->_masterDevice))
            {
                $this->_masterDevice = $this->getMasterDevice();
            }

            if (isset($meters [Proposalgen_Model_Meter::METER_TYPE_COLOR]) && $this->getMasterDevice()->tonerConfigId !== Proposalgen_Model_TonerConfig::BLACK_ONLY)
            {
                $startMeter = $meters [Proposalgen_Model_Meter::METER_TYPE_COLOR]->startMeter;
                $endMeter   = $meters [Proposalgen_Model_Meter::METER_TYPE_COLOR]->endMeter;
                $pageCount  = $endMeter - $startMeter;

                if ($this->getMpsMonitorInterval()->days > 0)
                {
                    $avgPageCount = $pageCount / $this->getMpsMonitorInterval()->days;
                }
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
     * @return DateInterval
     */
    public function getMpsMonitorInterval ()
    {
        if (!isset($this->_mpsMonitorInterval))
        {
            $startDate     = new DateTime($this->mpsMonitorStartDate);
            $endDate       = new DateTime($this->mpsMonitorEndDate);
            $discoveryDate = new DateTime($this->mpsDiscoveryDate);
            $interval1     = $startDate->diff($endDate);
            $interval2     = $discoveryDate->diff($endDate);

            if (!$interval1->invert)
            {
                $this->_mpsMonitorInterval = $interval1;
                if ($interval1->days > $interval2->days && !$interval2->invert)
                {
                    $this->_mpsMonitorInterval = $interval2;
                }
            }
            else if (!$interval2->invert)
            {
                $this->_mpsMonitorInterval = $interval2;
            }
            else
            {
                trigger_error("Device was discovered on the after or on the monitor end date.");
            }
        }

        return $this->_mpsMonitorInterval;
    }

    /**
     * @return float
     */
    public function getAge ()
    {
        if (!isset($this->_age))
        {
            // Get the time difference in seconds
            $launchDate          = time() - strtotime($this->getMasterDevice()->getLaunchDate());
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
     * @return Proposalgen_Model_Meter[]
     */
    public function getMeters ()
    {
        if (!isset($this->_meters))
        {
            $meterMapper = Proposalgen_Model_Mapper_Meter::getInstance();
            $meters      = $meterMapper->fetchAllForDevice($this->id);

            // If we do not have a BLACK meter, then we should try and calculate
            // it
            if (!isset($meters [Proposalgen_Model_Meter::METER_TYPE_BLACK]))
            {
                /**
                 * BLACK METER Calculation:
                 * StartMeterBLACK = StartMeterLife - StartMeterCOLOR
                 * EndMeterBLACK = EndMeterLIFE - EndMeterCOLOR
                 *
                 * To calculate the BLACK METER we need to have a LIFE meter AND
                 * a COLOR Meter
                 */
                if (isset($meters [Proposalgen_Model_Meter::METER_TYPE_LIFE]) && isset($meters [Proposalgen_Model_Meter::METER_TYPE_COLOR]))
                {
                    $startMeter                                         = $meters [Proposalgen_Model_Meter::METER_TYPE_LIFE]->startMeter - $meters [Proposalgen_Model_Meter::METER_TYPE_COLOR]->startMeter;
                    $endMeter                                           = $meters [Proposalgen_Model_Meter::METER_TYPE_LIFE]->endMeter - $meters [Proposalgen_Model_Meter::METER_TYPE_COLOR]->endMeter;
                    $newBlackMeter                                      = new Proposalgen_Model_Meter();
                    $newBlackMeter->startMeter                          = $startMeter;
                    $newBlackMeter->endMeter                            = $endMeter;
                    $newBlackMeter->meterType                           = Proposalgen_Model_Meter::METER_TYPE_BLACK;
                    $newBlackMeter->deviceInstanceId                    = $this->id;
                    $newBlackMeter->generatedBySystem                   = true;
                    $meters [Proposalgen_Model_Meter::METER_TYPE_BLACK] = $newBlackMeter;
                }
            }
            $this->_meters = $meters;
        }

        return $this->_meters;
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
            $this->_deviceName = $this->getMasterDevice()->getManufacturer()->fullname . " " . $this->getMasterDevice()->getPrinterModel();
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
            // Calculate device usage by dividing it's current monthly volume by
            // its maximum
            $this->_usage = $this->getAverageMonthlyPageCount() / $this->getMasterDevice()->getMaximumMonthlyPageVolume();
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
            $this->_lifeUsage = $this->getLifePageCount() / $this->getMasterDevice()->getMaximumMonthlyPageVolume() * 36;
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
     * @return Proposalgen_Model_UploadDataCollectorRow
     */
    public function getUploadDataCollectorRow ()
    {
        if (!isset($this->_uploadDataCollectorRow))
        {
            $this->_uploadDataCollectorRow = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->find($this->uploadDataCollectorId);
        }

        return $this->_uploadDataCollectorRow;
    }

    /**
     * @param Proposalgen_Model_UploadDataCollectorRow $UploadDataCollector
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
            $masterDeviceMapper  = Proposalgen_Model_Mapper_MasterDevice::getInstance();
            $this->_masterDevice = $masterDeviceMapper->find($this->masterDeviceId);
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
}