<?php
/**
 * Class Healthcheck_Model_PageCount
 */
class Healthcheck_Model_PageCount
{
    /**
     * The days in a time interval.
     */
    const DAYS_IN_A_WEEK    = 7;
    const DAYS_IN_A_MONTH   = 30.4;
    const DAYS_IN_A_QUARTER = 91.3105;
    const DAYS_IN_A_YEAR    = 365.242;

    /**
     * @var int
     */
    protected $_daily = 0;

    /**
     * @var int
     */
    protected $_weekly;

    /**
     * @var int
     */
    protected $_monthly;

    /**
     * @var int
     */
    protected $_quarterly;

    /**
     * @var int
     */
    protected $_yearly;

    /**
     * @var bool
     */
    protected $_recalculate = false;

    /**
     * Adds a page count
     *
     * @param Healthcheck_Model_PageCount $pageCount
     *
     * @return $this
     */
    public function add ($pageCount)
    {
        $this->_daily += $pageCount->_daily;
        $this->_recalculate = true;

        return $this;
    }

    /**
     * Subtracts a page count
     *
     * @param Healthcheck_Model_PageCount $pageCount
     *
     * @return $this
     */
    public function subtract ($pageCount)
    {
        $this->_daily -= $pageCount->_daily;
        $this->_recalculate = true;

        return $this;
    }

    /**
     * Gets the daily page count
     *
     * @return int
     */
    public function getDaily ()
    {
        return $this->_daily;
    }

    /**
     * Gets the weekly page count
     *
     * @return int
     */
    public function getWeekly ()
    {
        if ($this->_recalculate)
        {
            $this->_resetCalculatedPageCounts();
        }

        if (!isset($this->_weekly))
        {
            $this->_weekly = $this->_daily * self::DAYS_IN_A_WEEK;
        }

        return $this->_weekly;
    }

    /**
     * Gets the monthly page count
     *
     * @return int
     */
    public function getMonthly ()
    {
        if ($this->_recalculate)
        {
            $this->_resetCalculatedPageCounts();
        }

        if (!isset($this->_monthly))
        {
            $this->_monthly = $this->_daily * self::DAYS_IN_A_MONTH;
        }

        return $this->_monthly;
    }

    /**
     * Gets the quarterly page count
     *
     * @return int
     */
    public function getQuarterly ()
    {
        if ($this->_recalculate)
        {
            $this->_resetCalculatedPageCounts();
        }

        if (!isset($this->_quarterly))
        {
            $this->_quarterly = $this->_daily * self::DAYS_IN_A_QUARTER;
        }

        return $this->_quarterly;
    }

    /**
     * Gets the yearly page count
     *
     * @return int
     */
    public function getYearly ()
    {
        if ($this->_recalculate)
        {
            $this->_resetCalculatedPageCounts();
        }

        if (!isset($this->_yearly))
        {
            $this->_yearly = $this->_daily * self::DAYS_IN_A_YEAR;
        }

        return $this->_yearly;
    }

    /**
     *  Resets all the calculated variables
     */
    protected function _resetCalculatedPageCounts ()
    {
        $this->_recalculate = false;
        $this->_weekly      = null;
        $this->_monthly     = null;
        $this->_quarterly   = null;
        $this->_yearly      = null;
    }

    /**
     * Sets the daily value
     * @param int $pageCount
     */
    public function setDaily($pageCount)
    {
        $this->_daily = $pageCount;
    }
}