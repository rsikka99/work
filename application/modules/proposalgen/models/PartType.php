<?php
/**
 * Class Proposalgen_Model_PartType
 */
class Proposalgen_Model_PartType extends My_Model_Abstract
{
    const OEM  = 1;
    const COMP = 2;

    /**
     * An array of part type names with the id as the key
     *
     * @var string[]
     */
    static $PartTypeNames = array(
        self::OEM  => "OEM",
        self::COMP => "Compatible"
    );

    /**
     * @var int
     */
    public $partTypeId;

    /**
     * @var string
     */
    public $typeName;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->partTypeId) && !is_null($params->partTypeId))
        {
            $this->partTypeId = $params->partTypeId;
        }

        if (isset($params->typeName) && !is_null($params->typeName))
        {
            $this->typeName = $params->typeName;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "partTypeId" => $this->partTypeId,
            "typeName"   => $this->typeName,
        );
    }
}