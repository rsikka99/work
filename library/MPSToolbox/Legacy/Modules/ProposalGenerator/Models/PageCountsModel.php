<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

/**
 * Class PageCountsModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class PageCountsModel
{
    /**
     * @var PageCountModel
     */
    protected $_blackPageCount;

    /**
     * @var PageCountModel
     */
    protected $_colorPageCount;

    /**
     * @var PageCountModel
     */
    protected $_combinedPageCount;

    /**
     * @var PageCountModel
     */
    protected $_printBlackPageCount;

    /**
     * @var PageCountModel
     */
    protected $_printColorPageCount;

    /**
     * @var PageCountModel
     */
    protected $_printCombinedPageCount;

    /**
     * @var PageCountModel
     */
    protected $_copyBlackPageCount;

    /**
     * @var PageCountModel
     */
    protected $_copyColorPageCount;

    /**
     * @var PageCountModel
     */
    protected $_copyCombinedPageCount;

    /**
     * @var PageCountModel
     */
    protected $_faxPageCount;

    /**
     * @var PageCountModel
     */
    protected $_scanPageCount;

    /**
     * @var PageCountModel
     */
    protected $_printA3BlackPageCount;

    /**
     * @var PageCountModel
     */
    protected $_printA3ColorPageCount;

    /**
     * @var PageCountModel
     */
    protected $_printA3CombinedPageCount;

    /**
     * @var PageCountModel
     */
    protected $_lifePageCount;

    /**
     * Creates a page counts object
     */
    public function __construct ()
    {
        $this->_blackPageCount        = new PageCountModel();
        $this->_colorPageCount        = new PageCountModel();
        $this->_printBlackPageCount   = new PageCountModel();
        $this->_printColorPageCount   = new PageCountModel();
        $this->_copyBlackPageCount    = new PageCountModel();
        $this->_copyColorPageCount    = new PageCountModel();
        $this->_faxPageCount          = new PageCountModel();
        $this->_scanPageCount         = new PageCountModel();
        $this->_printA3BlackPageCount = new PageCountModel();
        $this->_printA3ColorPageCount = new PageCountModel();
        $this->_lifePageCount         = new PageCountModel();

        $this->resetCombinedPageCounts();
    }

    /**
     * Adds a page count
     *
     * @param PageCountsModel $pageCounts
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
     * @param PageCountsModel $pageCounts
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
     * @return PageCountModel
     */
    public function getBlackPageCount ()
    {
        return $this->_blackPageCount;
    }

    /**
     * @return PageCountModel
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
     * @return PageCountModel
     */
    public function getCombinedPageCount ()
    {
        if (!isset($this->_combinedPageCount))
        {
            $this->_combinedPageCount = new PageCountModel();
            $this->_combinedPageCount->add($this->_blackPageCount);
            $this->_combinedPageCount->add($this->_colorPageCount);
            $this->_combinedPageCount->add($this->_printA3BlackPageCount);
            $this->_combinedPageCount->add($this->_printA3ColorPageCount);
        }

        return $this->_combinedPageCount;
    }

    /**
     * @return PageCountModel
     */
    public function getPrintBlackPageCount ()
    {
        return $this->_printBlackPageCount;
    }

    /**
     * @return PageCountModel
     */
    public Function getPrintColorPageCount ()
    {
        return $this->_printColorPageCount;
    }

    /**
     * @return PageCountModel
     */
    public Function getPrintCombinedPageCount ()
    {
        if (!isset($this->_printCombinedPageCount))
        {
            $this->_printCombinedPageCount = new PageCountModel();
            $this->_printCombinedPageCount->add($this->_printBlackPageCount);
            $this->_printCombinedPageCount->add($this->_printColorPageCount);
        }

        return $this->_printCombinedPageCount;
    }

    /**
     * @return PageCountModel
     */
    public Function getCopyBlackPageCount ()
    {
        return $this->_copyBlackPageCount;
    }

    /**
     * @return PageCountModel
     */
    public Function getCopyColorPageCount ()
    {
        return $this->_copyColorPageCount;
    }

    /**
     * @return PageCountModel
     */
    public Function getCopyCombinedPageCount ()
    {
        if (!isset($this->_copyCombinedPageCount))
        {
            $this->_copyCombinedPageCount = new PageCountModel();
            $this->_copyCombinedPageCount->add($this->_copyBlackPageCount);
            $this->_copyCombinedPageCount->add($this->_copyColorPageCount);
        }

        return $this->_copyCombinedPageCount;
    }

    /**
     * @return PageCountModel
     */
    public Function getFaxPageCount ()
    {
        return $this->_faxPageCount;
    }

    /**
     * @return PageCountModel
     */
    public Function getScanPageCount ()
    {
        return $this->_scanPageCount;
    }

    /**
     * @return PageCountModel
     */
    public Function getPrintA3BlackPageCount ()
    {
        return $this->_printA3BlackPageCount;
    }

    /**
     * @return PageCountModel
     */
    public Function getPrintA3ColorPageCount ()
    {
        return $this->_printA3ColorPageCount;
    }

    /**
     * @return PageCountModel
     */
    public Function getPrintA3CombinedPageCount ()
    {
        if (!isset($this->_printA3CombinedPageCount))
        {
            $this->_printA3CombinedPageCount = new PageCountModel();
            $this->_printA3CombinedPageCount->add($this->_printA3BlackPageCount);
            $this->_printA3CombinedPageCount->add($this->_printA3ColorPageCount);
        }

        return $this->_printA3CombinedPageCount;
    }

    /**
     * @return PageCountModel
     */
    public function getLifePageCount ()
    {
        return $this->_lifePageCount;
    }

    /**
     * @param float $blackToColorRatio percentage
     */
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
         *
         * Norm: 2014/10/29 - Requested that we convert x% of pages and turn them into color. Total print volume does not change, only type of page.
         */

        // A3 Black
        $this->getPrintA3BlackPageCount()->setDaily($this->getPrintA3BlackPageCount()->getDaily() * $blackRatio);

        // A4 Black
        $this->getBlackPageCount()->setDaily($this->getBlackPageCount()->getDaily() * $blackRatio);

        // Copy Black
        $this->getCopyBlackPageCount()->setDaily($this->getCopyBlackPageCount()->getDaily() * $blackRatio);
    }
}