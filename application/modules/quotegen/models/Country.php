<?php
/**
 * Class Quotegen_Model_Country
 */
class Quotegen_Model_Country extends My_Model_Abstract
{
    const COUNTRY_CANADA        = 1;
    const COUNTRY_UNITED_STATES = 2;
    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $name;

    /**
     * @var int
     */
    public $locale;


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

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }

        if (isset($params->locale) && !is_null($params->locale))
        {
            $this->locale = $params->locale;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"     => $this->id,
            "name"   => $this->name,
            "locale" => $this->locale,
        );
    }
}