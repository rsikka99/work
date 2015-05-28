<?php

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;

class Proposalgen_Model_TonerTest extends PHPUnit_Framework_TestCase
{
    protected $_costPerPageSetting;

    public function getCostPerPageSetting ()
    {
        if (!isset($this->_costPerPageSetting))
        {
            $this->_costPerPageSetting                         = new CostPerPageSettingModel(array('dealerId' => 2));
            $this->_costPerPageSetting->adminCostPerPage       = 0.05;
            $this->_costPerPageSetting->monochromeLaborCostPerPage       = 0.06;
            $this->_costPerPageSetting->monochromePartsCostPerPage       = 0.07;
            $this->_costPerPageSetting->colorLaborCostPerPage  = 0.06;
            $this->_costPerPageSetting->colorPartsCostPerPage  = 0.07;
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
                    'tonerColorId'   => TonerColorModel::BLACK,
                ),
            ),
            array(
                0, 0.01026,
                array(
                    'cost'           => 85.5,
                    'calculatedCost' => 85.5,
                    'yield'          => 7500,
                    'tonerColorId'   => TonerColorModel::CYAN,
                ),
            ),
            array(
                0, 0.01026,
                array(
                    'cost'           => 85.5,
                    'calculatedCost' => 85.5,
                    'yield'          => 7500,
                    'tonerColorId'   => TonerColorModel::MAGENTA,
                ),
            ),
            array(
                0, 0.01026,
                array(
                    'cost'           => 85.5,
                    'calculatedCost' => 85.5,
                    'yield'          => 7500,
                    'tonerColorId'   => TonerColorModel::YELLOW,
                ),
            ),
            array(
                0, 0.01026,
                array(
                    'cost'           => 85.5,
                    'calculatedCost' => 85.5,
                    'yield'          => 7500,
                    'tonerColorId'   => TonerColorModel::THREE_COLOR,
                ),
            ),
            array(
                0.00912, 0.01026,
                array(
                    'cost'           => 85.5,
                    'calculatedCost' => 85.5,
                    'yield'          => 7500,
                    'tonerColorId'   => TonerColorModel::FOUR_COLOR,
                ),
            ),
        );
    }

    /**
     * @dataProvider calculateCostPerPageData
     */
    public function testCalculateCostPerPage ($expectedCostPerPageMonochrome, $expectedCostPerPageColor, $tonerData)
    {
        $toner       = new TonerModel($tonerData);
        $costPerPage = $toner->calculateCostPerPage($this->getCostPerPageSetting());

        if ($costPerPage instanceof CostPerPageModel)
        {
            #if ($costPerPage->monochromeCostPerPage != $expectedCostPerPageMonochrome)
            #{
            #    $this->fail("Toner monochrome CPP incorrect!");
            #}
            $this->assertEquals($expectedCostPerPageMonochrome, $costPerPage->monochromeCostPerPage);

            #if ($costPerPage->colorCostPerPage != $expectedCostPerPageColor)
            #{
            #    $this->fail("Toner color CPP incorrect!");
            #}
            $this->assertEquals($expectedCostPerPageColor, $costPerPage->colorCostPerPage);

        }
        else
        {
            $this->fail("Did not receive a cost per page model from toner");
        }
    }
}