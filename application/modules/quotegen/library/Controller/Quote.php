<?php

/**
 * This base class takes care of common code for sub controllers for quotes and quote device configurations.
 * This allows us to create a more reusable structure.
 *
 * @author Lee Robert
 */
class Quotegen_Library_Controller_Quote extends Tangent_Controller_Action
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
        $this->_helper->contextSwitch()
            ->addContext('docx', array(
                                      'suffix'    => 'docx',
                                      'callbacks' => array(
                                          'init' => array(
                                              $this,
                                              'initDocxContext'
                                          ),
                                          'post' => array(
                                              $this,
                                              'postDocxContext'
                                          )
                                      )
                                 ))
            ->addContext('xlsx', array(
                                      'suffix'    => 'xlsx',
                                      'callbacks' => array(
                                          'init' => array(
                                              $this,
                                              'initXlsxContext'
                                          ),
                                          'post' => array(
                                              $this,
                                              'postXlsxContext'
                                          )
                                      )
                                 ));

        $this->_userId       = Zend_Auth::getInstance()->getIdentity()->id;
        $this->_quoteSession = new Zend_Session_Namespace(Quotegen_Library_Controller_Quote::QUOTE_SESSION_NAMESPACE);

        // Get the quote id from the url parameters
        $this->_quoteId = $this->_getParam('quoteId');

        // If we have a quote id, fetch the quote object from the database. 
        if ($this->_quoteId)
        {
            $this->_quoteId = (int)$this->_quoteId;
            $this->_quote   = Quotegen_Model_Mapper_Quote::getInstance()->find($this->_quoteId);
            if (!$this->_quote)
            {
                $this->_flashMessenger->addMessage(array(
                                                        'danger' => 'Could not find the selected quote.'
                                                   ));
                $this->redirector('index', 'index');
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
        $this->_quote->dateModified = date('Y-m-d H:i:s');

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
        // We need the quote lease value
        $quoteLeaseValue   = (float)$this->_quote->calculateQuoteLeaseValue();
        $leasingSchemaTerm = $this->_quote->getLeasingSchemaTerm();

        // Make sure we have a term selected
        if ($leasingSchemaTerm)
        {
            // Get the leasing schema attached to the term and make sure it really exists.
            $leasingSchema = $leasingSchemaTerm->getLeasingSchema();
            if ($leasingSchema)
            {
                // Get all the ranges for the schema, and of course check to make sure theres at least 1
                $leasingSchemaRanges = $leasingSchema->getRanges();
                if (count($leasingSchemaRanges) > 0)
                {
                    // Selected range will be set to the very last schema range if the lease value is too high.
                    $selectedRange = false;
                    /* @var $leasingSchemaRange Quotegen_Model_LeasingSchemaRange */
                    foreach ($leasingSchemaRanges as $leasingSchemaRange)
                    {
                        $selectedRange = $leasingSchemaRange;

                        // If we found our range, break out of the loop
                        if ((float)$leasingSchemaRange->startRange <= $quoteLeaseValue)
                        {
                            break;
                        }
                    }

                    // If we found a range, set the quote up with the term and rate
                    if ($selectedRange)
                    {
                        // Get the rate
                        $leasingSchemaRate                       = new Quotegen_Model_LeasingSchemaRate();
                        $leasingSchemaRate->leasingSchemaRangeId = $selectedRange->id;
                        $leasingSchemaRate->leasingSchemaTermId  = $leasingSchemaTerm->id;
                        $rateMapper                              = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance();
                        $leasingSchemaRate                       = $rateMapper->find($rateMapper->getPrimaryKeyValueForObject($leasingSchemaRate));

                        // Set the quote lease months and lease rate so that we can just directly use the values
                        $this->_quote->leaseTerm = $leasingSchemaTerm->months;
                        $this->_quote->leaseRate = $leasingSchemaRate->rate;
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
     *                     The quote device to sync
     * @param $syncOptions boolean
     *                     If set to true, it will sync the quote options associated with the quote device
     *
     * @return boolean Returns true if the sync was successful. If it was false, chances are it is because there is no
     *         link between the quote device and a device in the system.
     */
    protected function performSyncOnQuoteDevice (Quotegen_Model_QuoteDevice $quoteDevice, $syncOptions = true)
    {
        $device = $quoteDevice->getDevice();

        // If we don't have a link back to the master then we return false.
        if (!$device)
        {
            return false;
        }

        // Sync the device and save
        $quoteDevice = $this->syncDevice($quoteDevice, $device);

        // Sync our options
        if ($syncOptions)
        {
            /* @var $quoteDeviceOption Quotegen_Model_QuoteDeviceOption */
            foreach ($quoteDevice->getQuoteDeviceOptions() as $quoteDeviceOption)
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

        $quoteDevice->packageCost = $quoteDevice->calculatePackageCost();

        Quotegen_Model_Mapper_QuoteDevice::getInstance()->save($quoteDevice);

        return true;
    }

    protected function recalculateQuoteDevice (Quotegen_Model_QuoteDevice &$quoteDevice)
    {
        // Recalculate the package cost
        $quoteDevice->packageCost = $quoteDevice->calculatePackageCost();
    }

    /**
     * Syncs a quote device with a device
     *
     * @param $quoteDevice Quotegen_Model_QuoteDevice
     *                     The quote device that will be updated
     * @param $device      Quotegen_Model_Device
     *                     The device that we will use to update the quote device
     *
     * @return Quotegen_Model_QuoteDevice The updated quote device
     */
    protected function syncDevice (Quotegen_Model_QuoteDevice $quoteDevice, Quotegen_Model_Device $device)
    {
        $masterDevice               = $device->getMasterDevice();
        $quoteDevice->name          = $masterDevice->getFullDeviceName();
        $quoteDevice->oemSku        = $device->oemSku;
        $quoteDevice->dealerSku     = $device->dealerSku;
        $quoteDevice->tonerConfigId = $masterDevice->tonerConfigId;
        $quoteDevice->cost          = $device->cost;

        // Sync Cost Per Page
        $quoteDevice = $this->syncCostPerPageForDevice($quoteDevice, $masterDevice);

        return $quoteDevice;
    }

    /**
     * Syncs a quote device's cost per page to be up to date with the latest master device cost per page
     *
     * @param Quotegen_Model_QuoteDevice     $quoteDevice
     * @param Proposalgen_Model_MasterDevice $masterDevice
     *
     * @return Quotegen_Model_QuoteDevice
     */
    public function syncCostPerPageForDevice (Quotegen_Model_QuoteDevice $quoteDevice, Proposalgen_Model_MasterDevice $masterDevice)
    {
        $OEMpricingConfig  = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find(Proposalgen_Model_PricingConfig::OEM);
        $COMPpricingConfig = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find(Proposalgen_Model_PricingConfig::COMP);

        $oemCostPerPageSetting                         = new Proposalgen_Model_CostPerPageSetting();
        $oemCostPerPageSetting->adminCostPerPage       = 0;
        $oemCostPerPageSetting->laborCostPerPage       = 0;
        $oemCostPerPageSetting->partsCostPerPage       = 0;
        $oemCostPerPageSetting->pageCoverageMonochrome = ($this->_quote->pageCoverageMonochrome) ? $this->_quote->pageCoverageMonochrome / 100 : 0.94;
        $oemCostPerPageSetting->pageCoverageColor      = ($this->_quote->pageCoverageColor) ? $this->_quote->pageCoverageColor / 100 : 0.76;
        $oemCostPerPageSetting->pricingConfiguration   = $OEMpricingConfig;

        $compCostPerPageSetting                       = clone $oemCostPerPageSetting;
        $compCostPerPageSetting->pricingConfiguration = $COMPpricingConfig;


        // Calculate the cost per page
        $oemCostPerPage  = $masterDevice->calculateCostPerPage($oemCostPerPageSetting);
        $compCostPerPage = $masterDevice->calculateCostPerPage($compCostPerPageSetting);

        // Set our mono cost per page
        $quoteDevice->oemCostPerPageMonochrome  = $oemCostPerPage->monochromeCostPerPage;
        $quoteDevice->compCostPerPageMonochrome = $compCostPerPage->monochromeCostPerPage;

        // Only set our color if the device is color
        $quoteDevice->oemCostPerPageColor  = 0;
        $quoteDevice->compCostPerPageColor = 0;
        if ($masterDevice->isColor())
        {
            $quoteDevice->oemCostPerPageColor  = $oemCostPerPage->colorCostPerPage;
            $quoteDevice->compCostPerPageColor = $compCostPerPage->colorCostPerPage;
        }

        return $quoteDevice;
    }

    /**
     * Syncs a quote device option with an option
     *
     * @param Quotegen_Model_QuoteDeviceOption $quoteDeviceOption The quote device option that will be updated
     * @param Quotegen_Model_DeviceOption      $deviceOption      The option to update the quote device option with
     *
     * @return Quotegen_Model_Option The updated quote device option
     */
    protected function syncOption (Quotegen_Model_QuoteDeviceOption $quoteDeviceOption, Quotegen_Model_DeviceOption $deviceOption)
    {
        // Copy the option
        $quoteDeviceOption->oemSku           = $deviceOption->getOption()->oemSku;
        $quoteDeviceOption->dealerSku        = $deviceOption->getOption()->dealerSku;
        $quoteDeviceOption->name             = $deviceOption->getOption()->name;
        $quoteDeviceOption->description      = $deviceOption->getOption()->description;
        $quoteDeviceOption->cost             = $deviceOption->getOption()->cost;
        $quoteDeviceOption->includedQuantity = $deviceOption->includedQuantity;

        return $quoteDeviceOption;
    }

    /**
     * Gets a quote device and validates to ensure it is part of the current quote.
     * If it is not, then we redirect the user.
     *
     * @param string $paramIdField The parameter that the quote device id resides in
     *
     * @return \Quotegen_Model_QuoteDevice
     */
    public function getQuoteDevice ($paramIdField = 'id')
    {
        $quoteDeviceId = $this->_getParam($paramIdField, false);

        // Make sure they passed an id to us
        if (!$quoteDeviceId)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a device to edit first.'));
            $this->redirector('index', null, null, array('quoteId' => $this->_quoteId));
        }

        $quoteDevice = Quotegen_Model_Mapper_QuoteDevice::getInstance()->find($quoteDeviceId);

        // Validate that we have a quote device that is associated with the quote
        if (!$quoteDevice || $quoteDevice->quoteId !== $this->_quoteId)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'You may only edit devices associated with this quote.'));
            $this->redirector('index', null, null, array('quoteId' => $this->_quoteId));
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
        if (!$this->_quoteId || !$this->_quote)
        {
            $this->_flashMessenger->addMessage(array('danger' => 'There was an error getting the quote you previously selected. Please try selecting a quote again and contact the system administrator if the issue persists.'));
            $this->redirector('index', 'index');
        }
    }

    /**
     * Checks to ensure we have a proper quote device group object id.
     * If not we redirect to the index controller/action and leave the user a message to select a quote.
     */
    public function requireQuoteDeviceGroup ()
    {
        // Redirect if we don't have a quote id or a quote
        if (!$this->_quoteId || !$this->_quote)
        {
            $this->_flashMessenger->addMessage(array('danger' => 'Invalid quote group selected!'));
            $this->redirector('index', null, null, array('quoteId' => $this->_quoteId));
        }
    }

    /**
     * Initializes the view to work with docx
     */
    public function initDocxContext ()
    {
        // Include php word and initialize a new instance
        require_once('PHPWord.php');
        $this->view->phpword = new PHPWord();
    }

    public function postDocxContext ()
    {
        // Nothing to do in post yet
    }

    /**
     * Initializes the view to work with excel
     */
    public function initXlsxContext ()
    {
        // Include php excel and initialize a new instance
        $this->view->phpexcel = new PHPExcel();
    }

    public function postXlsxContext ()
    {
        // Nothing to do in post yet
    }

    /**
     * Clones a favorite device into a new quote device
     *
     * @param Quotegen_Model_DeviceConfiguration $favoriteDevice The device configuration to clone
     * @param int|float                          $defaultMargin  The margin to apply?
     *
     * @return int The quote device id, or false on error
     */
    public function cloneFavoriteDeviceToQuote ($favoriteDevice, $defaultMargin = 20)
    {
        try
        {
            // If it's not an object, it must be an id, so try and find it
            if (!($favoriteDevice instanceof Quotegen_Model_DeviceConfiguration))
            {
                $favoriteDevice = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->find($favoriteDevice);
            }

            // Get a new device and sync the device properties
            $quoteDevice                = $this->syncDevice(new Quotegen_Model_QuoteDevice(), $favoriteDevice->getDevice());
            $quoteDevice->quoteId       = $this->_quote->id;
            $quoteDevice->margin        = $defaultMargin;
            $quoteDevice->residual      = 0;
            $quoteDevice->packageMarkup = 0;
            $quoteDevice->packageCost   = $quoteDevice->calculatePackageCost();
            $quoteDeviceId              = Quotegen_Model_Mapper_QuoteDevice::getInstance()->insert($quoteDevice);

            // Insert link to device
            $quoteDeviceConfiguration                 = new Quotegen_Model_QuoteDeviceConfiguration();
            $quoteDeviceConfiguration->masterDeviceId = $favoriteDevice->masterDeviceId;
            $quoteDeviceConfiguration->quoteDeviceId  = $quoteDeviceId;
            Quotegen_Model_Mapper_QuoteDeviceConfiguration::getInstance()->insert($quoteDeviceConfiguration);

            // Prepare option link
            $quoteDeviceConfigurationOption                 = new Quotegen_Model_QuoteDeviceConfigurationOption();
            $quoteDeviceConfigurationOption->masterDeviceId = $favoriteDevice->masterDeviceId;

            // Add to the default group
            Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance()->insertDeviceInDefaultGroup($this->_quote->id, (int)$quoteDeviceId);

            foreach ($favoriteDevice->getOptions() as $option)
            {
                // Get the device option
                $deviceOption = Quotegen_Model_Mapper_DeviceOption::getInstance()->find(array(
                                                                                             $favoriteDevice->masterDeviceId,
                                                                                             $option->optionId
                                                                                        ));

                // Insert quote device option
                $quoteDeviceOption                = $this->syncOption(new Quotegen_Model_QuoteDeviceOption(), $deviceOption);
                $quoteDeviceOption->quoteDeviceId = $quoteDeviceId;
                $quoteDeviceOption->quantity      = $option->quantity;
                $quoteDeviceOptionId              = Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->insert($quoteDeviceOption);

                // Insert link
                $quoteDeviceConfigurationOption->quoteDeviceOptionId = $quoteDeviceOptionId;
                $quoteDeviceConfigurationOption->optionId            = $option->optionId;
                Quotegen_Model_Mapper_QuoteDeviceConfigurationOption::getInstance()->insert($quoteDeviceConfigurationOption);
            }
        }
        catch (Exception $e)
        {
            Tangent_Log::logException($e);

            return false;
        }

        return $quoteDeviceId;
    }

    /**
     * Function gets the total page count for monochrome and color for this quote
     *
     * @return array The amount of pages tallied
     */
    public function getTotalPages ()
    {
        $quoteDeviceGroupDeviceMapper = Quotegen_Model_Mapper_QuoteDeviceGroupDevice::getInstance();

        $quanities                             = array();
        $quanities ['monochromePagesQuantity'] = 0;
        $quanities ['colorPagesQuantity']      = 0;

        foreach ($this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {
            foreach ($quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice)
            {
                $quanities ['monochromePagesQuantity'] += $quoteDeviceGroupDevice->monochromePagesQuantity;
                $quanities ['colorPagesQuantity'] += $quoteDeviceGroupDevice->colorPagesQuantity;
            }
        }

        return $quanities;
    }
}

