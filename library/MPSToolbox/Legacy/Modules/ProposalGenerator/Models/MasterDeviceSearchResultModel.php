<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class MasterDeviceSearchResultModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class MasterDeviceSearchResultModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $masterDeviceId;

    /**
     * @var string
     */
    public $masterDeviceModelName;

    /**
     * @var string
     */
    public $masterDeviceFullDeviceName;

    /**
     * @var int
     */
    public $masterDeviceTonerConfigId;

    /**
     * @var bool
     */
    public $masterDeviceIsMfp;

    /**
     * @var bool
     */
    public $masterDeviceIsColor;

    /**
     * @var int
     */
    public $manufacturerId;

    /**
     * @var string
     */
    public $manufacturerFullName;

    /**
     * @var string
     */
    public $manufacturerDisplayName;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->masterDeviceModelName) && !is_null($params->masterDeviceModelName))
        {
            $this->masterDeviceModelName = $params->masterDeviceModelName;
        }

        if (isset($params->masterDeviceFullDeviceName) && !is_null($params->masterDeviceFullDeviceName))
        {
            $this->masterDeviceFullDeviceName = $params->masterDeviceFullDeviceName;
        }

        if (isset($params->masterDeviceTonerConfigId) && !is_null($params->masterDeviceTonerConfigId))
        {
            $this->masterDeviceTonerConfigId = $params->masterDeviceTonerConfigId;
        }

        if (isset($params->masterDeviceIsMfp) && !is_null($params->masterDeviceIsMfp))
        {
            $this->masterDeviceIsMfp = $params->masterDeviceIsMfp;
        }

        if (isset($params->masterDeviceIsColor) && !is_null($params->masterDeviceIsColor))
        {
            $this->masterDeviceIsColor = $params->masterDeviceIsColor;
        }

        if (isset($params->manufacturerId) && !is_null($params->manufacturerId))
        {
            $this->manufacturerId = $params->manufacturerId;
        }

        if (isset($params->manufacturerFullName) && !is_null($params->manufacturerFullName))
        {
            $this->manufacturerFullName = $params->manufacturerFullName;
        }

        if (isset($params->manufacturerDisplayName) && !is_null($params->manufacturerDisplayName))
        {
            $this->manufacturerDisplayName = $params->manufacturerDisplayName;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "masterDeviceId"             => $this->masterDeviceId,
            "masterDeviceModelName"      => $this->masterDeviceModelName,
            "masterDeviceFullDeviceName" => $this->masterDeviceFullDeviceName,
            "masterDeviceTonerConfigId"  => $this->masterDeviceTonerConfigId,
            "masterDeviceIsMfp"          => $this->masterDeviceIsMfp,
            "masterDeviceIsColor"        => $this->masterDeviceIsColor,
            "manufacturerId"             => $this->manufacturerId,
            "manufacturerFullName"       => $this->manufacturerFullName,
            "manufacturerDisplayName"    => $this->manufacturerDisplayName,
        ];
    }

}