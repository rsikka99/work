<?php
class Hardwareoptimization_Model_Hardware_Optimization extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $clientId;

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var int
     */
    public $rmsUploadId;

    /**
     * @var int
     */
    public $hardwareOptimizationSettingId;

    /**
     * @var int
     */
    public $name;

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

        if (isset($params->clientId) && !is_null($params->clientId))
        {
            $this->clientId = $params->clientId;
        }
        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->rmsUploadId) && !is_null($params->rmsUploadId))
        {
            $this->rmsUploadId = $params->rmsUploadId;
        }

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }
        if (isset($params->hardwareOptimizationSettingId) && !is_null($params->hardwareOptimizationSettingId))
        {
            $this->hardwareOptimizationSettingId = $params->hardwareOptimizationSettingId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                            => $this->id,
            "dealerId"                      => $this->clientId,
            "deaerlId"                      => $this->dealerId,
            "rmsUploadId"                   => $this->rmsUploadId,
            "name"                          => $this->name,
            "hardwareOptimizationSettingId" => $this->hardwareOptimizationSettingId,
        );
    }
}