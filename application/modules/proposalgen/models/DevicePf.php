<?php
class Proposalgen_Model_DevicePf extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $devicesPfId;

    /**
     * @var int
     */
    public $pfModelId;

    /**
     * @var string
     */
    public $pfDbDeviceName;

    /**
     * @var string
     */
    public $pfDbManufacturer;

    /**
     * @var string
     */
    public $dateCreated;

    /**
     * @var int
     */
    public $createdBy;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->devicesPfId) && !is_null($params->devicesPfId))
        {
            $this->devicesPfId = $params->devicesPfId;
        }

        if (isset($params->pfModelId) && !is_null($params->pfModelId))
        {
            $this->pfModelId = $params->pfModelId;
        }

        if (isset($params->pfDbDeviceName) && !is_null($params->pfDbDeviceName))
        {
            $this->pfDbDeviceName = $params->pfDbDeviceName;
        }

        if (isset($params->pfDbManufacturer) && !is_null($params->pfDbManufacturer))
        {
            $this->pfDbManufacturer = $params->pfDbManufacturer;
        }

        if (isset($params->dateCreated) && !is_null($params->dateCreated))
        {
            $this->dateCreated = $params->dateCreated;
        }

        if (isset($params->createdBy) && !is_null($params->createdBy))
        {
            $this->createdBy = $params->createdBy;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "devicesPfId"      => $this->devicesPfId,
            "pfModelId"        => $this->pfModelId,
            "pfDbDeviceName"   => $this->pfDbDeviceName,
            "pfDbManufacturer" => $this->pfDbManufacturer,
            "dateCreated"      => $this->dateCreated,
            "createdBy"        => $this->createdBy,
        );
    }
}