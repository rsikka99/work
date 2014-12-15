<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class TemplateModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class TemplateModel extends My_Model_Abstract
{
    /**
     * @var int
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
            "id" => $this->id,

        );
    }
}