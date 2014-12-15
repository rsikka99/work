<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\DeviceMapping;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsMasterMatchupMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;

/**
 * Class MasterMatchupMappingService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\DeviceMapping
 */
class MasterMatchupMappingService extends AbstractDeviceMappingService
{
    /**
     * @var RmsMasterMatchupMapper
     */
    protected $_rmsMasterMatchupMapper;

    /**
     * Default Constructor
     */
    public function __construct ()
    {
        $this->_rmsMasterMatchupMapper = RmsMasterMatchupMapper::getInstance();
    }

    /**
     * MAP IT™ attempts to find a suitable master device by using the master matchup table. If there is no entry in the
     * matchup table it will return FALSE®
     *
     * @param DeviceInstanceModel $deviceInstance
     *
     * @return bool|int The master device id, or false if it could not map
     */
    public function mapIt (DeviceInstanceModel $deviceInstance)
    {
        $masterDeviceId = false;
        $rmsProviderId  = $deviceInstance->getRmsUploadRow()->rmsProviderId;
        $rmsModelId     = $deviceInstance->getRmsUploadRow()->rmsModelId;

        $rmsMasterMatchup = $this->_rmsMasterMatchupMapper->find(array($rmsProviderId, $rmsModelId));
        if ($rmsMasterMatchup)
        {
            $masterDeviceId = $rmsMasterMatchup->masterDeviceId;
        }

        return $masterDeviceId;
    }
}