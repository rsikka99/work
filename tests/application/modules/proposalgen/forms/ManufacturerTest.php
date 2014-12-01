<?php

/**
 * Class Proposalgen_Form_ManufacturerTest
 */
class Proposalgen_Form_ManufacturerTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    /**
     * @return Proposalgen_Form_Manufacturer
     */
    public function getForm ()
    {
        return new Proposalgen_Form_Manufacturer();
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
