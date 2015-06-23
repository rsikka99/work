<?php

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageModel;


/**
 * @property DeviceInstanceModel model
 * @property CostPerPageSettingModel cppSetting
 */
class MPSToolbox_Legacy_Modules_ProposalGenerator_Models_DeviceInstanceModelTest extends My_DatabaseTestCase
{

    public function setUp() {
        $this->model = new DeviceInstanceModel([
            'isManaged' => false,
        ]);
        $this->cppSetting = new \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel([
            "adminCostPerPage"              => 0.001,
            "monochromePartsCostPerPage"    => 0.002,
            "monochromeLaborCostPerPage"    => 0.003,
            "colorPartsCostPerPage"         => 0.004,
            "colorLaborCostPerPage"         => 0.005,
            "pageCoverageMonochrome"        => 6,
            "pageCoverageColor"             => 24,
            "useDevicePageCoverages"        => 0,
            "customerMonochromeCostPerPage" => 0.007,
            "customerColorCostPerPage"      => 0.008,
            "clientId"                      => 1,
            "dealerId"                      => 1,
            "pricingMargin"                 => 10,
            'useCustomerCostPerPageForManagedDevices' => true,
        ]);

        $this->model->setCombinedMonthlyPageCount(6000);
        $this->model->setBlackMonthlyPageCount(4000);
        $this->model->setColorMonthlyPageCount(2000);

        $cppDevice = $this->model->calculateCostPerPage($this->cppSetting);
        $toner = $this->getMock('\MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel');
        $toner->expects($this->once())
            ->method('calculateCostPerPage')
            ->will($this->returnValue(new CostPerPageModel([
                'monochromeCostPerPage'=>0.10,
                'colorCostPerPage'=>0.20,
            ])));
        $cppDevice->toners = [$toner];
    }

    public function tearDown() {

    }

    public function test_calculateCostPerPage() {
        $cppDevice = $this->model->calculateCostPerPage($this->cppSetting);
        $result = $cppDevice->toArray();
        $this->assertEquals([
            'costPerPageSetting'=>$this->cppSetting,
            'toners'=>$cppDevice->toners,
            'laborCostPerPage'=>null,
            'partsCostPerPage'=>null,
            'isManaged'=>false,
        ], $result);

        $cpp = $cppDevice->getCostPerPage();
        $this->assertEquals([
            'monochromeCostPerPage'=>
                (
                    \Tangent\Accounting::applyMargin(0.10,10)+
                    0.001+
                    0.004+
                    0.005
                ),
            'colorCostPerPage'=>
                (
                    \Tangent\Accounting::applyMargin(0.20,10)+
                    \Tangent\Accounting::applyMargin(0.10,10)+
                    0.001+
                    0.004+
                    0.005
                ),
        ], $cpp->toArray());
    }

    public function test_calculateMonthlyMonoCost() {
        $result = $this->model->calculateMonthlyMonoCost($this->cppSetting);
        $expected = 4000 * (
                \Tangent\Accounting::applyMargin(0.10,10)+
                0.001+
                0.004+
                0.005
        );
        $this->assertEquals($expected, $result);
    }

    public function test_calculateMonthlyColorCost() {
        $result = $this->model->calculateMonthlyColorCost($this->cppSetting);
        $expected = 2000 * (
                \Tangent\Accounting::applyMargin(0.20,10)+
                \Tangent\Accounting::applyMargin(0.10,10)+
                0.001+
                0.004+
                0.005
            );
        $this->assertEquals($expected, $result);
    }

    public function test_calculateMonthlyCost() {
        $result = $this->model->calculateMonthlyCost($this->cppSetting);
        $expected =
            (4000 * (
                \Tangent\Accounting::applyMargin(0.10,10)+
                0.001+
                0.004+
                0.005
            )) +
            (2000 * (
                \Tangent\Accounting::applyMargin(0.20,10)+
                \Tangent\Accounting::applyMargin(0.10,10)+
                0.001+
                0.004+
                0.005
        ));
        $this->assertEquals($expected, $result);
    }

}