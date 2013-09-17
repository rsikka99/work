<?php

/**
 * Class Quotegen_Form_Quote_NavigationTest
 */
class Quotegen_Form_Quote_NavigationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Quotegen_Form_Quote_Navigation
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Quotegen_Form_Quote_Navigation();
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