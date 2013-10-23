<?php

class Tangent_Statsd
{
    public static $rootBucket = 'mpstoolbox.unknownsite';
    public static $enabled = false;
    public static $host = '';
    public static $port = 8125;

    /**
     * Sets one or more timing values
     *
     * @param string|array $stats The metric(s) to set.
     * @param float        $time  The elapsed time (ms) to log
     **/
    public static function timing ($stats, $time)
    {
        Tangent_Statsd::updateStats($stats, $time, 1, 'ms');
    }

    /**
     * Sets one or more gauges to a value
     *
     * @param string|array $stats The metric(s) to set.
     * @param float        $value The value for the stats.
     **/
    public static function gauge ($stats, $value)
    {
        Tangent_Statsd::updateStats($stats, $value, 1, 'g');
    }

    /**
     * A "Set" is a count of unique events.
     * This data type acts like a counter, but supports counting
     * of unique occurrences of values between flushes. The backend
     * receives the number of unique events that happened since
     * the last flush.
     *
     * The reference use case involved tracking the number of active
     * and logged in users by sending the current userId of a user
     * with each request with a key of "uniques" (or similar).
     *
     * @param string|array $stats The metric(s) to set.
     * @param float        $value The value for the stats.
     **/
    public static function set ($stats, $value)
    {
        Tangent_Statsd::updateStats($stats, $value, 1, 's');
    }

    /**
     * Increments one or more stats counters
     *
     * @param string|array $stats      The metric(s) to increment.
     * @param float        $sampleRate The rate (0-1) for sampling.
     *
     * @return boolean
     **/
    public static function increment ($stats, $sampleRate = 1.0)
    {
        Tangent_Statsd::updateStats($stats, 1, $sampleRate, 'c');
    }

    /**
     * Decrements one or more stats counters.
     *
     * @param string|array $stats      The metric(s) to decrement.
     * @param float        $sampleRate The rate (0-1) for sampling.
     *
     * @return boolean
     **/
    public static function decrement ($stats, $sampleRate = 1.0)
    {
        Tangent_Statsd::updateStats($stats, -1, $sampleRate, 'c');
    }

    /**
     * Updates one or more stats.
     *
     * @param string|array $stats      The metric(s) to update. Should be either a string or array of metrics.
     * @param int          $delta      The amount to increment/decrement each metric by.
     * @param float        $sampleRate The rate (0-1) for sampling.
     * @param string       $metric     The metric type ("c" for count, "ms" for timing, "g" for gauge, "s" for set)
     *
     * @return boolean
     **/
    public static function updateStats ($stats, $delta = 1, $sampleRate = 1.0, $metric = 'c')
    {
        if (!is_array($stats))
        {
            $stats = array($stats);
        }
        $data = array();
        foreach ($stats as $stat)
        {
            $data[$stat] = "$delta|$metric";
        }

        Tangent_Statsd::send($data, $sampleRate);
    }

    /**
     * Squirt the metrics over UDP
     *
     * @param       $data
     * @param float $sampleRate The rate (0-1) for sampling.
     */
    public static function send ($data, $sampleRate = 1.0)
    {
        if (!self::$enabled)
        {
            return;
        }

        // Sampling
        $sampledData = array();

        if ($sampleRate < 1)
        {
            foreach ($data as $stat => $value)
            {
                if ((mt_rand() / mt_getrandmax()) <= $sampleRate)
                {
                    $sampledData[$stat] = "$value|@$sampleRate";
                }
            }
        }
        else
        {
            $sampledData = $data;
        }

        /**
         * Who would ever want to send nothing?
         *
         * ░░░░░░▄▄▄▄▀▀▀▀▀▀▀▀▄▄▄▄▄▄░░░░░░░
         * ░░░░░█░░░░▒▒▒▒▒▒▒▒▒▒▒▒░░▀▀▄░░░░
         * ░░░░█░░░▒▒▒▒▒▒░░░░░░░░▒▒▒░░█░░░
         * ░░░█░░░░░░▄██▀▄▄░░░░░▄▄▄░░░░█░░
         * ░▄▀▒▄▄▄▒░█▀▀▀▀▄▄█░░░██▄▄█░░░░█░
         * █░▒█▒▄░▀▄▄▄▀░░░░░░░░█░░░▒▒▒▒▒░█
         * █░▒█░█▀▄▄░░░░░█▀░░░░▀▄░░▄▀▀▀▄▒█
         * ░█░▀▄░█▄░█▀▄▄░▀░▀▀░▄▄▀░░░░█░░█░
         * ░░█░░░▀▄▀█▄▄░█▀▀▀▄▄▄▄▀▀█▀██░█░░
         * ░░░█░░░░██░░▀█▄▄▄█▄▄█▄████░█░░░
         * ░░░░█░░░░▀▀▄░█░░░█░█▀██████░█░░
         * ░░░░░▀▄░░░░░▀▀▄▄▄█▄█▄█▄█▄▀░░█░░
         * ░░░░░░░▀▄▄░▒▒▒▒░░░░░░░░░░▒░░░█░
         * ░░░░░░░░░░▀▀▄▄░▒▒▒▒▒▒▒▒▒▒░░░░█░
         * ░░░░░░░░░░░░░░▀▄▄▄▄▄░░░░░░░░█░░
         *
         */

        if (empty($sampledData))
        {
            return;
        }

        /**
         * Failures in any of this should be silently ignored
         */
        try
        {

            if (($filePointer = fsockopen("udp://" . self::$host, self::$port, $errorNumber, $errorMessage)) !== false)
            {
                foreach ($sampledData as $stat => $value)
                {
                    fwrite($filePointer, self::$rootBucket . "." . "$stat:$value");
                }
                fclose($filePointer);
            }
        }
        catch (Exception $e)
        {
        }
    }
}