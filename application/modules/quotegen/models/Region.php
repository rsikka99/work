<?php
class Quotegen_Model_Region extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $countryId;

    /**
     * @var string
     */
    public $region;

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

        if (isset($params->countryId) && ! is_null($params->countryId))
            $this->countryId = $params->countryId;

        if (isset($params->region) && ! is_null($params->region))
            $this->region = $params->region;

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array (
            "id" => $this->id,
            "countryId" => $this->countryId,
            "region" => $this->region,
        );
    }
}