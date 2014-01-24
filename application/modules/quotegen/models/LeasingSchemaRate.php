<?php

/**
 * Class Quotegen_Model_LeasingSchemaRate
 */
class Quotegen_Model_LeasingSchemaRate extends My_Model_Abstract
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
     * @var Quotegen_Model_LeasingSchemaTerm
     */
    protected $_term;
    /**
     * The range for the leasing schema rate
     *
     * @var Quotegen_Model_LeasingSchemaRange
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
            $this->_term = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance()->find($this->leasingSchemaTermId);
        }

        return $this->_term;
    }

    /**
     * Sets the term for the leasing schema rate
     *
     * @param Quotegen_Model_LeasingSchemaTerm $_term
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
     * @return Quotegen_Model_LeasingSchemaRange
     */
    public function getRange ()
    {
        if (!isset($this->_range))
        {
            $this->_range = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance()->find($this->leasingSchemaRangeId);
        }

        return $this->_range;
    }

    /**
     * Sets the range for the leasing schema rate
     *
     * @param Quotegen_Model_LeasingSchemaRange $_range
     *
     * @return \Quotegen_Model_LeasingSchemaRate
     */
    public function setRange ($_range)
    {
        $this->_range = $_range;

        return $this;
    }
}