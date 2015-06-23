<?php
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstancesGroupModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;

/**
 * Class Healthcheck_ViewModel_Devices
 */
class Healthcheck_ViewModel_Devices extends Assessment_ViewModel_Devices
{

    /**
     * @var DeviceInstancesGroupModel
     */
    public $a3DeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $faxAndScanDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $reportingTonerLevelsDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $isManagedDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $unmanagedDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $managedByThirdPartyDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $compatibleDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $notCompatibleDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $scanCapableDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $copyCapableDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $duplexCapableDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $faxCapableDeviceInstances;

    /**
     * @var DeviceInstancesGroupModel
     */
    public $colorCapableDeviceInstances;

    /**
     * @param int   $rmsUploadId
     */
    public function __construct ($rmsUploadId)
    {
        $this->a3DeviceInstances                   = new DeviceInstancesGroupModel();
        $this->reportingTonerLevelsDeviceInstances = new DeviceInstancesGroupModel();
        $this->faxAndScanDeviceInstances           = new DeviceInstancesGroupModel();
        $this->isManagedDeviceInstances            = new DeviceInstancesGroupModel();
        $this->unmanagedDeviceInstances            = new DeviceInstancesGroupModel();
        $this->managedByThirdPartyDeviceInstances  = new DeviceInstancesGroupModel();
        $this->compatibleDeviceInstances           = new DeviceInstancesGroupModel();
        $this->notCompatibleDeviceInstances        = new DeviceInstancesGroupModel();
        $this->scanCapableDeviceInstances          = new DeviceInstancesGroupModel();
        $this->copyCapableDeviceInstances          = new DeviceInstancesGroupModel();
        $this->duplexCapableDeviceInstances        = new DeviceInstancesGroupModel();
        $this->faxCapableDeviceInstances           = new DeviceInstancesGroupModel();
        $this->colorCapableDeviceInstances         = new DeviceInstancesGroupModel();

        parent::__construct($rmsUploadId);
    }


    /**
     * @param DeviceInstanceModel $deviceInstance
     */
    protected function _sortMappedDevice ($deviceInstance)
    {
        /**
         * If we're here, it's not excluded. Further sorting is needed.
         */
        if ($deviceInstance->getMasterDevice() instanceof MasterDeviceModel)
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

            if ($deviceInstance->getMasterDevice()->tonerConfigId != TonerConfigModel::BLACK_ONLY)
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