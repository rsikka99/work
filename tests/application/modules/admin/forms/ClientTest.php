<?php

/**
 * Class Admin_Form_ClientTest
 */
class Admin_Form_ClientTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     * @var Admin_Form_Client
     */
    protected $_form;

    /**
     * Builds the form to be used for testing
     *
     * @param bool $dealerManagement
     * @param bool $isAdmin
     */
    public function buildForm ($dealerManagement = true, $isAdmin = false)
    {
        $view = $this->getMock('Zend_View', array('IsAllowed'));

        $view->expects($this->any())
        ->method('IsAllowed')
        ->will($this->returnValue($isAdmin));

        $this->_form = new Admin_Form_Client($dealerManagement, array('view' => $view));
    }

    /**
     * This function loads an XML file of good data into arrays to be tested in the form
     */
    public function goodClientData ()
    {
        $xmlStr = simplexml_load_file(__DIR__ . "/_files/goodData_clientFormTest.xml");
        $xml    = new SimpleXMLElement($xmlStr->asXML());

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
        $xmlStr = simplexml_load_file(__DIR__ . "/_files/badData_clientFormTest.xml");
        $xml    = new SimpleXMLElement($xmlStr->asXML());

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
        $this->buildForm();


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
        $this->assertTrue($this->_form->isValid($data), "Client form did not accept good data.");
    }

    /**
     * Test the form using invalid/missing data
     *
     * @dataProvider badClientData
     */
    public function testFormRejectsInvalidData ($accountNumber, $companyName, $employeeCount, $legalName, $countryCode, $areaCode, $exchangeCode, $number, $extension, $addressLine1, $city, $region, $postCode, $countryId)
    {
        $this->buildForm();

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
        $this->assertFalse($this->_form->isValid($data), "Client form did not reject bad data.");
    }

    /**
     * Test the form using valid/required data as an admin
     *
     * @dataProvider goodClientData
     */
    public function testFormAcceptsValidDataAsAdmin ($accountNumber, $companyName, $employeeCount, $legalName, $countryCode, $areaCode, $exchangeCode, $number, $extension, $addressLine1, $city, $region, $postCode, $countryId, $dealerId)
    {
        $this->buildForm();

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
        $this->assertTrue($this->_form->isValid($data), "Client form did not accept good data.");
    }

    /**
     * Test the form using invalid/missing data as an admin
     *
     * @dataProvider badClientData
     */
    public function testFormRejectsInvalidDataAsAdmin ($accountNumber, $companyName, $employeeCount, $legalName, $countryCode, $areaCode, $exchangeCode, $number, $extension, $addressLine1, $city, $region, $postCode, $countryId, $dealerId)
    {
        $this->buildForm();

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
        $this->assertFalse($this->_form->isValid($data), "Client form did not reject bad data.");
    }

    /**
     * Test the form when user is a dealer and has admin rights
     *
     */
    public function testAdminAndDealer ()
    {
        $this->buildForm(true, true);
    }

    /**
     * Test form loading when not managing as a dealer and not an admin
     */
    public function testNotAdminOrDealer ()
    {
        $this->buildForm(false);
    }

    public function testDealerFieldExists ()
    {
        $this->buildForm(false, true);
        $this->assertInstanceOf('Zend_Form_Element_Select', $this->_form->getElement('dealerId'));
    }

    public function testDealerFieldDoesNotExist ()
    {
        $this->buildForm();
        $this->assertNotInstanceOf('Zend_Form_Element_Select', $this->_form->getElement('dealerId'));
    }

}