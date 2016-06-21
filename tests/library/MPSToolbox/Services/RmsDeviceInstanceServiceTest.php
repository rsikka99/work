<?php
class RmsDeviceInstanceServiceTest extends My_DatabaseTestCase {

    public $fixtures = [
        'clients',
        'base_printer',
        'base_printer_cartridge',
        'oem_printing_device_consumable',
        'ingram_products',
        'ingram_prices',
        'dealer_toner_attributes',
        'rms_device_instances',
    ];

    public function test_getIncomplete() {
        $service = new \MPSToolbox\Services\RmsDeviceInstanceService();
        $result = $service->getIncomplete(5,2);
        $this->assertEquals(9, count($result));
    }

}