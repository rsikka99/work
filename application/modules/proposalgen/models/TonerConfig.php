<?php

/**
 * Class Proposalgen_Model_TonerConfig
 * @author "Lee Robert"
 */
class Proposalgen_Model_TonerConfig extends Tangent_Model_Abstract
{
    const BLACK_ONLY = 1;
    const THREE_COLOR_SEPARATED = 2;
    const THREE_COLOR_COMBINED = 3;
    const FOUR_COLOR_COMBINED = 4;
    
    static $TonerConfigNames = array (
            self::BLACK_ONLY => "Black Only",
            self::THREE_COLOR_SEPARATED => "3 Color Separated",
            self::THREE_COLOR_COMBINED => "3 Color Combined",
            self::FOUR_COLOR_COMBINED => "4 Color Combined"
    );
    
    protected $TonerConfigId;
    protected $TonerConfigName;

    /**
     * Gets an array of toner colors associated with a specific toner config.
     * @param $$tonerConfigId The toner config id to use
     * @return array An array of TonerColorId's associated with a specified toner config
     */
    public static function getRequiredTonersForTonerConfig ($tonerConfigId)
    {
        $tonerColors = array ();
        // Get the colors, default to black as a last resort
        switch ($tonerConfigId)
        {
            
            case self::BLACK_ONLY :
                $tonerColors ["black"] = Proposalgen_Model_TonerColor::BLACK;
                break;
            case self::THREE_COLOR_SEPARATED :
                $tonerColors ["black"] = Proposalgen_Model_TonerColor::BLACK;
                $tonerColors ["cyan"] = Proposalgen_Model_TonerColor::CYAN;
                $tonerColors ["magenta"] = Proposalgen_Model_TonerColor::MAGENTA;
                $tonerColors ["yellow"] = Proposalgen_Model_TonerColor::YELLOW;
                break;
            case self::THREE_COLOR_COMBINED :
                $tonerColors ["black"] = Proposalgen_Model_TonerColor::BLACK;
                $tonerColors ["3 color"] = Proposalgen_Model_TonerColor::THREE_COLOR;
                break;
            case self::FOUR_COLOR_COMBINED :
                $tonerColors ["4 color"] = Proposalgen_Model_TonerColor::FOUR_COLOR;
                break;
            default :
                $tonerColors ["black"] = Proposalgen_Model_TonerColor::BLACK;
                break;
        }
        return $tonerColors;
    }

    /**
     * @return the $TonerConfigId
     */
    public function getTonerConfigId ()
    {
        if (! isset($this->TonerConfigId))
        {
            
            $this->TonerConfigId = null;
        }
        return $this->TonerConfigId;
    }

    /**
     * @param field_type $TonerConfigId
     */
    public function setTonerConfigId ($TonerConfigId)
    {
        $this->TonerConfigId = $TonerConfigId;
        return $this;
    }

    /**
     * @return the $TonerConfigName
     */
    public function getTonerConfigName ()
    {
        if (! isset($this->TonerConfigName))
        {
            
            $this->TonerConfigName = null;
        }
        return $this->TonerConfigName;
    }

    /**
     * @param field_type $TonerConfigName
     */
    public function setTonerConfigName ($TonerConfigName)
    {
        $this->TonerConfigName = $TonerConfigName;
        return $this;
    }

}