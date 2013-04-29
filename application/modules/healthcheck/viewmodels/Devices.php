<?php
class Healthcheck_ViewModel_Devices
{
    /**
     * @var Healthcheck_Model_DeviceInstancesGroup
     */
    public $allDeviceInstances;


    /**
     * @var Healthcheck_Model_DeviceInstancesGroup
     */
    public $allIncludedDeviceInstances;

    /**
     * @var Healthcheck_Model_DeviceInstancesGroup
     */
    public $excludedDeviceInstances;

    /**
     * @var Healthcheck_Model_DeviceInstancesGroup
     */
    public $leasedDeviceInstances;

    /**
     * @var Healthcheck_Model_DeviceInstancesGroup
     */
    public $purchasedDeviceInstances;

    /**
     * @var Healthcheck_Model_DeviceInstancesGroup
     */
    public $unmappedDeviceInstances;


    /**
     * @var Healthcheck_Model_Healthcheck
     */
    protected $_healthcheck;

    /**
     * @var bool
     */
    private $_devicesFetchedAndSorted = false;

    /**
     * Constructor
     *
     * @param Healthcheck_Model_Healthcheck $healthcheck
     */
    public function __construct (Healthcheck_Model_Healthcheck $healthcheck)
    {
        $this->_healthcheck                                     = $healthcheck;
        Proposalgen_Model_MasterDevice::$ReportLaborCostPerPage = $healthcheck->getHealthcheckSettings()->laborCostPerPage;
        Proposalgen_Model_MasterDevice::$ReportPartsCostPerPage = $healthcheck->getHealthcheckSettings()->partsCostPerPage;
        $this->_fetchAndSortAllDevices($healthcheck->getRmsUpload()->id);
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
            $this->allDeviceInstances = new Healthcheck_Model_DeviceInstancesGroup();

            $deviceInstances          = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->fetchAllForRmsUpload($rmsUploadId);
            foreach ($deviceInstances as $device)
            {
                $this->allDeviceInstances->add($device);
            }

            $this->allIncludedDeviceInstances = new Healthcheck_Model_DeviceInstancesGroup();
            $this->excludedDeviceInstances    = new Healthcheck_Model_DeviceInstancesGroup();
            $this->leasedDeviceInstances      = new Healthcheck_Model_DeviceInstancesGroup();
            $this->purchasedDeviceInstances   = new Healthcheck_Model_DeviceInstancesGroup();
            $this->unmappedDeviceInstances    = new Healthcheck_Model_DeviceInstancesGroup();

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
                        $deviceInstance->processOverrides($this->_healthcheck->getHealthcheckSettings()->adminCostPerPage);
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