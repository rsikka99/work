<?php

/**
 * This base class takes care of common code for sub controllers for quotes and quote device configurations.
 * This allows us to create a more reusable structure.
 *
 * @author Lee Robert
 */
class Quotegen_Library_Controller_Quote extends Zend_Controller_Action
{
    const QUOTE_SESSION_NAMESPACE = 'quotegen';
    
    /**
     * The quote model.
     * We should always have a quote id in the session when we are in this controller.
     *
     * @var Quotegen_Model_Quote
     */
    protected $_quote;
    
    /**
     * The id of our current quote.
     *
     * @var number
     */
    protected $_quoteId;
    
    /**
     * The quote session namespace
     *
     * @var Zend_Session_Namespace
     */
    protected $_quoteSession;

    /**
     * Last initialization step, called from the constructor.
     * Initializes all varabiables for the controller actions to use.
     */
    public function init ()
    {
        $this->_quoteSession = new Zend_Session_Namespace(Quotegen_Library_Controller_Quote::QUOTE_SESSION_NAMESPACE);
        
        // Get the quote id from the session for easy access
        $this->_quoteId = (isset($this->_quoteSession->id)) ? $this->_quoteSession->id : FALSE;
        
        // If we have a quote id, fetch the quote object from the database. 
        if ($this->_quoteId)
        {
            $this->_quote = Quotegen_Model_Mapper_Quote::getInstance()->find($this->_quoteId);
        }
        else
        {
            // Create a new one
            $this->_quote = new Quotegen_Model_Quote();
        }
        
        $this->view->quote = $this->_quote;
    }

    /**
     * Resets the quote session to a brand new quote id.
     *
     * @param int $newQuoteId
     *            The new quote id to work with
     */
    protected function resetQuoteSession ($newQuoteId)
    {
        // Reset the session and set the new id
        $this->_quoteSession->unsetAll();
        $this->_quoteSession->id = (int)$newQuoteId;
    }

    /**
     * Saves the quote to the database
     *
     * @return int The id of the quote
     */
    protected function saveQuote ()
    {
        if ($this->_quoteId)
        {
            Quotegen_Model_Mapper_Quote::getInstance()->save($this->_quote);
        }
        else
        {
            $this->_quoteId = Quotegen_Model_Mapper_Quote::getInstance()->insert($this->_quote);
        }
        return $this->_quoteId;
    }

    protected function getDeviceConfiguration ($deviceConfigurationId)
    {
    }

    protected function saveDeviceConfiguration ($walues)
    {
    }

