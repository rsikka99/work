<?php

use \MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;

class MPSToolbox_Legacy_Modules_ProposalGenerator_Mappers_TonerMapperTest extends My_DatabaseTestCase {

    public $fixtures = [ 'base_printer_cartridge', 'base_printer', 'oem_printing_device_consumable', 'dealers', 'dealer_toner_attributes', 'currency_exchange' ];

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
        $result = $mapper->getReportToners(1, 2);
        $this->assertTrue(isset($result[1][1][0]));
        /** @var \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel $toner */
        $toner = $result[1][1][0];
        $this->assertEquals(1, $toner->isUsingDealerPricing);
        $this->assertEquals(0, $toner->isUsingCustomerPricing);
        $this->assertEquals(92.44, $toner->calculatedCost);

        $result = $mapper->getReportToners(1, 2, 5, 'level1');
        $this->assertTrue(isset($result[1][1][0]));
        /** @var \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel $toner */
        $toner = $result[1][1][0];
        $this->assertEquals(1, $toner->isUsingDealerPricing);
        $this->assertEquals(0, $toner->isUsingCustomerPricing);
        $this->assertEquals(21, $toner->calculatedCost);
    }

    public function test_fetchTonersAssignedToDevice() {
        $mapper = TonerMapper::getInstance();
        $result = $mapper->fetchTonersAssignedToDevice(1);
        $this->assertEquals(8, count($result));
        $this->assertEquals(1, $result[0]->id);
    }

    public function test_fetchTonersAssignedToDeviceForCurrentDealer() {
        $this->user2();
        $mapper = TonerMapper::getInstance();
        $result = $mapper->fetchTonersAssignedToDeviceForCurrentDealer(1);
        $this->assertEquals(8, count($result));
        $this->assertEquals(1, $result[0]['id']);
    }

    public function test_fetchTonersForDealer() {
        $this->user2();
        $mapper = TonerMapper::getInstance();
        $result = $mapper->fetchTonersForDealer();
        $this->assertEquals(25, count($result));
        $this->assertEquals(1, $result[0]['id']);
    }

    public function test_fetchListOfToners() {
        $this->user2();
        $mapper = TonerMapper::getInstance();
        $result = $mapper->fetchListOfToners('1');
        $this->assertEquals(1, count($result));
        $this->assertEquals(1, $result[0]['id']);
    }

    public function test_fetchListOfAffectedToners() {

    }

    public function test_getCheapestTonersForDevice() {
        $this->user2();
        $mapper = TonerMapper::getInstance();
        $result = $mapper->getCheapestTonersForDevice(19,2,'3,5','3,5');
        $this->assertEquals(4, count($result));
        $this->assertEquals(132, $result[1]->id);
        $this->assertEquals(374, $result[2]->id);
        $this->assertEquals(375, $result[3]->id);
        $this->assertEquals(376, $result[4]->id);

        $result = $mapper->getCheapestTonersForDevice(1,2,'','',null,'level1');
        $this->assertEquals(4, count($result));
        $this->assertEquals(2, $result[2]->id);
        $this->assertEquals(3, $result[3]->id);
        $this->assertEquals(4, $result[4]->id);
        $this->assertEquals(1, $result[1]->id);
    }

    public function test_getTonerPricingForExport() {

    }

    public function test_getTonerMatchupForExport() {

    }

    public function test_getDbNoRecordExistsValidator() {

    }

    public function test_findCompatibleToners() {
        $this->user2();
        $mapper = TonerMapper::getInstance();
        $result = $mapper->findCompatibleToners(2);
        $this->assertEquals(1, count($result));
        $this->assertEquals(374, $result[0]->id);
    }

    public function test_findOemToners() {
        $this->user2();
        $mapper = TonerMapper::getInstance();
        $result = $mapper->findOemToners(33);
        $this->assertEquals(1, count($result));
        $this->assertEquals(32, $result[0]->id);
    }


}