<?php
class Proposalgen_Model_Meter extends Tangent_Model_Abstract
{
    const METER_TYPE_LIFE        = 'LIFE';
    const METER_TYPE_COLOR       = 'COLOR';
    const METER_TYPE_COPY_COLOR  = 'COPY COLOR';
    const METER_TYPE_PRINT_COLOR = 'PRINT COLOR';
    const METER_TYPE_BLACK       = 'BLACK';
    const METER_TYPE_COPY_BLACK  = 'COPY BLACK';
    const METER_TYPE_PRINT_BLACK = 'PRINT BLACK';
    const METER_TYPE_SCAN        = 'SCAN';
    const METER_TYPE_FAX         = 'FAX';

    // Database Fields
    /**
     * @var int
     */
    public $meterId;

    /**
     * @var int
     */
    public $deviceInstanceId;

    /**
     * @var int
     */
    public $meterType;

    /**
     * @var int
     */
    public $startMeter;

    /**
     * @var int
     */
    public $endMeter;

    // Extra Fields
    /**
     * When set to true it means it did not come from the database
     *
     * @var boolean
     */
    public $generatedBySystem = false;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->meterId) && !is_null($params->meterId))
        {
            $this->meterId = $params->meterId;
        }

        if (isset($params->deviceInstanceId) && !is_null($params->deviceInstanceId))
        {
            $this->deviceInstanceId = $params->deviceInstanceId;
        }

        if (isset($params->meterType) && !is_null($params->meterType))
        {
            $this->meterType = $params->meterType;
        }

        if (isset($params->startMeter) && !is_null($params->startMeter))
        {
            $this->startMeter = $params->startMeter;
        }

        if (isset($params->endMeter) && !is_null($params->endMeter))
        {
            $this->endMeter = $params->endMeter;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "meterId"          => $this->meterId,
            "deviceInstanceId" => $this->deviceInstanceId,
            "meterType"        => $this->meterType,
            "startMeter"       => $this->startMeter,
            "endMeter"         => $this->endMeter,
        );
    }
}