<?php

/**
 * Class Memjetoptimization_Form_DeviceSwapReasonsTest
 */
class Memjetoptimization_Form_DeviceSwapReasonsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Memjetoptimization_Form_DeviceSwapReasons
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Memjetoptimization_Form_DeviceSwapReasons();
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_form = null;
    }

    /**
     * Test the elements exist
     */
    public function testFormElementsExist ()
    {
        $this->assertInstanceOf('Zend_Form_Element_Checkbox', $this->_form->getElement('isDefault'));
        $this->assertInstanceOf('Zend_Form_Element_Text', $this->_form->getElement('reason'));
        $this->assertInstanceOf('Zend_Form_Element_Select', $this->_form->getElement('reasonCategory'));
    }

    public function goodData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_DeviceSwapReasonsTest.xml");
        $data = array();
        foreach ($xml->setting as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    public function badData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_DeviceSwapReasonsTest.xml");
        $data = array();
        foreach ($xml->setting as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * Test the form using valid data
     *
     *
     * @dataProvider goodData
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->assertTrue($this->_form->isValid((array)$data), implode(' | ', $this->_form->getErrorMessages()));
    }


    /**
     * Test the form using bad data
     *
     * @dataProvider badData
     */
    public function testFormRejectsBadData ($data)
    {
        $this->assertFalse($this->_form->isValid((array)$data), implode(' | ', $this->_form->getErrorMessages()));
    }

}