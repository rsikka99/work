<?php

namespace MPSToolbox\Legacy\Modules\Admin\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class LogTypeModel
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Models
 */
class LogTypeModel extends My_Model_Abstract
{
    /**
     * The log type id
     *
     * @var number
     */
    protected $_id;

    /**
     * The log type name
     *
     * @var string
     */
    protected $_name;

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

        if (isset($params->name) && !is_null($params->name))
        {
            $this->setName($params->name);
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"   => $this->getId(),
            "name" => $this->getName()
        );
    }

    /**
     * Getter for $_id
     *
     * @return number
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     * Setter for $_id
     *
     * @param number $_id
     *            The new value
     *
     * @return $this
     */
    public function setId ($_id)
    {
        $this->_id = $_id;

        return $this;
    }

    /**
     * Getter for $_name
     *
     * @return string
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * Setter for $_name
     *
     * @param string $_name
     *            The new value
     *
     * @return $this
     */
    public function setName ($_name)
    {
        $this->_name = $_name;

        return $this;
    }
}
