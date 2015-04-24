<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class RmsUserMatchupModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class RmsUserMatchupModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $rmsProviderId;

    /**
     * @var string
     */
    public $rmsModelId;

    /**
     * @var int
     */
    public $masterDeviceId;

    /**
     * @var int
     */
    public $userId;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->rmsProviderId) && !is_null($params->rmsProviderId))
        {
            $this->rmsProviderId = $params->rmsProviderId;
        }

        if (isset($params->rmsModelId) && !is_null($params->rmsModelId))
        {
            $this->rmsModelId = $params->rmsModelId;
        }

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "rmsProviderId"  => $this->rmsProviderId,
            "rmsModelId"     => $this->rmsModelId,
            "masterDeviceId" => $this->masterDeviceId,
            "userId"         => $this->userId,
        ];
    }
}