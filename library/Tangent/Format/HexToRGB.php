<?php
namespace Tangent\Format;

/**
 * Class HexToRGB
 * Coverts hex colors to rgb for use in graphs
 *
 * @package Tangent\Format
 */
class HexToRGB
{
    function hexToRgb ($hex)
    {
        $hex = ltrim($hex, '#');
        preg_match("/^#{0,1}([0-9a-f]{1,6})$/i", $hex, $match);
        if (!isset($match[1]))
        {
            return false;
        }

        if (strlen($match[1]) == 6)
        {
            list($r, $g, $b) = [$hex[0] . $hex[1], $hex[2] . $hex[3], $hex[4] . $hex[5]];
        }
        elseif (strlen($match[1]) == 3)
        {
            list($r, $g, $b) = [$hex[0] . $hex[0], $hex[1] . $hex[1], $hex[2] . $hex[2]];
        }
        else if (strlen($match[1]) == 2)
        {
            list($r, $g, $b) = [$hex[0] . $hex[1], $hex[0] . $hex[1], $hex[0] . $hex[1]];
        }
        else if (strlen($match[1]) == 1)
        {
            list($r, $g, $b) = [$hex . $hex, $hex . $hex, $hex . $hex];
        }
        else
        {
            return false;
        }

        $color      = [];
        $color['r'] = hexdec($r);
        $color['g'] = hexdec($g);
        $color['b'] = hexdec($b);

        return $color;
    }
}