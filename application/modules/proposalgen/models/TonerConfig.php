<?php
/**
 * Class Proposalgen_Model_TonerConfig
 */
class Proposalgen_Model_TonerConfig extends My_Model_Abstract
{
    const BLACK_ONLY            = 1;
    const THREE_COLOR_SEPARATED = 2;
    const THREE_COLOR_COMBINED  = 3;
    const FOUR_COLOR_COMBINED   = 4;
    static $TonerConfigNames = array(
        self::BLACK_ONLY            => "Black Only",
        self::THREE_COLOR_SEPARATED => "3 Color Separated",
        self::THREE_COLOR_COMBINED  => "3 Color Combined",
        self::FOUR_COLOR_COMBINED   => "4 Color Combined"
    );

    /**
     * @var int
     */
    public $tonerConfigId;

    /**
     * @var int
     */
    public $tonerConfigName;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->tonerConfigId) && !is_null($params->tonerConfigId))
        {
            $this->tonerConfigId = $params->tonerConfigId;
        }

        if (isset($params->tonerConfigName) && !is_null($params->tonerConfigName))
        {
            $this->tonerConfigName = $params->tonerConfigName;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "tonerConfigId"   => $this->tonerConfigId,
            "tonerConfigName" => $this->tonerConfigName,
        );
    }

    /**
     * Gets an array of toner colors associated with a specific toner config.
     *
     * @param int $tonerConfigId The toner config id to use
     *
     * @return array An array of TonerColorId's associated with a specified toner config
     */
    public static function getRequiredTonersForTonerConfig ($tonerConfigId)
    {
        $tonerColors = array();
        // Get the colors, default to black as a last resort
        switch ($tonerConfigId)
        {

            case self::BLACK_ONLY :
                $tonerColors ["Black"] = Proposalgen_Model_TonerColor::BLACK;
                break;
            case self::THREE_COLOR_SEPARATED :
                $tonerColors ["Black"]   = Proposalgen_Model_TonerColor::BLACK;
                $tonerColors ["Cyan"]    = Proposalgen_Model_TonerColor::CYAN;
                $tonerColors ["Magenta"] = Proposalgen_Model_TonerColor::MAGENTA;
                $tonerColors ["Yellow"]  = Proposalgen_Model_TonerColor::YELLOW;
                break;
            case self::THREE_COLOR_COMBINED :
                $tonerColors ["Black"]   = Proposalgen_Model_TonerColor::BLACK;
                $tonerColors ["ThreeColor"] = Proposalgen_Model_TonerColor::THREE_COLOR;
                break;
            case self::FOUR_COLOR_COMBINED :
                $tonerColors ["FourColor"] = Proposalgen_Model_TonerColor::FOUR_COLOR;
                break;
            default :
                $tonerColors ["Black"] = Proposalgen_Model_TonerColor::BLACK;
                break;
        }

        return $tonerColors;
    }


}