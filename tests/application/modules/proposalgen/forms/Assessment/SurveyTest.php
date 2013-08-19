<?php

/**
 * Class Proposalgen_Form_Assessment_SurveyTestTest
 */
class Proposalgen_Form_Assessment_SurveyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Proposalgen_Form_Assessment_Survey
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Proposalgen_Form_Assessment_Survey();
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
    public function goodHCFormSettingsData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_SurveyTest.xml");
        $data = array();
        foreach ($xml->survey as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * This function loads an XML file of bad data into arrays to be tested in the form
     */
    public function badHCFormSettingsData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_SurveyTest.xml");
        $data = array();
        foreach ($xml->survey as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * Test the form using valid data
     *
     *
     * @dataProvider goodHCFormSettingsData
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->assertTrue($this->_form->isValid((array)$data), implode(' | ', $this->_form->getErrorMessages()));
    }


    /**
     * Test the form using bad data
     *
     * @dataProvider badHCFormSettingsData
     */
    public
    function testFormRejectsBadData ($data)
    {
        $this->assertFalse($this->_form->isValid((array)$data), implode(' | ', $this->_form->getErrorMessages()));
    }

}