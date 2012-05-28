<?php

/**
 * Class Proposalgen_Model_Manufacturer
 * 
 * @author "Lee Robert"
 */
class Proposalgen_Model_Manufacturer extends Tangent_Model_Abstract
{
    protected $ManufacturerId;
    protected $ManufacturerName;
    protected $IsDeleted;

    /**
     *
     * @return the $ManufacturerId
     */
    public function getManufacturerId ()
    {
        if (! isset($this->ManufacturerId))
        {
            
            $this->ManufacturerId = null;
        }
        return $this->ManufacturerId;
    }

    /**
     *
     * @param field_type $ManufacturerId            
     */
    public function setManufacturerId ($ManufacturerId)
    {
        $this->ManufacturerId = $ManufacturerId;
        return $this;
    }

    /**
     *
     * @return the $ManufacturerName
     */
    public function getManufacturerName ()
    {
        if (! isset($this->ManufacturerName))
        {
            
            $this->ManufacturerName = null;
        }
        return $this->ManufacturerName;
    }

    /**
     *
     * @param field_type $ManufacturerName            
     */
    public function setManufacturerName ($ManufacturerName)
    {
        $this->ManufacturerName = $ManufacturerName;
        return $this;
    }

    /**
     *
     * @return the $IsDeleted
     */
    public function getIsDeleted ()
    {
        if (! isset($this->IsDeleted))
        {
            
            $this->IsDeleted = null;
        }
        return $this->IsDeleted;
    }

    /**
     *
     * @param field_type $IsDeleted            
     */
    public function setIsDeleted ($IsDeleted)
    {
        $this->IsDeleted = $IsDeleted;
        return $this;
    }
}