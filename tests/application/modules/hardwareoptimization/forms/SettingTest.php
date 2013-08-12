<?php

/**
 * Class Hardwareoptimization_Form_SettingTest
 */
class Hardwareoptimization_Form_SettingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Hardwareoptimization_Form_Setting
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Hardwareoptimization_Form_Setting();
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
    public function goodHOSettingData ()
    {
        $xml = simplexml_load_file(__DIR__ . "/_files/goodData_hardwareFormTest.xml");
        $data = array();
        foreach ($xml->setting as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * This function loads an XML file of bad data into arrays to be tested in the form
     */
    public function badHOSettingData ()
    {
        $xml = simplexml_load_file(__DIR__ . "/_files/badData_hardwareFormTest.xml");
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
     * @dataProvider goodHOSettingData
     */
    public function testFormAcceptsValidData ($name, $costThreshold, $pageCoverageMonochrome, $pageCoverageColor, $adminCostPerPage, $partsCostPerPage, $laborCostPerPage, $targetMonochromeCostPerPage, $targetColorCostPerPage)
    {
        $data = array(
            'name'                        => $name,
            'costThreshold'               => $costThreshold,
            'pageCoverageMonochrome'      => $pageCoverageMonochrome,
            'pageCoverageColor'           => $pageCoverageColor,
            'adminCostPerPage'            => $adminCostPerPage,
            'partsCostPerPage'            => $partsCostPerPage,
            'laborCostPerPage'            => $laborCostPerPage,
            'targetMonochromeCostPerPage' => $targetMonochromeCostPerPage,
            'targetColorCostPerPage'      => $targetColorCostPerPage
        );
        $this->assertTrue($this->_form->isValid($data), "Hardware optimization setting form did not accept good data.");
    }


    /**
     * Test the form using bad data
     *
     * @dataProvider badHOSettingData
     */
    public function testFormRejectsBadData ($name, $costThreshold, $pageCoverageMonochrome, $pageCoverageColor, $adminCostPerPage, $partsCostPerPage, $laborCostPerPage, $targetMonochromeCostPerPage, $targetColorCostPerPage)
    {
        $data = array(
            'name'                        => $name,
            'costThreshold'               => $costThreshold,
            'pageCoverageMonochrome'      => $pageCoverageMonochrome,
            'pageCoverageColor'           => $pageCoverageColor,
            'adminCostPerPage'            => $adminCostPerPage,
            'partsCostPerPage'            => $partsCostPerPage,
            'laborCostPerPage'            => $laborCostPerPage,
            'targetMonochromeCostPerPage' => $targetMonochromeCostPerPage,
            'targetColorCostPerPage'      => $targetColorCostPerPage
        );
        $this->assertFalse($this->_form->isValid($data), "Hardware optimization setting form accepted bad data!");
    }

}