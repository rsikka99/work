<?php
use MPSToolbox\Legacy\Modules\HardwareOptimization\Forms\DeviceSwapReasonsForm;

/**
 * Class Hardwareoptimization_Form_DeviceSwapReasonsTest
 */
class Hardwareoptimization_Form_DeviceSwapReasonsTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    public $fixtures = ['device_swap_reason_categories'];

    /**
     * @return DeviceSwapReasonsForm
     */
    public function getForm ()
    {
        return new DeviceSwapReasonsForm();
    }

    /**
     * Test the elements exist
     */
    public function testFormElementsExist ()
    {
        $this->assertInstanceOf('Zend_Form_Element_Checkbox', $this->getForm()->getElement('isDefault'));
        $this->assertInstanceOf('Zend_Form_Element_Text', $this->getForm()->getElement('reason'));
        $this->assertInstanceOf('Zend_Form_Element_Select', $this->getForm()->getElement('reasonCategory'));
    }

    /**
     * @return array|mixed
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_DeviceSwapReasonsTest.xml");
    }

    /**
     * @return array|mixed
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_DeviceSwapReasonsTest.xml");
    }
}