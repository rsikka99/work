<?php
/**
 * Class Proposalgen_Form_MasterDeviceTest
 */
class Proposalgen_Form_MasterDeviceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Proposalgen_Form_MasterDevice
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Proposalgen_Form_MasterDevice();
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_form = null;
    }

    public function goodData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_masterDeviceTest.xml");
        $data = array();
        foreach ($xml->masterDevice as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    public function badData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_masterDeviceTest.xml");
        $data = array();
        foreach ($xml->masterDevice as $row)
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
        $this->assertFalse($this->_form->isValid((array)$data));
    }

    /**
     * @dataProvider goodData
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->assertTrue($this->_form->isValid((array)$data));
    }

}
