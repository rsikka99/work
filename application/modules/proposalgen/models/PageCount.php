<?php

/**
 * Class Proposalgen_Model_PageCount
 */
class Proposalgen_Model_PageCount
{
    /**
     * The days in a time interval.
     */
    const DAYS_IN_A_WEEK    = 7;
    const DAYS_IN_A_MONTH   = 30.4;
    const DAYS_IN_A_QUARTER = 91.3105;
    const DAYS_IN_A_YEAR    = 365.242;

    /**
     * @var float
     */
    protected $_daily = 0;

    /**
     * @var float
     */
    protected $_weekly;

    /**
     * @var float
     */
    protected $_monthly;

    /**
     * @var float
     */
    protected $_quarterly;

    /**
     * @var float
     */
    protected $_yearly;

    /**
     * Adds a page count
     *
     * @param Proposalgen_Model_PageCount $pageCount
     *
     * @return $this
     */
    public function add ($pageCount)
    {
        $this->_daily += $pageCount->_daily;
        $this->_resetCalculatedPageCounts();

        return $this;
    }

    /**
     * Subtracts a page count
     *
     * @param Proposalgen_Model_PageCount $pageCount
     *
     * @return $this
     */
    public function subtract ($pageCount)
    {
        $this->_daily -= $pageCount->_daily;
        $this->_resetCalculatedPageCounts();

        return $this;
    }

    /**
     * Gets the daily page count
     *
     * @return float
     */
    public function getDaily ()
    {
        return $this->_daily;
    }

    /**
     * Sets the daily value
     *
     * @param float $pageCount
     *
     * @return $this
     */
    public function setDaily ($pageCount)
    {
        $this->_resetCalculatedPageCounts();
        $this->_daily = $pageCount;

        return $this;
    }

    /**
     * Gets the weekly page count
     *
     * @return float
     */
    public function getWeekly ()
    {
        if (!isset($this->_weekly))
        {
            $this->_weekly = $this->_daily * self::DAYS_IN_A_WEEK;
        }

        return $this->_weekly;
    }

    /**
     * Gets the monthly page count
     *
     * @return float
     */
    public function getMonthly ()
    {
        if (!isset($this->_monthly))
        {
            $this->_monthly = $this->_daily * self::DAYS_IN_A_MONTH;
        }

        return $this->_monthly;
    }

    /**
     * Gets the quarterly page count
     *
     * @return float
     */
    public function getQuarterly ()
    {
        if (!isset($this->_quarterly))
        {
            $this->_quarterly = $this->_daily * self::DAYS_IN_A_QUARTER;
        }

        return $this->_quarterly;
    }

    /**
     * Gets the yearly page count
     *
     * @return float
     */
    public function getYearly ()
    {
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
        $this->_weekly    = null;
        $this->_monthly   = null;
        $this->_quarterly = null;
        $this->_yearly    = null;
    }
}