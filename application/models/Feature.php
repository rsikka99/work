<?php

/**
 * Class Application_Model_Feature
 */
class Application_Model_Feature extends My_Model_Abstract
{
    /**
     * The id of the feature
     *
     * @var string
     */
    public $id;

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
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            'id' => $this->id,
        );
    }
}