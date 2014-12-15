<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceMasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\DeviceMapping\MasterMatchupMappingService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\DeviceMapping\NameBasedMappingService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\DeviceMapping\UserMatchupMappingService;

/**
 * Class DeviceMappingService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services
 */
class DeviceMappingService
{
    /**
     * @var UserMatchupMappingService
     */
    protected $_userMatchupBasedMappingService;

    /**
     * @var MasterMatchupMappingService
     */
    protected $_masterMatchupBasedMappingService;

    /**
     * @var NameBasedMappingService
     */
    protected $_nameBasedMappingService;

    /**
     * @var DeviceInstanceMasterDeviceMapper
     */
    protected $_deviceInstanceMasterDeviceMapper;


    /**
     * The default constructor. Initializes the various mapping services
     */
    public function __construct ()
    {
        $this->_userMatchupBasedMappingService   = new UserMatchupMappingService();
        $this->_masterMatchupBasedMappingService = new MasterMatchupMappingService();
        $this->_nameBasedMappingService          = new NameBasedMappingService();
        $this->_deviceInstanceMasterDeviceMapper = DeviceInstanceMasterDeviceMapper::getInstance();
    }

    /**
     * Loops through the given MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel[] and attempts to MAP IT™ if the device is not already mapped.
     *
     * @param DeviceInstanceModel[] $deviceInstances
     * @param int                   $userId
     * @param bool                  $useNameBasedMapping
     */
    public function mapDevices ($deviceInstances, $userId, $useNameBasedMapping = false)
    {
        foreach ($deviceInstances as $deviceInstance)
        {
            if (!$deviceInstance->useUserData)
            {
                if (!$deviceInstance->getIsMappedToMasterDevice())
                {
                    /*
                     * No Mapping, lets MAP IT™!
                     */
                    $this->_mapIt($deviceInstance, $userId, $useNameBasedMapping);
                }
            }
        }
    }

    /**
     * MAP IT™ takes a MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel and try to MAP IT™ to a MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel. A MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel
     * must have have a MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadRowModel attached to it that has an rmsModelId in order for User or Master matching to work.
     * If a MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel does not have a rmsModelId and TRUE® was passed to $useNamedBasedMapping it will use the
     * manufacturer and modelName to attempt mapping the MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel. Please note that named based mapping requires there to be only a
     * single MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel result when we look it up, otherwise it will be up to the user to MAP IT™ themselves.
     *
     * @param DeviceInstanceModel $deviceInstance
     * @param int                 $userId
     * @param bool                $useNameBasedMapping
     *
     * @return bool Returns whether or not the device got mapped
     */
    protected function _mapIt (DeviceInstanceModel $deviceInstance, $userId, $useNameBasedMapping)
    {
        $mapDeviceToMasterDeviceId = false;
        $deviceInstance->id;
        $rmsUploadRow = $deviceInstance->getRmsUploadRow();

        $hasRmsModelId = (strlen($rmsUploadRow->rmsModelId) > 0);

        if ($hasRmsModelId)
        {
            // User Based
            $mapDeviceToMasterDeviceId = $this->_userMatchupBasedMappingService->mapIt($deviceInstance, $userId);


            if (!$mapDeviceToMasterDeviceId)
            {
                // Master Based
                $mapDeviceToMasterDeviceId = $this->_masterMatchupBasedMappingService->mapIt($deviceInstance);
            }
        }

        if (!$mapDeviceToMasterDeviceId && $useNameBasedMapping)
        {
            // Named based
            $mapDeviceToMasterDeviceId = $this->_nameBasedMappingService->mapIt($deviceInstance);
        }

        if ($mapDeviceToMasterDeviceId !== false && $mapDeviceToMasterDeviceId > 0)
        {
            $this->_mapDeviceToMasterDevice($deviceInstance, $mapDeviceToMasterDeviceId);
        }

        return $mapDeviceToMasterDeviceId;
    }

    /**
     * Maps a device instance to a master device
     *
     * @param DeviceInstanceModel $deviceInstance The device instance
     * @param int                 $masterDeviceId The id of the master device
     *
     * @return int
     */
    protected function _mapDeviceToMasterDevice ($deviceInstance, $masterDeviceId)
    {
        $deviceInstanceMasterDevice                   = new DeviceInstanceMasterDeviceModel();
        $deviceInstanceMasterDevice->deviceInstanceId = $deviceInstance->id;
        $deviceInstanceMasterDevice->masterDeviceId   = $masterDeviceId;

        $result = $this->_deviceInstanceMasterDeviceMapper->insert($deviceInstanceMasterDevice);

        return $result;
    }
}