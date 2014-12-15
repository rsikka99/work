<?php

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\PageCountsModel;

class Proposalgen_Model_PageCountsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PageCountsModel
     */
    protected $_pageCounts;

    /**
     * @param bool $forceNewObject
     *
     * @return PageCountsModel
     */
    public function getPageCounts ($forceNewObject = false)
    {
        if (!isset($this->_pageCounts) || $forceNewObject)
        {
            $this->_pageCounts = new PageCountsModel();
        }

        return $this->_pageCounts;
    }

    public function testAddingRecalculates ()
    {
        $pageCounts    = $this->getPageCounts(true);
        $newPageCounts = new PageCountsModel();
        $newPageCounts->getBlackPageCount()->setDaily(100);
        $pageCounts->add($newPageCounts);
        $this->assertTrue(bccomp($pageCounts->getBlackPageCount()->getYearly(), 36524.2) === 0);
        $pageCounts->add($newPageCounts);
        $this->assertTrue(bccomp($pageCounts->getBlackPageCount()->getYearly(), 73048.4) === 0);
    }

    public function testSubtractingRecalculates ()
    {
        $pageCounts = $this->getPageCounts(true);
        $pageCounts->getPrintA3ColorPageCount()->setDaily(300);
        $newPageCounts = new PageCountsModel();
        $newPageCounts->getPrintA3ColorPageCount()->setDaily(100);
        $pageCounts->subtract($newPageCounts);
        $this->assertTrue(bccomp($pageCounts->getPrintA3ColorPageCount()->getYearly(), 73048.4) === 0);
        $pageCounts->subtract($newPageCounts);
        $this->assertTrue(bccomp($pageCounts->getPrintA3ColorPageCount()->getYearly(), 36524.2) === 0);
    }

    public function testPrintCombinedPageCountIsAccurate ()
    {
        $pageCounts    = $this->getPageCounts(true);
        $newPageCounts = new PageCountsModel();
        $newPageCounts->getBlackPageCount()->setDaily(200);
        $newPageCounts->getColorPageCount()->setDaily(175);
        $pageCounts->add($newPageCounts); //375 * 30.4 = 11400

        $this->assertTrue(bccomp($pageCounts->getCombinedPageCount()->getDaily(), 375) === 0);
        $this->assertTrue(bccomp($pageCounts->getCombinedPageCount()->getWeekly(), 2625) === 0);
        $this->assertTrue(bccomp($pageCounts->getCombinedPageCount()->getMonthly(), 11400) === 0);
        $this->assertTrue(bccomp($pageCounts->getCombinedPageCount()->getQuarterly(), 34241.4375) === 0);
        $this->assertTrue(bccomp($pageCounts->getCombinedPageCount()->getYearly(), 136965.75) === 0);
    }

    public function testCopyCombinedPageCountIsAccurate ()
    {
        $pageCounts    = $this->getPageCounts(true);
        $newPageCounts = new PageCountsModel();
        $newPageCounts->getCopyBlackPageCount()->setDaily(1100);
        $newPageCounts->getCopyColorPageCount()->setDaily(175);
        $pageCounts->add($newPageCounts);

        $this->assertTrue(bccomp($pageCounts->getCopyCombinedPageCount()->getDaily(), 1275) === 0);
        $this->assertTrue(bccomp($pageCounts->getCopyCombinedPageCount()->getWeekly(), 8925) === 0);
        $this->assertTrue(bccomp($pageCounts->getCopyCombinedPageCount()->getMonthly(), 38760) === 0);
        $this->assertTrue(bccomp($pageCounts->getCopyCombinedPageCount()->getQuarterly(), 116420.8875) === 0);
        $this->assertTrue(bccomp($pageCounts->getCopyCombinedPageCount()->getYearly(), 465683.55) === 0);
    }

    public function testA3PrintCombinedPageCountIsAccurate ()
    {
        $pageCounts    = $this->getPageCounts(true);
        $newPageCounts = new PageCountsModel();
        $newPageCounts->getPrintA3BlackPageCount()->setDaily(.05);
        $newPageCounts->getPrintA3ColorPageCount()->setDaily(.24);
        $pageCounts->add($newPageCounts);

        $this->assertTrue(bccomp($pageCounts->getPrintA3CombinedPageCount()->getDaily(), 0.29) === 0);
        $this->assertTrue(bccomp($pageCounts->getPrintA3CombinedPageCount()->getWeekly(), 2.03) === 0);
        $this->assertTrue(bccomp($pageCounts->getPrintA3CombinedPageCount()->getMonthly(), 8.816) === 0);
        $this->assertTrue(bccomp($pageCounts->getPrintA3CombinedPageCount()->getQuarterly(), 26.480045) === 0);
        $this->assertTrue(bccomp($pageCounts->getPrintA3CombinedPageCount()->getYearly(), 105.92018) === 0);
    }
}