<?php

/**
 * Class Proposalgen_Model_DevicePf
 * 
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_DevicePf extends Tangent_Model_Abstract
{
    protected $DevicesPfId;
    protected $PfModelId;
    protected $PfDbDeviceName;
    protected $PfDbManufacturer;
    protected $DateCreated;
    protected $CreatedBy;

    /**
     *
     * @return the $DevicesPfId
     */
    public function getDevicesPfId ()
    {
        if (! isset($this->DevicesPfId))
        {
            
            $this->DevicesPfId = null;
        }
        return $this->DevicesPfId;
    }

    /**
     *
     * @param field_type $DevicesPfId            
     */
    public function setDevicesPfId ($DevicesPfId)
    {
        $this->DevicesPfId = $DevicesPfId;
        return $this;
    }

    /**
     *
     * @return the $PfModelId
     */
    public function getPfModelId ()
    {
        if (! isset($this->PfModelId))
        {
            
            $this->PfModelId = null;
        }
        return $this->PfModelId;
    }

    /**
     *
     * @param field_type $PfModelId            
     */
    public function setPfModelId ($PfModelId)
    {
        $this->PfModelId = $PfModelId;
        return $this;
    }

    /**
     *
     * @return the $PfDbDeviceName
     */
    public function getPfDbDeviceName ()
    {
        if (! isset($this->PfDbDeviceName))
        {
            
            $this->PfDbDeviceName = null;
        }
        return $this->PfDbDeviceName;
    }

    /**
     *
     * @param field_type $PfDbDeviceName            
     */
    public function setPfDbDeviceName ($PfDbDeviceName)
    {
        $this->PfDbDeviceName = $PfDbDeviceName;
        return $this;
    }

    /**
     *
     * @return the $PfDbManufacturer
     */
    public function getPfDbManufacturer ()
    {
        if (! isset($this->PfDbManufacturer))
        {
            
            $this->PfDbManufacturer = null;
        }
        return $this->PfDbManufacturer;
    }

    /**
     *
     * @param field_type $PfDbManufacturer            
     */
    public function setPfDbManufacturer ($PfDbManufacturer)
    {
        $this->PfDbManufacturer = $PfDbManufacturer;
        return $this;
    }

    /**
     *
     * @return the $DateCreated
     */
    public function getDateCreated ()
    {
        if (! isset($this->DateCreated))
        {
            
            $this->DateCreated = null;
        }
        return $this->DateCreated;
    }

    /**
     *
     * @param field_type $DateCreated            
     */
    public function setDateCreated ($DateCreated)
    {
        $this->DateCreated = $DateCreated;
        return $this;
    }

    /**
     *
     * @return the $CreatedBy
     */
    public function getCreatedBy ()
    {
        if (! isset($this->CreatedBy))
        {
            
            $this->CreatedBy = null;
        }
        return $this->CreatedBy;
    }

    /**
     *
     * @param field_type $CreatedBy            
     */
    public function setCreatedBy ($CreatedBy)
    {
        $this->CreatedBy = $CreatedBy;
        return $this;
    }
}