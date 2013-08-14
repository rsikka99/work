<?php

class Default_Form_DealerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Admin_Form_Dealer
     */
    protected $_form;

    /**
     * Provides bad data for tests to use
     */
    public function badData ()
    {
        $xml = simplexml_load_file(__DIR__ . "/_files/badData_dealerTest.xml");
        $data = array();

        foreach ($xml->dealer as $dealer)
        {
            $data [] = (array)$dealer;
        }

        return $data;
    }

    /**
     * @dataProvider badData
     */
    public function testFormInvalid ($dealerName, $userLicenses)
    {
        $data = array(
            'dealerName'   => $dealerName,
            'userLicenses' => $userLicenses,
            'dealerLogo'   => null,
        );

        $_FILES = array(
            'dealerLogoImage' => array(
                'name'     => '',
                'tmp_name' => '',
                'type'     => 'null',
                'size'     => '',
                'error'    => 4
            )
        );

        $this->assertFalse($this->getForm()->isValid($data), implode(' | ', $this->_form->getErrorMessages()));
    }

    /**
     * This function returns an array of good data to put into the form
     */
    public function goodData ()
    {
        $xmlStr = simplexml_load_file(__DIR__ . "/_files/goodData_dealerTest.xml");
        $xml    = new SimpleXMLElement($xmlStr->asXML());

        $data = array();

        foreach ($xml->dealer as $dealer)
        {
            $data [] = (array)$dealer;
        }

        return $data;
    }

    /**
     * @dataProvider goodData
     */
    public function testFormPass ($dealerName, $userLicenses)
    {
        $data = array(
            'dealerName'   => $dealerName,
            'userLicenses' => $userLicenses,
            'dealerLogo'   => null,
        );

        $_FILES = array(
            'dealerLogoImage' => array(
                'name'     => '',
                'tmp_name' => '',
                'type'     => 'null',
                'size'     => '',
                'error'    => 4
            )
        );

        $this->assertTrue($this->getForm()->isValid($data), implode(' | ', $this->_form->getErrorMessages()));
    }

    public function testWillFailIncorrectKeys ()
    {
        $this->assertFalse($this->getForm()->isValid(array('testKey' => 'Dealer Name', 'userLicenses' => 16)));
    }

    public function testWillFailNoData ()
    {
        $this->assertFalse($this->getForm()->isValid(array()));
    }

    /**
     * Returns an Admin_Form_Dealer object, creates empty if not set
     *
     * @return Admin_Form_Dealer
     */
    protected function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new Admin_Form_Dealer();
        }

        return $this->_form;
    }
}