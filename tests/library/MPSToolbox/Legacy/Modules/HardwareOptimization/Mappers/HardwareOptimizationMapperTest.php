<?php

use \MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\HardwareOptimizationMapper;

class HardwareOptimizationMapperTest extends My_DatabaseTestCase {

    public $fixtures = [
        'hardware_optimizations'
    ];

    public function test_fetchAllForHardwareOptimization() {
        $user = new \MPSToolbox\Legacy\Models\UserModel(['dealerId'=>2]);
        \Zend_Auth::getInstance()->getStorage()->write($user);
        $mapper = HardwareOptimizationMapper::getInstance();
        $mapper->fetchAllForHardwareOptimization(1, null, null, null, 0, 0, true);
        $mapper->fetchAllForHardwareOptimization(1);

    }


}