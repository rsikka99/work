<?php

/**
 * Class Hardwareoptimization_Form_Hardware_Optimization_QuoteTest
 */
class Hardwareoptimization_Form_Hardware_Optimization_QuoteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Hardwareoptimization_Form_Hardware_Optimization_Quote
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Hardwareoptimization_Form_Hardware_Optimization_Quote();
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
        $this->assertInstanceOf('Twitter_Bootstrap_Form_Element_Submit', $this->_form->getElement('purchasedQuote'));
        $this->assertInstanceOf('Twitter_Bootstrap_Form_Element_Submit', $this->_form->getElement('leasedQuote'));
    }

}