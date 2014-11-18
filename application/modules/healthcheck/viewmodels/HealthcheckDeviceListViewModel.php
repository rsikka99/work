<?php

/**
 * Class Healthcheck_ViewModel_HealthcheckDeviceListViewModel
 */
class Healthcheck_ViewModel_HealthcheckDeviceListViewModel
{
    /**
     * @var array
     */
    protected $optimizedDevices;

    /**
     * @var array
     */
    protected $underUtilizedDevices;

    /**
     * @var array
     */
    protected $overUtilizedDevices;

    /**
     * @var array
     */
    protected $oldDevices;

    /**
     * @var array
     */
    protected $devicesNotReportingTonerLevels;

    /**
     * @var array
     */
    protected $faxAndScanDevices;

    /**
     * @var array
     */
    protected $compatibleWithProgramDevices;

    /**
     * @var array
     */
    protected $notCompatibleWithProgramDevices;

    /**
     * @var array
     */
    protected $a3Devices;

    /**
     * @var Healthcheck_ViewModel_Healthcheck
     */
    protected $healthCheckViewModel;

    /**
     * @param $viewModel Healthcheck_ViewModel_Healthcheck
     */
    public function __construct (Healthcheck_ViewModel_Healthcheck $viewModel)
    {
        $this->healthCheckViewModel = $viewModel;
    }

    /**
     * @return array|Proposalgen_Model_DeviceInstance[]
     */
    public function getGetCompatibleWithProgramDevices ()
    {
        if (!isset($this->compatibleWithProgramDevices))
        {
            $this->compatibleWithProgramDevices = [];

            foreach ($this->healthCheckViewModel->getDevices()->compatibleDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $this->compatibleWithProgramDevices[] = [
                    'deviceName'               => str_ireplace("hewlett-packard", "HP", $deviceInstance->getDeviceName()),
                    'ipAddress'                => $deviceInstance->ipAddress,
                    'serialNumber'             => $deviceInstance->serialNumber,
                    'deviceAge'                => $deviceInstance->getAge(),
                    'monthlyPageVolume'        => $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly(),
                    'percentOfTotalPageVolume' => $deviceInstance->calculateMonthlyPercentOfTotalVolume($this->healthCheckViewModel->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) / 100,
                    'suggestedMaxVolume'       => $deviceInstance->getMasterDevice()->calculateEstimatedMaxLifeCount(),
                    'lifePageCount'            => $deviceInstance->getMeter()->endMeterLife,
                    'isA3'                     => $deviceInstance->getMasterDevice()->isA3 ? 'Yes' : 'No',
                    'isColor'                  => $deviceInstance->getMasterDevice()->isA3 ? 'Yes' : 'No',
                    'isDuplex'                 => $deviceInstance->getMasterDevice()->isDuplex ? 'Yes' : 'No',
                    'isMFP'                    => $deviceInstance->getMasterDevice()->isMfp() ? 'Yes' : 'No',
                    'isFax'                    => $deviceInstance->getMasterDevice()->isFax ? 'Yes' : 'No',
                    'reportsTonerLevels'       => $deviceInstance->isCapableOfReportingTonerLevels() ? 'Yes' : 'No',
                    'linkedToJitProgram'       => $deviceInstance->isLeased ? 'No' : ($deviceInstance->isManaged ? 'Yes' : 'No'),
                ];
            }
        }

        return $this->compatibleWithProgramDevices;
    }

