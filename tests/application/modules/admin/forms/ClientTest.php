<?php

/**
 * Class Admin_Form_ClientTest
 */
class Admin_Form_ClientTest extends PHPUnit_Framework_TestCase
{

    /**
     * Builds the form to be used for testing
     *
     * @param bool $dealerManagement
     * @param bool $isAdmin
     *
     * @return \Admin_Form_Client
     */
    public function getForm ($dealerManagement = true, $isAdmin = false)
    {
        $view = $this->getMock('Zend_View', array('IsAllowed'));

        $view->expects($this->any())
        ->method('IsAllowed')
        ->will($this->returnValue($isAdmin));

        return new Admin_Form_Client($dealerManagement, array('view' => $view));
    }

    /**
     * This function loads an XML file of good data into arrays to be tested in the form
     */
    public function goodClientData ()
    {
        $xml     = simplexml_load_file(__DIR__ . "/_files/goodData_clientFormTest.xml");
        $clients = array();
        foreach ($xml->client as $client)
        {
            $clients[] = (array)$client;
        }

        return $clients;
    }

    /**
     * This function loads an XML file of bad data into arrays to be tested in the form
     */
    public function badClientData ()
    {
        $xml     = simplexml_load_file(__DIR__ . "/_files/badData_clientFormTest.xml");
        $clients = array();
        foreach ($xml->client as $client)
        {
            $clients[] = (array)$client;
        }

        return $clients;
    }

    /**
     * Test the form using valid/required data
     *
     * @dataProvider goodClientData
     */
    public function testFormAcceptsValidData ($accountNumber, $companyName, $employeeCount, $legalName, $countryCode, $areaCode, $exchangeCode, $number, $extension, $addressLine1, $city, $region, $postCode, $countryId)
    {
        $data = array(
            'accountNumber' => $accountNumber,
            'companyName'   => $companyName,
            'employeeCount' => $employeeCount,
            'legalName'     => $legalName,
            'countryCode'   => $countryCode,
            'areaCode'      => $areaCode,
            'exchangeCode'  => $exchangeCode,
            'number'        => $number,
            'extension'     => $extension,
            'addressLine1'  => $addressLine1,
            'city'          => $city,
            'region'        => $region,
            'postCode'      => $postCode,
            'countryId'     => $countryId
        );
        $this->assertTrue($this->getForm()->isValid($data), "Client form did not accept good data.");
    }

    /**
     * Test the form using invalid/missing data
     *
     * @dataProvider badClientData
     */
    public function testFormRejectsInvalidData ($accountNumber, $companyName, $employeeCount, $legalName, $countryCode, $areaCode, $exchangeCode, $number, $extension, $addressLine1, $city, $region, $postCode, $countryId)
    {

        $data = array(
            'accountNumber' => $accountNumber,
            'companyName'   => $companyName,
            'employeeCount' => $employeeCount,
            'legalName'     => $legalName,
            'countryCode'   => $countryCode,
            'areaCode'      => $areaCode,
            'exchangeCode'  => $exchangeCode,
            'number'        => $number,
            'extension'     => $extension,
            'addressLine1'  => $addressLine1,
            'city'          => $city,
            'region'        => $region,
            'postCode'      => $postCode,
            'countryId'     => $countryId
        );
        $this->assertFalse($this->getForm()->isValid($data), "Client form did not reject bad data.");
    }

    /**
     * Test the form using valid/required data as an admin
     *
     * @dataProvider goodClientData
     */
    public function testFormAcceptsValidDataAsAdmin ($accountNumber, $companyName, $employeeCount, $legalName, $countryCode, $areaCode, $exchangeCode, $number, $extension, $addressLine1, $city, $region, $postCode, $countryId, $dealerId)
    {
        $data = array(
            'accountNumber' => $accountNumber,
            'companyName'   => $companyName,
            'employeeCount' => $employeeCount,
            'legalName'     => $legalName,
            'countryCode'   => $countryCode,
            'areaCode'      => $areaCode,
            'exchangeCode'  => $exchangeCode,
            'number'        => $number,
            'extension'     => $extension,
            'addressLine1'  => $addressLine1,
            'city'          => $city,
            'region'        => $region,
            'postCode'      => $postCode,
            'countryId'     => $countryId,
            'dealerId'      => $dealerId
        );
        $this->assertTrue($this->getForm(true, true)->isValid($data), "Client form did not accept good data.");
    }

    /**
     * Test the form using invalid/missing data as an admin
     *
     * @dataProvider badClientData
     */
    public function testFormRejectsInvalidDataAsAdmin ($accountNumber, $companyName, $employeeCount, $legalName, $countryCode, $areaCode, $exchangeCode, $number, $extension, $addressLine1, $city, $region, $postCode, $countryId, $dealerId)
    {
        $data = array(
            'accountNumber' => $accountNumber,
            'companyName'   => $companyName,
            'employeeCount' => $employeeCount,
            'legalName'     => $legalName,
            'countryCode'   => $countryCode,
            'areaCode'      => $areaCode,
            'exchangeCode'  => $exchangeCode,
            'number'        => $number,
            'extension'     => $extension,
            'addressLine1'  => $addressLine1,
            'city'          => $city,
            'region'        => $region,
            'postCode'      => $postCode,
            'countryId'     => $countryId,
            'dealerId'      => $dealerId
        );
        $this->assertFalse($this->getForm(true, true)->isValid($data), "Client form did not reject bad data.");
    }


    public function testDealerFieldExists ()
    {
        $this->assertInstanceOf('Zend_Form_Element_Select', $this->getForm(false, true)->getElement('dealerId'));
    }

    public function testDealerFieldDoesNotExist ()
    {
        $this->assertNotInstanceOf('Zend_Form_Element_Select', $this->getForm()->getElement('dealerId'));
    }
}