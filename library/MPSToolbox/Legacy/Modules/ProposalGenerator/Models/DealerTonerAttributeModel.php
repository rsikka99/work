<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class DealerTonerAttributeModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class DealerTonerAttributeModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $tonerId;

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var Float
     */
    public $cost;

    /**
     * @var string
     */
    public $dealerSku;

    /**
     * @var string
     */
    public $dealerSrp;

    /** @var float */
    public $level1;
    /** @var float */
    public $level2;
    /** @var float */
    public $level3;
    /** @var float */
    public $level4;
    /** @var float */
    public $level5;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->tonerId) && !is_null($params->tonerId))
        {
            $this->tonerId = $params->tonerId;
        }

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->cost) && !is_null($params->cost))
        {
            $this->cost = $params->cost;
        }

        if (isset($params->dealerSku) && !is_null($params->dealerSku))
        {
            $this->dealerSku = $params->dealerSku;
        }

        if (isset($params->dealerSrp) && !is_null($params->dealerSrp)) $this->dealerSrp = $params->dealerSrp;

        if (isset($params->level1) && !is_null($params->level1)) $this->level1 = $params->level1;
        if (isset($params->level2) && !is_null($params->level2)) $this->level2 = $params->level2;
        if (isset($params->level3) && !is_null($params->level3)) $this->level3 = $params->level3;
        if (isset($params->level4) && !is_null($params->level4)) $this->level4 = $params->level4;
        if (isset($params->level5) && !is_null($params->level5)) $this->level5 = $params->level5;

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "tonerId"   => $this->tonerId,
            "dealerId"  => $this->dealerId,
            "cost"      => $this->cost,
            "dealerSku" => $this->dealerSku,
            "dealerSrp" => $this->dealerSrp,
            "level1" => $this->level1,
            "level2" => $this->level2,
            "level3" => $this->level3,
            "level4" => $this->level4,
            "level5" => $this->level5,
        ];
    }
}