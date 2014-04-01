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

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $faxAndScanDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $reportingTonerLevelsDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $isManagedDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $unmanagedDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $managedByThirdPartyDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $compatibleDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $notCompatibleDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $scanCapableDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $copyCapableDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $duplexCapableDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $faxCapableDeviceInstances;

    /**
     * @var Proposalgen_Model_DeviceInstancesGroup
     */
    public $colorCapableDeviceInstances;

    public function __construct ($rmsUploadId, $laborCostPerPage, $partsCostPerPage, $adminCostPerPage)
    {
        $this->a3DeviceInstances                   = new Proposalgen_Model_DeviceInstancesGroup();
        $this->reportingTonerLevelsDeviceInstances = new Proposalgen_Model_DeviceInstancesGroup();
        $this->faxAndScanDeviceInstances           = new Proposalgen_Model_DeviceInstancesGroup();
        $this->isManagedDeviceInstances            = new Proposalgen_Model_DeviceInstancesGroup();
        $this->unmanagedDeviceInstances            = new Proposalgen_Model_DeviceInstancesGroup();
        $this->managedByThirdPartyDeviceInstances  = new Proposalgen_Model_DeviceInstancesGroup();
        $this->compatibleDeviceInstances           = new Proposalgen_Model_DeviceInstancesGroup();
        $this->notCompatibleDeviceInstances        = new Proposalgen_Model_DeviceInstancesGroup();
        $this->scanCapableDeviceInstances          = new Proposalgen_Model_DeviceInstancesGroup();
        $this->copyCapableDeviceInstances          = new Proposalgen_Model_DeviceInstancesGroup();
        $this->duplexCapableDeviceInstances        = new Proposalgen_Model_DeviceInstancesGroup();
        $this->faxCapableDeviceInstances           = new Proposalgen_Model_DeviceInstancesGroup();
        $this->colorCapableDeviceInstances         = new Proposalgen_Model_DeviceInstancesGroup();

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

            if ($deviceInstance->getMasterDevice()->isFax || $deviceInstance->getMasterDevice()->isCopier)
            {
                $this->faxAndScanDeviceInstances->add($deviceInstance);
            }

            if ($deviceInstance->getMasterDevice()->isCopier)
            {
                $this->scanCapableDeviceInstances->add($deviceInstance);
            }

            if ($deviceInstance->getMasterDevice()->isMfp())
            {
                $this->copyCapableDeviceInstances->add($deviceInstance);
            }

            if ($deviceInstance->getMasterDevice()->isDuplex)
            {
                $this->duplexCapableDeviceInstances->add($deviceInstance);
            }

            if ($deviceInstance->getMasterDevice()->isFax)
            {
                $this->faxCapableDeviceInstances->add($deviceInstance);
            }

            if ($deviceInstance->getMasterDevice()->tonerConfigId != Proposalgen_Model_TonerConfig::BLACK_ONLY)
            {
                $this->colorCapableDeviceInstances->add($deviceInstance);
            }
        }

        if ($deviceInstance->isCapableOfReportingTonerLevels)
        {
            $this->reportingTonerLevelsDeviceInstances->add($deviceInstance);
        }

        if ($deviceInstance->isManaged)
        {
            $this->isManagedDeviceInstances->add($deviceInstance);
        }
        else if (!$deviceInstance->isLeased)
        {
            $this->unmanagedDeviceInstances->add($deviceInstance);

            if ($deviceInstance->compatibleWithJitProgram)
            {
                $this->compatibleDeviceInstances->add($deviceInstance);
            }
            else
            {
                $this->notCompatibleDeviceInstances->add($deviceInstance);
            }
        }
        else
        {
            $this->managedByThirdPartyDeviceInstances->add($deviceInstance);
        }

        parent::_sortMappedDevice($deviceInstance);
    }
}