<?php

/**
 * Class Proposalgen_Form_ManufacturerTest
 */
class Proposalgen_Form_ManufacturerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Proposalgen_Form_Manufacturer
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Proposalgen_Form_Manufacturer();
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_form = null;
    }

    public function goodData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_manufacturerTest.xml");
        $data = array();
        foreach ($xml->manufacturer as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    public function badData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_manufacturerTest.xml");
        $data = array();
        foreach ($xml->manufacturer as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * @dataProvider badData
     */
    public function testFormRejectsBadData ($data)
    {
        $this->assertFalse($this->_form->isValid((array)$data), implode(' | ', $this->_form->getErrorMessages()));
    }

    /**
     * @dataProvider goodData
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->assertTrue($this->_form->isValid((array)$data), implode(' | ', $this->_form->getErrorMessages()));
    }

}
