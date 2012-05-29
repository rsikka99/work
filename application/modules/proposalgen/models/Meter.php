<?php

/**
 * Class Proposalgen_Model_Meter
 * @author "Lee Robert"
 */
class Proposalgen_Model_Meter extends Tangent_Model_Abstract
{
    const METER_TYPE_LIFE = 'LIFE';
    const METER_TYPE_COLOR = 'COLOR';
    const METER_TYPE_COPY_COLOR = 'COPY COLOR';
    const METER_TYPE_PRINT_COLOR = 'PRINT COLOR';
    const METER_TYPE_BLACK = 'BLACK';
    const METER_TYPE_COPY_BLACK = 'COPY BLACK';
    const METER_TYPE_PRINT_BLACK = 'PRINT BLACK';
    const METER_TYPE_SCAN = 'SCAN';
    const METER_TYPE_FAX = 'FAX';
    
    // Database Fields
    protected $MeterId;
    protected $DeviceInstanceId;
    protected $MeterType;
    protected $StartMeter;
    protected $EndMeter;
    
    // Extra Fields
    protected $GeneratedBySystem;

    /**
     * @return the $MeterId
     */
    public function getMeterId ()
    {
        if (! isset($this->MeterId))
        {
            
            $this->MeterId = null;
        }
        return $this->MeterId;
    }

    /**
     * @param field_type $MeterId
     */
    public function setMeterId ($MeterId)
    {
        $this->MeterId = $MeterId;
        return $this;
    }

    /**
     * @return the $DeviceInstanceId
     */
    public function getDeviceInstanceId ()
    {
        if (! isset($this->DeviceInstanceId))
        {
            
            $this->DeviceInstanceId = null;
        }
        return $this->DeviceInstanceId;
    }

    /**
     * @param field_type $DeviceInstanceId
     */
    public function setDeviceInstanceId ($DeviceInstanceId)
    {
        $this->DeviceInstanceId = $DeviceInstanceId;
        return $this;
    }

    /**
     * @return the $MeterType
     */
    public function getMeterType ()
    {
        if (! isset($this->MeterType))
        {
            
            $this->MeterType = null;
        }
        return $this->MeterType;
    }

    /**
     * @param field_type $MeterType
     */
    public function setMeterType ($MeterType)
    {
        $this->MeterType = $MeterType;
        return $this;
    }

    /**
     * @return the $StartMeter
     */
    public function getStartMeter ()
    {
        if (! isset($this->StartMeter))
        {
            
            $this->StartMeter = null;
        }
        return $this->StartMeter;
    }

    /**
     * @param field_type $StartMeter
     */
    public function setStartMeter ($StartMeter)
    {
        $this->StartMeter = $StartMeter;
        return $this;
    }

    /**
     * @return the $EndMeter
     */
    public function getEndMeter ()
    {
        if (! isset($this->EndMeter))
        {
            
            $this->EndMeter = null;
        }
        return $this->EndMeter;
    }

    /**
     * @param field_type $EndMeter
     */
    public function setEndMeter ($EndMeter)
    {
        $this->EndMeter = $EndMeter;
        return $this;
    }

    /**
     * @return the $GeneratedBySystem
     */
    public function getGeneratedBySystem ()
    {
        if (! isset($this->GeneratedBySystem))
        {
            $this->GeneratedBySystem = false;
        }
        return $this->GeneratedBySystem;
    }

    /**
     * @param field_type $GeneratedBySystem
     */
    public function setGeneratedBySystem ($GeneratedBySystem)
    {
        $this->GeneratedBySystem = $GeneratedBySystem;
        return $this;
    }

}