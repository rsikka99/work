<?php

/**
 * Class Proposalgen_Model_PageCounts
 */
class Proposalgen_Model_PageCounts
{
    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_blackPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_colorPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_combinedPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_printBlackPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_printColorPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_printCombinedPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_copyBlackPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_copyColorPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_copyCombinedPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_faxPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_scanPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_printA3BlackPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_printA3ColorPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_printA3CombinedPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_lifePageCount;

    /**
     * Creates a page counts object
     */
    public function __construct ()
    {
        $this->_blackPageCount        = new Proposalgen_Model_PageCount();
        $this->_colorPageCount        = new Proposalgen_Model_PageCount();
        $this->_printBlackPageCount   = new Proposalgen_Model_PageCount();
        $this->_printColorPageCount   = new Proposalgen_Model_PageCount();
        $this->_copyBlackPageCount    = new Proposalgen_Model_PageCount();
        $this->_copyColorPageCount    = new Proposalgen_Model_PageCount();
        $this->_faxPageCount          = new Proposalgen_Model_PageCount();
        $this->_scanPageCount         = new Proposalgen_Model_PageCount();
        $this->_printA3BlackPageCount = new Proposalgen_Model_PageCount();
        $this->_printA3ColorPageCount = new Proposalgen_Model_PageCount();
        $this->_lifePageCount         = new Proposalgen_Model_PageCount();

        $this->resetCombinedPageCounts();
    }

    /**
     * Adds a page count
     *
     * @param Proposalgen_Model_PageCounts $pageCounts
     */
    public function add ($pageCounts)
    {
        $this->_blackPageCount->add($pageCounts->_blackPageCount);
        $this->_colorPageCount->add($pageCounts->_colorPageCount);
        $this->_printBlackPageCount->add($pageCounts->_printBlackPageCount);
        $this->_printColorPageCount->add($pageCounts->_printColorPageCount);
        $this->_copyBlackPageCount->add($pageCounts->_copyBlackPageCount);
        $this->_copyColorPageCount->add($pageCounts->_copyColorPageCount);
        $this->_faxPageCount->add($pageCounts->_faxPageCount);
        $this->_scanPageCount->add($pageCounts->_scanPageCount);
        $this->_printA3BlackPageCount->add($pageCounts->_printA3BlackPageCount);
        $this->_printA3ColorPageCount->add($pageCounts->_printA3ColorPageCount);
        $this->_lifePageCount->add($pageCounts->_lifePageCount);

        $this->resetCombinedPageCounts();
    }

    /**
     * Subtracts a page count
     *
     * @param Proposalgen_Model_PageCounts $pageCounts
     */
    public function subtract ($pageCounts)
    {
        $this->_blackPageCount->subtract($pageCounts->_blackPageCount);
        $this->_colorPageCount->subtract($pageCounts->_colorPageCount);
        $this->_printBlackPageCount->subtract($pageCounts->_printBlackPageCount);
        $this->_printColorPageCount->subtract($pageCounts->_printColorPageCount);
        $this->_copyBlackPageCount->subtract($pageCounts->_copyBlackPageCount);
        $this->_copyColorPageCount->subtract($pageCounts->_copyColorPageCount);
        $this->_faxPageCount->subtract($pageCounts->_faxPageCount);
        $this->_scanPageCount->subtract($pageCounts->_scanPageCount);
        $this->_printA3BlackPageCount->subtract($pageCounts->_printA3BlackPageCount);
        $this->_printA3ColorPageCount->subtract($pageCounts->_printA3ColorPageCount);
        $this->_lifePageCount->subtract($pageCounts->_lifePageCount);

        $this->resetCombinedPageCounts();
    }

