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
        $quoteDeviceGroups = array();
        $quoteDevices = array ();
        $quoteDevice = new Quotegen_Model_QuoteDevice();
        
        
        $quoteDeviceGroups[] = $quoteDeviceGroup;
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
        $quoteDevice->setQuoteDeviceGroup($quoteDeviceGroup);
        $quoteDeviceGroup->setPages($quoteDeviceGroupPages);
        
        $quoteDevice->setCost(1000);
        
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
        $quoteDeviceGroup->setQuoteDevices($quoteDevices);
        
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
            $this->assertTrue((count($quoteDeviceGroup->getQuoteDevices()) > 0));
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
            foreach ( $quoteDeviceGroup->getQuoteDevices() as $quoteDevice )
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
            foreach ( $quoteDeviceGroup->getQuoteDevices() as $quoteDevice )
            {
                $this->assertNotNull($quoteDevice->getQuoteDeviceGroup());
            }
        }
    }
}