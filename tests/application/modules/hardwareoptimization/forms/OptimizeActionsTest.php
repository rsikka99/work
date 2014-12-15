<?php
use MPSToolbox\Legacy\Modules\HardwareOptimization\Forms\OptimizeActionsForm;

/**
 * Class Hardwareoptimization_Form_OptimizeActionsTest
 */
class Hardwareoptimization_Form_OptimizeActionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var OptimizeActionsForm
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new OptimizeActionsForm();
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
        $this->assertInstanceOf('Zend_Form_Element_Submit', $this->_form->getElement('Submit'));
        $this->assertInstanceOf('Zend_Form_Element_Submit', $this->_form->getElement('Analyze'));
        $this->assertInstanceOf('Zend_Form_Element_Submit', $this->_form->getElement('ResetReplacements'));
    }

}