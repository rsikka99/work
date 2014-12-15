<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class RmsMasterMatchupModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class RmsMasterMatchupModel extends My_Model_Abstract
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

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "rmsProviderId"  => $this->rmsProviderId,
            "rmsModelId"     => $this->rmsModelId,
            "masterDeviceId" => $this->masterDeviceId,
        );
    }
}