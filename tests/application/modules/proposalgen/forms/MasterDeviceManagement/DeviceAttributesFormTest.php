<?php

use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\DeviceAttributesForm;

class Proposalgen_Form_MasterDeviceManagement_DeviceAttributesFormTest extends Tangent_PHPUnit_Framework_ZendFormTestCase

{
    /**
     * @var DeviceAttributesForm
     */
    protected $_form;

    /**
     * @return DeviceAttributesForm
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
        $this->_form = new DeviceAttributesForm(null, $isAllowed);
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
     * Test the form to make sure the fields are disabled when isAllowed = false
     */
    public function testDisabledFields ()
    {
        $this->buildForm();
        $this->assertTrue($this->_form->getElement('isCopier')->getAttrib('disabled'));
        $this->assertTrue($this->_form->getElement('isDuplex')->getAttrib('disabled'));
        $this->assertTrue($this->_form->getElement('isFax')->getAttrib('disabled'));
        $this->assertTrue($this->_form->getElement('isCapableOfReportingTonerLevels')->getAttrib('disabled'));
        $this->assertTrue($this->_form->getElement('ppmBlack')->getAttrib('disabled'));
        $this->assertTrue($this->_form->getElement('ppmColor')->getAttrib('disabled'));
        $this->assertTrue($this->_form->getElement('launchDate')->getAttrib('disabled'));
        $this->assertTrue($this->_form->getElement('wattsPowerNormal')->getAttrib('disabled'));
        $this->assertTrue($this->_form->getElement('wattsPowerIdle')->getAttrib('disabled'));
    }

    /**
     * Test the form to make sure the fields are all enabled when isAllowed = true
     */
    public function testFieldsAreEditableAsIsAllowed ()
    {
        $this->buildForm(true);
        $this->assertFalse($this->_form->getElement('isCopier')->getAttrib('disabled'));
        $this->assertFalse($this->_form->getElement('isDuplex')->getAttrib('disabled'));
        $this->assertFalse($this->_form->getElement('isFax')->getAttrib('disabled'));
        $this->assertFalse($this->_form->getElement('isCapableOfReportingTonerLevels')->getAttrib('disabled'));
        $this->assertFalse($this->_form->getElement('ppmBlack')->getAttrib('disabled'));
        $this->assertFalse($this->_form->getElement('ppmColor')->getAttrib('disabled'));
        $this->assertNull($this->_form->getElement('launchDate')->getAttrib('disabled')); // disabled only gets set to true if not allowed to edit - it's never set to false
        $this->assertFalse($this->_form->getElement('wattsPowerNormal')->getAttrib('disabled'));
        $this->assertFalse($this->_form->getElement('wattsPowerIdle')->getAttrib('disabled'));
    }
}

