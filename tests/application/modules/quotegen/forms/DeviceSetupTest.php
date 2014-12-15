<?php
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\DeviceSetupForm;

/**
 * Class Quotegen_Form_DeviceSetupTest
 */
class Quotegen_Form_DeviceSetupTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    /**
     * @return DeviceSetupForm
     */
    public function getForm ()
    {
        return new DeviceSetupForm();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_DeviceSetupTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_DeviceSetupTest.xml");
    }
}