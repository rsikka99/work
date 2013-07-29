<?php

class Default_IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp ()
    {
        parent::setUp();
    }


    public function testCanRunPHPUNIT ()
    {
        $this->assertTrue(true, "This should never fail unless unit testing is broken");
    }


//     public function testCanDisplayHomePage ()
//     {
//         // Go to the index
//         $this->dispatch('/');

//         // Make sure we didn't end up on an error page
//         $this->assertModule('default');
//         $this->assertController('index');
//         $this->assertAction('index');
//         $this->assertResponseCode(200);
//     }
}

