<?php

use MPSToolbox\Legacy\Modules\DDefault\Forms\LoginForm;

class Default_Form_LoginFormTest extends Tangent_PHPUnit_Framework_ZendFormTestCase

{
    public function getForm ()
    {
        return new LoginForm();
    }

    public function testCanRunPHPUNIT ()
    {
        $this->assertTrue(true, "This should never fail unless unit testing is broken");
    }

    /**
     * @return array|mixed
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_LoginFormTest.xml");

    }

    /**
     * @return array|mixed
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_LoginFormTest.xml");

    }
}

