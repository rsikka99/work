<?php
/**
 * Class Quotegen_Model_LeasingSchemaRange
 */
class Quotegen_Model_LeasingSchemaRange extends My_Model_Abstract
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
     * @var Quotegen_Model_LeasingSchema
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
        return array(
            "id"              => $this->id,
            "leasingSchemaId" => $this->leasingSchemaId,
            "startRange"      => $this->startRange,
        );
    }

    /**
     * Gets the leasing schema
     *
     * @return Quotegen_Model_LeasingSchema
     */
    public function getLeasingSchema ()
    {
        if (!isset($this->_leasingSchema))
        {
            $this->_leasingSchema = Quotegen_Model_Mapper_LeasingSchema::getInstance()->find($this->leasingSchemaId);
        }

        return $this->_leasingSchema;
    }

    /**
     * Sets the leasing schema
     *
     * @param Quotegen_Model_LeasingSchema $_leasingSchema
     *
     * @return Quotegen_Model_LeasingSchemaRange
     */
    public function setLeasingSchema ($_leasingSchema)
    {
        $this->_leasingSchema = $_leasingSchema;

        return $this;
    }
}