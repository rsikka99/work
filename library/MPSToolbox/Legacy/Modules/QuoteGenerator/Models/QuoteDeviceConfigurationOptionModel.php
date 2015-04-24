<?php
namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceOptionMapper;
use My_Model_Abstract;

/**
 * Class QuoteDeviceConfigurationOptionModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class QuoteDeviceConfigurationOptionModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $quoteDeviceOptionId = 0;

    /**
     * @var int
     */
    public $optionId = 0;

    /**
     * @var int
     */
    public $masterDeviceId;

    /**
     * The option associated with this configuration
     *
     * @var OptionModel
     */
    protected $_deviceOption;

    /**
     * The quote device option associated with this configuration
     *
     * @var QuoteDeviceOptionModel
     */
    protected $_quoteDeviceOption;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->quoteDeviceOptionId) && !is_null($params->quoteDeviceOptionId))
        {
            $this->quoteDeviceOptionId = $params->quoteDeviceOptionId;
        }

        if (isset($params->optionId) && !is_null($params->optionId))
        {
            $this->optionId = $params->optionId;
        }

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "quoteDeviceOptionId" => $this->quoteDeviceOptionId,
            "optionId"            => $this->optionId,
            "masterDeviceId"      => $this->masterDeviceId,
        ];
    }

    /**
     * Gets the option associated with the device configuration option
     *
     * @return DeviceOptionModel
     */
    public function getDeviceOption ()
    {
        if (!isset($this->_deviceOption))
        {
            $this->_deviceOption = DeviceOptionMapper::getInstance()->find([
                $this->masterDeviceId,
                $this->optionId,
            ]);
        }

        return $this->_deviceOption;
    }

    /**
     * Sets the option associated with the device configuration option
     *
     * @param DeviceOptionModel $_deviceOption
     *
     * @return $this
     */
    public function setDeviceOption ($_deviceOption)
    {
        $this->_deviceOption = $_deviceOption;

        return $this;
    }

    /**
     * Gets the quote device option
     *
     * @return QuoteDeviceOptionModel
     */
    public function getQuoteDeviceOption ()
    {
        if (!isset($this->_quoteDeviceOption))
        {
            $this->_quoteDeviceOption = QuoteDeviceOptionMapper::getInstance()->find($this->quoteDeviceOptionId);
        }

        return $this->_quoteDeviceOption;
    }

    /**
     * Sets the quote device option
     *
     * @param QuoteDeviceOptionModel $_quoteDeviceOption
     *            The new quote device option
     *
     * @return $this
     */
    public function setQuoteDeviceOption ($_quoteDeviceOption)
    {
        $this->_quoteDeviceOption = $_quoteDeviceOption;

        return $this;
    }
}