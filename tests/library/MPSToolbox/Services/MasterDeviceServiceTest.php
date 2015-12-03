<?php
class MasterDeviceServiceTest extends My_DatabaseTestCase {

    public $fixtures = [
        'clients',
        'master_devices',
        'toners',
        'device_toners',
        'ingram_products',
        'ingram_prices',
        'dealer_toner_attributes',
        'rms_device_instances',
    ];

    public function test_getIncomplete() {
        $service = new \MPSToolbox\Legacy\Modules\HardwareLibrary\Services\MasterDeviceService();
        $result = $service->getIncomplete(5,2);
        $this->assertEquals(18, count($result));
    }

}