<?php
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstancesGroupModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;

/**
 * Class Assessment_ViewModel_Devices
 */
class Assessment_ViewModel_Devices
{
    const MINIMUM_MONITOR_INTERVAL_DAYS = 4;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $allDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $allIncludedDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $excludedDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $leasedDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $purchasedDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $unmappedDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
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
        $this->_adminCostPerPage                   = $adminCostPerPage;
        MasterDeviceModel::$ReportLaborCostPerPage = $laborCostPerPage;
        MasterDeviceModel::$ReportPartsCostPerPage = $partsCostPerPage;

        /**
         * Initialize groups
         */
        $this->allDeviceInstances                 = new DeviceInstancesGroupModel();
        $this->allDevicesWithShortMonitorInterval = new DeviceInstancesGroupModel();
        $this->allIncludedDeviceInstances         = new DeviceInstancesGroupModel();
        $this->excludedDeviceInstances            = new DeviceInstancesGroupModel();
        $this->leasedDeviceInstances              = new DeviceInstancesGroupModel();
        $this->purchasedDeviceInstances           = new DeviceInstancesGroupModel();
        $this->unmappedDeviceInstances            = new DeviceInstancesGroupModel();

        /**
         *
         */
        $deviceInstances = DeviceInstanceMapper::getInstance()->fetchAllForRmsUpload($rmsUploadId);
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
     * @param DeviceInstanceModel $deviceInstance
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
                if ($deviceInstance->getMasterDevice() instanceof MasterDeviceModel)
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
     * @param DeviceInstanceModel $deviceInstance
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
    }

    /**
     * @param DeviceInstanceModel $deviceInstance
     */
    protected function _sortUnmappedDevice ($deviceInstance)
    {
        $this->unmappedDeviceInstances->add($deviceInstance);
    }

    /**
     * @param DeviceInstanceModel $deviceInstance
     */
    protected function _sortExcludedDevice ($deviceInstance)
    {
        $this->excludedDeviceInstances->add($deviceInstance);
    }
}