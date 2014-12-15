<?php
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\ManufacturerForm;

/**
 * Class Proposalgen_Form_ManufacturerTest
 */
class HardwareLibrary_Form_ManufacturerTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    /**
     * @return ManufacturerForm
     */
    public function getForm ()
    {
        return new ManufacturerForm();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_manufacturerTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_manufacturerTest.xml");
    }

}
