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
            $data[] = (array)$row;
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
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * Test the form using valid data
     *
     *
     * @dataProvider goodQuoteSettingPrefData
     */
    public function testFormAcceptsValidData ($pageCoverageMonochrome,
                                              $pageCoverageColor,
                                              $deviceMargin,
                                              $pageMargin,
                                              $adminCostPerPage,
                                              $dealerMonochromeRankSetArray,
                                              $dealerColorRankSetArray)
    {
        $data = array(
            'pageCoverageMonochrome'         => $pageCoverageMonochrome,
            'pageCoverageColor'              => $pageCoverageColor,
            'deviceMargin'                   => $deviceMargin,
            'pageMargin'                     => $pageMargin,
            'adminCostPerPage'               => $adminCostPerPage,
            'dealerMonochromeRankSetArray[]' => $dealerMonochromeRankSetArray,
            'dealerColorRankSetArray[]'      => $dealerColorRankSetArray
        );
        $this->assertTrue($this->_form->isValid($data), "Hardware optimization setting form did not accept good data.");
    }


    /**
     * Test the form using bad data
     *
     * @dataProvider badQuoteSettingPrefData
     */
    public
    function testFormRejectsBadData ($pageCoverageMonochrome,
                                     $pageCoverageColor,
                                     $deviceMargin,
                                     $pageMargin,
                                     $adminCostPerPage,
                                     $dealerMonochromeRankSetArray,
                                     $dealerColorRankSetArray)
    {
        $data = array(
            'pageCoverageMonochrome'         => $pageCoverageMonochrome,
            'pageCoverageColor'              => $pageCoverageColor,
            'deviceMargin'                   => $deviceMargin,
            'pageMargin'                     => $pageMargin,
            'adminCostPerPage'               => $adminCostPerPage,
            'dealerMonochromeRankSetArray[]' => $dealerMonochromeRankSetArray,
            'dealerColorRankSetArray[]'      => $dealerColorRankSetArray
        );
        $this->assertFalse($this->_form->isValid($data), "Hardware optimization setting form accepted bad data!");
    }

}