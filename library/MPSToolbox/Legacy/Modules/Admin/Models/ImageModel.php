<?php

namespace MPSToolbox\Legacy\Modules\Admin\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class ImageModel
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Models
 */
class ImageModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $image;

    /**
     * @var int
     */
    public $filename;


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

        if (isset($params->image) && !is_null($params->image))
        {
            $this->image = $params->image;
        }

        if (isset($params->filename) && !is_null($params->filename))
        {
            $this->filename = $params->filename;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            'id'       => $this->id,
            'image'    => $this->image,
            'filename' => $this->filename,
        ];
    }
}