<?php
/**
 * Class Proposalgen_Model_Map_Device_Instance
 */
class Proposalgen_Model_Map_Device_Instance extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $rmsProviderId;

    /**
     * @var int
     */
    public $rmsModelId;

    /**
     * @var int
     */
    public $manufacturer;

    /**
     * @var int
     */
    public $modelName;

    /**
     * @var int
     */
    public $useUserData;

    /**
     * @var int
     */
    public $reportId;

    /**
     * @var int
     */
    public $masterDeviceId;

    /**
     * @var int
     */
    public $isMapped;

    /**
     * @var int
     */
    public $mappedManufacturer;

    /**
     * @var int
     */
    public $mappedModelName;

    /**
     * @var int
     */
    public $deviceCount;

    /**
     * @var int
     */
    public $deviceInstanceIds;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->rmsProviderId) && !is_null($params->rmsProviderId))
        {
            $this->rmsProviderId = $params->rmsProviderId;
        }

        if (isset($params->rmsModelId) && !is_null($params->rmsModelId))
        {
            $this->rmsModelId = $params->rmsModelId;
        }

        if (isset($params->manufacturer) && !is_null($params->manufacturer))
        {
            $this->manufacturer = $params->manufacturer;
        }

        if (isset($params->modelName) && !is_null($params->modelName))
        {
            $this->modelName = $params->modelName;
        }

        if (isset($params->useUserData) && !is_null($params->useUserData))
        {
            $this->useUserData = $params->useUserData;
        }

        if (isset($params->reportId) && !is_null($params->reportId))
        {
            $this->reportId = $params->reportId;
        }

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->isMapped) && !is_null($params->isMapped))
        {
            $this->isMapped = $params->isMapped;
        }

        if (isset($params->mappedManufacturer) && !is_null($params->mappedManufacturer))
        {
            $this->mappedManufacturer = $params->mappedManufacturer;
        }

        if (isset($params->mappedModelName) && !is_null($params->mappedModelName))
        {
            $this->mappedModelName = $params->mappedModelName;
        }

        if (isset($params->deviceCount) && !is_null($params->deviceCount))
        {
            $this->deviceCount = $params->deviceCount;
        }

        if (isset($params->deviceInstanceIds) && !is_null($params->deviceInstanceIds))
        {
            $this->deviceInstanceIds = $params->deviceInstanceIds;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "rmsProviderId"      => $this->rmsProviderId,
            "rmsModelId"         => $this->rmsModelId,
            "manufacturer"       => $this->manufacturer,
            "modelName"          => $this->modelName,
            "useUserData"        => $this->useUserData,
            "reportId"           => $this->reportId,
            "masterDeviceId"     => $this->masterDeviceId,
            "isMapped"           => $this->isMapped,
            "mappedManufacturer" => $this->mappedManufacturer,
            "mappedModelName"    => $this->mappedModelName,
            "deviceCount"        => $this->deviceCount,
            "deviceInstanceIds"  => $this->deviceInstanceIds,
        );
    }
}