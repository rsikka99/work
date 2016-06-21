<?php

class TonerServiceTest extends My_DatabaseTestCase {

    public $fixtures = [
        'base_printer_cartridge',
        'dealer_toner_attributes',
        'ingram_products',
        'ingram_prices'
    ];

    public function test_getTonerPrice() {
        $service = new \MPSToolbox\Legacy\Modules\HardwareLibrary\Services\TonerService(null, 2);
        $price = $service->getTonerPrice(1);
        $this->assertEquals('17.88', $price);
    }

}
