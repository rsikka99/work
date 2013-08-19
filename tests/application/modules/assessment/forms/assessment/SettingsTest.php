<?php

/**
 * Class Assessment_Form_Assessment_SettingsTest
 */
class Assessment_Form_Assessment_SettingsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Assessment_Form_Assessment_Settings
     */
    protected $_form;

    public function setUp ()
    {
        /**
         * @var PHPUnit_Framework_MockObject_MockObject | Assessment_Model_Assessment_Setting $defaultSettings
         */
        $defaultSettings = $this->getMock('Assessment_Model_Assessment_Setting');
        $this->_form     = new Assessment_Form_Assessment_Settings($defaultSettings);
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
    public function goodSettingsData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_SettingsTest.xml");
        $data = array();

        foreach ($xml->setting as $row)
        {
            $row                                           = json_decode(json_encode($row), 1);
            $row["data"]["customerMonochromeRankSetArray"] = explode(',', $row["data"]["customerMonochromeRankSetArray"]);
            $row["data"]["customerColorRankSetArray"]      = explode(',', $row["data"]["customerColorRankSetArray"]);
            $row["data"]["dealerMonochromeRankSetArray"]   = explode(',', $row["data"]["dealerMonochromeRankSetArray"]);
            $row["data"]["dealerColorRankSetArray"]        = explode(',', $row["data"]["dealerColorRankSetArray"]);

            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * This function loads an XML file of bad data into arrays to be tested in the form
     */
    public function badSettingsData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_SettingsTest.xml");
        $data = array();

        foreach ($xml->setting as $row)
        {
            $row                                           = json_decode(json_encode($row), 1);
            $row["data"]["customerMonochromeRankSetArray"] = explode(',', $row["data"]["customerMonochromeRankSetArray"]);
            $row["data"]["customerColorRankSetArray"]      = explode(',', $row["data"]["customerColorRankSetArray"]);
            $row["data"]["dealerMonochromeRankSetArray"]   = explode(',', $row["data"]["dealerMonochromeRankSetArray"]);
            $row["data"]["dealerColorRankSetArray"]        = explode(',', $row["data"]["dealerColorRankSetArray"]);

            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * Test the form using valid data
     *
     * @dataProvider goodSettingsData
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->assertTrue($this->_form->isValid($data), implode(' | ', $this->_form->getErrorMessages()));
    }


    /**
     * Test the form using bad data
     *
     * @dataProvider badSettingsData
     */
    public
    function testFormRejectsBadData ($data)
    {
        $this->assertFalse($this->_form->isValid($data), implode(' | ', $this->_form->getErrorMessages()));
    }
}