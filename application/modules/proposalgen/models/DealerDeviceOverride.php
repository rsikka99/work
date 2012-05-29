<?php

/**
 * Class Proposalgen_Model_DealerDeviceOverride
 *
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_DealerDeviceOverride extends Tangent_Model_Abstract
{
    protected $DealerCompanyId;
    protected $MasterDeviceId;
    protected $OverrideDevicePrice;
    protected $IsLeased;

    /**
     *
     * @return the $DealerCompanyId
     */
    public function getDealerCompanyId ()
    {
        if (! isset($this->DealerCompanyId))
        {
            
            $this->DealerCompanyId = null;
        }
        return $this->DealerCompanyId;
    }

    /**
     *
     * @param field_type $DealerCompanyId            
     */
    public function setDealerCompanyId ($DealerCompanyId)
    {
        $this->DealerCompanyId = $DealerCompanyId;
        return $this;
    }

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
     * @return the $OverrideDevicePrice
     */
    public function getOverrideDevicePrice ()
    {
        if (! isset($this->OverrideDevicePrice))
        {
            
            $this->OverrideDevicePrice = null;
        }
        return $this->OverrideDevicePrice;
    }

    /**
     *
     * @param field_type $OverrideDevicePrice            
     */
    public function setOverrideDevicePrice ($OverrideDevicePrice)
    {
        $this->OverrideDevicePrice = $OverrideDevicePrice;
        return $this;
    }

    /**
     *
     * @return the $IsLeased
     */
    public function getIsLeased ()
    {
        if (! isset($this->IsLeased))
        {
            
            $this->IsLeased = null;
        }
        return $this->IsLeased;
    }

    /**
     *
     * @param field_type $IsLeased            
     */
    public function setIsLeased ($IsLeased)
    {
        $this->IsLeased = $IsLeased;
        return $this;
    }
}