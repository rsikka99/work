<?php

/**
 * Class Memjetoptimization_Model_Memjet_Optimization_Quote
 */
class Memjetoptimization_Model_Memjet_Optimization_Quote extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $memjetOptimizationId;
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
        if (isset($params->memjetOptimizationId) && !is_null($params->memjetOptimizationId))
        {
            $this->memjetOptimizationId = $params->memjetOptimizationId;
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
            "memjetOptimizationId" => $this->memjetOptimizationId,
            "quoteId"              => $this->quoteId,
        );
    }

}