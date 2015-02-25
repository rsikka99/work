<?php

namespace MPSToolbox\Legacy\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class DealerFeatureModel
 *
 * @package MPSToolbox\Legacy\Models
 */
class DealerFeatureModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $featureId;

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

        if (isset($params->featureId) && !is_null($params->featureId))
        {
            $this->featureId = $params->featureId;
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
            "featureId" => $this->featureId,
            "dealerId"  => $this->dealerId,
        ];
    }
}