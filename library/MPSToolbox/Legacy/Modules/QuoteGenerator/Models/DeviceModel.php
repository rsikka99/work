<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionMapper;
use My_Model_Abstract;

/**
 * Class DeviceModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class DeviceModel extends My_Model_Abstract
{
    /**
     * @var bool
     */
    public $isSelling;

    /**
     * @var int
     */
    public $masterDeviceId;

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var string
     */
    public $oemSku;

    /**
     * @var string
     */
    public $dealerSku;

    /**
     * @var string
     */
    public $description;

    /**
     * @var MasterDeviceModel
     */
    protected $masterDevice;

    /**
     * @var float
     */
    public $cost;

    /**
     * @var float
     */
    public $sellPrice;

    /**
     * @var float
     */
    public $rent;

    /**
     * @var int
     */
    public $pagesPerMonth;

    /**
     * @var float
     */
    public $additionalCpp;

    /**
     * @var string
     */
    public $dataSheetUrl;

    /**
     * @var string
     */
    public $reviewsUrl;

    /**
     * @var boolean
     */
    public $online;

    /**
     * @var string
     */
    public $onlineDescription;

    public $rent_values;
    public $plan_values;
    public $plan_page_values;
    public $tags;
    public $taxable;

    /**
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

        if (isset($params->isSelling) && !is_null($params->isSelling))
        {
            $this->isSelling = $params->isSelling;
        }

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->oemSku) && !is_null($params->oemSku))
        {
            $this->oemSku = $params->oemSku;
        }

        if (isset($params->dealerSku) && !is_null($params->dealerSku))
        {
            $this->dealerSku = $params->dealerSku;
        }

        if (isset($params->description) && !is_null($params->description))
        {
            $this->description = $params->description;
        }

        if (isset($params->cost) && !is_null($params->cost))
        {
            $this->cost = $params->cost;
        }

        if (isset($params->sellPrice) && !is_null($params->sellPrice))
        {
            $this->sellPrice = $params->sellPrice;
        }

        if (isset($params->rent) && !is_null($params->rent)) $this->rent = $params->rent;
        if (isset($params->pagesPerMonth) && !is_null($params->pagesPerMonth)) $this->pagesPerMonth = $params->pagesPerMonth;
        if (isset($params->dataSheetUrl) && !is_null($params->dataSheetUrl)) $this->dataSheetUrl = $params->dataSheetUrl;
        if (isset($params->reviewsUrl) && !is_null($params->reviewsUrl)) $this->reviewsUrl = $params->reviewsUrl;
        if (isset($params->online) && !is_null($params->online)) $this->online = $params->online;
        if (isset($params->onlineDescription) && !is_null($params->onlineDescription)) $this->onlineDescription = $params->onlineDescription;
        if (isset($params->additionalCpp) && !is_null($params->additionalCpp)) $this->additionalCpp = $params->additionalCpp;
        if (isset($params->rent_values) && !is_null($params->rent_values)) $this->rent_values = $params->rent_values;
        if (isset($params->plan_values) && !is_null($params->plan_values)) $this->plan_values = $params->plan_values;
        if (isset($params->plan_page_values) && !is_null($params->plan_page_values)) $this->plan_page_values = $params->plan_page_values;
        if (isset($params->tags) && !is_null($params->tags)) $this->tags = $params->tags;
        if (isset($params->taxable) && !is_null($params->taxable)) $this->taxable = $params->taxable;
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "isSelling"      => $this->isSelling,
            "masterDeviceId" => $this->masterDeviceId,
            "dealerId"       => $this->dealerId,
            "oemSku"         => $this->oemSku,
            "dealerSku"      => $this->dealerSku,
            "description"    => $this->description,
            "cost"           => $this->cost,
            "sellPrice"           => $this->sellPrice,
            "rent"           => $this->rent,
            "pagesPerMonth"  => $this->pagesPerMonth,
            "dataSheetUrl"   => $this->dataSheetUrl,
            "reviewsUrl"     => $this->reviewsUrl,
            "online"         => $this->online,
            "onlineDescription" => $this->onlineDescription,
            'additionalCpp'     => $this->additionalCpp,
            'rent_values'       => is_array($this->rent_values)?json_encode($this->rent_values):$this->rent_values,
            'plan_values'       => is_array($this->plan_values)?json_encode($this->plan_values):$this->plan_values,
            'plan_page_values'  => $this->plan_page_values,
            'tags'              => $this->tags,
            'taxable'          => $this->taxable,
        ];
    }

    /**
     * Gets the master device object associated with this device
     *
     * @return MasterDeviceModel
     */
    public function getMasterDevice ()
    {
        if (!isset($this->_masterDevice))
        {
            $this->_masterDevice = MasterDeviceMapper::getInstance()->find($this->masterDeviceId);
        }

        return $this->_masterDevice;
    }

    /**
     * Sets a new master device id for the object
     *
     * @param number $_masterDeviceId
     *            The new master device id to set
     *
     * @return DeviceModel
     */
    public function setMasterDeviceId ($_masterDeviceId)
    {
        $this->_masterDeviceId = $_masterDeviceId;

        return $this;
    }

    /**
     * Get the array of options for the device
     *
     * @return DeviceOptionModel[]
     */
    public function getDeviceOptions ()
    {
        if (!isset($this->_options))
        {
            $this->_options = OptionMapper::getInstance()->fetchAllDeviceOptionsForDevice($this->masterDeviceId);
        }

        return $this->_options;
    }

    /**
     * Set a new array of options for the device
     *
     * @param DeviceOptionModel[] $_options
     *
     * @return DeviceModel
     */
    public function setOptions ($_options)
    {
        $this->_options = $_options;

        return $this;
    }

    /**
     * Saves the current object
     *
     * @return $this
     */
    public function saveObject ()
    {
        // Do we have an instance of it in our database?
        $quoteDeviceMapper = DeviceMapper::getInstance();

        if ($quoteDeviceMapper->find([$this->masterDeviceId, $this->dealerId]))
        {
            $quoteDeviceMapper->save($this);
        }
        else
        {
            $quoteDeviceMapper->insert($this);
        }

        return $this;
    }
}