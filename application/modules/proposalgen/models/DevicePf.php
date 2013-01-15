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
     * @var int
     */
    public $pfDbDeviceName;

    /**
     * @var int
     */
    public $pfDbManufacturer;

    /**
     * @var int
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

        if (isset($params->DevicesPfId) && !is_null($params->DevicesPfId))
        {
            $this->devicesPfId = $params->DevicesPfId;
        }

        if (isset($params->PfModelId) && !is_null($params->PfModelId))
        {
            $this->pfModelId = $params->PfModelId;
        }

        if (isset($params->PfDbDeviceName) && !is_null($params->PfDbDeviceName))
        {
            $this->pfDbDeviceName = $params->PfDbDeviceName;
        }

        if (isset($params->PfDbManufacturer) && !is_null($params->PfDbManufacturer))
        {
            $this->pfDbManufacturer = $params->PfDbManufacturer;
        }

        if (isset($params->DateCreated) && !is_null($params->DateCreated))
        {
            $this->dateCreated = $params->DateCreated;
        }

        if (isset($params->CreatedBy) && !is_null($params->CreatedBy))
        {
            $this->createdBy = $params->CreatedBy;
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