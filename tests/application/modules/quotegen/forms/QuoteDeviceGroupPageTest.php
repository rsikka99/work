<?php

/**
 * Class Quotegen_Form_QuoteDeviceGroupPageTest
 */
class Quotegen_Form_QuoteDeviceGroupPageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Quotegen_Form_QuoteDeviceGroupPage
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Quotegen_Form_QuoteDeviceGroupPage();
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_form = null;
    }

    /**
     * This function loads an XML file of good data into arrays to be tested in the form
     */
    public function goodQuoteDeviceGroupPageData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_QuoteDeviceGroupPageTest.xml");
        $data = array();
        foreach ($xml->quote as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * This function loads an XML file of bad data into arrays to be tested in the form
     */
    public function badQuoteDeviceGroupPageData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_QuoteDeviceGroupPageTest.xml");
        $data = array();
        foreach ($xml->quote as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * Test the form using valid data
     *
     *
     * @dataProvider goodQuoteDeviceGroupPageData
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->assertTrue($this->_form->isValid((array)$data), implode(' | ', $this->_form->getErrorMessages()));
    }


    /**
     * Test the form using bad data
     *
     * @dataProvider badQuoteDeviceGroupPageData
     */
    public
    function testFormRejectsBadData ($data)
    {
        $this->assertFalse($this->_form->isValid((array)$data), implode(' | ', $this->_form->getErrorMessages()));
    }
}