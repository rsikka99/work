<?php
class Proposalgen_Service_DeviceMapping_UserMatchup extends Proposalgen_Service_DeviceMapping_Abstract
{
    /**
     * @var Proposalgen_Model_Mapper_Rms_User_Matchup
     */
    protected $_rmsUserMatchupMapper;

    /**
     * Default Constructor
     */
    public function __construct ()
    {
        $this->_rmsUserMatchupMapper = Proposalgen_Model_Mapper_Rms_User_Matchup::getInstance();
    }

    /**
     * MAP IT™ attempts to find a suitable master device by using the user matchup table. If there is no entry in the
     * matchup table it will return FALSE®
     *
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     * @param int                              $userId
     *
     * @return bool|
     */
    public function mapIt (Proposalgen_Model_DeviceInstance $deviceInstance, $userId)
    {
        $isMapped      = false;
        $rmsProviderId = $deviceInstance->getRmsUploadRow()->rmsProviderId;
        $rmsModelId    = $deviceInstance->getRmsUploadRow()->rmsModelId;

        $rmsUserMatchup = $this->_rmsUserMatchupMapper->find(array($rmsProviderId, $rmsModelId, $userId));
        if ($rmsUserMatchup)
        {
            $isMapped = $rmsUserMatchup->masterDeviceId;
        }

        return $isMapped;
    }
}