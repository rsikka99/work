<?php

/**
 * Class Healthcheck_Form_Healthcheck_SettingsTest
 */
class Healthcheck_Form_Healthcheck_SettingsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Healthcheck_Form_Healthcheck_Settings
     */
    protected $_form;

    public function setUp ()
    {
        /**
         * @var PHPUnit_Framework_MockObject_MockObject | Healthcheck_Model_Healthcheck_Setting $defaultSettings
         */
        $defaultSettings = $this->getMock('Healthcheck_Model_Healthcheck_Setting');
        $this->_form     = new Healthcheck_Form_Healthcheck_Settings($defaultSettings);
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
        $xml = simplexml_load_file(__DIR__ . "/_files/goodData_HCFormSettingsTest.xml");
        $data = array();
        foreach ($xml->healthcheck as $row)
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
        $xml = simplexml_load_file(__DIR__ . "/_files/badData_HCFormSettingsTest.xml");
        $parentArr = array();
        foreach ($xml->healthcheck as $childArr)
        {
            $parentArr[] = (array)$childArr;
        }

        return $parentArr;
    }

    /**
     * Test the form using valid data
     *
     *
     * @dataProvider goodHCFormSettingsData
     */
    public function testFormAcceptsValidData ($reportDate,
                                              $healthcheckMargin,
                                              $monthlyLeasePayment,
                                              $defaultPrinterCost,
                                              $leasedBwCostPerPage,
                                              $leasedColorCostPerPage,
                                              $mpsBwCostPerPage,
                                              $mpsColorCostPerPage,
                                              $kilowattsPerHour,
                                              $pageCoverageMonochrome,
                                              $pageCoverageColor,
                                              $adminCostPerPage,
                                              $laborCostPerPage,
                                              $partsCostPerPage,
                                              $averageItHourlyRate,
                                              $hoursSpentOnIt,
                                              $costOfLabor,
                                              $costToExecuteSuppliesOrder,
                                              $numberOfSupplyOrdersPerMonth)
    {
        $data = array(
            'reportDate'                   => $reportDate,
            'healthcheckMargin'            => $healthcheckMargin,
            'monthlyLeasePayment'          => $monthlyLeasePayment,
            'defaultPrinterCost'           => $defaultPrinterCost,
            'leasedBwCostPerPage'          => $leasedBwCostPerPage,
            'leasedColorCostPerPage'       => $leasedColorCostPerPage,
            'mpsBwCostPerPage'             => $mpsBwCostPerPage,
            'mpsColorCostPerPage'          => $mpsColorCostPerPage,
            'kilowattsPerHour'             => $kilowattsPerHour,
            'pageCoverageMonochrome'       => $pageCoverageMonochrome,
            'pageCoverageColor'            => $pageCoverageColor,
            'adminCostPerPage'             => $adminCostPerPage,
            'laborCostPerPage'             => $laborCostPerPage,
            'partsCostPerPage'             => $partsCostPerPage,
            'averageItHourlyRate'          => $averageItHourlyRate,
            'hoursSpentOnIt'               => $hoursSpentOnIt,
            'costOfLabor'                  => $costOfLabor,
            'costToExecuteSuppliesOrder'   => $costToExecuteSuppliesOrder,
            'numberOfSupplyOrdersPerMonth' => $numberOfSupplyOrdersPerMonth,
        );
        $this->assertTrue($this->_form->isValid($data), "Hardware optimization setting form did not accept good data.");
    }


    /**
     * Test the form using bad data
     *
     * @dataProvider badHCFormSettingsData
     */
    public
    function testFormRejectsBadData ($reportDate,
                                     $healthcheckMargin,
                                     $monthlyLeasePayment,
                                     $defaultPrinterCost,
                                     $leasedBwCostPerPage,
                                     $leasedColorCostPerPage,
                                     $mpsBwCostPerPage,
                                     $mpsColorCostPerPage,
                                     $kilowattsPerHour,
                                     $pageCoverageMonochrome,
                                     $pageCoverageColor,
                                     $adminCostPerPage,
                                     $laborCostPerPage,
                                     $partsCostPerPage,
                                     $averageItHourlyRate,
                                     $hoursSpentOnIt,
                                     $costOfLabor,
                                     $costToExecuteSuppliesOrder,
                                     $numberOfSupplyOrdersPerMonth)
    {
        $data = array(
            'reportDate'                   => $reportDate,
            'healthcheckMargin'            => $healthcheckMargin,
            'monthlyLeasePayment'          => $monthlyLeasePayment,
            'defaultPrinterCost'           => $defaultPrinterCost,
            'leasedBwCostPerPage'          => $leasedBwCostPerPage,
            'leasedColorCostPerPage'       => $leasedColorCostPerPage,
            'mpsBwCostPerPage'             => $mpsBwCostPerPage,
            'mpsColorCostPerPage'          => $mpsColorCostPerPage,
            'kilowattsPerHour'             => $kilowattsPerHour,
            'pageCoverageMonochrome'       => $pageCoverageMonochrome,
            'pageCoverageColor'            => $pageCoverageColor,
            'adminCostPerPage'             => $adminCostPerPage,
            'laborCostPerPage'             => $laborCostPerPage,
            'partsCostPerPage'             => $partsCostPerPage,
            'averageItHourlyRate'          => $averageItHourlyRate,
            'hoursSpentOnIt'               => $hoursSpentOnIt,
            'costOfLabor'                  => $costOfLabor,
            'costToExecuteSuppliesOrder'   => $costToExecuteSuppliesOrder,
            'numberOfSupplyOrdersPerMonth' => $numberOfSupplyOrdersPerMonth,
        );
        $this->assertFalse($this->_form->isValid($data), "Hardware optimization setting form accepted bad data! [" . strtotime('01/19/2037') . "][" . strtotime($reportDate) . "] [{$reportDate}]");
    }

}