<?php

use MPSToolbox\Legacy\Modules\Admin\Forms\DealerForm;

class Default_Form_DealerTest extends Tangent_PHPUnit_Framework_ZendFormTestCase

{

    /**
     * @return DealerForm
     */
    public function getForm ()
    {
        return new DealerForm();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_dealerTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_dealerTest.xml");
    }

    public function testWillFailIncorrectKeys ()
    {
        $this->assertFalse($this->getForm()->isValid(array('testKey' => 'Dealer Name', 'userLicenses' => 16)));
    }

    public function testWillFailNoData ()
    {
        $this->assertFalse($this->getForm()->isValid(array()));
    }


}