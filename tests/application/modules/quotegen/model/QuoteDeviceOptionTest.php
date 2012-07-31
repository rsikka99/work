<?php

class Quotegen_Model_QuoteDeviceOptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * The quote device option model to test
     *
     * @var Quotegen_Model_QuoteDeviceOption
     */
    protected $_quoteDeviceOption;

    public function setUp ()
    {
        // Initialize
        $this->_quoteDeviceOption = new Quotegen_Model_QuoteDeviceOption();
        
        // Create option
        $this->_quoteDeviceOption = new Quotegen_Model_QuoteDeviceOption();
        $this->_quoteDeviceOption->setQuantity(1);
        $this->_quoteDeviceOption->setIncludedQuantity(1);
        $this->_quoteDeviceOption->setCost(20);
        
     
        
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_quoteDeviceOption = null;
    }
    
    public function testTotalQuantity()
    {
        $expectedAnswer = 2;
        $actualResult = $this->_quoteDeviceOption->getTotalQuantity();
        $this->assertEquals($expectedAnswer, $actualResult);
    }
    
    public function testSubTotal()
    {
        $expectedAnswer = 20;
        $actualResult = $this->_quoteDeviceOption->getSubTotal();
        $this->assertEquals($expectedAnswer, $actualResult);
    }
}