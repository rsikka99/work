<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class RmsExcludedRowModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class RmsExcludedRowModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $rmsUploadId;

    /**
     * @var int
     */
    public $rmsProviderId;

    /**
     * @var string
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
     * @var number
     */
    public $csvLineNumber;

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

        if (isset($params->rmsUploadId) && !is_null($params->rmsUploadId))
        {
            $this->rmsUploadId = $params->rmsUploadId;
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

        if (isset($params->csvLineNumber) && !is_null($params->csvLineNumber))
        {
            $this->csvLineNumber = $params->csvLineNumber;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "id"               => $this->id,
            "rmsUploadId"      => $this->rmsUploadId,
            "rmsProviderId"    => $this->rmsProviderId,
            "rmsModelId"       => $this->rmsModelId,
            "serialNumber"     => $this->serialNumber,
            "ipAddress"        => $this->ipAddress,
            "modelName"        => $this->modelName,
            "manufacturerName" => $this->manufacturerName,
            "reason"           => $this->reason,
            "csvLineNumber"    => $this->csvLineNumber,
        ];
    }
}