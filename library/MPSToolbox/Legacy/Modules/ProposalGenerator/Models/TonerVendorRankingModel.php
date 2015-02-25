<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class TonerVendorRankingModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class TonerVendorRankingModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $manufacturerId;
    /**
     * @var int
     */
    public $tonerVendorRankingSetId;
    /**
     * @var int
     */
    public $rank;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->manufacturerId) && !is_null($params->manufacturerId))
        {
            $this->manufacturerId = $params->manufacturerId;
        }
        if (isset($params->rank) && !is_null($params->rank))
        {
            $this->rank = $params->rank;
        }
        if (isset($params->tonerVendorRankingSetId) && !is_null($params->tonerVendorRankingSetId))
        {
            $this->tonerVendorRankingSetId = $params->tonerVendorRankingSetId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "manufacturerId"          => $this->manufacturerId,
            "rank"                    => $this->rank,
            "tonerVendorRankingSetId" => $this->tonerVendorRankingSetId,
        ];
    }
}