    /**
     * @return array|Proposalgen_Model_DeviceInstance[]
     */
    public function getNotCompatibleWithProgramDevices ()
    {
        if (!isset($this->notCompatibleWithProgramDevices))
        {
            $this->notCompatibleWithProgramDevices = [];

            foreach ($this->healthCheckViewModel->getDevices()->notCompatibleDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $this->notCompatibleWithProgramDevices[] = [
                    'deviceName'               => str_ireplace("hewlett-packard", "HP", $deviceInstance->getDeviceName()),
                    'ipAddress'                => $deviceInstance->ipAddress,
                    'serialNumber'             => $deviceInstance->serialNumber,
                    'deviceAge'                => $deviceInstance->getAge(),
                    'monthlyPageVolume'        => $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly(),
                    'percentOfTotalPageVolume' => $deviceInstance->calculateMonthlyPercentOfTotalVolume($this->healthCheckViewModel->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) / 100,
                    'suggestedMaxVolume'       => $deviceInstance->getMasterDevice()->calculateEstimatedMaxLifeCount(),
                    'lifePageCount'            => $deviceInstance->getMeter()->endMeterLife,
                    'isA3'                     => $deviceInstance->getMasterDevice()->isA3 ? 'Yes' : 'No',
                    'isColor'                  => $deviceInstance->getMasterDevice()->isA3 ? 'Yes' : 'No',
                    'isDuplex'                 => $deviceInstance->getMasterDevice()->isDuplex ? 'Yes' : 'No',
                    'isMFP'                    => $deviceInstance->getMasterDevice()->isMfp() ? 'Yes' : 'No',
                    'isFax'                    => $deviceInstance->getMasterDevice()->isFax ? 'Yes' : 'No',
                    'reportsTonerLevels'       => $deviceInstance->isCapableOfReportingTonerLevels() ? 'Yes' : 'No',
                    'linkedToJitProgram'       => $deviceInstance->isLeased ? 'No' : ($deviceInstance->isManaged ? 'Yes' : 'No'),
                ];
            }
        }

        return $this->notCompatibleWithProgramDevices;
    }

    /**
     * @return array|Proposalgen_Model_DeviceInstance[]
     */
    public function getOptimizedDevices ()
    {
        if (!isset($this->optimizedDevices))
        {
            $this->optimizedDevices = [];

            foreach ($this->healthCheckViewModel->getOptimizedDevices() as $deviceInstance)
            {
                $this->optimizedDevices[] = [
                    'deviceName'                     => str_ireplace("hewlett-packard", "HP", $deviceInstance->getDeviceName()),
                    'ipAddress'                      => $deviceInstance->ipAddress,
                    'serialNumber'                   => $deviceInstance->serialNumber,
                    'deviceAge'                      => $deviceInstance->getAge(),
                    'monthlyPageVolume'              => $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly(),
                    'percentOfFleetsTotalPageVolume' => $deviceInstance->calculateMonthlyPercentOfTotalVolume($this->healthCheckViewModel->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) / 100,
                    'suggestedMaxVolume'             => $deviceInstance->getMasterDevice()->calculateEstimatedMaxLifeCount(),
                    'lifePageCount'                  => $deviceInstance->getMeter()->endMeterLife,
                    'isA3'                           => $deviceInstance->getMasterDevice()->isA3 ? 'Yes' : 'No',
                    'isColor'                        => $deviceInstance->getMasterDevice()->isColor() ? 'Yes' : 'No',
                    'isDuplex'                       => $deviceInstance->getMasterDevice()->isDuplex ? 'Yes' : 'No',
                    'isMFP'                          => $deviceInstance->getMasterDevice()->isMfp() ? 'Yes' : 'No',
                    'isFax'                          => $deviceInstance->getMasterDevice()->isFax ? 'Yes' : 'No',
                    'reportsTonerLevels'             => $deviceInstance->isCapableOfReportingTonerLevels() ? 'Yes' : 'No',
                    'linkedToJitProgram'             => $deviceInstance->isLeased ? 'No' : ($deviceInstance->isManaged ? 'Yes' : 'No'),
                ];
            }
        }

        return $this->optimizedDevices;
    }

