<?php

/**
 * Class Proposalgen_Model_DeviceInstance
 *
 * @author "Lee Robert"
 */
class Proposalgen_Model_DeviceInstance extends Tangent_Model_Abstract
{

    // Static Fields
    static $RUNNING_HOUR_ARRAY = array(
        500 => 8,
        100 => 4,
        0   => 2
    );
    static $KWH_Cost = 0;
    static $ITCPP = 0;

    // Database Fields
    protected $DeviceInstanceId;
    protected $ReportId;
    protected $MasterDeviceId;
    protected $UploadDataCollectorId;
    protected $SerialNumber;
    protected $MPSMonitorStartDate;
    protected $MPSMonitorEndDate;
    protected $MPSDiscoveryDate;
    protected $IsExcluded;
    protected $IsUnknown;
    protected $IpAddress;
    protected $JITSuppliesSupported;

    // Related Objects
    protected $Meters; // An array of all the meters
    protected $MasterDevice;

    // Calculated Fields
    protected $Age;
    protected $MPSMonitorInterval;
    protected $AverageDailyPowerConsumption;
    protected $AverageMonthlyPowerConsumption;
    protected $LifePageCount;
    protected $LifeBlackAndWhitePageCount;
    protected $LifeColorPageCount;
    protected $AverageDailyBlackAndWhitePageCount;
    protected $AverageDailyColorPageCount;
    protected $AverageDailyPageCount;
    protected $AverageMonthlyBlackAndWhitePageCount;
    protected $AverageMonthlyColorPageCount;
    protected $AverageMonthlyPageCount;
    protected $AverageYearlyBlackAndWhitePageCount;
    protected $AverageYearlyColorPageCount;
    protected $AverageYearlyPageCount;
    protected $CostOfInkAndToner;
    protected $CostOfBlackAndWhiteInkAndToner;
    protected $CostOfColorInkAndToner;
    protected $Usage;
    protected $LifeUsage;
    protected $DeviceName;
    protected $GrossMarginMonthlyBlackAndWhiteCost;
    protected $GrossMarginMonthlyColorCost;
    protected $MonthlyRate;
    protected $AverageMonthlyPowerCost;
    protected $AverageDailyPowerCost;

    // Non calculated fields
    protected $AgeRank;
    protected $LifeUsageRank;
    protected $RiskRank;
    protected $UploadDataCollector;
    protected $ReplacementDevice;
    protected $ExclusionReason;
    static $uniqueTonerArray = array();

    /**
     * @param $device        Proposalgen_Model_DeviceInstance
     * @param $report        Proposalgen_Model_Report
     * @param $reportMargin  number
     * @param $companyMargin number
     */
    static function processOverrides ($device, $report, $reportMargin, $companyMargin)
    {
//        $dealerDeviceOverrideMapper = Proposalgen_Model_Mapper_DealerDeviceOverride::getInstance();
//        $dealerTonerOverrideMapper  = Proposalgen_Model_Mapper_DealerTonerOverride::getInstance();
        $userDeviceOverrideMapper   = Proposalgen_Model_Mapper_UserDeviceOverride::getInstance();
        $userTonerOverrideMapper    = Proposalgen_Model_Mapper_UserTonerOverride::getInstance();
        $deviceOverride             = null;

        // Known Device, override
        if (!$device->IsUnknown)
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
                                                                       "master_device_id = ?" => $device->getMasterDeviceId(),
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
            $device->getMasterDevice()->setCost($device->getMasterDevice()
                                                           ->getCost() / $companyMargin);
        }

        // Apply Report Margin to the device price
        $device->getMasterDevice()->setCost($device->getMasterDevice()
                                                       ->getCost() / $reportMargin);

