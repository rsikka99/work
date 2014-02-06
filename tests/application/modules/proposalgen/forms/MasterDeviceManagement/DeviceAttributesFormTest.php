<?php

class Proposalgen_Form_MasterDeviceManagement_DeviceAttributesFormTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     * @var Proposalgen_Form_MasterDeviceManagement_DeviceAttributes
     */
    protected $_form;

    /**
     * Builds the form to be used for testing
     *
     * @param bool $isAllowed
     *
     */
    public function buildForm ($isAllowed = false)
    {
        $this->_form = new Proposalgen_Form_MasterDeviceManagement_DeviceAttributes(null, $isAllowed);
    }

    /**
     * This function loads an XML file of good data into arrays to be tested in the form
     */
    public function goodData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_deviceAttributesFormTest.xml");
        $data = array();

        foreach ($xml->deviceAttribute as $row)
        {
            $row    = json_decode(json_encode($row), 1);
            $data[] = $row;
        }

        return $data;
    }

    /**
     * @dataProvider goodData
     *               Tests whether the form accepts valid data
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->buildForm();
        $this->assertTrue($this->_form->isValid($data), implode(' | ', $this->_form->getErrorMessages()));
    }

    /**
     * This function loads an XML file of good data into arrays to be tested in the form
     */
    public function badData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_deviceAttributesFormTest.xml");
        $data = array();

        foreach ($xml->deviceAttribute as $row)
        {
            $row    = json_decode(json_encode($row), 1);
            $data[] = $row;
        }

        return $data;
    }

    /**
     * @dataProvider badData
     *               Tests if the form errors on invalid data
     */
    public function testFormRejectsBadData ($data)
    {
        $this->buildForm();
        $this->assertFalse($this->_form->isValid($data), implode(' | ', $this->_form->getErrorMessages()));
    }

    /**
     * @dataProvider goodData
     *               Tests whether the form accepts valid data when it has isAllowed privileges
     */
    public function testFormAcceptsValidDataAsIsAllowed ($data)
    {
        $this->buildForm(true);
        $this->assertTrue($this->_form->isValid($data), implode(' | ', $this->_form->getErrorMessages()));
    }

    /**
     * @dataProvider badData
     *               Tests if the form errors on invalid data when it has isAllowed privileges
     */
    public function testFormRejectsBadDataAsIsAllowed ($data)
    {
        $this->buildForm(true);
        $this->assertFalse($this->_form->isValid($data), implode(' | ', $this->_form->getErrorMessages()));
    }

    /**
     * Test the form when isAllowed
     *
     */
    public function testIsAllowed ()
    {
        $this->buildForm(true);
    }

    /**
     * Test the form when not isAllowed
     */
    public function testNotIsAllowed ()
    {
        $this->buildForm();
    }

    /**
     * Test the form to make sure the fields are all edibable when they have isAllowed
     */
    public function testDisabledFields ()
    {
        $this->buildForm();
        $this->assertAttributeEquals('disabled', 'disabled', $this->_form->getElement('isCopier'));
        $this->assertAttributeEquals('disabled', 'disabled', $this->_form->getElement('isDuplex'));
        $this->assertAttributeEquals('disabled', 'disabled', $this->_form->getElement('isFax'));
        $this->assertAttributeEquals('disabled', 'disabled', $this->_form->getElement('isCapableOfReportingTonerLevels'));
        $this->assertAttributeEquals('readonly', 'readonly', $this->_form->getElement('ppmBlack'));
        $this->assertAttributeEquals('readonly', 'readonly', $this->_form->getElement('ppmColor'));
        $this->assertAttributeEquals('readonly', 'readonly', $this->_form->getElement('launchDate'));
        $this->assertAttributeEquals('readonly', 'readonly', $this->_form->getElement('wattsPowerNormal'));
        $this->assertAttributeEquals('readonly', 'readonly', $this->_form->getElement('wattsPowerIdle'));
    }

    /**
     * Test the form to make sure the fields are all editable when they have isAllowed
     */
    public function testFieldsAreEditableAsIsAllowed ()
    {
        $this->buildForm(true);
        $this->assertObjectNotHasAttribute('disabled', $this->_form->getElement('isCopier'));
        $this->assertObjectNotHasAttribute('disabled', $this->_form->getElement('isDuplex'));
        $this->assertObjectNotHasAttribute('disabled', $this->_form->getElement('isFax'));
        $this->assertObjectNotHasAttribute('disabled', $this->_form->getElement('isCapableOfReportingTonerLevels'));
        $this->assertObjectNotHasAttribute('readonly', $this->_form->getElement('ppmBlack'));
        $this->assertObjectNotHasAttribute('readonly', $this->_form->getElement('ppmColor'));
        $this->assertObjectNotHasAttribute('readonly', $this->_form->getElement('launchDate'));
        $this->assertObjectNotHasAttribute('readonly', $this->_form->getElement('wattsPowerNormal'));
        $this->assertObjectNotHasAttribute('readonly', $this->_form->getElement('wattsPowerIdle'));
    }
}

