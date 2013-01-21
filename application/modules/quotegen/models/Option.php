<?php
class Quotegen_Model_Option extends My_Model_Abstract
{

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var double
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
     * An array of Quotegen_Model_Category associated with the object
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

        if (isset($params->id) && ! is_null($params->id))
            $this->id = $params->id;

        if (isset($params->name) && ! is_null($params->name))
            $this->name = $params->name;

        if (isset($params->description) && ! is_null($params->description))
            $this->description = $params->description;

        if (isset($params->cost) && ! is_null($params->cost))
            $this->cost = $params->cost;

        if (isset($params->oemSku) && ! is_null($params->oemSku))
            $this->oemSku = $params->oemSku;

        if (isset($params->dealerSku) && ! is_null($params->dealerSku))
            $this->dealerSku = $params->dealerSku;

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array (
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "cost" => $this->cost,
            "oemSku" => $this->oemSku,
            "dealerSku" => $this->dealerSku,
        );
    }

    /**
     * Gets all categories associated with this objects id
     *
     * @return Quotegen_Model_Category $_categories
     */
    public function getCategories ()
    {
        if (! isset($this->_categories))
        {
            $this->_categories = Quotegen_Model_Mapper_OptionCategory::getInstance()->fetchAllCategoriesForOption($this->id);
        }
        return $this->_categories;
    }

    /**
     * Set new categories for this object
     *
     * @param multitype: $_categories
     *            The new array of categories
     */
    public function setCategories ($_categories)
    {
        $this->_categories = $_categories;
        return $this;
    }

    /**
     * Gets the included quantity for a device option
     *
     * @return int $includedQuantity
     */
    public function getIncludedQuantity ($deviceId)
    {
        if (! isset($this->_includedQuantity))
        {
            $includedQuantity = Quotegen_Model_Mapper_DeviceOption::getInstance()->fetch("masterDeviceId = {$deviceId} AND optionid = {$this->id}");
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