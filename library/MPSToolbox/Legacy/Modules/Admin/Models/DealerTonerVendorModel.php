<?php

namespace MPSToolbox\Legacy\Modules\Admin\Models;

use ArrayObject;
use MPSToolbox\Legacy\Mappers\DealerMapper;
use MPSToolbox\Legacy\Models\DealerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\ManufacturerModel;
use My_Model_Abstract;

/**
 * Class DealerTonerVendorModel
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Models
 */
class DealerTonerVendorModel extends My_Model_Abstract
{
    /**
     * The id of the dealer
     *
     * @var int
     */
    public $dealerId;

    /**
     * The id of the manufacturer
     *
     * @var int
     */
    public $manufacturerId;

    /**
     * @var DealerModel
     */
    protected $_dealer;

    /**
     * @var ManufacturerModel
     */
    protected $_manufacturer;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
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
            "dealerId"       => $this->dealerId,
            "manufacturerId" => $this->manufacturerId,
        );
    }

    /**
     * Gets the dealer
     *
     * @return DealerModel
     */
    public function getDealer ()
    {
        if (!isset($this->_dealer))
        {
            $this->_dealer = DealerMapper::getInstance()->find($this->dealerId);
        }

        return $this->_dealer;
    }

    /**
     * Sets the dealer
     *
     * @param DealerModel $dealer
     *
     * @return $this
     */
    public function setDealer ($dealer)
    {
        $this->_dealer = $dealer;

        return $this;
    }

    /**
     * Gets the manufacturer
     *
     * @return ManufacturerModel
     */
    public function getManufacturer ()
    {
        if (!isset($this->_manufacturer))
        {
            $this->_manufacturer = ManufacturerMapper::getInstance()->find($this->manufacturerId);
        }

        return $this->_manufacturer;
    }

    /**
     * Sets the manufacturer
     *
     * @param ManufacturerModel $manufacturer
     *
     * @return $this
     */
    public function setManufacturer ($manufacturer)
    {
        $this->_manufacturer = $manufacturer;

        return $this;
    }
}