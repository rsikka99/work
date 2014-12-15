<?php

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\PageCountModel;

class Proposalgen_Model_PageCountTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PageCountModel
     */
    protected $_pageCount;

    /**
     * @param bool $forceNewObject
     *
     * @return PageCountModel
     */
    public function getPageCount ($forceNewObject = false)
    {
        if (!isset($this->_pageCount) || $forceNewObject)
        {
            $this->_pageCount = new PageCountModel();
        }

        return $this->_pageCount;
    }

    public function testMonthlyIsAccurate ()
    {
        $pageCount = $this->getPageCount(true);
        $pageCount->setDaily(100.524);
        $this->assertTrue(bccomp($pageCount->getMonthly(), 3055.9296) === 0);
    }

    public function testSettingDaily ()
    {
        $pageCount = $this->getPageCount(true);
        $pageCount->setDaily(400);
        $this->assertTrue(bccomp($pageCount->getDaily(), 400) === 0);
    }

    public function testAddingRecalculates ()
    {
        $pageCount    = $this->getPageCount(true);
        $newPageCount = new PageCountModel();
        $newPageCount->setDaily(100);
        $pageCount->add($newPageCount);
        $this->assertTrue(bccomp($pageCount->getYearly(), 36524.2) === 0);
        $pageCount->add($newPageCount);
        $this->assertTrue(bccomp($pageCount->getYearly(), 73048.4) === 0);
    }

    public function testSubtractingRecalculates ()
    {
        $pageCount = $this->getPageCount(true);
        $pageCount->setDaily(300);
        $newPageCount = new PageCountModel();
        $newPageCount->setDaily(100);
        $pageCount->subtract($newPageCount);
        $this->assertTrue(bccomp($pageCount->getYearly(), 73048.4) === 0);
        $pageCount->subtract($newPageCount);
        $this->assertTrue(bccomp($pageCount->getYearly(), 36524.2) === 0);
    }
}