    /**
     * @return array|Proposalgen_Model_DeviceInstance[]
     */
    public function getUnderUtilizedDevices ()
    {
        if (!isset($this->underUtilizedDevices))
        {
            $this->underUtilizedDevices = [];

            foreach ($this->healthCheckViewModel->getUnderutilizedDevices() as $deviceInstance)
            {
                $this->underUtilizedDevices[] = [
                    'deviceName'                     => str_ireplace("hewlett-packard", "HP", $deviceInstance->getDeviceName()),
                    'ipAddress'                      => $deviceInstance->ipAddress,
                    'serialNumber'                   => $deviceInstance->serialNumber,
                    'deviceAge'                      => $deviceInstance->getAge(),
                    'monthlyPageVolume'              => $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly(),
                    'percentOfFleetsTotalPageVolume' => $deviceInstance->calculateMonthlyPercentOfTotalVolume($this->healthCheckViewModel->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) / 100,
                    'suggestedMaxVolume'             => $deviceInstance->getMasterDevice()->calculateEstimatedMaxLifeCount(),
                    'lifePageCount'                  => $deviceInstance->getMeter()->endMeterLife,
                    'isA3'                           => $deviceInstance->getMasterDevice()->isA3 ? 'Yes' : 'No',
                    'isColor'                        => $deviceInstance->getMasterDevice()->isColor() ? 'Yes' : 'No',
                    'isDuplex'                       => $deviceInstance->getMasterDevice()->isDuplex ? 'Yes' : 'No',
                    'isMFP'                          => $deviceInstance->getMasterDevice()->isMfp() ? 'Yes' : 'No',
                    'isFax'                          => $deviceInstance->getMasterDevice()->isFax ? 'Yes' : 'No',
                    'reportsTonerLevels'             => $deviceInstance->isCapableOfReportingTonerLevels() ? 'Yes' : 'No',
                    'linkedToJitProgram'             => $deviceInstance->isLeased ? 'No' : ($deviceInstance->isManaged ? 'Yes' : 'No'),

                    'percentOfMonthlyCost'           => ($deviceInstance->isLeased)
                        ? $deviceInstance->getLeasedMonthlyRatePercentage(
                            $this->healthCheckViewModel->healthcheck->getHealthcheckSettings()->monthlyLeasePayment,
                            $this->healthCheckViewModel->getLeasedBlackAndWhiteCharge(),
                            $this->healthCheckViewModel->getLeasedColorCharge(),
                            $this->healthCheckViewModel->calculateTotalMonthlyCost()
                        )
                        : $deviceInstance->getMonthlyRatePercentage(
                            $this->healthCheckViewModel->calculateTotalMonthlyCost(),
                            $this->healthCheckViewModel->getCostPerPageSettingForCustomer()
                        ),

                    'suggestedAction'                => sprintf('Migrate all page volume to %1$s compatible device.', My_Brand::$jit),
                ];
            }
        }

        return $this->underUtilizedDevices;
    }

    /**
     * @return array|Proposalgen_Model_DeviceInstance[]
     */
    public function getOverUtilizedDevices ()
    {
        if (!isset($this->overUtilizedDevices))
        {
            $this->overUtilizedDevices = [];

            foreach ($this->healthCheckViewModel->getOverutilizedDevices() as $deviceInstance)
            {
                $this->overUtilizedDevices[] = [
                    'deviceName'                     => str_ireplace("hewlett-packard", "HP", $deviceInstance->getDeviceName()),
                    'ipAddress'                      => $deviceInstance->ipAddress,
                    'serialNumber'                   => $deviceInstance->serialNumber,
                    'deviceAge'                      => $deviceInstance->getAge(),
                    'monthlyPageVolume'              => $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly(),
                    'percentOfFleetsTotalPageVolume' => $deviceInstance->calculateMonthlyPercentOfTotalVolume($this->healthCheckViewModel->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) / 100,
                    'suggestedMaxVolume'             => $deviceInstance->getMasterDevice()->calculateEstimatedMaxLifeCount(),
                    'lifePageCount'                  => $deviceInstance->getMeter()->endMeterLife,
                    'isA3'                           => $deviceInstance->getMasterDevice()->isA3 ? 'Yes' : 'No',
                    'isColor'                        => $deviceInstance->getMasterDevice()->isColor() ? 'Yes' : 'No',
                    'isDuplex'                       => $deviceInstance->getMasterDevice()->isDuplex ? 'Yes' : 'No',
                    'isMFP'                          => $deviceInstance->getMasterDevice()->isMfp() ? 'Yes' : 'No',
                    'isFax'                          => $deviceInstance->getMasterDevice()->isFax ? 'Yes' : 'No',
                    'reportsTonerLevels'             => $deviceInstance->isCapableOfReportingTonerLevels() ? 'Yes' : 'No',
                    'linkedToJitProgram'             => $deviceInstance->isLeased ? 'No' : ($deviceInstance->isManaged ? 'Yes' : 'No'),

                    'percentOfMonthlyCost'           => ($deviceInstance->isLeased)
                        ? $deviceInstance->getLeasedMonthlyRatePercentage(
                            $this->healthCheckViewModel->healthcheck->getHealthcheckSettings()->monthlyLeasePayment,
                            $this->healthCheckViewModel->getLeasedBlackAndWhiteCharge(),
                            $this->healthCheckViewModel->getLeasedColorCharge(),
                            $this->healthCheckViewModel->calculateTotalMonthlyCost()
                        )
                        : $deviceInstance->getMonthlyRatePercentage(
                            $this->healthCheckViewModel->calculateTotalMonthlyCost(),
                            $this->healthCheckViewModel->getCostPerPageSettingForCustomer()
                        ),

                    'suggestedAction'                => sprintf('Migrate excess page volume to a %1$s compatible device.', My_Brand::$jit),
                ];
            }
        }

        return $this->overUtilizedDevices;
    }

