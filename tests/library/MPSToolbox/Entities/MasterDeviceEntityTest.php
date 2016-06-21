<?php

class MasterDeviceEntityTest extends My_DatabaseTestCase {

    public $fixtures = ['base_printer', 'base_printer_cartridge', 'oem_printing_device_consumable'];

    public function test_load() {
        $result = \MPSToolbox\Entities\MasterDeviceEntity::find(1);
        $this->assertTrue($result instanceof \MPSToolbox\Entities\MasterDeviceEntity);
    }

    public function test_getToners() {
        $md = \MPSToolbox\Entities\MasterDeviceEntity::find(1);
        $result = $md->getToners();
        $this->assertEquals(4,count($result));
    }

}