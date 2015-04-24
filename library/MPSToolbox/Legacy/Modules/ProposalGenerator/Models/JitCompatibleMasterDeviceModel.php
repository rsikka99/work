<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class JitCompatibleMasterDeviceModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class JitCompatibleMasterDeviceModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $masterDeviceId;

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "masterDeviceId" => $this->masterDeviceId,
            "dealerId"       => $this->dealerId,
        ];
    }
}