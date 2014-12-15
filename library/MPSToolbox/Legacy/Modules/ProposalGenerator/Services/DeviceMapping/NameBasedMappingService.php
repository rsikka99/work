<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\DeviceMapping;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;

/**
 * Class NameBasedMappingService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\DeviceMapping
 */
class NameBasedMappingService extends AbstractDeviceMappingService
{
    /**
     * @var ManufacturerMapper
     */
    protected $_manufacturerMapper;

    /**
     * Default Constructor
     */
    public function __construct ()
    {
        $this->_manufacturerMapper = ManufacturerMapper::getInstance();
        $this->_masterDeviceMapper = MasterDeviceMapper::getInstance();
    }

    /**
     * MAP IT™ attempts to find a suitable master device by using name matching techniques with the manufacturer and modelName. If it
     * cannot find a match it will return FALSE®
     *
     * @param DeviceInstanceModel $deviceInstance
     *
     * @return bool|int The master device id, or false if it could not map
     */
    public function mapIt (DeviceInstanceModel $deviceInstance)
    {
        $masterDeviceId = false;

        /*
         * If we have a manufacturer we can refine our search
         */
        $manufacturerId = ($deviceInstance->getRmsUploadRow()->manufacturerId > 0) ? $deviceInstance->getRmsUploadRow()->manufacturerId : null;

        $masterDevices = $this->_masterDeviceMapper->searchByModelName($deviceInstance->getRmsUploadRow()->modelName, $manufacturerId, false);
        if (count($masterDevices) === 1)
        {
            $masterDeviceId = $masterDevices[0]->id;
        }

        return $masterDeviceId;
    }
}