<?php

/**
 * Class Healthcheck_Form_Healthcheck_NavigationTest
 */
class Healthcheck_Form_Healthcheck_NavigationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Healthcheck_Form_Healthcheck_Navigation
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Healthcheck_Form_Healthcheck_Navigation();
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_form = null;
    }

    public function testFormLoadsAllButtons ()
    {
        $this->assertNotInstanceOf('Zend_Form_Element_Button', $this->_form->getElement('goBack'));
        $this->assertNotInstanceOf('Zend_Form_Element_Button', $this->_form->getElement('save'));
        $this->assertNotInstanceOf('Zend_Form_Element_Button', $this->_form->getElement('saveAndContinue'));
    }
}