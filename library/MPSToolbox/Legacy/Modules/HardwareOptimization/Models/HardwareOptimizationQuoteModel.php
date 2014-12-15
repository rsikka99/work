<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class HardwareOptimizationQuoteModel
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Models
 */
class HardwareOptimizationQuoteModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $hardwareOptimizationId;
    /**
     * @var int
     */
    public $quoteId;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->hardwareOptimizationId) && !is_null($params->hardwareOptimizationId))
        {
            $this->hardwareOptimizationId = $params->hardwareOptimizationId;
        }
        if (isset($params->quoteId) && !is_null($params->quoteId))
        {
            $this->quoteId = $params->quoteId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "hardwareOptimizationId" => $this->hardwareOptimizationId,
            "quoteId"                => $this->quoteId,
        );
    }

}