<?php
/**
 * Class Hardwareoptimization_ViewModel_Devices
 */
class Hardwareoptimization_ViewModel_Devices
{
    /**
     * @var Proposalgen_Model_DeviceInstance[]
     */
    public $allDeviceInstances = array();


    /**
     * @var Proposalgen_Model_DeviceInstance[]
     */
    public $allIncludedDeviceInstances = array();

    /**
     * @var Proposalgen_Model_DeviceInstance[]
     */
    public $excludedDeviceInstances = array();

    /**
     * @var Proposalgen_Model_DeviceInstance[]
     */
    public $leasedDeviceInstances = array();

    /**
     * @var Proposalgen_Model_DeviceInstance[]
     */
    public $purchasedDeviceInstances = array();

    /**
     * @var Proposalgen_Model_DeviceInstance[]
     */
    public $unmappedDeviceInstances = array();

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
        $this->_hardwareOptimization                                      = $hardwareOptimization;
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

            $this->allDeviceInstances = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->fetchAllForRmsUpload($rmsUploadId);

            /*
             * Sort our devices into their categories
             */
            foreach ($this->allDeviceInstances as $deviceInstance)
            {
                /*
                 * Sort excluded devices
                 */
                if ($deviceInstance->isExcluded)
                {
                    $this->excludedDeviceInstances[] = $deviceInstance;
                }
                else
                {
                    /*
                     * If we're here, it's not excluded. Further sorting is needed.
                     */
                    $masterDevice = $deviceInstance->getMasterDevice();
                    if ($masterDevice instanceof Proposalgen_Model_MasterDevice)
                    {
                        $this->allIncludedDeviceInstances[] = $deviceInstance;
                        /*
                         * Sort leased and purchased devices
                         */
                        if ($masterDevice->isLeased)
                        {
                            $this->leasedDeviceInstances[] = $deviceInstance;
                        }
                        else
                        {
                            $this->purchasedDeviceInstances[] = $deviceInstance;
                        }

                        // Might as well process the overrides now too
                        $deviceInstance->processOverrides($this->_hardwareOptimization->getHardwareOptimizationSetting()->adminCostPerPage);
                    }
                    else
                    {
                        $this->unmappedDeviceInstances[] = $deviceInstance;
                    }
                }

            }

            $this->_devicesFetchedAndSorted = true;
        }

        return $this->_devicesFetchedAndSorted;
    }
}