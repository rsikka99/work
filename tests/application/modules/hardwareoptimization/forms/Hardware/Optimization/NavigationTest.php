<?php

/**
 * Class Hardwareoptimization_Form_Hardware_Optimization_NavigationTest
 */
class Hardwareoptimization_Form_Hardware_Optimization_NavigationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Hardwareoptimization_Form_Hardware_Optimization_Navigation
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Hardwareoptimization_Form_Hardware_Optimization_Navigation();
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_form = null;
    }

    /**
     * Test the buttons exist
     */
    public function testButtonsExist ()
    {
        $this->assertInstanceOf('Twitter_Bootstrap_Form_Element_Button', $this->_form->getElement('saveAndContinue'));
        $this->assertInstanceOf('Twitter_Bootstrap_Form_Element_Button', $this->_form->getElement('save'));
        $this->assertInstanceOf('Twitter_Bootstrap_Form_Element_Button', $this->_form->getElement('goBack'));
    }

}