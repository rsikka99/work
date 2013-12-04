<?php

/**
 * Class My_Brand
 */
class My_Brand
{
    public static $reportHeadingColor = "#000";
    public static $reportHeadingBackgroundColor = "#000";

    /**
     * @param array $params An array of data to populate the model with
     */
    public static function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        // TODO: Fill out populate
    }

}