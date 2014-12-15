<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use My_Model_Abstract;

/**
 * Class TonerVendorManufacturerModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class TonerVendorManufacturerModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $manufacturerId;

    /**
     * @var string
     */
    protected $_manufacturerName;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->manufacturerId) && !is_null($params->manufacturerId))
        {
            $this->manufacturerId = $params->manufacturerId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "manufacturerId" => $this->manufacturerId,
        );
    }

    /**
     * @return string
     */
    public function getManufacturerName ()
    {
        if (!isset($this->_manufacturerName))
        {
            $this->_manufacturerName = ManufacturerMapper::getInstance()->find($this->manufacturerId)->fullname;
        }

        return $this->_manufacturerName;
    }
}