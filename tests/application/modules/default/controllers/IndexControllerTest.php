<?php

class Default_IndexControllerTest extends My_ControllerTestCase
{

    public $fixtures = ['users','clients'];

    public function setUp() {
        parent::setUp();
        $user = \MPSToolbox\Legacy\Mappers\UserMapper::getInstance()->find(2);
        Zend_Auth::getInstance()->getStorage()->write($user);
    }

    public function tearDown() {
        Zend_Auth::getInstance()->getStorage()->write(null);
        parent::tearDown();
    }

    public function test_indexAction()
    {
        $this->dispatch('/');

        $this->assertModule('default');
        $this->assertController('index');
        $this->assertAction('index');

        $this->assertRedirectTo('/select-client');
    }

}

