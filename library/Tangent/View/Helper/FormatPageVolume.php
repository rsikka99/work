<?php

/**
 * View Helper to Format Page Volume.
 *
 * @package Tangent_View
 */
class Tangent_View_Helper_FormatPageVolume extends Zend_View_Helper_Abstract
{
    /**
     *  Formats a Page Volume
     *
     * @param int|float|string $pageVolume
     * @param int              $precision
     *
     * @return string
     */
    public function formatPageVolume ($pageVolume, $precision = 0)
    {
        return number_format($pageVolume, $precision);
    }
}