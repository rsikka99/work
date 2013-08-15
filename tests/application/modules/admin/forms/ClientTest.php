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
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_clientFormTest.xml");
        $data = array();
        foreach ($xml->client as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * This function loads an XML file of bad data into arrays to be tested in the form
     */
    public function badClientData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_clientFormTest.xml");
        $data = array();
        foreach ($xml->client as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * Test the form using valid/required data
     *
     * @dataProvider goodClientData
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->assertTrue($this->getForm()->isValid((array)$data), "Client form did not accept good data.");
    }

    /**
     * Test the form using invalid/missing data
     *
     * @dataProvider badClientData
     */
    public function testFormRejectsInvalidData ($data)
    {
        $this->assertFalse($this->getForm()->isValid((array)$data), "Client form did not reject bad data.");
    }

    /**
     * Test the form using valid/required data as an admin
     *
     * @dataProvider goodClientData
     */
    public function testFormAcceptsValidDataAsAdmin ($data)
    {
        $this->assertTrue($this->getForm(true, true)->isValid((array)$data), "Client form did not accept good data.");
    }

    /**
     * Test the form using invalid/missing data as an admin
     *
     * @dataProvider badClientData
     */
    public function testFormRejectsInvalidDataAsAdmin ($data)
    {
        $this->assertFalse($this->getForm(true, true)->isValid((array)$data), "Client form did not reject bad data.");
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