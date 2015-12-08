<?php

class RmsUpdateEntityTest extends My_DatabaseTestCase {

    public $fixtures = ['clients','master_devices','rms_providers','rms_device_instances','rms_update','toners','device_toners'];

    public function test_load() {
        $result = \MPSToolbox\Entities\RmsUpdateEntity::find([
            'client'=>'185',
            'assetId'=>'197027cf-98ed-4834-a6c7-8ed19ea6f0a8',
            'ipAddress'=>'192.168.15.61',
            'serialNumber'=>'CN44PGX0HP'
        ]);
        $this->assertTrue($result instanceof \MPSToolbox\Entities\RmsUpdateEntity);
        $this->assertEquals(6.5, $result->getPageCoverageMonochrome());
    }

    public function test_needsToner() {
        /** @var \MPSToolbox\Entities\RmsUpdateEntity $device */
        $device = \MPSToolbox\Entities\RmsUpdateEntity::find([
            'rmsDeviceInstance'=>'393'
        ]);

        $meter=$device->getEndMeterBlack() - $device->getStartMeterBlack();
        $diff = date_diff($device->getMonitorStartDate(), $device->getMonitorEndDate());

        $daily = $meter/$diff->days;
        $settings = new \MPSToolbox\Settings\Entities\ShopSettingsEntity();
        $settings->thresholdDays = 10;
        $settings->thresholdPercent = 5;
        $result = $device->needsToner(\MPSToolbox\Entities\TonerColorEntity::BLACK, $daily, $settings);

        $this->assertFalse($result);

    }

}