        // Toner Overrides + Margin
        foreach ($device->getMasterDevice()->getToners() as $tonersByPartType)
        {
            foreach ($tonersByPartType as $tonersByColor)
            {
                foreach ($tonersByColor as $toner)
                {
                    if (!in_array($toner->TonerSKU, self::$uniqueTonerArray))
                    {
                        self::$uniqueTonerArray [] = $toner->TonerSKU;
                        $tonerOverride             = null;
                        // Known Device, override
                        if (!$device->IsUnknown)
                        {
                            // Toner Overrides
//                            $tonerOverride = $dealerTonerOverrideMapper->fetchRow(array (
//                                    "toner_id = ?" => $toner->getTonerId(),
//                                    "dealer_company_id = ?" => Proposalgen_Model_DealerCompany::getCurrentUserCompany()->getDealerCompanyId()
//                            ));
//                            if (!$tonerOverride)
//                            {
                                $tonerOverride = $userTonerOverrideMapper->fetchRow(array(
                                                                                         "toner_id = ?" => $toner->getTonerId(),
                                                                                         "user_id = ?"  => Proposalgen_Model_User::getCurrentUserId()
                                                                                    ));
//                            }
                        }

                        // If we found a toner override, apply it
                        if ($tonerOverride)
                        {
                            $toner->setTonerPrice($tonerOverride->OverrideTonerPrice);
                        }
                        else // Apply Company Margin if no overrides
                        {
                            $toner->setTonerPrice($toner->getTonerPrice() / $companyMargin);
                        }
                    }
                } // endforeach
            } // endforeach
        } // endforeach

