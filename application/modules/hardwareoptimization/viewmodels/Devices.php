<?php
/**
 * Class Hardwareoptimization_ViewModel_Devices
 */
class Hardwareoptimization_ViewModel_Devices
{
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
     * @var Hardwareoptimization_Model_Hardware_Optimization
     */
    protected $_hardwareOptimization;

    /**
     * @var bool
     */
    private $_devicesFetchedAndSorted = false;

    /**
     * Constructor
     *
     * @param Hardwareoptimization_Model_Hardware_Optimization $hardwareOptimization
     */
    public function __construct (Hardwareoptimization_Model_Hardware_Optimization $hardwareOptimization)
    {
        $this->_hardwareOptimization = $hardwareOptimization;
        $this->_fetchAndSortAllDevices($hardwareOptimization->getRmsUpload()->id);
    }

    /**
     * Fetches and sorts all of our device instances
     *
     * @param $rmsUploadId
     *
     * @return bool
     */
    public function _fetchAndSortAllDevices ($rmsUploadId)
    {
        if (!$this->_devicesFetchedAndSorted)
        {
            $this->allDeviceInstances = new Proposalgen_Model_DeviceInstancesGroup();

            $deviceInstances = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->fetchAllForRmsUpload($rmsUploadId);
            foreach ($deviceInstances as $device)
            {
                $this->allDeviceInstances->add($device);
            }

            $this->allIncludedDeviceInstances = new Proposalgen_Model_DeviceInstancesGroup();
            $this->excludedDeviceInstances    = new Proposalgen_Model_DeviceInstancesGroup();
            $this->leasedDeviceInstances      = new Proposalgen_Model_DeviceInstancesGroup();
            $this->purchasedDeviceInstances   = new Proposalgen_Model_DeviceInstancesGroup();
            $this->unmappedDeviceInstances    = new Proposalgen_Model_DeviceInstancesGroup();

            /*
             * Sort our devices into their categories
             */
            foreach ($this->allDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                /*
                 * Sort excluded devices
                 */
                if ($deviceInstance->isExcluded)
                {
                    $this->excludedDeviceInstances->add($deviceInstance);
                }
                else
                {
                    /*
                     * If we're here, it's not excluded. Further sorting is needed.
                     */
                    $masterDevice = $deviceInstance->getMasterDevice();
                    if ($masterDevice instanceof Proposalgen_Model_MasterDevice)
                    {
                        $this->allIncludedDeviceInstances->add($deviceInstance);
                        /*
                         * Sort leased and purchased devices
                         */
                        if ($masterDevice->isLeased)
                        {
                            $this->leasedDeviceInstances->add($deviceInstance);
                        }
                        else
                        {
                            $this->purchasedDeviceInstances->add($deviceInstance);
                        }

                        // Might as well process the overrides now too
                        $deviceInstance->processOverrides($this->_hardwareOptimization->getHardwareOptimizationSetting()->adminCostPerPage);
                    }
                    else
                    {
                        $this->unmappedDeviceInstances->add($deviceInstance);
                    }
                }
            }

            $this->_devicesFetchedAndSorted = true;
        }

        return $this->_devicesFetchedAndSorted;
    }
}