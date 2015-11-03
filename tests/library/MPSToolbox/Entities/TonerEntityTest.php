<?php

class TonerEntityTest extends My_DatabaseTestCase {

    public $fixtures = ['users', 'manufacturers', 'toners'];

    public function test_load() {
        $result = \MPSToolbox\Entities\TonerEntity::find(1);
        $this->assertTrue($result instanceof \MPSToolbox\Entities\TonerEntity);
        $this->assertTrue($result->getUser() instanceof \MPSToolbox\Entities\UserEntity);
        $this->assertTrue($result->getManufacturer() instanceof \MPSToolbox\Entities\ManufacturerEntity);
        $this->assertTrue($result->getTonerColor() instanceof \MPSToolbox\Entities\TonerColorEntity);
    }

}