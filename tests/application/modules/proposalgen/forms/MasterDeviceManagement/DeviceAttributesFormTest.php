<?php

class Proposalgen_Form_MasterDeviceManagement_DeviceAttributesFormTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * @var Proposalgen_Form_MasterDeviceManagement_DeviceAttributes
     */
    protected $_form;

    /**
     * @return Proposalgen_Form_MasterDeviceManagement_DeviceAttributes
     */
    public function getForm ()
    {
        $this->buildForm();

        return $this->_form;
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_deviceAttributesFormTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_deviceAttributesFormTest.xml");
    }


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

