<?php

namespace Tangent\View\Helper;

use Tangent\Format\NumberToWord as NumberToWordFormatter;

/**
 * Class NumberToWord
 *
 * View Helper to format numbers to words
 *
 * @package Tangent\View\Helper
 */
class NumberToWord extends \Zend_View_Helper_Abstract
{

    /**
     * @param  double $number The decimal number to be formatted
     * @param int     $precision
     *
     * @return string
     */
    public function numberToWord ($number, $precision = 0)
    {
        $numberToWord = new NumberToWordFormatter();
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