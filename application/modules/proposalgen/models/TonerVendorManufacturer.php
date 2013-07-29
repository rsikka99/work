<?php
/**
 * Class Proposalgen_Model_TonerVendorManufacturer
 */
class Proposalgen_Model_TonerVendorManufacturer extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $manufacturerId;

    /**
     * @var string
     */
    protected $_manufacturerName;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->manufacturerId) && !is_null($params->manufacturerId))
        {
            $this->manufacturerId = $params->manufacturerId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "manufacturerId" => $this->manufacturerId,
        );
    }

    /**
     * @return string
     */
    public function getManufacturerName ()
    {
        if (!isset($this->_manufacturerName))
        {
            $this->_manufacturerName = Proposalgen_Model_Mapper_Manufacturer::getInstance()->find($this->manufacturerId)->fullname;
        }

        return $this->_manufacturerName;
    }

}