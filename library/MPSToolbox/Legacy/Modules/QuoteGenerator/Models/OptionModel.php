<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionCategoryMapper;
use My_Model_Abstract;

/**
 * Class OptionModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class OptionModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var float
     */
    public $cost;

    /**
     * @var string
     */
    public $oemSku;

    /**
     * @var string
     */
    public $dealerSku;

    /**
     * An array of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CategoryModel associated with the object
     *
     * @var array
     */
    protected $_categories;

    /**
     * Returns this options included quantity for a device
     *
     * @var int
     */
    protected $_includedQuantity;

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

        if (isset($params->cost) && !is_null($params->cost))
        {
            $this->cost = $params->cost;
        }

        if (isset($params->oemSku) && !is_null($params->oemSku))
        {
            $this->oemSku = $params->oemSku;
        }

        if (isset($params->dealerSku) && !is_null($params->dealerSku))
        {
            $this->dealerSku = $params->dealerSku;
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
            "cost"        => $this->cost,
            "oemSku"      => $this->oemSku,
            "dealerSku"   => $this->dealerSku,
        ];
    }

    /**
     * Gets all categories associated with this objects id
     *
     * @return CategoryModel[]
     */
    public function getCategories ()
    {
        if (!isset($this->_categories))
        {
            $this->_categories = OptionCategoryMapper::getInstance()->fetchAllCategoriesForOption($this->id);
        }

        return $this->_categories;
    }

    /**
     * Set new categories for this object
     *
     * @param CategoryModel[] $_categories The new array of categories
     *
     * @return $this
     */
    public function setCategories ($_categories)
    {
        $this->_categories = $_categories;

        return $this;
    }

    /**
     * Gets the included quantity for a device option
     *
     * @param $deviceId
     *
     * @return int $includedQuantity
     */
    public function getIncludedQuantity ($deviceId)
    {
        if (!isset($this->_includedQuantity))
        {
            $includedQuantity = DeviceOptionMapper::getInstance()->fetch("masterDeviceId = {$deviceId} AND optionid = {$this->id}");
            if ($includedQuantity)
            {
                $this->_includedQuantity = $includedQuantity->includedQuantity;
            }
            else
            {
                $this->_includedQuantity = 0;
            }
        }

        return $this->_includedQuantity;
    }
}