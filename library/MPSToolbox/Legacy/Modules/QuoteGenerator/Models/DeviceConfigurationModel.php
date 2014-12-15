<?php
namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionMapper;
use My_Model_Abstract;

/**
 * Class DeviceConfigurationModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class DeviceConfigurationModel extends My_Model_Abstract
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
     * @var int
     */
    public $masterDeviceId = 0;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * The quote device associated with this configuration
     *
     * @var DeviceModel
     */
    protected $_device;

    /**
     * The options added to the configuration
     *
     * @var DeviceOptionModel[]
     */
    protected $_options;

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

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }

        if (isset($params->description) && !is_null($params->description))
        {
            $this->description = $params->description;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"             => $this->id,
            "dealerId"       => $this->dealerId,
            "masterDeviceId" => $this->masterDeviceId,
            "name"           => $this->name,
            "description"    => $this->description,
        );
    }

    /**
     * Gets the quote device associated with this configuration
     *
     * @return DeviceModel
     */
    public function getDevice ()
    {
        if (!isset($this->_device))
        {
            $this->_device = DeviceMapper::getInstance()->find(array($this->masterDeviceId, $this->dealerId));
        }

        return $this->_device;
    }

    /**
     * Sets the quote device associated with this configuration
     *
     * @param DeviceModel $_device
     *
     * @return DeviceConfigurationModel
     */
    public function setDevice ($_device)
    {
        $this->_device = $_device;

        return $this;
    }

    /**
     * Get the array of options for the device
     *
     * @return DeviceConfigurationOptionModel[]
     */
    public function getOptions ()
    {
        if (!isset($this->_options))
        {
            $this->_options = OptionMapper::getInstance()->fetchAllOptionsForDeviceConfiguration($this->id);
        }

        return $this->_options;
    }

    /**
     * Set a new array of options for the device
     *
     * @param DeviceOptionModel[] $_options
     *
     * @return DeviceConfigurationModel
     */
    public function setOptions ($_options)
    {
        $this->_options = $_options;

        return $this;
    }

}