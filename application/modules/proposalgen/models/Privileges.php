<?php

/**
 * Class Proposalgen_Model_Privileges
 * 
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_Privileges extends Tangent_Model_Abstract
{
    // Database Fields
    protected $PrivId;
    protected $PrivType;

    /**
     *
     * @return the $PrivId
     */
    public function getPrivId ()
    {
        if (! isset($this->PrivId))
        {
            
            $this->PrivId = null;
        }
        return $this->PrivId;
    }

    /**
     *
     * @param field_type $PrivId            
     */
    public function setPrivId ($PrivId)
    {
        $this->PrivId = $PrivId;
        return $this;
    }

    /**
     *
     * @return the $PrivType
     */
    public function getPrivType ()
    {
        if (! isset($this->PrivType))
        {
            
            $this->PrivType = null;
        }
        return $this->PrivType;
    }

    /**
     *
     * @param field_type $PrivType            
     */
    public function setPrivType ($PrivType)
    {
        $this->PrivType = $PrivType;
        return $this;
    }
}