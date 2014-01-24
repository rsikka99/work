<?php

/**
 * Class Hardwareoptimization_Model_Hardware_Optimization_Quote
 */
class Hardwareoptimization_Model_Hardware_Optimization_Quote extends My_Model_Abstract
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