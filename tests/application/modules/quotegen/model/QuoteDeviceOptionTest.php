<?php

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceOptionModel;

class Quotegen_Model_QuoteDeviceOptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * The quote device option model to test
     *
     * @var QuoteDeviceOptionModel
     */
    protected $_quoteDeviceOption;

    public function setUp ()
    {
        // Create option
        $this->_quoteDeviceOption                   = new QuoteDeviceOptionModel();
        $this->_quoteDeviceOption->quantity         = 1;
        $this->_quoteDeviceOption->includedQuantity = 1;
        $this->_quoteDeviceOption->cost             = 20;

        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_quoteDeviceOption = null;
    }

    public function testTotalQuantity ()
    {
        $expectedAnswer = 2;
        $actualResult   = $this->_quoteDeviceOption->getTotalQuantity();
        $this->assertEquals($expectedAnswer, $actualResult);
    }

    public function testSubTotal ()
    {
        $expectedAnswer = 20;
        $actualResult   = $this->_quoteDeviceOption->getTotalCost();
        $this->assertEquals($expectedAnswer, $actualResult);
    }
}