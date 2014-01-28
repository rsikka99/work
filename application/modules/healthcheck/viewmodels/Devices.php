<?php

/**
 * Class Healthcheck_ViewModel_Devices
 */
class Healthcheck_ViewModel_Devices extends Assessment_ViewModel_Devices
{

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $a3DeviceInstances;

    public function __construct ($rmsUploadId, $laborCostPerPage, $partsCostPerPage, $adminCostPerPage)
    {
        $this->a3DeviceInstances = new Proposalgen_Model_DeviceInstancesGroup();

        parent::__construct($rmsUploadId, $laborCostPerPage, $partsCostPerPage, $adminCostPerPage);
    }


    /**
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     */
    protected function _sortMappedDevice ($deviceInstance)
    {
        /**
         * If we're here, it's not excluded. Further sorting is needed.
         */
        if ($deviceInstance->getMasterDevice() instanceof Proposalgen_Model_MasterDevice)
        {
            if ($deviceInstance->getMasterDevice()->isA3)
            {
                $this->a3DeviceInstances->add($deviceInstance);
            }
        }

        parent::_sortMappedDevice($deviceInstance);
    }
}