    /**
     * @return array|Proposalgen_Model_DeviceInstance[]
     */
    public function getOldDevices ()
    {
        if (!isset($this->oldDevices))
        {
            $this->oldDevices = [];

            foreach ($this->healthCheckViewModel->getOldDevices() as $deviceInstance)
            {
                $this->oldDevices[] = [
                    'deviceName'                     => str_ireplace("hewlett-packard", "HP", $deviceInstance->getDeviceName()),
                    'ipAddress'                      => $deviceInstance->ipAddress,
                    'serialNumber'                   => $deviceInstance->serialNumber,
                    'deviceAge'                      => $deviceInstance->getAge(),
                    'monthlyPageVolume'              => $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly(),
                    'percentOfFleetsTotalPageVolume' => $deviceInstance->calculateMonthlyPercentOfTotalVolume($this->healthCheckViewModel->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) / 100,
                    'suggestedMaxVolume'             => $deviceInstance->getMasterDevice()->calculateEstimatedMaxLifeCount(),
                    'lifePageCount'                  => $deviceInstance->getMeter()->endMeterLife,
                    'isA3'                           => $deviceInstance->getMasterDevice()->isA3 ? 'Yes' : 'No',
                    'isColor'                        => $deviceInstance->getMasterDevice()->isA3 ? 'Yes' : 'No',
                    'isDuplex'                       => $deviceInstance->getMasterDevice()->isDuplex ? 'Yes' : 'No',
                    'isMFP'                          => $deviceInstance->getMasterDevice()->isMfp() ? 'Yes' : 'No',
                    'isFax'                          => $deviceInstance->getMasterDevice()->isFax ? 'Yes' : 'No',
                    'reportsTonerLevels'             => $deviceInstance->isCapableOfReportingTonerLevels() ? 'Yes' : 'No',
                    'linkedToJitProgram'             => $deviceInstance->isLeased ? 'No' : ($deviceInstance->isManaged ? 'Yes' : 'No'),

                    'percentOfMonthlyCost'           => ($deviceInstance->isLeased)
                        ? $deviceInstance->getLeasedMonthlyRatePercentage(
                            $this->healthCheckViewModel->healthcheck->getHealthcheckSettings()->monthlyLeasePayment,
                            $this->healthCheckViewModel->getLeasedBlackAndWhiteCharge(),
                            $this->healthCheckViewModel->getLeasedColorCharge(),
                            $this->healthCheckViewModel->calculateTotalMonthlyCost()
                        )
                        : $deviceInstance->getMonthlyRatePercentage(
                            $this->healthCheckViewModel->calculateTotalMonthlyCost(),
                            $this->healthCheckViewModel->getCostPerPageSettingForCustomer()
                        ),

                    'suggestedAction'                => sprintf('Migrate all page volume to a newer %1$s compatible device.', My_Brand::$jit),
                ];
            }
        }

        return $this->oldDevices;
    }

