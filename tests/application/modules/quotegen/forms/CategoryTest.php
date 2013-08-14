<?php

/**
 * Class Quotegen_Form_CategoryTest
 */
class Quotegen_Form_CategoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Quotegen_Form_Category
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Quotegen_Form_Category();
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
    public function goodCategoryData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_CategoryTest.xml");
        $data = array();
        foreach ($xml->category as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * This function loads an XML file of bad data into arrays to be tested in the form
     */
    public function badCategoryData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_CategoryTest.xml");
        $data = array();
        foreach ($xml->category as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * Test the form using valid data
     *
     *
     * @dataProvider goodCategoryData
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->assertTrue($this->_form->isValid((array)$data), "Quotegen Category form did not accept good data.");
    }


    /**
     * Test the form using bad data
     *
     * @dataProvider badCategoryData
     */
    public
    function testFormRejectsBadData ($data)
    {
        $this->assertFalse($this->_form->isValid((array)$data), "Quotegen Category form accepted bad data!");
    }
}