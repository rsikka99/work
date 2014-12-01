<?php

class Proposalgen_Form_MasterDeviceManagement_HardwareQuotesFormTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * @return Proposalgen_Form_MasterDeviceManagement_HardwareQuote
     */
    public function getForm ()
    {
        return new Proposalgen_Form_MasterDeviceManagement_HardwareQuote();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_hardwareQuotesFormTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_hardwareQuotesFormTest.xml");
    }

}

