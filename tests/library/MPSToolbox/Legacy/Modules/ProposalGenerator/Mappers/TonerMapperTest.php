<?php

use \MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;

class MPSToolbox_Legacy_Modules_ProposalGenerator_Mappers_TonerMapperTest extends My_DatabaseTestCase {

    public $fixtures = [ 'toners', 'master_devices', 'device_toners', 'dealers', 'dealer_toner_attributes' ];

    public function test_construct() {
        $result = TonerMapper::getInstance();
        $this->assertTrue($result instanceof TonerMapper);
    }

    public function test_getTonersForDevice() {
        $mapper = TonerMapper::getInstance();
        $result = $mapper->getTonersForDevice(1);
        $this->assertEquals(2, count($result));
        $this->assertEquals(4, count($result[1]));
        $this->assertEquals(4, count($result[3]));
    }

    public function test_getReportToners() {
        $mapper = TonerMapper::getInstance();
        $result = $mapper->getReportToners(1, 2, 5, 'level1');
        $this->assertTrue(isset($result[1][1][0]));
        $toner = $result[1][1][0];
        $this->assertEquals(1, $toner->isUsingDealerPricing);
        $this->assertEquals(21, $toner->calculatedCost);
    }

    public function test_fetchTonersAssignedToDevice() {

    }

    public function test_fetchTonersAssignedToDeviceForCurrentDealer() {

    }

    public function test_fetchTonersAssignedToDeviceWithMachineCompatibility() {

    }

    public function test_fetchTonersWithMachineCompatibilityUsingColorConfigId() {

    }

    public function test_fetchTonersWithMachineCompatibilityUsingColorId() {

    }

    public function test_fetchListOfToners() {

    }

    public function test_fetchListOfTonersWithMachineCompatibility() {

    }

    public function test_fetchListOfAffectedToners() {

    }

    public function test_getTonerPricingForExport() {

    }

    public function test_getTonerMatchupForExport() {

    }

    public function test_getDbNoRecordExistsValidator() {

    }

    public function test_fetchAllTonersWithMachineCompatibility() {

    }

    public function test_getCompatibleToners() {

    }

    public function test_findCompatibleToners() {

    }

    public function test_findOemToners() {

    }


}