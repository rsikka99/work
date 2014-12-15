<?php
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\QuoteDeviceGroupPageForm;

/**
 * Class Quotegen_Form_QuoteDeviceGroupPageTest
 */
class Quotegen_Form_QuoteDeviceGroupPageTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    /**
     * @return QuoteDeviceGroupPageForm
     */
    public function getForm ()
    {
        return new QuoteDeviceGroupPageForm();
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