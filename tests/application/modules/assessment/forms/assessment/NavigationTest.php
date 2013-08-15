<?php

/**
 * Class Assessment_Form_Assessment_NavigationTest
 */
class Assessment_Form_Assessment_NavigationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Assessment_Form_Assessment_Navigation
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Assessment_Form_Assessment_Navigation();
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