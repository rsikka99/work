<?php
use MPSToolbox\Legacy\Modules\HardwareOptimization\Forms\HardwareOptimizationNavigationForm;

/**
 * Class Hardwareoptimization_Form_Hardware_Optimization_NavigationTest
 */
class Hardwareoptimization_Form_Hardware_Optimization_NavigationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var HardwareOptimizationNavigationForm
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new HardwareOptimizationNavigationForm();
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
        $this->assertInstanceOf('Zend_Form_Element_Button', $this->_form->getElement('saveAndContinue'));
        $this->assertInstanceOf('Zend_Form_Element_Button', $this->_form->getElement('save'));
        $this->assertInstanceOf('Zend_Form_Element_Button', $this->_form->getElement('goBack'));
    }

}