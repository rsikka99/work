<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\LeasingSchemaRangeMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\LeasingSchemaTermMapper;
use My_Model_Abstract;

/**
 * Class LeasingSchemaRateModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class LeasingSchemaRateModel extends My_Model_Abstract
{

    /**
     * @var int
     */
    public $leasingSchemaTermId = 0;

    /**
     * @var int
     */
    public $leasingSchemaRangeId = 0;

    /**
     * @var int
     */
    public $rate = 0;

    /**
     * The term for the leasing schema rate
     *
     * @var LeasingSchemaTermModel
     */
    protected $_term;
    /**
     * The range for the leasing schema rate
     *
     * @var LeasingSchemaRangeModel
     */
    protected $_range;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->leasingSchemaTermId) && !is_null($params->leasingSchemaTermId))
        {
            $this->leasingSchemaTermId = $params->leasingSchemaTermId;
        }

        if (isset($params->leasingSchemaRangeId) && !is_null($params->leasingSchemaRangeId))
        {
            $this->leasingSchemaRangeId = $params->leasingSchemaRangeId;
        }

        if (isset($params->rate) && !is_null($params->rate))
        {
            $this->rate = $params->rate;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "leasingSchemaTermId"  => $this->leasingSchemaTermId,
            "leasingSchemaRangeId" => $this->leasingSchemaRangeId,
            "rate"                 => $this->rate,
        );
    }

    /**
     * Gets the term for the leasing schema rate
     *
     *
     */
    public function getTerm ()
    {
        if (!isset($this->_term))
        {
            $this->_term = LeasingSchemaTermMapper::getInstance()->find($this->leasingSchemaTermId);
        }

        return $this->_term;
    }

    /**
     * Sets the term for the leasing schema rate
     *
     * @param LeasingSchemaTermModel $_term
     *
     * @return $this
     */
    public function setTerm ($_term)
    {
        $this->_term = $_term;

        return $this;
    }

    /**
     * Gets the range for the leasing schema rate
     *
     * @return LeasingSchemaRangeModel
     */
    public function getRange ()
    {
        if (!isset($this->_range))
        {
            $this->_range = LeasingSchemaRangeMapper::getInstance()->find($this->leasingSchemaRangeId);
        }

        return $this->_range;
    }

    /**
     * Sets the range for the leasing schema rate
     *
     * @param LeasingSchemaRangeModel $_range
     *
     * @return $this
     */
    public function setRange ($_range)
    {
        $this->_range = $_range;

        return $this;
    }
}