    /**
     * @return array|Proposalgen_Model_DeviceInstance[]
     */
    public function getDevicesNotReportingTonerLevels ()
    {
        if (!isset($this->devicesNotReportingTonerLevels))
        {
            $this->devicesNotReportingTonerLevels = [];

            foreach ($this->healthCheckViewModel->getDevicesNotReportingTonerLevels() as $deviceInstance)
            {
                $this->devicesNotReportingTonerLevels[] = [

                    'deviceName'                     => str_ireplace("hewlett-packard", "HP", $deviceInstance->getDeviceName()),
                    'ipAddress'                      => $deviceInstance->ipAddress,
                    'serialNumber'                   => $deviceInstance->serialNumber,
                    'deviceAge'                      => $deviceInstance->getAge(),
                    'monthlyPageVolume'              => $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly(),
                    'percentOfFleetsTotalPageVolume' => $deviceInstance->calculateMonthlyPercentOfTotalVolume($this->healthCheckViewModel->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) / 100,
                    'suggestedMaxVolume'             => $deviceInstance->getMasterDevice()->calculateEstimatedMaxLifeCount(),
                    'lifePageCount'                  => $deviceInstance->getMeter()->endMeterLife,
                    'isA3'                           => $deviceInstance->getMasterDevice()->isA3 ? 'Yes' : 'No',
                    'isColor'                        => $deviceInstance->getMasterDevice()->isColor() ? 'Yes' : 'No',
                    'isDuplex'                       => $deviceInstance->getMasterDevice()->isDuplex ? 'Yes' : 'No',
                    'isMFP'                          => $deviceInstance->getMasterDevice()->isMfp() ? 'Yes' : 'No',
                    'isFax'                          => $deviceInstance->getMasterDevice()->isFax ? 'Yes' : 'No',
                    'reportsTonerLevels'             => $deviceInstance->isCapableOfReportingTonerLevels() ? 'Yes' : 'No',
                    'linkedToJitProgram'             => $deviceInstance->isLeased ? 'No' : ($deviceInstance->isManaged ? 'Yes' : 'No'),

                    'percentOfMonthlyCost'           => ($deviceInstance->isLeased)
                        ? $deviceInstance->getLeasedMonthlyRatePercentage(
                            $this->healthCheckViewModel->healthcheck->getHealthcheckSettings()->monthlyLeasePayment,
                            $this->healthCheckViewModel->getLeasedBlackAndWhiteCharge(),
                            $this->healthCheckViewModel->getLeasedColorCharge(),
                            $this->healthCheckViewModel->calculateTotalMonthlyCost()
                        )
                        : $deviceInstance->getMonthlyRatePercentage(
                            $this->healthCheckViewModel->calculateTotalMonthlyCost(),
                            $this->healthCheckViewModel->getCostPerPageSettingForCustomer()
                        ),

                    'suggestedAction'                => sprintf('Migrate all page volume to %1$s compatible device.', My_Brand::$jit),
                ];
            }
        }

        return $this->devicesNotReportingTonerLevels;
    }

