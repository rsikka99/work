<?php

class Proposalgen_Model_TonerTest extends PHPUnit_Framework_TestCase
{
    protected $_costPerPageSetting;

    public function getCostPerPageSetting ()
    {
        if (!isset($this->_costPerPageSetting))
        {
            $this->_costPerPageSetting                         = new Proposalgen_Model_CostPerPageSetting(array('dealerId' => 2));
            $this->_costPerPageSetting->adminCostPerPage       = 0.05;
            $this->_costPerPageSetting->laborCostPerPage       = 0.06;
            $this->_costPerPageSetting->partsCostPerPage       = 0.07;
            $this->_costPerPageSetting->pageCoverageMonochrome = 4;
            $this->_costPerPageSetting->pageCoverageColor      = 18;
        }

        return $this->_costPerPageSetting;
    }

    public function calculateCostPerPageData ()
    {
        return array(
            array(
                0.00912, 0,
                array(
                    'cost'           => 85.5,
                    'calculatedCost' => 85.5,
                    'yield'          => 7500,
                    'tonerColorId'   => Proposalgen_Model_TonerColor::BLACK,
                ),
            ),
            array(
                0, 0.01026,
                array(
                    'cost'           => 85.5,
                    'calculatedCost' => 85.5,
                    'yield'          => 7500,
                    'tonerColorId'   => Proposalgen_Model_TonerColor::CYAN,
                ),
            ),
            array(
                0, 0.01026,
                array(
                    'cost'           => 85.5,
                    'calculatedCost' => 85.5,
                    'yield'          => 7500,
                    'tonerColorId'   => Proposalgen_Model_TonerColor::MAGENTA,
                ),
            ),
            array(
                0, 0.01026,
                array(
                    'cost'           => 85.5,
                    'calculatedCost' => 85.5,
                    'yield'          => 7500,
                    'tonerColorId'   => Proposalgen_Model_TonerColor::YELLOW,
                ),
            ),
            array(
                0, 0.03078,
                array(
                    'cost'           => 85.5,
                    'calculatedCost' => 85.5,
                    'yield'          => 7500,
                    'tonerColorId'   => Proposalgen_Model_TonerColor::THREE_COLOR,
                ),
            ),
            array(
                0.00912, 0.04104,
                array(
                    'cost'           => 85.5,
                    'calculatedCost' => 85.5,
                    'yield'          => 7500,
                    'tonerColorId'   => Proposalgen_Model_TonerColor::FOUR_COLOR,
                ),
            ),
        );
    }

    /**
     * @dataProvider calculateCostPerPageData
     */
    public function testCalculateCostPerPage ($expectedCostPerPageMonochrome, $expectedCostPerPageColor, $tonerData)
    {
        $toner       = new Proposalgen_Model_Toner($tonerData);
        $costPerPage = $toner->calculateCostPerPage($this->getCostPerPageSetting());

        if ($costPerPage instanceof Proposalgen_Model_CostPerPage)
        {
            if ($costPerPage->monochromeCostPerPage != $expectedCostPerPageMonochrome)
            {
                $this->fail("Toner monochrome CPP incorrect!");
            }

            if ($costPerPage->colorCostPerPage != $expectedCostPerPageColor)
            {
                $this->fail("Toner color CPP incorrect!");
            }

        }
        else
        {
            $this->fail("Did not receive a cost per page model from toner");
        }
    }
}