<?php
class Proposalgen_Model_Rms_Excluded_Row extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $reportId;

    /**
     * @var int
     */
    public $rmsProviderId;

    /**
     * @var int
     */
    public $rmsModelId;

    /**
     * @var string
     */
    public $serialNumber;

    /**
     * @var string
     */
    public $ipAddress;

    /**
     * @var string
     */
    public $modelName;

    /**
     * @var string
     */
    public $manufacturerName;

    /**
     * @var string
     */
    public $reason;


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

        if (isset($params->reportId) && !is_null($params->reportId))
        {
            $this->reportId = $params->reportId;
        }

        if (isset($params->rmsProviderId) && !is_null($params->rmsProviderId))
        {
            $this->rmsProviderId = $params->rmsProviderId;
        }

        if (isset($params->rmsModelId) && !is_null($params->rmsModelId))
        {
            $this->rmsModelId = $params->rmsModelId;
        }

        if (isset($params->serialNumber) && !is_null($params->serialNumber))
        {
            $this->serialNumber = $params->serialNumber;
        }

        if (isset($params->ipAddress) && !is_null($params->ipAddress))
        {
            $this->ipAddress = $params->ipAddress;
        }

        if (isset($params->modelName) && !is_null($params->modelName))
        {
            $this->modelName = $params->modelName;
        }

        if (isset($params->manufacturerName) && !is_null($params->manufacturerName))
        {
            $this->manufacturerName = $params->manufacturerName;
        }

        if (isset($params->reason) && !is_null($params->reason))
        {
            $this->reason = $params->reason;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"               => $this->id,
            "reportId"         => $this->reportId,
            "rmsProviderId"    => $this->rmsProviderId,
            "rmsModelId"       => $this->rmsModelId,
            "serialNumber"     => $this->serialNumber,
            "ipAddress"        => $this->ipAddress,
            "modelName"        => $this->modelName,
            "manufacturerName" => $this->manufacturerName,
            "reason"           => $this->reason,
        );
    }
}