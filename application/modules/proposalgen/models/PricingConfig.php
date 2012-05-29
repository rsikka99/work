<?php

/**
 * Class Proposalgen_Model_PricingConfig
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_PricingConfig extends Tangent_Model_Abstract
{
    const NONE = 1;
    const OEM = 2;
    const COMP = 3;
    const OEMMONO_COMPCOLOR = 4;
    const COMPMONO_OEMCOLOR = 5;
    
    public static $ConfigNames = array (
            1 => "No Preference",
            2 => "OEM Only",
            3 => "Compatible Only",
            4 => "OEM Mono and Compatible Color",
            5 => "Compatible Mono and OEM Color"
    );
    
    protected $PricingConfigId;
    protected $ConfigName;
    protected $ColorTonerPartTypeId;
    protected $MonoTonerPartTypeId;
    
    // Extra variables
    protected $ColorTonerPartType;
    protected $MonoTonerPartType;

    public function getNiceConfigName ()
    {
        return self::$ConfigNames [$this->getPricingConfigId()];
    }

    /**
     * @return the $PricingConfigId
     */
    public function getPricingConfigId ()
    {
        if (! isset($this->PricingConfigId))
        {
            
            $this->PricingConfigId = null;
        }
        return $this->PricingConfigId;
    }

    /**
     * @param field_type $PricingConfigId
     */
    public function setPricingConfigId ($PricingConfigId)
    {
        $this->PricingConfigId = $PricingConfigId;
        return $this;
    }

    /**
     * @return the $ConfigName
     */
    public function getConfigName ()
    {
        if (! isset($this->ConfigName))
        {
            
            $this->ConfigName = null;
        }
        return $this->ConfigName;
    }

    /**
     * @param field_type $ConfigName
     */
    public function setConfigName ($ConfigName)
    {
        $this->ConfigName = $ConfigName;
        return $this;
    }

    /**
     * @return the $ColorTonerPartTypeId
     */
    public function getColorTonerPartTypeId ()
    {
        if (! isset($this->ColorTonerPartTypeId))
        {
            
            $this->ColorTonerPartTypeId = null;
        }
        return $this->ColorTonerPartTypeId;
    }

    /**
     * @param field_type $ColorTonerPartTypeId
     */
    public function setColorTonerPartTypeId ($ColorTonerPartTypeId)
    {
        $this->ColorTonerPartTypeId = $ColorTonerPartTypeId;
        return $this;
    }

    /**
     * @return the $MonoTonerPartTypeId
     */
    public function getMonoTonerPartTypeId ()
    {
        if (! isset($this->MonoTonerPartTypeId))
        {
            
            $this->MonoTonerPartTypeId = null;
        }
        return $this->MonoTonerPartTypeId;
    }

    /**
     * @param field_type $MonoTonerPartTypeId
     */
    public function setMonoTonerPartTypeId ($MonoTonerPartTypeId)
    {
        $this->MonoTonerPartTypeId = $MonoTonerPartTypeId;
        return $this;
    }

    /**
     * @return the $ColorTonerPartType
     */
    public function getColorTonerPartType ()
    {
        if (! isset($this->ColorTonerPartType))
        {
            if (isset($this->ColorTonerPartTypeId))
            {
                $this->ColorTonerPartType = Proposalgen_Model_Mapper_PartType::getInstance()->find($this->getColorTonerPartTypeId());
            }
        }
        return $this->ColorTonerPartType;
    }

    /**
     * @param field_type $ColorTonerPartType
     */
    public function setColorTonerPartType ($ColorTonerPartType)
    {
        $this->ColorTonerPartType = $ColorTonerPartType;
        return $this;
    }

    /**
     * @return the $MonoTonerPartType
     */
    public function getMonoTonerPartType ()
    {
        if (! isset($this->MonoTonerPartType))
        {
            if (isset($this->MonoTonerPartTypeId))
            {
                $this->MonoTonerPartType = Proposalgen_Model_Mapper_PartType::getInstance()->find($this->getMonoTonerPartTypeId());
            }
        }
        return $this->MonoTonerPartType;
    }

    /**
     * @param field_type $MonoTonerPartType
     */
    public function setMonoTonerPartType ($MonoTonerPartType)
    {
        $this->MonoTonerPartType = $MonoTonerPartType;
        return $this;
    }

}