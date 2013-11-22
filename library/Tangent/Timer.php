<?php

/**
 * A class to use to profile pages
 *
 * @author "Lee Robert"
 *
 */
class Tangent_Timer
{
    static $_milestones;

    /**
     * Sets a milestone for the page timer as well as the group
     *
     * @param string  $name                  The name of the milestone
     * @param string  $group                 Optional Group Name
     * @param boolean $includeInPageCategory Include in the default "Page" category. If no group is provided this parameter will be ignored
     */
    public static function Milestone ($name, $group = null, $includeInPageCategory = true)
    {
        $time = self::getMicrotimeFloat();
        if ($includeInPageCategory || is_null($group))
        {
            self::$_milestones ["Page"] [] = array(
                $name,
                $time
            );
        }
        if (!is_null($group))
        {
            self::$_milestones [$group] [] = array(
                $name,
                $time
            );
        }
    }

    /**
     * @return float The current microtime as a float
     */
    static function getMicrotimeFloat ()
    {
        list ($utime, $time) = explode(" ", microtime());

        return ((float)$utime + (float)$time);
    }

    static function getTotalTime ()
    {
        return number_format((self::$_milestones ["Page"] [count(self::$_milestones["Page"]) - 1][1] - self::$_milestones ["Page"][0] [1]), 5);
    }

    public static function getMilestones ()
    {
        return self::$_milestones;
    }
}