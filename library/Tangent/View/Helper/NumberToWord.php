<?php

/**
 * View Helper to Format Numbers.
 *
 * @package Tangent_View
 */
class Tangent_View_Helper_NumberToWord extends Zend_View_Helper_Abstract
{

    /**
     * @param  double $number The decimal number to be formatted
     * @param int     $precision
     *
     * @return string
     */
    public function numberToWord ($number, $precision = 0)
    {
        $numberToWord = new Tangent_Format_NumberToWord();
        if (is_array($number))
        {
            foreach ($number as $key => $value)
            {
                $number[$key] = $numberToWord->format($value, $precision);
            }
        }
        else
        {
            $number = $numberToWord->format($number, $precision);
        }

        return $number;
    }
}