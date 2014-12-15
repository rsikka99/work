<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class TonerColorModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class TonerColorModel extends My_Model_Abstract
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
    static $ColorNames = array(
        self::BLACK       => "Black",
        self::CYAN        => "Cyan",
        self::MAGENTA     => "Magenta",
        self::YELLOW      => "Yellow",
        self::THREE_COLOR => "Three Color",
        self::FOUR_COLOR  => "Four Color"
    );

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
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
            "id"   => $this->id,
            "name" => $this->name,
        );
    }
}