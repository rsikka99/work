<?php

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageModel;

class Proposalgen_Model_CostPerPageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CostPerPageModel
     */
    protected $_costPerPage;

    /**
     * @param bool $forceNewObject
     *
     * @return CostPerPageModel
     */
    public function getCostPerPage ($forceNewObject = false)
    {
        if (!isset($this->_costPerPage) || $forceNewObject)
        {
            $this->_costPerPage = new CostPerPageModel();
        }

        return $this->_costPerPage;
    }

    public function testCostPerPageIsZero ()
    {
        $costPerPage = $this->getCostPerPage();
        $this->assertTrue($costPerPage->monochromeCostPerPage === 0 && $costPerPage->colorCostPerPage === 0);

        $this->assertSame($costPerPage->toArray(), array('monochromeCostPerPage' => 0, 'colorCostPerPage' => 0));
    }

    public function testAddingCostPerPage ()
    {
        $costPerPage                        = $this->getCostPerPage();
        $costPerPage->monochromeCostPerPage = 0.05;
        $costPerPage->colorCostPerPage      = 0.95;

        $costPerPage->add($costPerPage);

        $this->assertTrue($costPerPage->monochromeCostPerPage === 0.10 && $costPerPage->colorCostPerPage === 1.9);
        $this->assertSame($costPerPage->toArray(), array('monochromeCostPerPage' => 0.10, 'colorCostPerPage' => 1.9));
    }
}