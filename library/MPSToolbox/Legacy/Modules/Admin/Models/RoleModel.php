<?php

namespace MPSToolbox\Legacy\Modules\Admin\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class RoleModel
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Models
 */
class RoleModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $name;

    /**
     * @var int
     */
    public $systemRole;


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

        if (isset($params->systemRole) && !is_null($params->systemRole))
        {
            $this->systemRole = $params->systemRole;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'systemRole' => $this->systemRole,
        ];
    }
}