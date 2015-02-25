<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class RmsDeviceModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class RmsDeviceModel extends My_Model_Abstract
{
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
    public $manufacturer;

    /**
     * @var string
     */
    public $modelName;

    /**
     * @var string
     */
    public $dateCreated;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var bool
     */
    public $isGeneric;


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

        if (isset($params->dateCreated) && !is_null($params->dateCreated))
        {
            $this->dateCreated = $params->dateCreated;
        }

        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }

        if (isset($params->isGeneric) && !is_null($params->isGeneric))
        {
            $this->isGeneric = $params->isGeneric;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "rmsProviderId" => $this->rmsProviderId,
            "rmsModelId"    => $this->rmsModelId,
            "manufacturer"  => $this->manufacturer,
            "modelName"     => $this->modelName,
            "dateCreated"   => $this->dateCreated,
            "userId"        => $this->userId,
            "isGeneric"     => $this->isGeneric,
        ];
    }
}