<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class TonerConfigModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class TonerConfigModel extends My_Model_Abstract
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
    public $id;

    /**
     * @var int
     */
    public $name;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->id) && !is_null($params->id))
        {
            $this->id = $params->id;
        }

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            'id'   => $this->id,
            'name' => $this->name,
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
                $tonerColors ["Black"] = TonerColorModel::BLACK;
                break;
            case self::THREE_COLOR_SEPARATED :
                $tonerColors ["Black"]   = TonerColorModel::BLACK;
                $tonerColors ["Cyan"]    = TonerColorModel::CYAN;
                $tonerColors ["Magenta"] = TonerColorModel::MAGENTA;
                $tonerColors ["Yellow"]  = TonerColorModel::YELLOW;
                break;
            case self::THREE_COLOR_COMBINED :
                $tonerColors ["Black"]      = TonerColorModel::BLACK;
                $tonerColors ["ThreeColor"] = TonerColorModel::THREE_COLOR;
                break;
            case self::FOUR_COLOR_COMBINED :
                $tonerColors ["FourColor"] = TonerColorModel::FOUR_COLOR;
                break;
            default :
                $tonerColors ["Black"] = TonerColorModel::BLACK;
                break;
        }

        return $tonerColors;
    }
}