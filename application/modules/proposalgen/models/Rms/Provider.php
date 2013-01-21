<?php
class Proposalgen_Model_Rms_Provider extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;


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
            "id"   => $this->id,
            "name" => $this->name,
        );
    }
}