<?php
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\HardwareOptimizationMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\ViewModels\OptimizationViewModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\ViewModels\DevicesViewModel;

/**
 * Class MPSToolbox_Settings_Form_AllSettingsTest
 *
 * @property OptimizationViewModel model
 * @property HardwareOptimizationModel hardwareOptimizationModel
 */
class MPSToolbox_Legacy_Modules_HardwareOptimization_ViewModels_OptimizationViewModelTest extends My_DatabaseTestCase
{
    public $fixtures = [
        'images','dealers','clients','users',
        'toner_configs','manufacturers',
        'master_devices',
        'rms_providers','rms_devices','rms_uploads','rms_upload_rows',
        'device_instances','device_instance_meters','device_instance_master_devices',
        'device_swap_reason_categories','device_swap_reasons',
        'device_swaps','hardware_optimizations','hardware_optimization_device_instances',
        'toner_vendor_ranking_sets','fleet_settings','generic_settings','optimization_settings','quote_settings','dealer_settings'
    ];

    public function setUp()
    {
        parent::setUp();
        My_Model_Abstract::setAuthDealerId(2);

        $this->hardwareOptimizationModel = HardwareOptimizationMapper::getInstance()->find(1);
        $this->model = new OptimizationViewModel($this->hardwareOptimizationModel);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testGetDevices() {
        $result = $this->model->getDevices();
        $this->assertTrue($result instanceof DevicesViewModel);
        $this->assertEquals(28, $result->allDeviceInstances->getCount());
    }

    public function testGetNumberOfDevicesWithReplacements() {
        $result = $this->model->getNumberOfDevicesWithReplacements();
        $this->assertEquals(4, $result);
    }

}