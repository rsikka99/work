<?php

/**
 * Class My_Brand
 */
class My_Brand
{
    // Brand Colors
    public static $reportTitlePageTitleBackgroundColor = "#4C4C4C";
    public static $reportTitlePageInformationBackgroundColor = "#7F7F7F";
    public static $reportWhiteTitlePageTextColor = "#000000";
    public static $reportTitlePageTitleColor = "#FFFFFF";
    public static $reportTitlePageInformationColor = "#FFFFFF";
    public static $reportHeadingColor = "#0096D6";
    public static $reportHeadingBackgroundColor = "#0096D6";
    public static $reportSubHeadingColor = "#FFFFFF";
    public static $reportSubHeadingBackgroundColor = "#0096D6";

    // Branding Text Replacements
    public static $jit = "JIT";
    public static $jitName = "MPSToolbox.com JIT";
    public static $jitFullName = "MPSToolbox.com Just In Time (JIT)";
    public static $brandName = "MPSToolbox.com";
    public static $companyName = "MPSToolbox.com";
    public static $companyNameFull = "MPSToolbox.com";
    public static $dealerSku = "Dealer SKU";


    /**
     * @param array $params An array of data to populate the model with
     */
    public static function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        /**
         * Brand Colors
         */
        if (isset($params->reportTitlePageTitleBackgroundColor) && !is_null($params->reportTitlePageTitleBackgroundColor))
        {
            self::$reportTitlePageTitleBackgroundColor = $params->reportTitlePageTitleBackgroundColor;
        }

        if (isset($params->reportTitlePageInformationBackgroundColor) && !is_null($params->reportTitlePageInformationBackgroundColor))
        {
            self::$reportTitlePageInformationBackgroundColor = $params->reportTitlePageInformationBackgroundColor;
        }

        if (isset($params->reportWhiteTitlePageTextColor) && !is_null($params->reportWhiteTitlePageTextColor))
        {
            self::$reportWhiteTitlePageTextColor = $params->reportWhiteTitlePageTextColor;
        }

        if (isset($params->reportTitlePageTitleColor) && !is_null($params->reportTitlePageTitleColor))
        {
            self::$reportTitlePageTitleColor = $params->reportTitlePageTitleColor;
        }

        if (isset($params->reportTitlePageInformationColor) && !is_null($params->reportTitlePageInformationColor))
        {
            self::$reportTitlePageInformationColor = $params->reportTitlePageInformationColor;
        }

        if (isset($params->reportHeadingColor) && !is_null($params->reportHeadingColor))
        {
            self::$reportHeadingColor = $params->reportHeadingColor;
        }

        if (isset($params->reportHeadingBackgroundColor) && !is_null($params->reportHeadingBackgroundColor))
        {
            self::$reportHeadingBackgroundColor = $params->reportHeadingBackgroundColor;
        }

        if (isset($params->reportSubHeadingColor) && !is_null($params->reportSubHeadingColor))
        {
            self::$reportSubHeadingColor = $params->reportSubHeadingColor;
        }

        if (isset($params->reportSubHeadingBackgroundColor) && !is_null($params->reportSubHeadingBackgroundColor))
        {
            self::$reportSubHeadingBackgroundColor = $params->reportSubHeadingBackgroundColor;
        }

        /**
         * Brand Text
         */
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

        if (isset($params->dealerSku) && !is_null($params->dealerSku))
        {
            self::$dealerSku = $params->dealerSku;
        }
    }

    /**
     * @return array
     */
    public static function toArray ()
    {
        return array(
            "reportTitlePageTitleBackgroundColor"       => self::$reportTitlePageTitleBackgroundColor,
            "reportTitlePageInformationBackgroundColor" => self::$reportTitlePageInformationBackgroundColor,
            "reportWhiteTitlePageTextColor"             => self::$reportWhiteTitlePageTextColor,
            "reportTitlePageTitleColor"                 => self::$reportTitlePageTitleColor,
            "reportTitlePageInformationColor"           => self::$reportTitlePageInformationColor,
            "reportHeadingColor"                        => self::$reportHeadingColor,
            "reportHeadingBackgroundColor"              => self::$reportHeadingBackgroundColor,
            "reportSubHeadingColor"                     => self::$reportSubHeadingColor,
            "reportSubHeadingBackgroundColor"           => self::$reportSubHeadingBackgroundColor,
            "brandName"                                 => self::$brandName,
            "companyName"                               => self::$companyName,
            "companyNameFull"                           => self::$companyNameFull,
            "jit"                                       => self::$jit,
            "jitName"                                   => self::$jitName,
            "jitFullName"                               => self::$jitFullName,
            "dealerSku"                                 => self::$dealerSku,
        );
    }

    /**
     * @return array
     */
    public static function getColorVariablesAsArray ()
    {
        return array(
            "reportTitlePageTitleBackgroundColor"       => self::$reportTitlePageTitleBackgroundColor,
            "reportTitlePageInformationBackgroundColor" => self::$reportTitlePageInformationBackgroundColor,
            "reportWhiteTitlePageTextColor"             => self::$reportWhiteTitlePageTextColor,
            "reportTitlePageTitleColor"                 => self::$reportTitlePageTitleColor,
            "reportTitlePageInformationColor"           => self::$reportTitlePageInformationColor,
            "reportHeadingColor"                        => self::$reportHeadingColor,
            "reportHeadingBackgroundColor"              => self::$reportHeadingBackgroundColor,
            "reportSubHeadingColor"                     => self::$reportSubHeadingColor,
            "reportSubHeadingBackgroundColor"           => self::$reportSubHeadingBackgroundColor,
        );
    }

}