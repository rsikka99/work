<?php
class Quotegen_Model_Address extends My_Model_Abstract
{

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $clientId;

    /**
     * @var string
     */
    public $addressLine1;

    /**
     * @var string
     */
    public $addressLine2;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $region;

    /**
     * @var string
     */
    public $postCode;

    /**
     * @var int
     */
    public $countryId;



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

        if (isset($params->clientId) && ! is_null($params->clientId))
            $this->clientId = $params->clientId;

        if (isset($params->addressLine1) && ! is_null($params->addressLine1))
            $this->addressLine1 = $params->addressLine1;

        if (isset($params->addressLine2) && ! is_null($params->addressLine2))
            $this->addressLine2 = $params->addressLine2;

        if (isset($params->city) && ! is_null($params->city))
            $this->city = $params->city;

        if (isset($params->region) && ! is_null($params->region))
            $this->region = $params->region;

        if (isset($params->postCode) && ! is_null($params->postCode))
            $this->postCode = $params->postCode;

        if (isset($params->countryId) && ! is_null($params->countryId))
            $this->countryId = $params->countryId;

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array (
            "id" => $this->id,
            "clientId" => $this->clientId,
            "addressLine1" => $this->addressLine1,
            "addressLine2" => $this->addressLine2,
            "city" => $this->city,
            "region" => $this->region,
            "postCode" => $this->postCode,
            "countryId" => $this->countryId,
        );
    }

    /*
     * Gets the countries name
     * @return string
     */
    public function getCountry ()
    {
        return Quotegen_Model_Mapper_Country::getInstance()->find($this->countryId)
            ->name;
    }

    /**Gets a the region and then gets the name
     *
     * @return string
     */
    public function getRegionName(){
        return Quotegen_Model_Mapper_Region::getInstance()->getById($this->region)->region;
    }

    /**
     * This gets all the address fields in line
     *
     * @return string all the address information combined
     */
    public function getFullAddressOneLine ()
    {
        $address = "{$this->addressLine1}";
        if (strlen($this->addressLine2)>0)
        {
            $address .= " {$this->addressLine2}, ";
        }
        else
        {
            $address .= ", ";
        }
        
        $address .= "{$this->city}, {$this->getRegionName()} {$this->postCode}";
        return $address;
    }

    /**
     * This gets all the address fields with line breaks
     *
     * @return string all the address information combined
     */
    public function getFullAddressMultipleLines ()
    {
                $address = "{$this->addressLine1}";
        if ($this->addressLine2)
        {
            $address .= "\n{$this->addressLine2}\n";
        }
        else
        {
            $address .= "\n";
        }
        
        $address .= "{$this->city}, {$this->getRegionName()}\n {$this->postCode}";
        return $address;
    }

    public function __toString()
    {
        return $this->getFullAddressMultipleLines();
    }
}