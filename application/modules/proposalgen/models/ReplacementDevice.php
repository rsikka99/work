<?php

/**
 * Class Proposalgen_Model_ReplacementDevices
 * 
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_ReplacementDevice extends Tangent_Model_Abstract
{
    const REPLACMENT_BW = 1;
    const REPLACMENT_BWMFP = 2;
    const REPLACMENT_COLOR = 3;
    const REPLACMENT_COLORMFP = 4;
    public static $replacementTypes = array (
            self::REPLACMENT_BW => 'BLACK & WHITE', 
            self::REPLACMENT_BWMFP => 'BLACK & WHITE MFP', 
            self::REPLACMENT_COLOR => 'COLOR', 
            self::REPLACMENT_COLORMFP => 'COLOR MFP' 
    );
    // Database Fields
    protected $MasterDeviceId;
    protected $ReplacementCategory;
    protected $PrintSpeed;
    protected $Resolution;
    protected $MonthlyRate;
    protected $MasterDevice;

    /**
     *
     * @return the $MasterDeviceId
     */
    public function getMasterDeviceId ()
    {
        if (! isset($this->MasterDeviceId))
        {
            
            $this->MasterDeviceId = null;
        }
        return $this->MasterDeviceId;
    }

    /**
     *
     * @param field_type $MasterDeviceId            
     */
    public function setMasterDeviceId ($MasterDeviceId)
    {
        $this->MasterDeviceId = $MasterDeviceId;
        return $this;
    }

    /**
     *
     * @return the $ReplacementCategory
     */
    public function getReplacementCategory ()
    {
        if (! isset($this->ReplacementCategory))
        {
            
            $this->ReplacementCategory = null;
        }
        return $this->ReplacementCategory;
    }

    /**
     *
     * @param field_type $ReplacementCategory            
     */
    public function setReplacementCategory ($ReplacementCategory)
    {
        $this->ReplacementCategory = $ReplacementCategory;
        return $this;
    }

    /**
     *
     * @return the $PrintSpeed
     */
    public function getPrintSpeed ()
    {
        if (! isset($this->PrintSpeed))
        {
            
            $this->PrintSpeed = null;
        }
        return $this->PrintSpeed;
    }

    /**
     *
     * @param field_type $PrintSpeed            
     */
    public function setPrintSpeed ($PrintSpeed)
    {
        $this->PrintSpeed = $PrintSpeed;
        return $this;
    }

    /**
     *
     * @return the $Resolution
     */
    public function getResolution ()
    {
        if (! isset($this->Resolution))
        {
            
            $this->Resolution = null;
        }
        return $this->Resolution;
    }

    /**
     *
     * @param field_type $Resolution            
     */
    public function setResolution ($Resolution)
    {
        $this->Resolution = $Resolution;
        return $this;
    }

    /**
     *
     * @return the $MonthlyRate
     */
    public function getMonthlyRate ()
    {
        if (! isset($this->MonthlyRate))
        {
            
            $this->MonthlyRate = null;
        }
        return $this->MonthlyRate;
    }

    /**
     *
     * @param field_type $MonthlyRate            
     */
    public function setMonthlyRate ($MonthlyRate)
    {
        $this->MonthlyRate = $MonthlyRate;
        return $this;
    }

    /**
     *
     * @return the $MasterDevice
     */
    public function getMasterDevice ()
    {
        if (! isset($this->MasterDevice))
        {
            $masterDeviceMapper = Proposalgen_Model_Mapper_MasterDevice::getInstance();
            $this->MasterDevice = $masterDeviceMapper->find($this->getMasterDeviceId());
        }
        return $this->MasterDevice;
    }

    /**
     *
     * @param field_type $MasterDevice            
     */
    public function setMasterDevice ($MasterDevice)
    {
        $this->MasterDevice = $MasterDevice;
        return $this;
    }
}