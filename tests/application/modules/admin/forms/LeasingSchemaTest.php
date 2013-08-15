<?php

/**
 * Class Admin_Form_LeasingSchemaTest
 */
class Admin_Form_LeasingSchemaTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     * @var Admin_Form_LeasingSchema
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

        $this->_form = new Admin_Form_LeasingSchema($dealerManagement, array('view' => $view));
    }

    /**
     * This function loads an XML file of good data into arrays to be tested in the form
     */
    public function goodLeasingSchemaData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_leasingSchemaFormTest.xml");
        $data = array();
        foreach ($xml->leasingRate as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * This function loads an XML file of bad data into arrays to be tested in the form
     */
    public function badLeasingSchemaData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_leasingSchemaFormTest.xml");
        $data = array();
        foreach ($xml->leasingRate as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * Test the form using valid/required data
     *
     * @dataProvider goodLeasingSchemaData
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->buildForm();
        $this->assertTrue($this->_form->isValid((array)$data), "Leasing schema form did not accept good data.");
    }

    /**
     * Test the form using invalid/missing data
     *
     * @dataProvider badLeasingSchemaData
     */
    public function testFormRejectsInvalidData ($data)
    {
        $this->buildForm();
        $this->assertFalse($this->_form->isValid((array)$data), "Leasing schema form did not reject bad data.");
    }

    /**
     * Test the form using valid/required data as an admin
     *
     * @dataProvider goodLeasingSchemaData
     */
    public function testFormAcceptsValidDataAsAdmin ($data)
    {
        $this->buildForm();
        $this->assertTrue($this->_form->isValid((array)$data), "Leasing schema form did not accept good data.");
    }

    /**
     * Test the form using invalid/missing data as an admin
     *
     * @dataProvider badLeasingSchemaData
     */
    public function testFormRejectsInvalidDataAsAdmin ($data)
    {
        $this->buildForm();
        $this->assertFalse($this->_form->isValid((array)$data), "Leasing schema form did not reject bad data.");
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

    /**
     * Test form creating the dealerId element
     */
    public function testDealerFieldExists ()
    {
        $this->buildForm(false, true);
        $this->assertInstanceOf('Zend_Form_Element_Select', $this->_form->getElement('dealerId'));
    }

    /**
     * * Test form NOT creating the dealerId element
     */
    public function testDealerFieldDoesNotExist ()
    {
        $this->buildForm();
        $this->assertNotInstanceOf('Zend_Form_Element_Select', $this->_form->getElement('dealerId'));
    }

}