<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class TonerConfigEntity
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Entities
 *
 * @property int                id
 * @property string             name
 */
class TonerConfigurationEntity extends EloquentModel
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

    protected $table      = 'toner_configs';
    public    $timestamps = false;


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
                $tonerColors ["Black"] = TonerColorEntity::BLACK;
                break;
            case self::THREE_COLOR_SEPARATED :
                $tonerColors ["Black"]   = TonerColorEntity::BLACK;
                $tonerColors ["Cyan"]    = TonerColorEntity::CYAN;
                $tonerColors ["Magenta"] = TonerColorEntity::MAGENTA;
                $tonerColors ["Yellow"]  = TonerColorEntity::YELLOW;
                break;
            case self::THREE_COLOR_COMBINED :
                $tonerColors ["Black"]      = TonerColorEntity::BLACK;
                $tonerColors ["ThreeColor"] = TonerColorEntity::THREE_COLOR;
                break;
            case self::FOUR_COLOR_COMBINED :
                $tonerColors ["FourColor"] = TonerColorEntity::FOUR_COLOR;
                break;
            default :
                $tonerColors ["Black"] = TonerColorEntity::BLACK;
                break;
        }

        return $tonerColors;
    }
}