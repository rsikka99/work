<?php

/**
 * Class Quotegen_Form_CategoryTest
 */
class Quotegen_Form_CategoryTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    /**
     * @return Quotegen_Form_Category
     */
    public function getForm ()
    {
        return new Quotegen_Form_Category();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_CategoryTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_CategoryTest.xml");
    }
}