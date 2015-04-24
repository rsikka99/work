<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class CategoryModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class CategoryModel extends My_Model_Abstract
{

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $dealerId = 0;

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

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
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
        return [
            "id"          => $this->id,
            "dealerId"    => $this->dealerId,
            "name"        => $this->name,
            "description" => $this->description,
        ];
    }
}