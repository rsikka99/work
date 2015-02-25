<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorManufacturerMapper;
use My_Model_Abstract;

/**
 * Class ManufacturerModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class ManufacturerModel extends My_Model_Abstract
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
     * @var bool
     */
    protected $_isTonerVendor;


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
        return [
            "id"          => $this->id,
            "fullname"    => $this->fullname,
            "displayname" => $this->displayname,
            "isDeleted"   => $this->isDeleted,
        ];
    }

    public function isTonerVendor ()
    {
        if (!isset($this->_isTonerVendor))
        {
            $this->_isTonerVendor = (TonerVendorManufacturerMapper::getInstance()->find($this->id) instanceof TonerVendorManufacturerModel);
        }

        return $this->_isTonerVendor;
    }
}