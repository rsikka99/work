<?php

/**
 * Class Quotegen_Form_QuoteDeviceGroupPageTest
 */
class Quotegen_Form_QuoteDeviceGroupPageTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    /**
     * @return Quotegen_Form_QuoteDeviceGroupPage
     */
    public function getForm ()
    {
        return new Quotegen_Form_QuoteDeviceGroupPage();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_QuoteDeviceGroupPageTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_QuoteDeviceGroupPageTest.xml");
    }
}