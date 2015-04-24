<?php

namespace MPSToolbox\Legacy\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class FeatureModel
 *
 * @package MPSToolbox\Legacy\Models
 */
class FeatureModel extends My_Model_Abstract
{
    /**
     * The id of the feature
     *
     * @var string
     */
    public $id;

    /**
     * The name of the feature
     *
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
        return [
            'id'   => $this->id,
            'name' => $this->name,
        ];
    }
}