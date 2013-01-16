<?php
class Proposalgen_Model_TonerColor extends My_Model_Abstract
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
    public $tonerColorId;

    /**
     * @var string
     */
    public $tonerColorName;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->tonerColorId) && !is_null($params->tonerColorId))
        {
            $this->tonerColorId = $params->tonerColorId;
        }

        if (isset($params->tonerColorName) && !is_null($params->tonerColorName))
        {
            $this->tonerColorName = $params->tonerColorName;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "tonerColorId"   => $this->tonerColorId,
            "tonerColorName" => $this->tonerColorName,
        );
    }
}