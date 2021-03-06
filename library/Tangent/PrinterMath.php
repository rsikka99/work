<?php

namespace Tangent;

class PrinterMath
{
    /*
     * Toner Colors
     */
    const TONER_COLOR_BLACK       = 1;
    const TONER_COLOR_CYAN        = 2;
    const TONER_COLOR_MAGENTA     = 3;
    const TONER_COLOR_YELLOW      = 4;
    const TONER_COLOR_THREE_COLOR = 5;
    const TONER_COLOR_FOUR_COLOR  = 6;

    /*
     * Toner Configurations
     */
    const TONER_CONFIG_BLACK_ONLY            = 1;
    const TONER_CONFIG_THREE_COLOR_SEPARATED = 2;
    const TONER_CONFIG_THREE_COLOR_COMBINED  = 3;
    const TONER_CONFIG_FOUR_COLOR_COMBINED   = 4;

    /**
     * The coverage that most manufacturers use to determine yield of a cartridge
     *
     * @var number
     */
    const OEM_COVERAGE_RATE = 0.05;

    /**
     * Takes an OEM cost per page and changes the assumed page coverage.
     * See the OEM_COVERAGE_RATE constant for the assumed OEM coverage
     *
     * @param number $costPerPage
     *            The cost per page to adjust.
     * @param number $coverage
     *            The new coverage % (eg 20 for 20%).
     * @param number $tonerColorId
     *            The toner color id of the toner cpp we're applying a new coverage to.
     *
     * @throws \InvalidArgumentException When toner color id is invalid or when page coverage is not between 0 and 100
     * @return number The adjusted cost per page
     */
    public static function coverageAdjustTonerCostPerPage ($costPerPage, $coverage, $tonerColorId)
    {
        if ($costPerPage > 0)
        {
            // If we have a valid page coverage amount
            if ($coverage >= 0 && $coverage <= 100)
            {
                // Convert to a decimal
                if ($coverage > 1)
                {
                    $coverage = $coverage / 100;
                }

                // Split up the coverage if needed based on what color we're processing
                switch ($tonerColorId)
                {
                    case self::TONER_COLOR_THREE_COLOR :
                        $coverage = ($coverage / 4) * 3;
                        break;
                    case self::TONER_COLOR_CYAN :
                    case self::TONER_COLOR_MAGENTA :
                    case self::TONER_COLOR_YELLOW :
                        $coverage = $coverage / 4;
                        break;
                    case self::TONER_COLOR_BLACK :
                    case self::TONER_COLOR_FOUR_COLOR :
                        // Coverage is fine as is
                        break;
                    default :
                        // They sent an invalid toner coverage
                        throw new \InvalidArgumentException("Toner Color {$tonerColorId} is not valid.");
                        break;
                }
                $costPerPage = $costPerPage * (self::OEM_COVERAGE_RATE / $coverage);
            }
            else
            {
                throw new \InvalidArgumentException("Page coverage must always be between 0 and 100 inclusively.");
            }
        }

        return $costPerPage;
    }

    /**
     * Takes an OEM cost per page and changes the assumed page coverage.
     * See the OEM_COVERAGE_RATE constant for the assumed OEM coverage
     *
     * @param number $costPerPage
     *            The cost per page to adjust.
     * @param number $coverage
     *            The new coverage % (eg 20 for 20%).
     * @param number $tonerConfigurationId
     *            The toner configuration id of the device's cost per page that we are applying a new coverage to.
     *
     * @throws \InvalidArgumentException When toner configuration id is invalid or when page coverage is not between 0
     *         and 100
     * @return number The adjusted cost per page
     */
    public static function adjustDeviceCostPerPage ($costPerPage, $coverage, $tonerConfigurationId)
    {
        if ($costPerPage > 0)
        {
            // If we have a valid page coverage amount
            if ($coverage >= 0 && $coverage <= 100)
            {
                // Convert to a decimal
                if ($coverage > 1)
                {
                    $coverage = $coverage / 100;
                }

                // Split up the coverage if needed based on what color we're processing
                switch ($tonerConfigurationId)
                {
                    case self::TONER_CONFIG_THREE_COLOR_COMBINED :
                    case self::TONER_CONFIG_THREE_COLOR_SEPARATED :
                        $coverage = ($coverage / 4) * 3;
                        break;
                    case self::TONER_CONFIG_BLACK_ONLY :
                    case self::TONER_CONFIG_FOUR_COLOR_COMBINED :
                        // Coverage is fine as is
                        break;
                    default :
                        // They sent an invalid toner coverage
                        throw new \InvalidArgumentException("Toner Color {$tonerConfigurationId} is not valid.");
                        break;
                }
                $costPerPage = $costPerPage * (self::OEM_COVERAGE_RATE / $coverage);
            }
            else
            {
                throw new \InvalidArgumentException("Page coverage must always be between 0 and 100 inclusively.");
            }
        }

        return $costPerPage;
    }
}