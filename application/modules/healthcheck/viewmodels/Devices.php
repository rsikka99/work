<?php
class Healthcheck_ViewModel_Devices
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
     * @var Healthcheck_Model_Healthcehck
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
        $this->_healthcheck                                      = $healthcheck;
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
                        $deviceInstance->processOverrides($this->_healthcheck->getHealthcheckSettings()->adminCostPerPage);
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