    /**
     * @return array|Proposalgen_Model_DeviceInstance[]
     */
    public function getFaxAndScanDevices ()
    {
        if (!isset($this->faxAndScanDevices))
        {
            $this->faxAndScanDevices = [];
            /**
             * Devices Not Reporting Toner Levels Data
             *
             * @var $deviceInstance Proposalgen_Model_DeviceInstance
             */
            foreach ($this->healthCheckViewModel->getFaxAndScanTableDevices() as $deviceInstance)
            {
                $this->faxAndScanDevices[] = [
                    'deviceName'               => str_ireplace("hewlett-packard", "HP", $deviceInstance->getDeviceName()),
                    'ipAddress'                => $deviceInstance->ipAddress,
                    'serialNumber'             => $deviceInstance->serialNumber,
                    'deviceAge'                => $deviceInstance->getAge(),
                    'monthlyPageVolume'        => $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly(),
                    'monthlyScanPageVolume'    => $deviceInstance->getPageCounts()->getScanPageCount()->getMonthly(),
                    'monthlyFaxPageVolume'     => $deviceInstance->getPageCounts()->getFaxPageCount()->getMonthly(),
                    'percentOfTotalPageVolume' => $deviceInstance->calculateMonthlyPercentOfTotalVolume($this->healthCheckViewModel->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) / 100,
                    'suggestedMaxVolume'       => $deviceInstance->getMasterDevice()->calculateEstimatedMaxLifeCount(),
                    'lifePageCount'            => $deviceInstance->getMeter()->endMeterLife,
                    'isA3'                     => $deviceInstance->getMasterDevice()->isA3 ? 'Yes' : 'No',
                    'isColor'                  => $deviceInstance->getMasterDevice()->isA3 ? 'Yes' : 'No',
                    'isDuplex'                 => $deviceInstance->getMasterDevice()->isDuplex ? 'Yes' : 'No',
                    'isMFP'                    => $deviceInstance->getMasterDevice()->isMfp() ? 'Yes' : 'No',
                    'isFax'                    => $deviceInstance->getMasterDevice()->isFax ? 'Yes' : 'No',
                    'reportsTonerLevels'       => $deviceInstance->isCapableOfReportingTonerLevels() ? 'Yes' : 'No',
                    'linkedToJitProgram'       => $deviceInstance->isLeased ? 'No' : ($deviceInstance->isManaged ? 'Yes' : 'No'),
                ];
            }
        }

        return $this->faxAndScanDevices;
    }

    /**
     * @return array|Proposalgen_Model_DeviceInstance[]
     */
    public function getA3Devices ()
    {
        if (!isset($this->faxAndScanDevices))
        {
            $this->a3Devices = [];
            /**
             * Devices Not Reporting Toner Levels Data
             *
             * @var $deviceInstance Proposalgen_Model_DeviceInstance
             */
            foreach ($this->healthCheckViewModel->getDevices()->a3DeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $this->a3Devices[] = [
                    'deviceName'               => str_ireplace("hewlett-packard", "HP", $deviceInstance->getDeviceName()),
                    'ipAddress'                => $deviceInstance->ipAddress,
                    'serialNumber'             => $deviceInstance->serialNumber,
                    'deviceAge'                => $deviceInstance->getAge(),
                    'monthlyPageVolume'        => $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly(),
                    'monthlyA3PageVolume'      => $deviceInstance->getPageCounts()->getScanPageCount()->getMonthly(),
                    'percentOfTotalPageVolume' => $deviceInstance->calculateMonthlyPercentOfTotalVolume($this->healthCheckViewModel->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) / 100,
                    'suggestedMaxVolume'       => $deviceInstance->getMasterDevice()->calculateEstimatedMaxLifeCount(),
                    'lifePageCount'            => $deviceInstance->getMeter()->endMeterLife,
                    'isA3'                     => $deviceInstance->getMasterDevice()->isA3 ? 'Yes' : 'No',
                    'isColor'                  => $deviceInstance->getMasterDevice()->isA3 ? 'Yes' : 'No',
                    'isDuplex'                 => $deviceInstance->getMasterDevice()->isDuplex ? 'Yes' : 'No',
                    'isMFP'                    => $deviceInstance->getMasterDevice()->isMfp() ? 'Yes' : 'No',
                    'isFax'                    => $deviceInstance->getMasterDevice()->isFax ? 'Yes' : 'No',
                    'reportsTonerLevels'       => $deviceInstance->isCapableOfReportingTonerLevels() ? 'Yes' : 'No',
                    'linkedToJitProgram'       => $deviceInstance->isLeased ? 'No' : ($deviceInstance->isManaged ? 'Yes' : 'No'),
                ];
            }
        }

        return $this->a3Devices;
    }
}