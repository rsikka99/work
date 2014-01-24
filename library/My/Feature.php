<?php

/**
 * Class My_Feature
 */
class My_Feature
{
    /**
     * Constants for each feature
     */
    const MEMJET_OPTIMIZATION                        = "hardware_optimization_memjet";
    const HARDWARE_OPTIMIZATION                      = "hardware_optimization";
    const HARDWARE_QUOTE                             = "hardware_quote";
    const HEALTHCHECK                                = "healthcheck";
    const HEALTHCHECK_PRINTIQ                        = "healthcheck_printiq";
    const ASSESSMENT                                 = "assessment";
    const ASSESSMENT_CUSTOMER_COST_ANALYSIS          = "assessment_customer_cost_analysis";
    const ASSESSMENT_GROSS_MARGIN                    = "assessment_gross_margin";
    const ASSESSMENT_TONER_VENDOR_GROSS_MARGIN       = "assessment_toner_vendor_gross_margin";
    const ASSESSMENT_JIT_SUPPLY_AND_TONER_SKU_REPORT = "assessment_jit_supply_and_toner_sku_report";
    const ASSESSMENT_OLD_DEVICE_LIST                 = "assessment_old_device_list";
    const ASSESSMENT_PRINTING_DEVICE_LIST            = "assessment_printing_device_list";
    const ASSESSMENT_SOLUTION                        = "assessment_solution";
    const ASSESSMENT_LEASE_BUYBACK                   = "assessment_lease_buyback";
    const ASSESSMENT_FLEET_ATTRIBUTES                = "assessment_fleet_attributes";
    const ASSESSMENT_UTILIZATION                     = "assessment_utilization";

    /**
     * @var My_Feature_AdapterInterface
     */
    protected static $_adapter;

    /**
     * @var string[]
     */
    protected static $_features;

    /**
     * Checks to see if this dealer has access to a certain feature
     *
     * @param string $feature
     *
     * @return bool
     */
    public static function canAccess ($feature)
    {
        return in_array($feature, self::getFeatures());
    }

    /**
     * @return string[]
     */
    public static function getFeatures ()
    {
        if (!isset(self::$_features))
        {
            self::$_features = self::getAdapter()->getFeatures();
        }

        return self::$_features;
    }

    /**
     * Gets the adapter
     *
     * @return My_Feature_AdapterInterface
     * @throws Exception
     */
    public static function getAdapter ()
    {
        if (!isset(self::$_adapter))
        {
            Throw new Exception("My_Feature_MapperAdapter has not been set");
        }

        return self::$_adapter;
    }

    /**
     * Sets the adapter
     *
     * @param $adapter
     */
    public static function setAdapter ($adapter)
    {
        self::$_adapter = $adapter;
    }
}