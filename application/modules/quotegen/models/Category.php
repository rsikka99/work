<?php
class Quotegen_Model_Category extends My_Model_Abstract
{

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

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

        if (isset($params->description) && !is_null($params->description))
        {
            $this->description = $params->description;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"          => $this->id,
            "name"        => $this->name,
            "description" => $this->description,
        );
    }
}