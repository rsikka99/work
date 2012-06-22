<?php

/**
 * Class Proposalgen_Model_TonerColor
 *
 * @author "Lee Robert"
 */
class Proposalgen_Model_TonerColor extends Tangent_Model_Abstract
{
    const BLACK = 1;
    const CYAN = 2;
    const MAGENTA = 3;
    const YELLOW = 4;
    const THREE_COLOR = 5;
    const FOUR_COLOR = 6;
    static $ColorNames = array (
            self::BLACK => "Black", 
            self::CYAN => "Cyan", 
            self::MAGENTA => "Magenta", 
            self::YELLOW => "Yellow", 
            self::THREE_COLOR => "Three Color", 
            self::FOUR_COLOR => "Four Color" 
    );
    protected $TonerColorId;
    protected $TonerColorName;

    /**
     *
     * @return the $TonerColorId
     */
    public function getTonerColorId ()
    {
        if (! isset($this->TonerColorId))
        {
            
            $this->TonerColorId = null;
        }
        return $this->TonerColorId;
    }

    /**
     *
     * @param field_type $TonerColorId            
     */
    public function setTonerColorId ($TonerColorId)
    {
        $this->TonerColorId = $TonerColorId;
        return $this;
    }

    /**
     *
     * @return the $TonerColorName
     */
    public function getTonerColorName ()
    {
        if (! isset($this->TonerColorName))
        {
            
            $this->TonerColorName = null;
        }
        return $this->TonerColorName;
    }

    /**
     *
     * @param field_type $TonerColorName            
     */
    public function setTonerColorName ($TonerColorName)
    {
        $this->TonerColorName = $TonerColorName;
        return $this;
    }
}