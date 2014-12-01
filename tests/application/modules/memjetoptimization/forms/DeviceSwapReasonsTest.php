<?php

/**
 * Class Memjetoptimization_Form_DeviceSwapReasonsTest
 */
class Memjetoptimization_Form_DeviceSwapReasonsTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * @return Memjetoptimization_Form_DeviceSwapReasons|Zend_Form
     */
    public function getForm ()
    {
        return new Memjetoptimization_Form_DeviceSwapReasons();
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

    /**
     * Test the elements exist
     */
    public function testFormElementsExist ()
    {
        $this->assertInstanceOf('Zend_Form_Element_Checkbox', $this->getForm()->getElement('isDefault'));
        $this->assertInstanceOf('Zend_Form_Element_Text', $this->getForm()->getElement('reason'));
        $this->assertInstanceOf('Zend_Form_Element_Select', $this->getForm()->getElement('reasonCategory'));
    }
}