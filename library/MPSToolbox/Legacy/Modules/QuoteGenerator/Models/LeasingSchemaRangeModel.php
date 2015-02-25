<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\LeasingSchemaMapper;
use My_Model_Abstract;

/**
 * Class LeasingSchemaRangeModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class LeasingSchemaRangeModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $leasingSchemaId = 0;

    /**
     * @var int
     */
    public $startRange = 0;

    /**
     * Leasing Schema
     *
     * @var LeasingSchemaModel
     */
    protected $_leasingSchema;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->id) && !is_null($params->id))
        {
            $this->id = $params->id;
        }

        if (isset($params->leasingSchemaId) && !is_null($params->leasingSchemaId))
        {
            $this->leasingSchemaId = $params->leasingSchemaId;
        }

        if (isset($params->startRange) && !is_null($params->startRange))
        {
            $this->startRange = $params->startRange;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "id"              => $this->id,
            "leasingSchemaId" => $this->leasingSchemaId,
            "startRange"      => $this->startRange,
        ];
    }

    /**
     * Gets the leasing schema
     *
     * @return LeasingSchemaModel
     */
    public function getLeasingSchema ()
    {
        if (!isset($this->_leasingSchema))
        {
            $this->_leasingSchema = LeasingSchemaMapper::getInstance()->find($this->leasingSchemaId);
        }

        return $this->_leasingSchema;
    }

    /**
     * Sets the leasing schema
     *
     * @param LeasingSchemaModel $_leasingSchema
     *
     * @return $this
     */
    public function setLeasingSchema ($_leasingSchema)
    {
        $this->_leasingSchema = $_leasingSchema;

        return $this;
    }
}