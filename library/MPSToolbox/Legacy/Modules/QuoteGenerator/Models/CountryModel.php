<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class CountryModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class CountryModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $country_id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $iso_alpha2;

    /**
     * @var string
     */
    public $iso_alpha3;

    /**
     * @var int
     */
    public $iso_numeric;

    /**
     * @var string
     */
    public $currency_code;

    /**
     * @var string
     */
    public $currency_name;

    /**
     * @var string
     */
    public $currency_symbol;

    /**
     * @var string
     */
    public $flag;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->country_id) && !is_null($params->country_id))
        {
            $this->country_id = $params->country_id;
        }

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }

        if (isset($params->iso_alpha2) && !is_null($params->iso_alpha2))
        {
            $this->iso_alpha2 = $params->iso_alpha2;
        }

        if (isset($params->iso_alpha3) && !is_null($params->iso_alpha3))
        {
            $this->iso_alpha3 = $params->iso_alpha3;
        }

        if (isset($params->iso_numeric) && !is_null($params->iso_numeric))
        {
            $this->iso_numeric = $params->iso_numeric;
        }

        if (isset($params->currency_code) && !is_null($params->currency_code))
        {
            $this->currency_code = $params->currency_code;
        }

        if (isset($params->currency_name) && !is_null($params->currency_name))
        {
            $this->currency_name = $params->currency_name;
        }

        if (isset($params->currency_symbol) && !is_null($params->currency_symbol))
        {
            $this->currency_symbol = $params->currency_symbol;
        }

        if (isset($params->flag) && !is_null($params->flag))
        {
            $this->flag = $params->flag;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "country_id"      => $this->country_id,
            "name"            => $this->name,
            "iso_alpha2"      => $this->iso_alpha2,
            "iso_alpha3"      => $this->iso_alpha3,
            "iso_numeric"     => $this->iso_numeric,
            "currency_code"   => $this->currency_code,
            "currency_name"   => $this->currency_name,
            "currency_symbol" => $this->currency_symbol,
            "flag"            => $this->flag,
        ];
    }
}