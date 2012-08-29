<?php

class Quotegen_Model_QuoteTest extends PHPUnit_Framework_TestCase
{
    /**
     * The quote model to test
     *
     * @var Quotegen_Model_Quote
     */
    protected $_quote;

    public function setUp ()
    {
        // Initialize
        $this->_quote = new Quotegen_Model_Quote();
        $quoteDeviceGroup = new Quotegen_Model_QuoteDeviceGroup();
        $quoteDeviceGroups = array ();
        $quoteDevices = array ();
        $quoteDevice = new Quotegen_Model_QuoteDevice();
        
        $quoteDeviceGroups [] = $quoteDeviceGroup;
        // Populate quote
        $this->_quote->setLeaseRate(0.05);
        $this->_quote->setLeaseTerm(48);
        
        $this->_quote->setPageCoverageMonochrome(6);
        $this->_quote->setPageCoverageColor(24);
        
        $this->_quote->setQuoteDevices($quoteDeviceGroups);
        // Populate a quote device group page
        

        $quoteDeviceGroupPage = new Quotegen_Model_QuoteDeviceGroupPage();
        $quoteDeviceGroupPage->setIncludedPrice(500);
        $quoteDeviceGroupPage->setIncludedQuantity(5000);
        $quoteDeviceGroupPage->setPricePerPage(0.05);
        
        $quoteDeviceGroupPages = array ();
        $quoteDeviceGroupPages [] = $quoteDeviceGroupPage;
        
        // Populate quote device
        $quoteDevice->setQuote($quoteDeviceGroup);
        $quoteDeviceGroup->setPages($quoteDeviceGroupPages);
        
        $quoteDevice->setQuantity(1);
        $quoteDevice->setCost(1000);
        $quoteDevice->setMargin(20);
        $quoteDevice->setPackagePrice(1275);
        $quoteDevice->setResidual(50);
        
        $quoteDevice->setCompCostPerPageColor(0);
        $quoteDevice->setCompCostPerPageMonochrome(0);
        $quoteDevice->setOemCostPerPageColor(0);
        $quoteDevice->setOemCostPerPageMonochrome(0);
        
        $quoteDeviceOptions = array ();
        
        // Create option
        $quoteDeviceOption_1 = new Quotegen_Model_QuoteDeviceOption();
        $quoteDeviceOption_1->setQuantity(1);
        $quoteDeviceOption_1->setIncludedQuantity(1);
        $quoteDeviceOption_1->setCost(20);
        
        // Add option to array
        $quoteDeviceOptions [] = $quoteDeviceOption_1;
        
        $quoteDevice->setQuoteDeviceOptions($quoteDeviceOptions);
        $quoteDevices [] = $quoteDevice;
        $quoteDeviceGroup->setQuote($this->_quote);
        $quoteDeviceGroup->setQuoteDeviceGroupDevices($quoteDevices);
        
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_quote = null;
    }

    public function testQuoteHasDeviceGroups ()
    {
        $this->assertTrue((count($this->_quote->getQuoteDeviceGroups()) > 0));
    }

    /**
     * @depends testQuoteHasDeviceGroups
     */
    public function testAllQuoteDeviceGroupsHaveDevices ()
    {
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            $this->assertTrue((count($quoteDeviceGroup->getQuoteDeviceGroupDevices()) > 0));
        }
    }

    /**
     * @depends testQuoteHasDeviceGroups
     */
    public function testAllQuoteDeviceGroupsHavePages ()
    {
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            $this->assertTrue((count($quoteDeviceGroup->getPages()) > 0));
        }
    }

    /**
     * @depends testAllQuoteDeviceGroupsHaveDevices
     */
    public function testAllQuoteDevicesHaveOptions ()
    {
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            /* @var $quoteDevice Quotegen_Model_QuoteDevice */
            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDevice )
            {
                
                $this->assertTrue((count($quoteDevice->getQuoteDeviceOptions()) > 0));
            }
        }
    }

    /**
     * @depends testAllQuoteDeviceGroupsHaveDevices
     */
    public function testAllQuoteDevicesHaveQuoteDeviceGroups ()
    {
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup )
        {
            /* @var $quoteDevice Quotegen_Model_QuoteDevice */
            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDevice )
            {
                $this->assertNotNull($quoteDevice->getQuote());
            }
        }
    }

    public function testCalculateQuoteSubtotal ()
    {
        $expectedAnswer = 1275;
        $actualResult = $this->_quote->calculateQuoteSubtotal();
        $this->assertEquals($expectedAnswer, $actualResult);
    }

    public function testCalculateQuoteLeaseSubtotal ()
    {
        $expectedAnswer = 561;
        $actualResult = $this->_quote->calculateQuoteMonthlyLeaseSubtotal();
        $this->assertEquals($expectedAnswer, $actualResult);
    }

    public function testCalculateQuoteSubtotalWithResidualsApplied ()
    {
        $expectedAnswer = 25225;
        $actualResult = $this->_quote->calculateQuoteLeaseValue();
        $this->assertEquals($expectedAnswer, $actualResult);
    }

    public function testCalculateTotalResidual ()
    {
        $expectedAnswer = 50;
        $actualResult = $this->_quote->calculateTotalResidual();
        $this->assertEquals($expectedAnswer, $actualResult);
    }

    public function testCountDevices ()
    {
        $expectedAnswer = 1;
        $actualResult = $this->_quote->countDevices();
        $this->assertEquals($expectedAnswer, $actualResult);
    }

    public function testCalculateTotalCost ()
    {
        $expectedAnswer = 1020;
        $actualResult = $this->_quote->calculateTotalCost();
        $this->assertEquals($expectedAnswer, $actualResult);
    }

    public function testCalculateTotalMargin ()
    {
        $expectedAnswer = 20;
        $actualResult = $this->_quote->calculateTotalMargin();
        $this->assertEquals($expectedAnswer, $actualResult);
    }

    public function testCalculateTotalMonthlyPagePrice ()
    {
        $expectedAnswer = 500;
        $actualResult = $this->_quote->calculateTotalMonthlyPagePrice();
        $this->assertEquals($expectedAnswer, $actualResult);
    }
}