    /**
     * Syncs a device configuration into a quote device for a quote.
     * If a device does not exist for the current quote it will create it for you.
     *
     * @param number $deviceConfigurationId
     *            The device configuration id
     * @return Quotegen_Model_Quote_Device The quote device
     */
    protected function syncDeviceConfigurationToQuote ($deviceConfigurationId)
    {
        $deviceConfiguration = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->find($deviceConfigurationId);
        // Check to see if it exists already
        $quoteDeviceConfiguration = Quotegen_Model_Mapper_QuoteDeviceConfiguration::getInstance()->findByDeviceConfigurationId($deviceConfigurationId);
        $quoteDevice = null;
        
        // If the quote configuraiton doesnt exist, make one.
        if ($quoteDeviceConfiguration)
        {
            $quoteDevice = Quotegen_Model_Mapper_QuoteDevice::getInstance()->find($quoteDeviceConfiguration->getQuoteDeviceId());
            $quoteDeviceId = $quoteDeviceConfiguration->getQuoteDeviceId();
        }
        else
        {
            // Insert new quote device with defaults
            $quoteDevice = new Quotegen_Model_QuoteDevice();
            $quoteDevice->setMargin(0);
            $quoteDevice->setQuantity(1);
            $quoteDevice->setQuoteId($this->_quoteId);
            $quoteDevice->setName('Temp Name');
            $quoteDevice->setSku('Temp Sku');
            $quoteDevice->setPrice(0);
            
            $quoteDevice->setOemCostPerPageMonochrome(0);
            $quoteDevice->setOemCostPerPageColor(0);
            $quoteDevice->setCompCostPerPageMonochrome(0);
            $quoteDevice->setCompCostPerPageColor(0);
            
            $quoteDeviceId = Quotegen_Model_Mapper_QuoteDevice::getInstance()->insert($quoteDevice);
            
            // Insert teh link
            $quoteDeviceConfiguration = new Quotegen_Model_QuoteDeviceConfiguration();
            $quoteDeviceConfiguration->setDeviceConfigurationId($deviceConfigurationId);
            $quoteDeviceConfiguration->setQuoteDeviceId($quoteDeviceId);
            Quotegen_Model_Mapper_QuoteDeviceConfiguration::getInstance()->insert($quoteDeviceConfiguration);
        }
        
        // Copy the master device and sku
        $device = $deviceConfiguration->getDevice();
        $masterDevice = $device->getMasterDevice();
        $quoteDevice->setName($masterDevice->getFullDeviceName());
        $quoteDevice->setSku($device->getSku());
        $quoteDevice->setPrice($masterDevice->getDevicePrice());
        
        // FIXME: These need to use calculated values!
        $quoteDevice->setOemCostPerPageMonochrome(999);
        $quoteDevice->setOemCostPerPageColor(999);
        $quoteDevice->setCompCostPerPageMonochrome(999);
        $quoteDevice->setCompCostPerPageColor(999);
        
        // Save the quote device to the database
        Quotegen_Model_Mapper_QuoteDevice::getInstance()->save($quoteDevice);
        
        // Delete all the options and copy them again
        Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->deleteAllOptionsForQuoteDevice($quoteDeviceId);
        
        // Clone all the things (options)!
        $quoteDeviceOption = new Quotegen_Model_QuoteDeviceOption();
        $quoteDeviceOption->setQuoteDeviceId($quoteDeviceId);
        
        /* @var $deviceConfigurationOption Quotegen_Model_DeviceConfigurationOption */
        foreach ( $deviceConfiguration->getOptions() as $deviceConfigurationOption )
        {
            $option = $deviceConfigurationOption->getOption();
            // Copy the option
            $quoteDeviceOption->setSku($option->getSku());
            $quoteDeviceOption->setName($option->getName());
            $quoteDeviceOption->setDescription($option->getDescription());
            $quoteDeviceOption->setPrice($option->getPrice());
            $quoteDeviceOption->setQuantity($deviceConfigurationOption->getQuantity());
            $quoteDeviceOption->setIncludedQuantity($deviceConfigurationOption->getIncludedQuantity());
            
            // Save the device option
            Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->insert($quoteDeviceOption);
        }
        
        return $quoteDevice;
    }

    /**
     * Clones a device configuration for a quote.
     * (Favorite -> Quote Device Configuration)
     *
     * @param int $deviceConfigurationId
     *            The device configuration id to clone
     * @throws InvalidArgumentException
     * @return Quotegen_Model_DeviceConfiguration The new device configuration
     */
    protected function cloneDeviceConfiguration ($deviceConfigurationId)
    {
        // Get the device configuration
        $deviceConfiguration = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->find($deviceConfigurationId);
        
        // In case we get an invalid id
        if (! $deviceConfiguration)
        {
            throw new InvalidArgumentException('No device configuration exists by that device configuration id');
        }
        
        // Get all the options before we start changing things.
        $options = $deviceConfiguration->getOptions();
        
        $deviceConfigurationId = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->insert($deviceConfiguration);
        
        /* @var $deviceConfigurationOption Quotegen_Model_DeviceConfigurationOption */
        foreach ( $options as &$deviceConfigurationOption )
        {
            $deviceConfigurationOption->setDeviceConfigurationId($deviceConfigurationId);
            Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance()->insert($deviceConfigurationOption);
        }
        
        return $deviceConfiguration;
    }
    
}
