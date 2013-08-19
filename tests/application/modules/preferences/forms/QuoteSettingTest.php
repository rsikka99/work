<?php

/**
 * Class Preferences_Form_QuoteSettingTest
 */
class Preferences_Form_QuoteSettingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Preferences_Form_QuoteSetting
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Preferences_Form_QuoteSetting();
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
    public function goodQuoteSettingPrefData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_QuoteSettingPrefs.xml");
        $data = array();
        foreach ($xml->quote as $row)
        {
            $row                                         = json_decode(json_encode($row), 1);
            $row["data"]["dealerMonochromeRankSetArray"] = explode(',', $row["data"]["dealerMonochromeRankSetArray"]);
            $row["data"]["dealerColorRankSetArray"]      = explode(',', $row["data"]["dealerColorRankSetArray"]);
            $data[]                                      = (array)$row;
        }

        return $data;
    }

    /**
     * This function loads an XML file of bad data into arrays to be tested in the form
     */
    public function badQuoteSettingPrefData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_QuoteSettingPrefs.xml");
        $data = array();
        foreach ($xml->quote as $row)
        {
            $row                                         = json_decode(json_encode($row), 1);
            $row["data"]["dealerMonochromeRankSetArray"] = explode(',', $row["data"]["dealerMonochromeRankSetArray"]);
            $row["data"]["dealerColorRankSetArray"]      = explode(',', $row["data"]["dealerColorRankSetArray"]);
            $data[]                                  = (array)$row;
        }

        return $data;
    }

    /**
     * Test the form using valid data
     *
     *
     * @dataProvider goodQuoteSettingPrefData
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->assertTrue($this->_form->isValid((array)$data), implode(' | ', $this->_form->getErrorMessages()));
    }


    /**
     * Test the form using bad data
     *
     * @dataProvider badQuoteSettingPrefData
     */
    public
    function testFormRejectsBadData ($data)
    {
        $this->assertFalse($this->_form->isValid((array)$data), implode(' | ', $this->_form->getErrorMessages()));
    }

}