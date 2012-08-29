<?php

class Quotegen_Model_QuoteDeviceTest extends PHPUnit_Framework_TestCase
{
    /**
     * The quote device option model to test
     *
     * @var Quotegen_Model_QuoteDevice
     */
    protected $_quoteDevice;

    public function setUp ()
    {
        // Initialize
        $quote = new Quotegen_Model_Quote();
        $quoteDeviceGroup = new Quotegen_Model_QuoteDeviceGroup();
        $quoteDeviceGroups = array ();
        $quoteDevices = array ();
        $quoteDevice = new Quotegen_Model_QuoteDevice();
        
        $this->_quoteDevice = $quoteDevice;
        
        $quoteDeviceGroups [] = $quoteDeviceGroup;
        // Populate quote
        $quote->setLeaseRate(0.05);
        $quote->setLeaseTerm(48);
        
        $quote->setPageCoverageMonochrome(6);
        $quote->setPageCoverageColor(24);
        
        $quote->setQuoteDevices($quoteDeviceGroups);
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
        $quoteDeviceGroup->setQuote($quote);
        $quoteDeviceGroup->setQuoteDeviceGroupDevices($quoteDevices);
        
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_quoteDevice = null;
    }

    public function testCalculateOptionsCost ()
    {
        $expectedAnswer = 20;
        $actualResult = $this->_quoteDevice->calculateOptionsCost();
        $this->assertEquals($expectedAnswer, $actualResult);
    }

    public function testCalculatePackageCost ()
    {
        $expectedAnswer = 1020;
        $actualResult = $this->_quoteDevice->calculatePackageCost();
        $this->assertEquals($expectedAnswer, $actualResult);
    }

    public function testCalculateMargin ()
    {
        $expectedAnswer = 20;
        $actualResult = $this->_quoteDevice->calculateMargin();
        $this->assertEquals($expectedAnswer, $actualResult);
    }

    public function testCalculatePackagePrice ()
    {
        $expectedAnswer = 1275;
        $actualResult = $this->_quoteDevice->calculatePackagePrice();
        $this->assertEquals($expectedAnswer, $actualResult);
    }

    public function testCalculateSubtotal ()
    {
        $expectedAnswer = 1275;
        $actualResult = $this->_quoteDevice->calculatePurchaseSubtotal();
        $this->assertEquals($expectedAnswer, $actualResult);
    }

    public function testCalculateLeaseValue ()
    {
        $expectedAnswer = 1225;
        $actualResult = $this->_quoteDevice->calculateLeaseValue();
        $this->assertEquals($expectedAnswer, $actualResult);
    }

    public function testCalculateMonthlyLeasePrice ()
    {
        $expectedAnswer = 61.25;
        $actualResult = $this->_quoteDevice->calculateMonthlyLeasePrice();
        $this->assertEquals($expectedAnswer, $actualResult);
    }

    public function testCalculatePackageMonthlyLeasePrice ()
    {
        $expectedAnswer = 61;
        $actualResult = $this->_quoteDevice->calculatePackageMonthlyLeasePrice();
        $this->assertEquals($expectedAnswer, $actualResult);
    }
}