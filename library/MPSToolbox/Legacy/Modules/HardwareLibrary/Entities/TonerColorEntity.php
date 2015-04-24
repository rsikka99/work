<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class TonerColorEntity
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Entities
 *
 * @property int                id
 * @property string             name
 */
class TonerColorEntity extends EloquentModel
{
    const BLACK       = 1;
    const CYAN        = 2;
    const MAGENTA     = 3;
    const YELLOW      = 4;
    const THREE_COLOR = 5;
    const FOUR_COLOR  = 6;

    /**
     * Friendly toner color names with the toner color id as the array key
     *
     * @var string[]
     */
    static $ColorNames = [
        self::BLACK       => "Black",
        self::CYAN        => "Cyan",
        self::MAGENTA     => "Magenta",
        self::YELLOW      => "Yellow",
        self::THREE_COLOR => "Three Color",
        self::FOUR_COLOR  => "Four Color",
    ];

    protected $table      = 'toner_colors';
    public    $timestamps = false;
}