<?php

/**
 * Class Assessment_ViewModel_DevicesTest
 */
class Assessment_ViewModel_DevicesTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        My_Model_Abstract::setAuthDealerId(3);
    }

    public function tearDown()
    {
    }

    public function testContruct() {
        $result = new Assessment_ViewModel_Devices(9);
        $this->assertTrue($result instanceof Assessment_ViewModel_Devices);
    }

}