<?php

/**
 * Class Admin_Model_Image
 */
class Admin_Model_Image extends My_Model_Abstract
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
        return array(
            "id"       => $this->id,
            "image"    => $this->image,
            "filename" => $this->filename,
        );
    }
}