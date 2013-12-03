<?php

/**
 * Class Memjetoptimization_Form_OptimizeActionsTest
 */
class Memjetoptimization_Form_OptimizeActionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Memjetoptimization_Form_OptimizeActions
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Memjetoptimization_Form_OptimizeActions();
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_form = null;
    }

    /**
     * Test the elements exist
     */
    public function testFormElementsExist ()
    {
        $this->assertInstanceOf('Twitter_Bootstrap_Form_Element_Button', $this->_form->getElement('Submit'));
        $this->assertInstanceOf('Twitter_Bootstrap_Form_Element_Button', $this->_form->getElement('Analyze'));
        $this->assertInstanceOf('Twitter_Bootstrap_Form_Element_Button', $this->_form->getElement('ResetReplacements'));
    }

}