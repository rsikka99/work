<?php
use MPSToolbox\Legacy\Modules\HardwareOptimization\Forms\HardwareOptimizationQuoteForm;

/**
 * Class Hardwareoptimization_Form_Hardware_Optimization_QuoteTest
 */
class Hardwareoptimization_Form_Hardware_Optimization_QuoteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var HardwareOptimizationQuoteForm
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new HardwareOptimizationQuoteForm();
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
    public function testFormButtonsExist ()
    {
        $this->assertInstanceOf('Zend_Form_Element_Submit', $this->_form->getElement('purchasedQuote'));
        $this->assertInstanceOf('Zend_Form_Element_Submit', $this->_form->getElement('leasedQuote'));
    }

}