<?php
class Healthcheck_Model_PageCounts
{
    /**
     * @var Healthcheck_Model_PageCount
     */
    public $monochrome;

    /**
     * @var Healthcheck_Model_PageCount
     */
    public $color;

    /**
     * @var Healthcheck_Model_PageCount
     */
    protected $_combined;

    /**
     * @var bool
     */
    protected $_recalculateCombined = false;

    public function __construct ()
    {
        $this->monochrome = new Healthcheck_Model_PageCount();
        $this->color      = new Healthcheck_Model_PageCount();
        $this->_combined  = new Healthcheck_Model_PageCount();
    }

    /**
     * Adds a page count
     *
     * @param Healthcheck_Model_PageCounts $pageCounts
     */
    public function add ($pageCounts)
    {
        $this->monochrome->add($pageCounts->monochrome);
        $this->color->add($pageCounts->color);
        $this->_recalculateCombined = true;
    }

    /**
     * Subtracts a page count
     *
     * @param Healthcheck_Model_PageCounts $pageCounts
     */
    public function subtract ($pageCounts)
    {
        $this->monochrome->subtract($pageCounts->monochrome);
        $this->color->subtract($pageCounts->color);
        $this->_recalculateCombined = true;
    }

    /**
     * Gets the combined page count
     *
     * @return Healthcheck_Model_PageCount
     */
    public function getCombined ()
    {
        if (!isset($this->_combined) || $this->_recalculateCombined)
        {
            $this->_recalculateCombined = false;
            $this->_combined            = new Healthcheck_Model_PageCount();
            $this->_combined->add($this->monochrome);
            $this->_combined->add($this->color);
        }

        return $this->_combined;
    }
}