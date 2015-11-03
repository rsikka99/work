<?php

class UserEntityTest extends My_DatabaseTestCase {

    public $fixtures = ['users'];

    public function test_load() {
        $result = \MPSToolbox\Entities\UserEntity::find(1);
        $this->assertTrue($result instanceof \MPSToolbox\Entities\UserEntity);
        $this->assertTrue($result->getDealer() instanceof \MPSToolbox\Entities\DealerEntity);
    }

}