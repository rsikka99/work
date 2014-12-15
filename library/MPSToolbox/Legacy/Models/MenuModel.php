<?php

namespace MPSToolbox\Legacy\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class MenuModel
 *
 * @package MPSToolbox\Legacy\Models
 */
class MenuModel extends My_Model_Abstract
{

    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;

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
            $this->setId($params->id);
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            'id' => $this->getId()
        );
    }

    /**
     * Gets the id of the object
     *
     * @return number The id of the object
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     * Sets the id of the object
     *
     * @param number $_id
     *            the new id
     */
    public function setId ($_id)
    {
        $this->_id = $_id;
    }
}
