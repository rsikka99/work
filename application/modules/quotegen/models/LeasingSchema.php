<?php
class Quotegen_Model_LeasingSchema extends My_Model_Abstract
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
     * @var array
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
     * @return Quotegen_Model_LeasingSchemaTerm[]
     */
    public function getTerms ()
    {
        if (!isset($this->_terms))
        {
            $this->_terms = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance()->fetchAllForLeasingSchema($this->id);
        }

        return $this->_terms;
    }

    /**
     * Sets all terms for leasing schema
     *
     * @param Quotegen_Form_LeasingSchemaTerm[] $_terms
     *
     * @return Quotegen_Model_LeasingSchema
     */
    public function setTerms ($_terms)
    {
        $this->_terms = $_terms;

        return $this;
    }

    /**
     * Gets all ranges for leasing schema
     *
     * @return Quotegen_Model_LeasingSchemaRange[]
     */
    public function getRanges ()
    {
        if (!isset($this->_ranges))
        {
            $this->_ranges = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance()->fetchAllForLeasingSchema($this->id);
        }

        return $this->_ranges;
    }

    /**
     * Sets all ranges for leasing schema
     *
     * @param multitype: $_ranges
     *
     * @return Quotegen_Model_LeasingSchema
     */
    public function setRanges ($_ranges)
    {
        $this->_ranges = $_ranges;

        return $this;
    }

    /**
     * Gets all rates for leasing schema
     *
     * @return array|\multitype 2 dimensional array rates.
     * @internal param $
     */
    public function getRates ()
    {
        if (!isset($this->_rates))
        {
            $this->_rates = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance()->fetchAllForLeasingSchema($this->id);
        }

        return $this->_rates;
    }

    /**
     * Sets all rates for leasing schema
     *
     * @param multitype: $_rates
     *            2 dimensional array rates.
     *                 First key is term id.
     *                 Second key is range id.
     *
     * @return Quotegen_Model_LeasingSchema
     */
    public function setRates ($_rates)
    {
        $this->_rates = $_rates;

        return $this;
    }
}
