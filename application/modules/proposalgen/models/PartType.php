<?php

/**
 * Class Proposalgen_Model_PartType
 * @author "Lee Robert"
 */
class Proposalgen_Model_PartType extends Tangent_Model_Abstract
{
    const OEM = 1;
    const COMP = 2;
    
    static $PartTypeNames = array (
            self::OEM => "OEM", 
            self::COMP => "Compatible" 
    );
    
    protected $PartTypeId;
    protected $TypeName;

    /**
     * @return the $PartTypeId
     */
    public function getPartTypeId ()
    {
        if (! isset($this->PartTypeId))
        {
            
            $this->PartTypeId = null;
        }
        return $this->PartTypeId;
    }

    /**
     * @param field_type $PartTypeId
     */
    public function setPartTypeId ($PartTypeId)
    {
        $this->PartTypeId = $PartTypeId;
        return $this;
    }

    /**
     * @return the $TypeName
     */
    public function getTypeName ()
    {
        if (! isset($this->TypeName))
        {
            
            $this->TypeName = null;
        }
        return $this->TypeName;
    }

    /**
     * @param field_type $TypeName
     */
    public function setTypeName ($TypeName)
    {
        $this->TypeName = $TypeName;
        return $this;
    }

}