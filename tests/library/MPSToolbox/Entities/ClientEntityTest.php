<?php

class ClientEntityTest extends My_DatabaseTestCase {

    public $fixtures = ['clients'];

    public function test_load() {
        $result = \MPSToolbox\Entities\ClientEntity::find(1);
        $this->assertTrue($result instanceof \MPSToolbox\Entities\ClientEntity);
        $this->assertTrue($result->getDealer() instanceof \MPSToolbox\Entities\DealerEntity);
    }

}