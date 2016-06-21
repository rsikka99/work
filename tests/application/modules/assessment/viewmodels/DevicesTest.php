<?php

/**
 * Class Assessment_ViewModel_DevicesTest
 */
class Assessment_ViewModel_DevicesTest extends My_DatabaseTestCase
{

/*
TRUNCATE TABLE `rms_devices`;
TRUNCATE TABLE `rms_uploads`;
TRUNCATE TABLE `rms_upload_rows`;
TRUNCATE TABLE `device_instances`;
TRUNCATE TABLE `device_instance_meters`;
TRUNCATE TABLE `device_instance_master_devices`;
*/


    public $fixtures = [
        'images','dealers','clients','users',
        'toner_configs','manufacturers',
        'base_printer',
        'rms_providers','rms_devices','rms_uploads','rms_upload_rows',
        'device_instances','device_instance_meters','device_instance_master_devices'
    ];

    public function setUp()
    {
        parent::setUp();
        My_Model_Abstract::setAuthDealerId(2);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testConstruct() {
        $result = new Assessment_ViewModel_Devices(2);
        $this->assertTrue($result instanceof Assessment_ViewModel_Devices);
        $this->assertEquals(28, $result->allDeviceInstances->getCount());
        $this->assertEquals(1, $result->allDevicesWithShortMonitorInterval->getCount());
        $this->assertEquals(17, $result->allIncludedDeviceInstances->getCount());
        $this->assertEquals(2, $result->excludedDeviceInstances->getCount());
        $this->assertEquals(2, $result->leasedDeviceInstances->getCount());
        $this->assertEquals(15, $result->purchasedDeviceInstances->getCount());
        $this->assertEquals(8, $result->unmappedDeviceInstances->getCount());
    }

}