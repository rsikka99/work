<?php
/**
 * Class Proposalgen_Model_DeviceInstanceMeter
 */
class Proposalgen_Model_DeviceInstanceMeter extends My_Model_Abstract
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
    const DAYS_IN_MONTH          = 30.4;

    // Database Fields
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $deviceInstanceId;

    /**
     * @var string
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

    /**
     * @var string
     */
    public $monitorStartDate;

    /**
     * @var string
     */
    public $monitorEndDate;

    // Extra Fields
    /**
     * When set to true it means it did not come from the database
     *
     * @var boolean
     */
    public $generatedBySystem = false;

    /**
     * The average number of pages printed per day
     *
     * @var float
     */
    protected $_averageDailyPageVolume;

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

        if (isset($params->monitorStartDate) && !is_null($params->monitorStartDate))
        {
            $this->monitorStartDate = $params->monitorStartDate;
        }

        if (isset($params->monitorEndDate) && !is_null($params->monitorEndDate))
        {
            $this->monitorEndDate = $params->monitorEndDate;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"               => $this->id,
            "deviceInstanceId" => $this->deviceInstanceId,
            "meterType"        => $this->meterType,
            "startMeter"       => $this->startMeter,
            "endMeter"         => $this->endMeter,
            "monitorStartDate" => $this->monitorStartDate,
            "monitorEndDate"   => $this->monitorEndDate,
        );
    }

    /**
     * Gets the DateInterval for how many days the meters were monitored for
     *
     * @return DateInterval
     */
    public function calculateMpsMonitorInterval ()
    {
        if (!isset($this->_mpsMonitorInterval))
        {
            $startDate                 = new DateTime($this->monitorStartDate);
            $endDate                   = new DateTime($this->monitorEndDate);
            $this->_mpsMonitorInterval = $startDate->diff($endDate);
        }

        return $this->_mpsMonitorInterval;
    }

    /**
     * Calculates the average daily page volume
     *
     * @return float
     */
    public function calculateAverageDailyPageVolume ()
    {
        if (!isset($this->_averageDailyPageVolume))
        {
            $pageVolume   = $this->endMeter - $this->startMeter;
            $dateInterval = $this->calculateMpsMonitorInterval();

            if ($pageVolume > 0 && $dateInterval->days > 0)
            {
                $pageVolume = $pageVolume / $dateInterval->days;
            }
            else
            {
                $pageVolume = 0.0;
            }
            $this->_averageDailyPageVolume = $pageVolume;
        }

        return $this->_averageDailyPageVolume;
    }
}