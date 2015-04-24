<?php
use MPSToolbox\Legacy\Mappers\DealerBrandingMapper;
use MPSToolbox\Legacy\Mappers\DealerMapper;
use MPSToolbox\Legacy\Models\DealerBrandingModel;
use MPSToolbox\Legacy\Models\DealerModel;

/**
 * Class My_Brand
 */
class My_Brand
{
    // Brand Colors
    public static $reportWhiteTitlePageTextColor = "#000000";

    // Branding Text Replacements
    public static $jit       = "JIT";
    public static $dealerSku = "Dealer SKU";

    protected static $dealerBrandingCache = [];


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
        if (isset($params->reportWhiteTitlePageTextColor) && !is_null($params->reportWhiteTitlePageTextColor))
        {
            self::$reportWhiteTitlePageTextColor = $params->reportWhiteTitlePageTextColor;
        }

        /**
         * Brand Text
         */
        if (isset($params->jit) && !is_null($params->jit))
        {
            self::$jit = $params->jit;
        }

        if (isset($params->dealerSku) && !is_null($params->dealerSku))
        {
            self::$dealerSku = $params->dealerSku;
        }
    }

    /**
     * Gets the branding for a dealer
     *
     * @param null $dealerId
     *
     * @return DealerBrandingModel
     */
    public static function getDealerBranding ($dealerId = null)
    {
        if (!isset(self::$dealerBrandingCache))
        {
            self::$dealerBrandingCache = [];
        }

        if ($dealerId === null)
        {
            $dealerId = (Zend_Auth::getInstance()->hasIdentity()) ? Zend_Auth::getInstance()->getIdentity()->dealerId : 0;
        }

        if (!isset(self::$dealerBrandingCache[$dealerId]))
        {
            $dealerBranding = DealerBrandingMapper::getInstance()->find($dealerId);
            if (!$dealerBranding instanceof DealerBrandingModel)
            {
                $dealerBranding = new DealerBrandingModel();

                $dealer = DealerMapper::getInstance()->find($dealerId);
                if ($dealer instanceof DealerModel)
                {
                    $dealerBranding->dealerName      = $dealer->dealerName;
                    $dealerBranding->shortDealerName = $dealer->dealerName;
                }

            }
            self::$dealerBrandingCache[$dealerId] = $dealerBranding;
        }

        return self::$dealerBrandingCache[$dealerId];
    }

    /**
     * Reset the dealer branding cache
     */
    public static function resetDealerBrandingCache ()
    {
        self::$dealerBrandingCache = [];
    }

    /**
     * @param int $dealerId
     *
     * @return array
     */
    public static function toArray ($dealerId = null)
    {
        $dealerBranding = self::getDealerBranding($dealerId);

        return [
            "reportTitlePageTitleColor"                 => $dealerBranding->titlePageTitleFontColor,
            "reportTitlePageTitleBackgroundColor"       => $dealerBranding->titlePageTitleBackgroundColor,
            "reportTitlePageInformationColor"           => $dealerBranding->titlePageInformationFontColor,
            "reportTitlePageInformationBackgroundColor" => $dealerBranding->titlePageInformationBackgroundColor,
            "reportHeadingColor"                        => $dealerBranding->h1FontColor,
            "reportHeadingBackgroundColor"              => $dealerBranding->h1BackgroundColor,
            "reportSubHeadingColor"                     => $dealerBranding->h2FontColor,
            "reportSubHeadingBackgroundColor"           => $dealerBranding->h2BackgroundColor,

            // TODO lrobert: Fix this color to either be hard coded into for the Print IQ version of the software
            "reportWhiteTitlePageTextColor"             => self::$reportWhiteTitlePageTextColor,

            "brandName"                                 => $dealerBranding->mpsProgramName,
            "companyName"                               => $dealerBranding->dealerName,
            "companyNameFull"                           => $dealerBranding->shortDealerName,
            "jit"                                       => $dealerBranding->dealerName,
            "jitName"                                   => $dealerBranding->shortJitProgramName,
            "jitFullName"                               => $dealerBranding->jitProgramName,
            "dealerSku"                                 => self::$dealerSku,
        ];
    }

    /**
     * @param int $dealerId
     *
     * @return array
     */
    public static function getColorVariablesAsArray ($dealerId = null)
    {
        $dealerBranding = self::getDealerBranding($dealerId);

        return [
            "reportTitlePageTitleColor"                 => $dealerBranding->titlePageTitleFontColor,
            "reportTitlePageTitleBackgroundColor"       => $dealerBranding->titlePageTitleBackgroundColor,
            "reportTitlePageInformationColor"           => $dealerBranding->titlePageInformationFontColor,
            "reportTitlePageInformationBackgroundColor" => $dealerBranding->titlePageInformationBackgroundColor,
            "reportHeadingColor"                        => $dealerBranding->h1FontColor,
            "reportHeadingBackgroundColor"              => $dealerBranding->h1BackgroundColor,
            "reportSubHeadingColor"                     => $dealerBranding->h2FontColor,
            "reportSubHeadingBackgroundColor"           => $dealerBranding->h2BackgroundColor,

            // TODO lrobert: Fix this color to either be hard coded into for the Print IQ version of the software
            "reportWhiteTitlePageTextColor"             => self::$reportWhiteTitlePageTextColor,
        ];
    }

}