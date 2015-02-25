<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use Exception;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionMapper;
use My_Model_Abstract;

/**
 * Class DeviceConfigurationOptionModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class DeviceConfigurationOptionModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $deviceConfigurationId = 0;

    /**
     * @var int
     */
    public $optionId = 0;

    /**
     * @var int
     */
    public $quantity = 0;

    /**
     * @var int
     */
    public $includedQuantity = 0;

    /**
     * The option associated with this configuration
     *
     * @var OptionModel
     */
    protected $_option;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->deviceConfigurationId) && !is_null($params->deviceConfigurationId))
        {
            $this->deviceConfigurationId = $params->deviceConfigurationId;
        }

        if (isset($params->optionId) && !is_null($params->optionId))
        {
            $this->optionId = $params->optionId;
        }

        if (isset($params->quantity) && !is_null($params->quantity))
        {
            $this->quantity = $params->quantity;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "deviceConfigurationId" => $this->deviceConfigurationId,
            "optionId"              => $this->optionId,
            "quantity"              => $this->quantity,
        ];
    }

    /**
     * Gets the option associated with the device configuration option
     *
     * @return OptionModel
     */
    public function getOption ()
    {
        if (!isset($this->_option))
        {
            $this->_option = OptionMapper::getInstance()->find($this->optionId);
        }

        return $this->_option;
    }

    /**
     * Sets the option associated with the device configuration option
     *
     * @param OptionModel $_option
     *
     * @return DeviceConfigurationOptionModel
     */
    public function setOption ($_option)
    {
        $this->_option = $_option;

        return $this;
    }

    /**
     * Inserts or saves the object to the database.
     *
     * @param null|array $data
     *
     * @return bool
     * @throws Exception
     */
    public function saveObject ($data = null)
    {
        $successful = false;
        if (is_array($data))
        {
            $this->populate($data);
        }

        if (!isset($this->deviceConfigurationId) || !isset($this->optionId))
        {
            throw new Exception("Option missing required data. Please try again.");
        }

        $deviceConfigurationOptionMapper = DeviceConfigurationOptionMapper::getInstance();

        $deviceConfigurationOption = $deviceConfigurationOptionMapper->fetch($deviceConfigurationOptionMapper->getWhereId([$this->deviceConfigurationId, $this->optionId]));
        try
        {
            if ($deviceConfigurationOption instanceof DeviceConfigurationOptionModel)
            {
                $deviceConfigurationOptionMapper->save($this);
            }
            else
            {
                $deviceConfigurationOptionMapper->insert($this);
            }
            $successful = true;
        }
        catch (Exception $e)
        {

        }

        return $successful;
    }
}