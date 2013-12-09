<?php

/**
 * Class My_Brand
 */
class My_Brand
{
    // Report Colors
    public static $reportTitlePageBackgroundColor = "#4C4C4C";
    public static $reportTitlePageBackgroundColor2 = "#7F7F7F";
    public static $reportTitlePageTextColor = "#FFFFFF";
    public static $reportTitlePageTextColor2 = "#FFFFFF";
    public static $reportWhiteTitlePageTextColor = "#000000";
    public static $reportHeadingColor = "#0096D6";
    public static $reportHeadingColor2 = "#FFFFFF";
    public static $reportHeadingBackgroundColor = "#0096D6";

    // Branding Replacements
    public static $jit = "JIT";
    public static $jitName = "MPSToolbox.com JIT";
    public static $jitFullName = "MPSToolbox.com Just In Time (JIT)";
    public static $brandName = "MPSToolbox.com";
    public static $companyName = "MPSToolbox.com";
    public static $companyNameFull = "MPSToolbox.com";


    /**
     * @param array $params An array of data to populate the model with
     */
    public static function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->reportTitlePageBackgroundColor) && !is_null($params->reportTitlePageBackgroundColor))
        {
            self::$reportTitlePageBackgroundColor = $params->reportTitlePageBackgroundColor;
        }

        if (isset($params->reportTitlePageBackgroundColor2) && !is_null($params->reportTitlePageBackgroundColor2))
        {
            self::$reportTitlePageBackgroundColor2 = $params->reportTitlePageBackgroundColor2;
        }

        if (isset($params->reportWhiteTitlePageTextColor) && !is_null($params->reportWhiteTitlePageTextColor))
        {
            self::$reportWhiteTitlePageTextColor = $params->reportWhiteTitlePageTextColor;
        }

        if (isset($params->reportTitlePageTextColor) && !is_null($params->reportTitlePageTextColor))
        {
            self::$reportTitlePageTextColor = $params->reportTitlePageTextColor;
        }

        if (isset($params->reportTitlePageTextColor2) && !is_null($params->reportTitlePageTextColor2))
        {
            self::$reportTitlePageTextColor2 = $params->reportTitlePageTextColor2;
        }

        if (isset($params->reportHeadingColor) && !is_null($params->reportHeadingColor))
        {
            self::$reportHeadingColor = $params->reportHeadingColor;
        }

        if (isset($params->reportHeadingColor2) && !is_null($params->reportHeadingColor2))
        {
            self::$reportHeadingColor2 = $params->reportHeadingColor2;
        }

        if (isset($params->reportHeadingBackgroundColor) && !is_null($params->reportHeadingBackgroundColor))
        {
            self::$reportHeadingBackgroundColor = $params->reportHeadingBackgroundColor;
        }

        if (isset($params->jit) && !is_null($params->jit))
        {
            self::$jit = $params->jit;
        }

        if (isset($params->jitName) && !is_null($params->jitName))
        {
            self::$jitName = $params->jitName;
        }

        if (isset($params->jitFullName) && !is_null($params->jitFullName))
        {
            self::$jitFullName = $params->jitFullName;
        }

        if (isset($params->brandName) && !is_null($params->brandName))
        {
            self::$brandName = $params->brandName;
        }

        if (isset($params->companyName) && !is_null($params->companyName))
        {
            self::$companyName = $params->companyName;
        }

        if (isset($params->companyNameFull) && !is_null($params->companyNameFull))
        {
            self::$companyNameFull = $params->companyNameFull;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "reportTitlePageBackgroundColor"  => self::$reportTitlePageBackgroundColor,
            "reportTitlePageBackgroundColor2" => self::$reportTitlePageBackgroundColor2,
            "reportWhiteTitlePageTextColor"   => self::$reportWhiteTitlePageTextColor,
            "reportTitlePageTextColor"        => self::$reportTitlePageTextColor,
            "reportTitlePageTextColor2"       => self::$reportTitlePageTextColor2,
            "reportHeadingColor"              => self::$reportHeadingColor,
            "reportHeadingColor2"             => self::$reportHeadingColor2,
            "reportHeadingBackgroundColor"    => self::$reportHeadingColor,
            "brandName"                       => self::$brandName,
            "companyName"                     => self::$companyName,
            "companyNameFull"                 => self::$companyNameFull,
            "jit"                             => self::$jit,
            "jitName"                         => self::$jitName,
            "jitFullName"                     => self::$jitFullName
        );
    }

}