        // Service Cost Per Page Cost
        if ($device->getMasterDevice()->getServiceCostPerPage() <= 0)
        {
            $device->getMasterDevice()->setServiceCostPerPage($report->getReportSettings()->getServiceCostPerPage());
        }
        // Admin Charge
        $device->getMasterDevice()->setAdminCostPerPage($report->getReportSettings()->getAdminCostPerPage());
    }

    /**
     *
     * @return the $AverageDailyPageCount
     */
    public function getAverageDailyPageCount ()
    {
        if (!isset($this->AverageDailyPageCount))
        {
            $this->AverageDailyPageCount = $this->getAverageDailyBlackAndWhitePageCount() + $this->getAverageDailyColorPageCount();
        }

        return $this->AverageDailyPageCount;
    }

    /**
     *
     * @return the $AverageMonthlyPageCount
     */
    public function getAverageMonthlyPageCount ()
    {
        if (!isset($this->AverageMonthlyPageCount))
        {
            $this->AverageMonthlyPageCount = $this->getAverageMonthlyBlackAndWhitePageCount() + $this->getAverageMonthlyColorPageCount();
        }

        return $this->AverageMonthlyPageCount;
    }

    /**
     *
     * @return the $AverageMonthlyPowerConsumption in KWH
     */
    public function getAverageMonthlyPowerConsumption ()
    {
        if (!isset($this->AverageMonthlyPowerConsumption))
        {
            $this->AverageMonthlyPowerConsumption = $this->getAverageDailyPowerConsumption() * 30;
        }

        return $this->AverageMonthlyPowerConsumption;
    }

    /**
     * The average daily power consumption for a device is caluclated based on a
     * running hour basis
     * If a printer prints over x amount of pages per day, then it is likely to
     * be operating for y hours
     * You can cehck the running hour array to see the specific values
     *
     * @return the $AverageDailyPowerConsumption in KWH
     */
    public function getAverageDailyPowerConsumption ()
    {
        if (!isset($this->AverageDailyPowerConsumption))
        {
            $powerUsage = 0;
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
            $powerUsage                         = $powerUsage / 1000;
            $this->AverageDailyPowerConsumption = $powerUsage;
        }

        return $this->AverageDailyPowerConsumption;
    }

    /**
     *
     * @return the $LifePageCount
     */
    public function getLifePageCount ()
    {
        if (!isset($this->LifePageCount))
        {
            $meters    = $this->getMeters();
            $lifeCount = 0;
            $lifeCount += $this->getLifeBlackAndWhitePageCount();
            $lifeCount += $this->getLifeColorPageCount();
            $this->LifePageCount = $lifeCount;
        }

        return $this->LifePageCount;
    }

    /**
     *
     * @return the $LifeBlackAndWhitePageCount
     */
    public function getLifeBlackAndWhitePageCount ()
    {
        if (!isset($this->LifeBlackAndWhitePageCount))
        {
            $meters    = $this->getMeters();
            $pagecount = 0;
            if (!isset($meters [Proposalgen_Model_Meter::METER_TYPE_BLACK]))
            {
                // if no life or color then throw exception
                throw new Exception("Device does not have a BLACK meter! " . $this->DeviceInstanceId);
            }
            else
            {
                $pagecount = $meters [Proposalgen_Model_Meter::METER_TYPE_BLACK]->getEndMeter();
            }
            $this->LifeBlackAndWhitePageCount = $pagecount;
        }

        return $this->LifeBlackAndWhitePageCount;
    }

    /**
     *
     * @return the $LifeColorPageCount
     */
    public function getLifeColorPageCount ()
    {
        if (!isset($this->LifeColorPageCount))
        {
            $meters    = $this->getMeters();
            $pagecount = 0;
            if (isset($meters [Proposalgen_Model_Meter::METER_TYPE_COLOR]))
            {
                $pagecount = $meters [Proposalgen_Model_Meter::METER_TYPE_COLOR]->getEndMeter();
            }
            $this->LifeColorPageCount = $pagecount;
        }

        return $this->LifeColorPageCount;
    }

    /**
     *
     * @return the $AverageDailyBlackAndWhitePageCount
     */
    public function getAverageDailyBlackAndWhitePageCount ()
    {
        if (!isset($this->AverageDailyBlackAndWhitePageCount))
        {
            $meters    = $this->getMeters();
            $pagecount = 0;
            if (!isset($meters [Proposalgen_Model_Meter::METER_TYPE_BLACK]))
            {
                // If we do not have a black meter, throw an exception
                throw new Exception("Device does not have a BLACK meter! " . $this->DeviceInstanceId);
            }
            else
            {
                $startmeter = $meters [Proposalgen_Model_Meter::METER_TYPE_BLACK]->getStartMeter();
                $endmeter   = $meters [Proposalgen_Model_Meter::METER_TYPE_BLACK]->getEndMeter();
                $pagecount  = $endmeter - $startmeter;
            }

            if ($this->getMPSMonitorInterval()->days > 0)
            {
                $this->AverageDailyBlackAndWhitePageCount = $pagecount / $this->getMPSMonitorInterval()->days;
            }
            else
            {
                $this->AverageDailyBlackAndWhitePageCount = 0;
            }
        }

        return $this->AverageDailyBlackAndWhitePageCount;
    }

    /**
     *
     * @return the $AverageMonthlyBlackAndWhiteCount
     */
    public function getAverageMonthlyBlackAndWhitePageCount ()
    {
        if (!isset($this->AverageMonthlyBlackAndWhitePageCount))
        {
            $this->AverageMonthlyBlackAndWhitePageCount = $this->getAverageDailyBlackAndWhitePageCount() * 30;
        }

        return $this->AverageMonthlyBlackAndWhitePageCount;
    }

    /**
     *
     * @return the $AverageDailyColorPageCount
     */
    public function getAverageDailyColorPageCount ()
    {
        if (!isset($this->AverageDailyColorPageCount))
        {
            $meters       = $this->getMeters();
            $pagecount    = 0;
            $avgPageCount = 0;

            if (!isset($this->MasterDevice))
            {
                $this->MasterDevice = $this->getMasterDevice();
            }

            if (isset($meters [Proposalgen_Model_Meter::METER_TYPE_COLOR]) && $this->getMasterDevice()->getTonerConfigId() !== Proposalgen_Model_TonerConfig::BLACK_ONLY)
            {
                $startmeter = $meters [Proposalgen_Model_Meter::METER_TYPE_COLOR]->getStartMeter();
                $endmeter   = $meters [Proposalgen_Model_Meter::METER_TYPE_COLOR]->getEndMeter();
                $pagecount  = $endmeter - $startmeter;

                if ($this->getMPSMonitorInterval()->days > 0)
                {
                    $avgPageCount = $pagecount / $this->getMPSMonitorInterval()->days;
                }
            }
            $this->AverageDailyColorPageCount = $avgPageCount;
        }

        return $this->AverageDailyColorPageCount;
    }

    /**
     *
     * @return the $AverageMonthlyColorPageCount
     */
    public function getAverageMonthlyColorPageCount ()
    {
        if (!isset($this->AverageMonthlyColorPageCount))
        {
            $this->AverageMonthlyColorPageCount = $this->getAverageDailyColorPageCount() * 30;
        }

        return $this->AverageMonthlyColorPageCount;
    }

    /**
     *
     * @return the $MPSMonitorInterval
     */
    public function getMPSMonitorInterval ()
    {
        if (!isset($this->MPSMonitorInterval))
        {
            $startDate     = new DateTime($this->getMPSMonitorStartDate());
            $endDate       = new DateTime($this->getMPSMonitorEndDate());
            $discoveryDate = new DateTime($this->getMPSDiscoveryDate());
            $interval1     = $startDate->diff($endDate);
            $interval2     = $discoveryDate->diff($endDate);

            if (!$interval1->invert)
            {
                $this->MPSMonitorInterval = $interval1;
                if ($interval1->days > $interval2->days && !$interval2->invert)
                {
                    $this->MPSMonitorInterval = $interval2;
                }
            }
            else if (!$interval2->invert)
            {
                $this->MPSMonitorInterval = $interval2;
            }
            else
            {
                trigger_error("Device was discovered on the after or on the monitor end date.");
            }
        }

        return $this->MPSMonitorInterval;
    }

    /**
     *
     * @return the Calculated device age in years
     */
    public function getAge ()
    {
        if (!isset($this->Age))
        {
            // Get the time difference in seconds
            $launchDate          = time() - strtotime($this->getMasterDevice()->getLaunchDate());
            $correctedLaunchDate = ($launchDate > 31556926) ? ($launchDate - 31556926) : $launchDate;
            $this->Age           = floor($correctedLaunchDate / 31556926);
            if ($this->Age == 0)
            {
                $this->Age = 1;
            }
        }

        return $this->Age;
    }

    /**
     *
     * @return the $DeviceInstanceId
     */
    public function getDeviceInstanceId ()
    {
        return $this->DeviceInstanceId;
    }

    /**
     *
     * @param $DeviceInstanceId field_type
     */
    public function setDeviceInstanceId ($DeviceInstanceId)
    {
        $this->DeviceInstanceId = $DeviceInstanceId;

        return $this;
    }

    /**
     *
     * @return the $ReportId
     */
    public function getReportId ()
    {
        return $this->ReportId;
    }

    /**
     *
     * @param $ReportId field_type
     */
    public function setReportId ($ReportId)
    {
        $this->ReportId = $ReportId;

        return $this;
    }

    /**
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function getMasterDevice ()
    {
        if (!isset($this->MasterDevice))
        {
            $masterDeviceMapper = Proposalgen_Model_Mapper_MasterDevice::getInstance();
            $this->MasterDevice = $masterDeviceMapper->find($this->getMasterDeviceId());
        }

        return $this->MasterDevice;
    }

    /**
     *
     * @param $MasterDevice field_type
     */
    public function setMasterDevice ($MasterDevice)
    {
        $this->MasterDevice = $MasterDevice;

        return $this;
    }

    /**
     *
     * @return the $UploadDataCollectorId
     */
    public function getUploadDataCollectorId ()
    {
        return $this->UploadDataCollectorId;
    }

    /**
     *
     * @param $UploadDataCollectorId field_type
     */
    public function setUploadDataCollectorId ($UploadDataCollectorId)
    {
        $this->UploadDataCollectorId = $UploadDataCollectorId;

        return $this;
    }

    /**
     *
     * @return the $SerialNumber
     */
    public function getSerialNumber ()
    {
        if (!isset($this->SerialNumber))
        {
            $this->SerialNumber = "Unknown";
        }

        return $this->SerialNumber;
    }

    /**
     *
     * @param $SerialNumber field_type
     */
    public function setSerialNumber ($SerialNumber)
    {
        $this->SerialNumber = $SerialNumber;

        return $this;
    }

    /**
     *
     * @return the $MPSMonitorStartDate
     */
    public function getMPSMonitorStartDate ()
    {
        return $this->MPSMonitorStartDate;
    }

    /**
     *
     * @param $MPSMonitorStartDate field_type
     */
    public function setMPSMonitorStartDate ($MPSMonitorStartDate)
    {
        $this->MPSMonitorStartDate = $MPSMonitorStartDate;

        return $this;
    }

    /**
     *
     * @return the $MPSMonitorEndDate
     */
    public function getMPSMonitorEndDate ()
    {
        return $this->MPSMonitorEndDate;
    }

    /**
     *
     * @param $MPSMonitorEndDate field_type
     */
    public function setMPSMonitorEndDate ($MPSMonitorEndDate)
    {
        $this->MPSMonitorEndDate = $MPSMonitorEndDate;

        return $this;
    }

    /**
     *
     * @return the $IsExcluded
     */
    public function getIsExcluded ()
    {
        return $this->IsExcluded;
    }

    /**
     *
     * @param $IsExcluded field_type
     */
    public function setIsExcluded ($IsExcluded)
    {
        $this->IsExcluded = $IsExcluded;

        return $this;
    }

    /**
     *
     * @return the $IpAddress
     */
    public function getIpAddress ()
    {
        if (!isset($this->IpAddress))
        {
            $this->IpAddress = "";
        }

        return $this->IpAddress;
    }

    /**
     *
     * @param $IpAddress field_type
     */
    public function setIpAddress ($IpAddress)
    {
        $this->IpAddress = $IpAddress;

        return $this;
    }

    /**
     *
     * @return the $Meters
     */
    public function getMeters ()
    {
        if (!isset($this->Meters))
        {
            $meterMapper = Proposalgen_Model_Mapper_Meter::getInstance();
            $meters      = $meterMapper->fetchAllForDevice($this->getDeviceInstanceId());

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
                    $startmeter    = $meters [Proposalgen_Model_Meter::METER_TYPE_LIFE]->getStartMeter() - $meters [Proposalgen_Model_Meter::METER_TYPE_COLOR]->getStartMeter();
                    $endmeter      = $meters [Proposalgen_Model_Meter::METER_TYPE_LIFE]->getEndMeter() - $meters [Proposalgen_Model_Meter::METER_TYPE_COLOR]->getEndMeter();
                    $newBlackMeter = new Proposalgen_Model_Meter();
                    $newBlackMeter->startMeter = $startmeter;
                    $newBlackMeter->endMeter = $endmeter;
                    $newBlackMeter->meterType = Proposalgen_Model_Meter::METER_TYPE_BLACK;
                    $newBlackMeter->deviceInstanceId = $this->getDeviceInstanceId();
                    $newBlackMeter->generatedBySystem = true;
                    $meters [Proposalgen_Model_Meter::METER_TYPE_BLACK] = $newBlackMeter;
                }
            }
            $this->Meters = $meters;
        }

        return $this->Meters;
    }

    /**
     *
     * @param $Meters field_type
     */
    public function setMeters ($Meters)
    {
        $this->Meters = $Meters;

        return $this;
    }

    /**
     *
     * @return the $MasterDeviceId
     */
    public function getMasterDeviceId ()
    {
        if (!isset($this->MasterDeviceId))
        {
            $this->MasterDeviceId = 0;
        }

        return $this->MasterDeviceId;
    }

    /**
     *
     * @param $MasterDeviceId field_type
     */
    public function setMasterDeviceId ($MasterDeviceId)
    {
        $this->MasterDeviceId = $MasterDeviceId;

        return $this;
    }

    /**
     *
     * @return the $AverageMonthlyPowerCost
     */
    public function getAverageMonthlyPowerCost ()
    {
        if (!isset($this->AverageMonthlyPowerCost))
        {
            $this->AverageMonthlyPowerCost = $this->getAverageDailyPowerCost() * 30;
        }

        return $this->AverageMonthlyPowerCost;
    }

    /**
     *
     * @param $AverageMonthlyPowerCost field_type
     */
    public function setAverageMonthlyPowerCost ($AverageMonthlyPowerCost)
    {
        $this->AverageMonthlyPowerCost = $AverageMonthlyPowerCost;

        return $this;
    }

    /**
     *
     * @return the $AverageDailyPowerCost
     */
    public function getAverageDailyPowerCost ()
    {
        if (!isset($this->AverageDailyPowerCost))
        {
            $this->AverageDailyPowerCost = $this->getAverageDailyPowerConsumption() * self::$KWH_Cost;
        }

        return $this->AverageDailyPowerCost;
    }

    /**
     *
     * @param $AverageDailyPowerCost field_type
     */
    public function setAverageDailyPowerCost ($AverageDailyPowerCost)
    {
        $this->AverageDailyPowerCost = $AverageDailyPowerCost;

        return $this;
    }

    /**
     *
     * @return the $CostOfInkAndToner
     */
    public function getCostOfInkAndToner ()
    {
        if (!isset($this->CostOfInkAndToner))
        {
            $this->CostOfInkAndToner = $this->getCostOfBlackAndWhiteInkAndToner() + $this->getCostOfColorInkAndToner();
        }

        return $this->CostOfInkAndToner;
    }

    /**
     *
     * @return the $CostOfBlackAndWhiteInkAndToner
     */
    public function getCostOfBlackAndWhiteInkAndToner ()
    {
        if (!isset($this->CostOfBlackAndWhiteInkAndToner))
        {
            $this->CostOfBlackAndWhiteInkAndToner = $this->getMasterDevice()->getCostPerPage()->Estimated->BasePlusMargin->BlackAndWhite * $this->getAverageMonthlyBlackAndWhitePageCount();
        }

        return $this->CostOfBlackAndWhiteInkAndToner;
    }

    /**
     *
     * @return the $CostOfColorInkAndToner
     */
    public function getCostOfColorInkAndToner ()
    {
        if (!isset($this->CostOfColorInkAndToner))
        {
            $this->CostOfColorInkAndToner = $this->getMasterDevice()->getCostPerPage()->Estimated->BasePlusMargin->Color * $this->getAverageMonthlyColorPageCount();
        }

        return $this->CostOfColorInkAndToner;
    }

    /**
     *
     * @return the $DeviceName
     */
    public function getDeviceName ()
    {
        if (!isset($this->DeviceName))
        {
            $this->DeviceName = $this->getMasterDevice()
                                    ->getManufacturer()
                                    ->getManufacturerName() . " " . $this->getMasterDevice()->getPrinterModel();
        }

        return $this->DeviceName;
    }

    /**
     *
     * @param $DeviceName field_type
     */
    public function setDeviceName ($DeviceName)
    {
        $this->DeviceName = $DeviceName;

        return $this;
    }

    /**
     *
     * @return the $Usage
     */
    public function getUsage ()
    {
        if (!isset($this->Usage))
        {
            // Calculate device usage by dividing it's current monthly volume by
            // its maximum
            $this->Usage = $this->getAverageMonthlyPageCount() / $this->getMasterDevice()->getMaximumMonthlyPageVolume();
        }

        return $this->Usage;
    }

    /**
     *
     * @param $Usage field_type
     */
    public function setUsage ($Usage)
    {
        $this->Usage = $Usage;

        return $this;
    }

    /**
     *
     * @return the $Usage
     */
    public function getLifeUsage ()
    {
        if (!isset($this->LifeUsage))
        {
            // Calculate device life usage by dividing it's current life count
            // by it's estimated max life page count (maximum monthly page
            // volume * 36 months)
            $this->LifeUsage = $this->getLifePageCount() / $this->getMasterDevice()->getMaximumMonthlyPageVolume() * 36;
        }

        return $this->LifeUsage;
    }

    /**
     *
     * @param $LifeUsage field_type
     */
    public function setLifeUsage ($LifeUsage)
    {
        $this->LifeUsage = $LifeUsage;

        return $this;
    }

    /**
     *
     * @return the $AgeRank
     */
    public function getAgeRank ()
    {
        if (!isset($this->AgeRank))
        {

            $this->AgeRank = null;
        }

        return $this->AgeRank;
    }

    /**
     *
     * @param $AgeRank field_type
     */
    public function setAgeRank ($AgeRank)
    {
        $this->AgeRank = $AgeRank;

        return $this;
    }

    /**
     *
     * @return the $LifeUsageRank
     */
    public function getLifeUsageRank ()
    {
        if (!isset($this->LifeUsageRank))
        {

            $this->LifeUsageRank = null;
        }

        return $this->LifeUsageRank;
    }

    /**
     *
     * @param $LifeUsageRank field_type
     */
    public function setLifeUsageRank ($LifeUsageRank)
    {
        $this->LifeUsageRank = $LifeUsageRank;

        return $this;
    }

    /**
     *
     * @return the $RiskRank
     */
    public function getRiskRank ()
    {
        if (!isset($this->RiskRank))
        {

            $this->RiskRank = null;
        }

        return $this->RiskRank;
    }

    /**
     *
     * @param $RiskRank field_type
     */
    public function setRiskRank ($RiskRank)
    {
        $this->RiskRank = $RiskRank;

        return $this;
    }

    /**
     *
     * @return the $KWH_Cost
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
     *
     * @param $KWH_Cost field_type
     */
    public static function setKWH_Cost ($KWH_Cost)
    {
        Proposalgen_Model_DeviceInstance::$KWH_Cost = $KWH_Cost;
    }

    /**
     *
     * @return the $JITSuppliesSupported
     */
    public function getJITSuppliesSupported ()
    {
        if (!isset($this->JITSuppliesSupported))
        {

            $this->JITSuppliesSupported = null;
        }

        return $this->JITSuppliesSupported;
    }

    /**
     *
     * @param $JITSuppliesSupported field_type
     */
    public function setJITSuppliesSupported ($JITSuppliesSupported)
    {
        $this->JITSuppliesSupported = $JITSuppliesSupported;

        return $this;
    }

    /**
     *
     * @return the $IsUnknown
     */
    public function getIsUnknown ()
    {
        if (!isset($this->IsUnknown))
        {
            $this->IsUnknown = false;
        }

        return $this->IsUnknown;
    }

    /**
     *
     * @param $IsUnknown field_type
     */
    public function setIsUnknown ($IsUnknown)
    {
        $this->IsUnknown = $IsUnknown;

        return $this;
    }

    /**
     *
     * @return the $MPSDiscoveryDate
     */
    public function getMPSDiscoveryDate ()
    {
        if (!isset($this->MPSDiscoveryDate))
        {

            $this->MPSDiscoveryDate = null;
        }

        return $this->MPSDiscoveryDate;
    }

    /**
     *
     * @param $MPSDiscoveryDate field_type
     */
    public function setMPSDiscoveryDate ($MPSDiscoveryDate)
    {
        $this->MPSDiscoveryDate = $MPSDiscoveryDate;

        return $this;
    }

    /**
     *
     * @return the $UploadDataCollector
     */
    public function getUploadDataCollector ()
    {
        if (!isset($this->UploadDataCollector))
        {
            $this->UploadDataCollector = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->find($this->getUploadDataCollectorId());
        }

        return $this->UploadDataCollector;
    }

    /**
     *
     * @param $UploadDataCollector field_type
     */
    public function setUploadDataCollector ($UploadDataCollector)
    {
        $this->UploadDataCollector = $UploadDataCollector;

        return $this;
    }

    /**
     *
     * @return the $AverageYearlyBlackAndWhitePageCount
     */
    public function getAverageYearlyBlackAndWhitePageCount ()
    {
        if (!isset($this->AverageYearlyBlackAndWhitePageCount))
        {
            $this->AverageYearlyBlackAndWhitePageCount = $this->getAverageMonthlyBlackAndWhitePageCount() * 12;
        }

        return $this->AverageYearlyBlackAndWhitePageCount;
    }

    /**
     *
     * @param $AverageYearlyBlackAndWhitePageCount field_type
     */
    public function setAverageYearlyBlackAndWhitePageCount ($AverageYearlyBlackAndWhitePageCount)
    {
        $this->AverageYearlyBlackAndWhitePageCount = $AverageYearlyBlackAndWhitePageCount;

        return $this;
    }

    /**
     *
     * @return the $AverageYearlyColorPageCount
     */
    public function getAverageYearlyColorPageCount ()
    {
        if (!isset($this->AverageYearlyColorPageCount))
        {
            $this->AverageYearlyColorPageCount = $this->getAverageMonthlyColorPageCount() * 12;
        }

        return $this->AverageYearlyColorPageCount;
    }

    /**
     *
     * @param $AverageYearlyColorPageCount field_type
     */
    public function setAverageYearlyColorPageCount ($AverageYearlyColorPageCount)
    {
        $this->AverageYearlyColorPageCount = $AverageYearlyColorPageCount;

        return $this;
    }

    /**
     *
     * @return the $AverageYearlyPageCount
     */
    public function getAverageYearlyPageCount ()
    {
        if (!isset($this->AverageYearlyPageCount))
        {
            $this->AverageYearlyPageCount = $this->getAverageYearlyBlackAndWhitePageCount() + $this->getAverageYearlyColorPageCount();
        }

        return $this->AverageYearlyPageCount;
    }

    /**
     *
     * @param $AverageYearlyPageCount field_type
     */
    public function setAverageYearlyPageCount ($AverageYearlyPageCount)
    {
        $this->AverageYearlyPageCount = $AverageYearlyPageCount;

        return $this;
    }

    /**
     *
     * @return the $ReplacementDevice
     */
    public function getReplacementDevice ()
    {
        if (!isset($this->ReplacementDevice))
        {

            $this->ReplacementDevice = null;
        }

        return $this->ReplacementDevice;
    }

    /**
     *
     * @param $ReplacementDevice field_type
     */
    public function setReplacementDevice ($ReplacementDevice)
    {
        $this->ReplacementDevice = $ReplacementDevice;

        return $this;
    }

    /**
     *
     * @return the $GrossMarginMonthlyBlackAndWhiteCost
     */
    public function getGrossMarginMonthlyBlackAndWhiteCost ()
    {
        if (!isset($this->GrossMarginMonthlyBlackAndWhiteCost))
        {
            $this->GrossMarginMonthlyBlackAndWhiteCost = ($this->getMasterDevice()->getCostPerPage()->Actual->BasePlusService->BlackAndWhite * $this->getAverageMonthlyBlackAndWhitePageCount());
        }

        return $this->GrossMarginMonthlyBlackAndWhiteCost;
    }

    /**
     *
     * @param $GrossMarginMonthlyBlackAndWhiteCost field_type
     */
    public function setGrossMarginMonthlyBlackAndWhiteCost ($GrossMarginMonthlyBlackAndWhiteCost)
    {
        $this->GrossMarginMonthlyBlackAndWhiteCost = $GrossMarginMonthlyBlackAndWhiteCost;

        return $this;
    }

    /**
     *
     * @return the $GrossMarginMonthlyColorCost
     */
    public function getGrossMarginMonthlyColorCost ()
    {
        if (!isset($this->GrossMarginMonthlyColorCost))
        {
            $this->GrossMarginMonthlyColorCost = ($this->getMasterDevice()->getCostPerPage()->Actual->BasePlusService->Color * $this->getAverageMonthlyColorPageCount());
        }

        return $this->GrossMarginMonthlyColorCost;
    }

    /**
     *
     * @param $GrossMarginMonthlyColorCost field_type
     */
    public function setGrossMarginMonthlyColorCost ($GrossMarginMonthlyColorCost)
    {
        $this->GrossMarginMonthlyColorCost = $GrossMarginMonthlyColorCost;

        return $this;
    }

    /**
     * Calculates the cost of the device on a monthly basis to compare with
     * replacement devices
     *
     * @return float
     */
    public function getMonthlyRate ()
    {
        if (!isset($this->MonthlyRate))
        {
            $this->MonthlyRate = 0;
            $this->MonthlyRate += ($this->getMasterDevice()->getCostPerPage()->Estimated->BasePlusMargin->BlackAndWhite * $this->getAverageMonthlyBlackAndWhitePageCount());
            $this->MonthlyRate += ($this->getMasterDevice()->getCostPerPage()->Estimated->BasePlusMargin->Color * $this->getAverageMonthlyColorPageCount());
            $this->MonthlyRate += ($this->getAverageMonthlyPageCount() * self::getITCPP());
        }

        return $this->MonthlyRate;
    }

    /**
     *
     * @param $MonthlyRate field_type
     */
    public function setMonthlyRate ($MonthlyRate)
    {
        $this->MonthlyRate = $MonthlyRate;

        return $this;
    }

    /**
     *
     * @return the $ITCPP
     */
    public static function getITCPP ()
    {
        if (!isset(Proposalgen_Model_DeviceInstance::$ITCPP))
        {
            Proposalgen_Model_DeviceInstance::$ITCPP = 0;
        }

        return Proposalgen_Model_DeviceInstance::$ITCPP;
    }

    /**
     *
     * @param $ITCPP field_type
     */
    public static function setITCPP ($ITCPP)
    {
        Proposalgen_Model_DeviceInstance::$ITCPP = $ITCPP;
    }

    /**
     *
     * @return the $ExclusionReason
     */
    public function getExclusionReason ()
    {
        if (!isset($this->ExclusionReason))
        {
            $this->ExclusionReason = "Manually excluded";
        }

        return $this->ExclusionReason;
    }

    /**
     *
     * @param $ExclusionReason field_type
     */
    public function setExclusionReason ($ExclusionReason)
    {
        $this->ExclusionReason = $ExclusionReason;

        return $this;
    }
}