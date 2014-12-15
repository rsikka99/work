<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\LeasingSchemaTermForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\LeasingSchemaRangeMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\LeasingSchemaRateMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\LeasingSchemaTermMapper;
use My_Model_Abstract;

/**
 * Class LeasingSchemaModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class LeasingSchemaModel extends My_Model_Abstract
{

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var string
     */
    public $name;

    /**
     * All terms
     *
     * @var array
     */
    protected $_terms;

    /**
     * All ranges
     *
     * @var LeasingSchemaRangeModel[]
     */
    protected $_ranges;

    /**
     * 2 dimensional array rates.
     * First key is term id.
     * Second key is range id.
     *
     * @var array
     */
    protected $_rates;

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

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"       => $this->id,
            "dealerId" => $this->dealerId,
            "name"     => $this->name,
        );
    }

    /**
     * Validates the leasing schema
     */
    public function isValid ()
    {
    }

    /**
     * Gets all terms for leasing schema
     *
     * @return LeasingSchemaTermModel[]
     */
    public function getTerms ()
    {
        if (!isset($this->_terms))
        {
            $this->_terms = LeasingSchemaTermMapper::getInstance()->fetchAllForLeasingSchema($this->id);
        }

        return $this->_terms;
    }

    /**
     * Sets all terms for leasing schema
     *
     * @param LeasingSchemaTermForm[] $_terms
     *
     * @return LeasingSchemaModel
     */
    public function setTerms ($_terms)
    {
        $this->_terms = $_terms;

        return $this;
    }

    /**
     * Gets all ranges for leasing schema
     *
     * @return LeasingSchemaRangeModel[]
     */
    public function getRanges ()
    {
        if (!isset($this->_ranges))
        {
            $this->_ranges = LeasingSchemaRangeMapper::getInstance()->fetchAllForLeasingSchema($this->id);
        }

        return $this->_ranges;
    }

    /**
     * Sets all ranges for leasing schema
     *
     * @param LeasingSchemaRangeModel[] $_ranges
     *
     * @return $this
     */
    public function setRanges ($_ranges)
    {
        $this->_ranges = $_ranges;

        return $this;
    }

    /**
     * Gets all rates for leasing schema
     *
     * @return LeasingSchemaRateModel[]
     */
    public function getRates ()
    {
        if (!isset($this->_rates))
        {
            $this->_rates = LeasingSchemaRateMapper::getInstance()->fetchAllForLeasingSchema($this->id);
        }

        return $this->_rates;
    }

    /**
     * Sets all rates for leasing schema
     *
     * @param LeasingSchemaRateModel[] $_rates 2 dimensional array rates. First key is term id. Second key is range id.
     *
     * @return $this
     */
    public function setRates ($_rates)
    {
        $this->_rates = $_rates;

        return $this;
    }
}
