<?php

use MPSToolbox\Entities\ExtComputerEntity;

class ExtComputerEntityTest extends My_DatabaseTestCase {

    public $fixtures = ['users','manufacturers'];

    public function test() {

        $mfg = \MPSToolbox\Entities\ManufacturerEntity::find(1);
        $test = new ExtComputerEntity('',new DateTime(),$mfg,1,true);
        $test->save();
        $id = $test->getId();
        echo "Created Product with ID " . $id . "\n";

        $found = ExtComputerEntity::find($id);
        $this->assertEquals($id, $found->getId());

        $test->delete();
    }

}