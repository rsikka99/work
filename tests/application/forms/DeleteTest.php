<?php

/**
 * Class Application_Form_DeleteTest
 */
class Application_Form_DeleteTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     * @var Application_Form_Delete
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Application_Form_Delete();
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_form = null;
    }

    /**
     * Test form initialization
     */
    public function testInit ()
    {
        $this->_form = new Application_Form_Delete();
        $this->_form->init();
        $this->assertInstanceOf('Zend_Form_Element_Submit', $this->_form->getElement('submit'));
    }
}
