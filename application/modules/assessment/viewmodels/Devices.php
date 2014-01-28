<?php

/**
 * Class Assessment_ViewModel_Devices
 */
class Assessment_ViewModel_Devices
{
    const MINIMUM_MONITOR_INTERVAL_DAYS = 4;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $allDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $allIncludedDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $excludedDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $leasedDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $purchasedDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $unmappedDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $allDevicesWithShortMonitorInterval;

    /**
     * @var float
     */
    protected $_adminCostPerPage;

    /**
     * Constructor
     *
     * @param int   $rmsUploadId
     * @param float $laborCostPerPage
     * @param float $partsCostPerPage
     * @param float $adminCostPerPage
     */
    public function __construct ($rmsUploadId, $laborCostPerPage, $partsCostPerPage, $adminCostPerPage)
    {
        $this->_adminCostPerPage                                = $adminCostPerPage;
        Proposalgen_Model_MasterDevice::$ReportLaborCostPerPage = $laborCostPerPage;
        Proposalgen_Model_MasterDevice::$ReportPartsCostPerPage = $partsCostPerPage;

        /**
         * Initialize groups
         */
        $this->allDeviceInstances                 = new Proposalgen_Model_DeviceInstancesGroup();
        $this->allDevicesWithShortMonitorInterval = new Proposalgen_Model_DeviceInstancesGroup();
        $this->allIncludedDeviceInstances         = new Proposalgen_Model_DeviceInstancesGroup();
        $this->excludedDeviceInstances            = new Proposalgen_Model_DeviceInstancesGroup();
        $this->leasedDeviceInstances              = new Proposalgen_Model_DeviceInstancesGroup();
        $this->purchasedDeviceInstances           = new Proposalgen_Model_DeviceInstancesGroup();
        $this->unmappedDeviceInstances            = new Proposalgen_Model_DeviceInstancesGroup();

        /**
         *
         */
        $deviceInstances = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->fetchAllForRmsUpload($rmsUploadId);
        foreach ($deviceInstances as $device)
        {
            $this->allDeviceInstances->add($device);
        }

        /**
         * Sort our devices into their categories
         */
        foreach ($this->allDeviceInstances->getDeviceInstances() as $deviceInstance)
        {
            $this->_sortDevice($deviceInstance);
        }
    }


    /**
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     */
    protected function _sortDevice ($deviceInstance)
    {
        /**
         * Sort excluded devices
         */
        if ($deviceInstance->isExcluded)
        {
            $this->_sortExcludedDevice($deviceInstance);
        }
        else
        {
            if ($deviceInstance->getMeter()->calculateMpsMonitorInterval()->days < self::MINIMUM_MONITOR_INTERVAL_DAYS)
            {
                $this->allDevicesWithShortMonitorInterval->add($deviceInstance);
            }
            else
            {
                /**
                 * If we're here, it's not excluded. Further sorting is needed.
                 */
                if ($deviceInstance->getMasterDevice() instanceof Proposalgen_Model_MasterDevice)
                {
                    $this->_sortMappedDevice($deviceInstance);
                }
                else
                {
                    $this->_sortUnmappedDevice($deviceInstance);
                }
            }
        }
    }

    /**
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     */
    protected function _sortMappedDevice ($deviceInstance)
    {
        $this->allIncludedDeviceInstances->add($deviceInstance);
        /*
         * Sort leased and purchased devices
         */
        if ($deviceInstance->isLeased)
        {
            $this->leasedDeviceInstances->add($deviceInstance);
        }
        else
        {
            $this->purchasedDeviceInstances->add($deviceInstance);
        }

        // Might as well process the overrides now too
        $deviceInstance->processOverrides($this->_adminCostPerPage);
    }

    /**
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     */
    protected function _sortUnmappedDevice ($deviceInstance)
    {
        $this->unmappedDeviceInstances->add($deviceInstance);
    }

    /**
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     */
    protected function _sortExcludedDevice ($deviceInstance)
    {
        $this->unmappedDeviceInstances->add($deviceInstance);
    }
}