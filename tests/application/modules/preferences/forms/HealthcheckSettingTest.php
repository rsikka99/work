<?php

/**
 * Class Preferences_Form_HealthcheckSettingTest
 */
class Preferences_Form_HealthcheckSettingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Preferences_Form_HealthcheckSetting
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Preferences_Form_HealthcheckSetting();
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
    public function goodHCFormSettingsPrefData ()
    {
        $xml = simplexml_load_file(__DIR__ . "/_files/goodData_HCFormSettingsPrefTest.xml");
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
    public function badHCFormSettingsPrefData ()
    {
        $xml = simplexml_load_file(__DIR__ . "/_files/badData_HCFormSettingsPrefTest.xml");
        $data = array();
        foreach ($xml->healthcheck as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * Test the form using valid data
     *
     *
     * @dataProvider goodHCFormSettingsPrefData
     */
    public function testFormAcceptsValidData ($pageCoverageMonochrome,
                                              $pageCoverageColor,
                                              $healthcheckMargin,
                                              $monthlyLeasePayment,
                                              $defaultPrinterCost,
                                              $leasedBwCostPerPage,
                                              $leasedColorCostPerPage,
                                              $mpsBwCostPerPage,
                                              $mpsColorCostPerPage,
                                              $kilowattsPerHour,
                                              $adminCostPerPage,
                                              $laborCostPerPage,
                                              $partsCostPerPage,
                                              $averageItHourlyRate,
                                              $hoursSpentOnIt,
                                              $costOfLabor,
                                              $costToExecuteSuppliesOrder,
                                              $numberOfSupplyOrdersPerMonth,
                                              $customerMonochromeRankSetArray,
                                              $customerColorRankSetArray
    )
    {
        $data = array(
            'pageCoverageMonochrome'       => $pageCoverageMonochrome,
            'pageCoverageColor'            => $pageCoverageColor,
            'healthcheckMargin'            => $healthcheckMargin,
            'monthlyLeasePayment'          => $monthlyLeasePayment,
            'defaultPrinterCost'           => $defaultPrinterCost,
            'leasedBwCostPerPage'          => $leasedBwCostPerPage,
            'leasedColorCostPerPage'       => $leasedColorCostPerPage,
            'mpsBwCostPerPage'             => $mpsBwCostPerPage,
            'mpsColorCostPerPage'          => $mpsColorCostPerPage,
            'kilowattsPerHour'             => $kilowattsPerHour,
            'adminCostPerPage'             => $adminCostPerPage,
            'laborCostPerPage'             => $laborCostPerPage,
            'partsCostPerPage'             => $partsCostPerPage,
            'averageItHourlyRate'          => $averageItHourlyRate,
            'hoursSpentOnIt'               => $hoursSpentOnIt,
            'costOfLabor'                  => $costOfLabor,
            'costToExecuteSuppliesOrder'   => $costToExecuteSuppliesOrder,
            'numberOfSupplyOrdersPerMonth' => $numberOfSupplyOrdersPerMonth,
            'customerMonochromeRankSetArray[]' => $customerMonochromeRankSetArray,
            'customerColorRankSetArray[]' => $customerColorRankSetArray,
        );
        $this->assertTrue($this->_form->isValid($data), "Healthcheck setting preferences form did not accept good data.");
    }


    /**
     * Test the form using bad data
     *
     * @dataProvider badHCFormSettingsPrefData
     */
    public
    function testFormRejectsBadData ($pageCoverageMonochrome,
                                     $pageCoverageColor,
                                     $healthcheckMargin,
                                     $monthlyLeasePayment,
                                     $defaultPrinterCost,
                                     $leasedBwCostPerPage,
                                     $leasedColorCostPerPage,
                                     $mpsBwCostPerPage,
                                     $mpsColorCostPerPage,
                                     $kilowattsPerHour,
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
            'pageCoverageMonochrome'       => $pageCoverageMonochrome,
            'pageCoverageColor'            => $pageCoverageColor,
            'healthcheckMargin'            => $healthcheckMargin,
            'monthlyLeasePayment'          => $monthlyLeasePayment,
            'defaultPrinterCost'           => $defaultPrinterCost,
            'leasedBwCostPerPage'          => $leasedBwCostPerPage,
            'leasedColorCostPerPage'       => $leasedColorCostPerPage,
            'mpsBwCostPerPage'             => $mpsBwCostPerPage,
            'mpsColorCostPerPage'          => $mpsColorCostPerPage,
            'kilowattsPerHour'             => $kilowattsPerHour,
            'adminCostPerPage'             => $adminCostPerPage,
            'laborCostPerPage'             => $laborCostPerPage,
            'partsCostPerPage'             => $partsCostPerPage,
            'averageItHourlyRate'          => $averageItHourlyRate,
            'hoursSpentOnIt'               => $hoursSpentOnIt,
            'costOfLabor'                  => $costOfLabor,
            'costToExecuteSuppliesOrder'   => $costToExecuteSuppliesOrder,
            'numberOfSupplyOrdersPerMonth' => $numberOfSupplyOrdersPerMonth,
        );
        $this->assertFalse($this->_form->isValid($data), "Healthcheck setting preferences form accepted bad data! [" . strtotime('01/19/2037') . "][" . strtotime($reportDate) . "] [{$reportDate}]");
    }

}