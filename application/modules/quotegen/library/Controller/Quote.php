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
     * The user id of the user who is logged in
     *
     * @var number
     */
    protected $_userId;

    /**
     * Last initialization step, called from the constructor.
     * Initializes all varabiables for the controller actions to use.
     */
    public function init ()
    {
        // Add the ability to have a docx context
        $this->_helper->contextSwitch()->addContext('docx', array (
                'suffix' => 'docx', 
                'callbacks' => array (
                        'init' => array (
                                $this, 
                                'initDocxContext' 
                        ), 
                        'post' => array (
                                $this, 
                                'postDocxContext' 
                        ) 
                ) 
        ));
        
        $this->_userId = Zend_Auth::getInstance()->getIdentity()->id;
        $this->_quoteSession = new Zend_Session_Namespace(Quotegen_Library_Controller_Quote::QUOTE_SESSION_NAMESPACE);
        
        // Get the quote id from the url parameters
        $this->_quoteId = $this->_getParam('quoteId');
        
        // If we have a quote id, fetch the quote object from the database. 
        if ($this->_quoteId)
        {
            $this->_quoteId = (int)$this->_quoteId;
            $this->_quote = Quotegen_Model_Mapper_Quote::getInstance()->find($this->_quoteId);
            if (! $this->_quote)
            {
                $this->_helper->flashMessenger(array (
                        'danger' => 'Could not find the selected quote.' 
                ));
                $this->_helper->redirector('index', 'index');
            }
        }
        else
        {
            // Create a new one
            $this->_quote = new Quotegen_Model_Quote();
        }
    }

    /**
     * Saves the quote to the database
     *
     * @return int The id of the quote
     */
    protected function saveQuote ()
    {
        // We always want the modified date to reflect our last change
        $this->_quote->setDateModified(date('Y-m-d H:i:s'));
        
        // We always want to update our lease rate if available
        $this->recalculateLeaseData();
        
        // If we already have an id then we update, otherwise insert
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

    /**
     * Recalculates and repopulates the lease data for a quote.
     */
    private function recalculateLeaseData ()
    {
        $quoteLeaseValue = (float)$this->_quote->calculateQuoteLeaseValue();
        $leasingSchemaTerm = $this->_quote->getLeasingSchemaTerm();
        if ($leasingSchemaTerm)
        {
            $leasingSchema = $leasingSchemaTerm->getLeasingSchema();
            if ($leasingSchema)
            {
                $leasingSchemaRanges = $leasingSchema->getRanges();
                if (count($leasingSchemaRanges) > 0)
                {
                    $selectedRange = false;
                    /* @var $leasingSchemaRange Quotegen_Model_LeasingSchemaRange */
                    foreach ( $leasingSchemaRanges as $leasingSchemaRange )
                    {
                        $selectedRange = $leasingSchemaRange;
                        if ((float)$leasingSchemaRange->getStartRange() <= $quoteLeaseValue)
                        {
                            
                            break;
                        }
                    }
                    
                    if ($selectedRange)
                    {
                        // Get the rate
                        $leasingSchemaRate = new Quotegen_Model_LeasingSchemaRate();
                        $leasingSchemaRate->setLeasingSchemaRangeId($selectedRange->getId());
                        $leasingSchemaRate->setLeasingSchemaTermId($leasingSchemaTerm->getId());
                        
                        $rateMapper = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                        $leasingSchemaRate = $rateMapper->find($rateMapper->getPrimaryKeyValueForObject($leasingSchemaRate));
                        $this->_quote->setLeaseTerm($leasingSchemaTerm->getMonths());
                        $this->_quote->setLeaseRate($leasingSchemaRate->getRate());
                    }
                }
            }
        }
    }

    /**
     * Syncs a device configuration into a quote device for a quote.
     * If a device does not exist for the current quote it will create it for you.
     *
     * @param $quoteDevice Quotegen_Model_QuoteDevice
     *            The quote device to sync
     * @param $syncOptions boolean
     *            If set to true, it will sync the quote options associated with the quote device
     * @return boolean Returns true if the sync was successful. If it was false, chances are it is because there is no
     *         link between the quote device and a device in the system.
     */
    protected function performSyncOnQuoteDevice (Quotegen_Model_QuoteDevice $quoteDevice, $syncOptions = true)
    {
        $device = $quoteDevice->getDevice();
        
        // If we don't have a link back to the master then we return false.
        if (! $device)
        {
            return false;
        }
        
        // Sync the device and save
        $quoteDevice = $this->syncDevice($quoteDevice, $device);
        
        // Sync our options
        if ($syncOptions)
        {
            /* @var $quoteDeviceOption Quotegen_Model_QuoteDeviceOption */
            foreach ( $quoteDevice->getQuoteDeviceOptions() as $quoteDeviceOption )
            {
                // Only sync options that still have a link back to the master
                $deviceOption = $quoteDeviceOption->getDeviceOption();
                if ($deviceOption)
                {
                    $quoteDeviceOption = $this->syncOption($quoteDeviceOption, $deviceOption);
                    Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->save($quoteDeviceOption);
                }
            }
        }
        
        $quoteDevice->setPackagePrice($quoteDevice->calculatePackagePrice());
        if ($quoteDevice->getResidual() > $quoteDevice->getPackagePrice())
        {
            $quoteDevice->setResidual(0);
        }
        
        Quotegen_Model_Mapper_QuoteDevice::getInstance()->save($quoteDevice);
        
        return true;
    }

    /**
     * Syncs a quote device with a device
     *
     * @param $quoteDevice Quotegen_Model_QuoteDevice
     *            The quote device that will be updated
     * @param $device Quotegen_Model_Device
     *            The device that we will use to update the quote device
     * @return Quotegen_Model_QuoteDevice The updated quote device
     */
    protected function syncDevice (Quotegen_Model_QuoteDevice $quoteDevice, Quotegen_Model_Device $device)
    {
        $masterDevice = $device->getMasterDevice();
        $quoteDevice->setName($masterDevice->getFullDeviceName());
        $quoteDevice->setSku($device->getSku());
        $quoteDevice->setCost($masterDevice->getCost());
        
        // FIXME: These need to use calculated values!
        $quoteDevice->setOemCostPerPageMonochrome(999);
        $quoteDevice->setOemCostPerPageColor(999);
        $quoteDevice->setCompCostPerPageMonochrome(999);
        $quoteDevice->setCompCostPerPageColor(999);
        
        return $quoteDevice;
    }

    /**
     * Syncs a quote device option with an option
     *
     * @param $quoteDeviceOption Quotegen_Model_QuoteDeviceOption
     *            The quote device option that will be updated
     * @param $deviceOption Quotegen_Model_Option
     *            The option to update the quote device option with
     * @return Quotegen_Model_Option The updated quote device option
     */
    protected function syncOption (Quotegen_Model_QuoteDeviceOption $quoteDeviceOption, Quotegen_Model_DeviceOption $deviceOption)
    {
        // Copy the option
        $quoteDeviceOption->setSku($deviceOption->getOption()
            ->getSku());
        $quoteDeviceOption->setName($deviceOption->getOption()
            ->getName());
        $quoteDeviceOption->setDescription($deviceOption->getOption()
            ->getDescription());
        $quoteDeviceOption->setCost($deviceOption->getOption()
            ->getCost());
        $quoteDeviceOption->setIncludedQuantity($deviceOption->getIncludedQuantity());
        
        return $quoteDeviceOption;
    }

    /**
     * Gets a quote device and validates to ensure it is part of the current quote.
     * If it is not, then we redirect the user.
     *
     * @param $quoteDeviceId number
     *            The quote device id
     * @return Ambigous <Quotegen_Model_QuoteDevice, void>
     */
    public function getQuoteDevice ($paramIdField = 'id')
    {
        $quoteDeviceId = $this->_getParam($paramIdField, FALSE);
        
        // Make sure they passed an id to us
        if (! $quoteDeviceId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a device to edit first.' 
            ));
            $this->_helper->redirector('index', null, null, array (
                    'quoteId' => $this->_quoteId 
            ));
        }
        
        $quoteDevice = Quotegen_Model_Mapper_QuoteDevice::getInstance()->find($quoteDeviceId);
        
        // Validate that we have a quote device that is associated with the quote
        if (! $quoteDevice || $quoteDevice->getQuoteDeviceGroup()->getQuoteId() !== $this->_quoteId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'You may only edit devices associated with this quote.' 
            ));
            $this->_helper->redirector('index', null, null, array (
                    'quoteId' => $this->_quoteId 
            ));
        }
        
        return $quoteDevice;
    }

    /**
     * Checks to ensure we have a proper quote object and quote id.
     * If not we redirect to the index controller/action and leave the user a message to select a quote.
     */
    public function requireQuote ()
    {
        // Redirect if we don't have a quote id or a quote
        if (! $this->_quoteId || ! $this->_quote)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error getting the quote you previously selected. Please try selecting a quote again and contact the system administrator if the issue persists.' 
            ));
            $this->_helper->redirector('index', 'index');
        }
    }

    /**
     * Checks to ensure we have a proper quote device group object id.
     * If not we redirect to the index controller/action and leave the user a message to select a quote.
     */
    public function requireQuoteDeviceGroup ()
    {
        // Redirect if we don't have a quote id or a quote
        if (! $this->_quoteId || ! $this->_quote)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'Invalid quote group selected!' 
            ));
            $this->_helper->redirector('index', null, null, array (
                    'quoteId' => $this->_quoteId 
            ));
        }
    }

    /**
     * Initializes the view to work with docx
     */
    public function initDocxContext ()
    {
        // Include php word and initialize a new instance
        require_once ('PHPWord.php');
        $this->view->phpword = new PHPWord();
    }

    public function postDocxContext ()
    {
        // TODO: Nothing to do in post yet
    }

    /**
     * Clones a favorite device into a new quote device
     *
     * @param Quotegen_Model_DeviceConfiguration $favoriteDevice
     *            The device configuration to clone
     * @param Quotegen_Model_QuoteDeviceGroup $quoteDeviceGroup
     *            The quote device group to clone to
     * @return boolean
     */
    public function cloneFavoriteDeviceToQuote ($favoriteDevice, $quoteDeviceGroup)
    {
        // If it's not an object, it must be an id, so try and find it
        if (! ($favoriteDevice instanceof Quotegen_Model_DeviceConfiguration))
        {
            $favoriteDevice = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->find($favoriteDevice);
        }
        
        // If it's not an object, it must be an id, so try and find it
        if (! ($quoteDeviceGroup instanceof Quotegen_Model_QuoteDeviceGroup))
        {
            $quoteDeviceGroup = Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->find($quoteDeviceGroup);
        }
        
        // Get a new device and sync the device properties
        $quoteDevice = $this->syncDevice(new Quotegen_Model_QuoteDevice(), $favoriteDevice->getDevice());
        $quoteDevice->setQuoteDeviceGroupId($quoteDeviceGroup->getId());
        $quoteDeviceId = Quotegen_Model_Mapper_QuoteDevice::getInstance()->insert($quoteDevice);
        
        /* @var $option Quotegen_Model_DeviceConfigurationOption */
        foreach ( $favoriteDevice->getOptions() as $option )
        {
            $quoteDeviceOption = new Quotegen_Model_QuoteDeviceOption();
            $quoteDeviceOption->setQuoteDeviceId($quoteDeviceId);
            $quoteDeviceOption->setQuantity($option->getQuantity());
            $quoteDeviceOption->setIncludedQuantity($option->getQuantity());
        }
        
        return true;
    }
}