    /**
     * Resets all the combined and total page counts.
     */
    protected function resetCombinedPageCounts ()
    {
        $this->_printCombinedPageCount   = null;
        $this->_copyCombinedPageCount    = null;
        $this->_combinedPageCount        = null;
        $this->_printA3CombinedPageCount = null;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public function getBlackPageCount ()
    {
        return $this->_blackPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public function getColorPageCount ()
    {
        return $this->_colorPageCount;
    }


    /**
     * Monochrome page count percentage
     *
     * @return float
     */
    public function getMonochromePagePercentage ()
    {
        $percent = 0;
        if ($this->getBlackPageCount()->getDaily() > 0)
        {
            $percent = $this->getBlackPageCount()->getDaily() / $this->getCombinedPageCount()->getDaily() * 100;
        }

        return $percent;
    }

    /**
     * Color page count percentage
     *
     * @return float
     */
    public function getColorPagePercentage ()
    {
        $percent = 0;
        if ($this->getBlackPageCount()->getDaily() > 0)
        {
            $percent = $this->getColorPageCount()->getDaily() / $this->getCombinedPageCount()->getDaily() * 100;
        }

        return $percent;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public function getCombinedPageCount ()
    {
        if (!isset($this->_combinedPageCount))
        {
            $this->_combinedPageCount = new Proposalgen_Model_PageCount();
            $this->_combinedPageCount->add($this->_blackPageCount);
            $this->_combinedPageCount->add($this->_colorPageCount);
            $this->_combinedPageCount->add($this->_printA3BlackPageCount);
            $this->_combinedPageCount->add($this->_printA3ColorPageCount);
        }

        return $this->_combinedPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public function getPrintBlackPageCount ()
    {
        return $this->_printBlackPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getPrintColorPageCount ()
    {
        return $this->_printColorPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getPrintCombinedPageCount ()
    {
        if (!isset($this->_printCombinedPageCount))
        {
            $this->_printCombinedPageCount = new Proposalgen_Model_PageCount();
            $this->_printCombinedPageCount->add($this->_printBlackPageCount);
            $this->_printCombinedPageCount->add($this->_printColorPageCount);
        }

        return $this->_printCombinedPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getCopyBlackPageCount ()
    {
        return $this->_copyBlackPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getCopyColorPageCount ()
    {
        return $this->_copyColorPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getCopyCombinedPageCount ()
    {
        if (!isset($this->_copyCombinedPageCount))
        {
            $this->_copyCombinedPageCount = new Proposalgen_Model_PageCount();
            $this->_copyCombinedPageCount->add($this->_copyBlackPageCount);
            $this->_copyCombinedPageCount->add($this->_copyColorPageCount);
        }

        return $this->_copyCombinedPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getFaxPageCount ()
    {
        return $this->_faxPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getScanPageCount ()
    {
        return $this->_scanPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getPrintA3BlackPageCount ()
    {
        return $this->_printA3BlackPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getPrintA3ColorPageCount ()
    {
        return $this->_printA3ColorPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getPrintA3CombinedPageCount ()
    {
        if (!isset($this->_printA3CombinedPageCount))
        {
            $this->_printA3CombinedPageCount = new Proposalgen_Model_PageCount();
            $this->_printA3CombinedPageCount->add($this->_printA3BlackPageCount);
            $this->_printA3CombinedPageCount->add($this->_printA3ColorPageCount);
        }

        return $this->_printA3CombinedPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public function getLifePageCount ()
    {
        return $this->_lifePageCount;
    }

    public function processPageRatio ($blackToColorRatio)
    {
        $blackRatio = 1 - ($blackToColorRatio / 100);
        $colorRatio = $blackToColorRatio / 100;

        /**
         * Color Page Counts (Take $colorRatio of the black volume and put it into the color page count.)
         * Note: Color needs to be done as once the black page counts are modified they won't calculate properly for the color page counts.
         */

        // A3 Color
        $this->getPrintA3ColorPageCount()->setDaily($this->getPrintA3BlackPageCount()->getDaily() * $colorRatio);

        // A4 Color
        $this->getColorPageCount()->setDaily($this->getBlackPageCount()->getDaily() * $colorRatio);

        // Copy Color
        $this->getCopyColorPageCount()->setDaily($this->getCopyBlackPageCount()->getDaily() * $colorRatio);

        /**
         * Black Page Counts (Take $blackRatio of the black volume and put it into the black page count.)
         */

        // A3 Black
        $this->getPrintA3BlackPageCount()->setDaily($this->getPrintA3BlackPageCount()->getDaily() * $blackRatio);

        // A4 Black
        $this->getBlackPageCount()->setDaily($this->getBlackPageCount()->getDaily() * $blackRatio);

        // Copy Black
        $this->getCopyBlackPageCount()->setDaily($this->getCopyBlackPageCount()->getDaily() * $blackRatio);
    }

}