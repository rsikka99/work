<?php

/**
 * Class My_Feature
 */
class My_Feature
{
    /**
     * Constants for each feature
     */
    const HARDWARE_OPTIMIZATION_MEMJET = "hardware_optimization_memjet";

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