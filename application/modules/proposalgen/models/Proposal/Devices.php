<?php
class Proposalgen_Model_Proposal_Devices
{
    /**
     * @var Proposalgen_Model_DeviceInstance[]
     */
    public $allDeviceInstances = array();

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
     * @var Proposalgen_Model_Report
     */
    protected $_report;

    /**
     * @var bool
     */
    private $_devicesFetchedAndSorted = false;

    /**
     * Constructor
     *
     * @param Proposalgen_Model_Report $report
     */
    public function __construct (Proposalgen_Model_Report $report)
    {
        $this->_report = $report;
        $this->_fetchAndSortAllDevices($report->id);
    }

    /**
     * Fetches and sorts all of our device instances
     *
     * @param $reportId
     *
     * @return bool
     */
    public function _fetchAndSortAllDevices ($reportId)
    {
        if (!$this->_devicesFetchedAndSorted)
        {
            $this->allDeviceInstances = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->fetchAllForReport($reportId);

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
                        $deviceInstance->processOverrides($this->_report);
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
