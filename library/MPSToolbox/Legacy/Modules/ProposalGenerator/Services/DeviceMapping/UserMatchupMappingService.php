<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\DeviceMapping;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsUserMatchupMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;

/**
 * Class UserMatchupMappingService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\DeviceMapping
 */
class UserMatchupMappingService extends AbstractDeviceMappingService
{
    /**
     * @var RmsUserMatchupMapper
     */
    protected $_rmsUserMatchupMapper;

    /**
     * Default Constructor
     */
    public function __construct ()
    {
        $this->_rmsUserMatchupMapper = RmsUserMatchupMapper::getInstance();
    }

    /**
     * MAP IT™ attempts to find a suitable master device by using the user matchup table. If there is no entry in the
     * matchup table it will return FALSE®
     *
     * @param DeviceInstanceModel $deviceInstance
     * @param int                 $userId
     *
     * @return bool|int The master device id, or false if it could not map
     */
    public function mapIt (DeviceInstanceModel $deviceInstance, $userId)
    {
        $masterDeviceId = false;
        $rmsProviderId  = $deviceInstance->getRmsUploadRow()->rmsProviderId;
        $rmsModelId     = $deviceInstance->getRmsUploadRow()->rmsModelId;

        $rmsUserMatchup = $this->_rmsUserMatchupMapper->find(array($rmsProviderId, $rmsModelId, $userId));
        if ($rmsUserMatchup)
        {
            $masterDeviceId = $rmsUserMatchup->masterDeviceId;
        }

        return $masterDeviceId;
    }
}