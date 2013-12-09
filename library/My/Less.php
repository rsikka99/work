<?php
class My_Less
{
    /**
     * @var \lessc
     */
    protected static $less;

    /**
     * Gets an instance of our less compiler
     *
     * @return lessc
     */
    public static function getLess ()
    {
        if (!isset(self::$less))
        {
            self::$less = new lessc();
        }

        return self::$less;
    }

    /**
     * Shortcut function to set less variables
     *
     * @param $variables
     */
    public static function setLessVariables ($variables)
    {
        self::getLess()->setVariables($variables);
    }

    /**
     * Compiles less on demand. Stores a .cache file in the same folder as $outputFile
     *
     * @param string $inputFile
     * @param string $outputFile
     * @param bool   $forceRecompile
     *
     * @throws Exception
     */
    public static function autoCompileLess ($inputFile, $outputFile, $theme = 'default', $forceRecompile = false)
    {
        try
        {

            /**
             * Load the cache
             */
            $cacheFile      = $outputFile . ".cache";
            $themeCacheFile = $outputFile . ".theme.cache";

            $cache          = (file_exists($cacheFile) && !$forceRecompile) ? unserialize(file_get_contents($cacheFile)) : $inputFile;
            $builtWithTheme = (file_exists($themeCacheFile)) ? unserialize(file_get_contents($themeCacheFile)) : false;

            if ($builtWithTheme === false || $builtWithTheme != $theme)
            {
                $forceRecompile = true;
            }

            $newCache = self::getLess()->cachedCompile($cache, $forceRecompile);

            if (!is_array($cache) || $newCache["updated"] > $cache["updated"])
            {
                file_put_contents($cacheFile, serialize($newCache));
                file_put_contents($themeCacheFile, serialize($theme));
                file_put_contents($outputFile, $newCache['compiled']);
            }

        }
        catch (Exception $e)
        {
            // FIXME lrobert: Better exception handling needed
            throw new Exception("Passing exception up the chain.", 0, $e);
        }
    }
}