<?php

/**
 * Class Proposalgen_Model_Manufacturer
 */
class Proposalgen_Model_Manufacturer extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $fullname;

    /**
     * @var string
     */
    public $displayname;

    /**
     * @var boolean
     */
    public $isDeleted;


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

        if (isset($params->fullname) && !is_null($params->fullname))
        {
            $this->fullname = $params->fullname;
        }

        if (isset($params->displayname) && !is_null($params->displayname))
        {
            $this->displayname = $params->displayname;
        }

        if (isset($params->isDeleted) && !is_null($params->isDeleted))
        {
            $this->isDeleted = $params->isDeleted;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"          => $this->id,
            "fullname"    => $this->fullname,
            "displayname" => $this->displayname,
            "isDeleted"   => $this->isDeleted,
        );
    }
}