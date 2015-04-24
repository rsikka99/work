<?php
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceGroupDeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceConfigurationOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\LeasingSchemaRateMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceOptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceConfigurationOptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceConfigurationModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaRateModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteStepsModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Services\QuoteDeviceService;

/**
 * This base class takes care of common code for sub controllers for quotes and quote device configurations.
 * This allows us to create a more reusable structure.
 *
 * @author Lee Robert
 */
class Quotegen_Library_Controller_Quote extends My_Controller_Report
{
    const QUOTE_SESSION_NAMESPACE = 'quotegen';

    /**
     * The navigation steps
     *
     * @var QuoteStepsModel
     */
    protected $_navigation;

    /**
     * The quote model.
     *
     * @var QuoteModel
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
     * @var QuoteDeviceService
     */
    protected $_quoteDeviceService;

    /**
     * @var string
     */
    protected $_firstStepName = QuoteStepsModel::STEP_ADD_HARDWARE;

    /**
     * Last initialization step, called from the constructor.
     * Initializes all variables for the controller actions to use.
     */
    public function init ()
    {
        parent::init();

        $this->_navigation = QuoteStepsModel::getInstance();

        if (!My_Feature::canAccess(My_Feature::HARDWARE_QUOTE))
        {
            $this->_flashMessenger->addMessage([
                "error" => "You do not have permission to access this."
            ]);

            $this->redirectToRoute('app.dashboard');
        }

        $this->_helper->contextSwitch()
                      ->addContext('docx', [
                          'suffix'    => 'docx',
                          'callbacks' => [
                              'init' => [
                                  $this,
                                  'initDocxContext'
                              ],
                              'post' => [
                                  $this,
                                  'postDocxContext'
                              ]
                          ]
                      ])
                      ->addContext('xlsx', [
                          'suffix'    => 'xlsx',
                          'callbacks' => [
                              'init' => [
                                  $this,
                                  'initXlsxContext'
                              ],
                              'post' => [
                                  $this,
                                  'postXlsxContext'
                              ]
                          ]
                      ]);

        $this->_userId       = Zend_Auth::getInstance()->getIdentity()->id;
        $this->_quoteSession = new Zend_Session_Namespace(Quotegen_Library_Controller_Quote::QUOTE_SESSION_NAMESPACE);

        // Get the quote id from the URL parameters
        $this->_quoteId = $this->_getParam('quoteId');

        // If we have a quote id, fetch the quote object from the database. 
        if ($this->_quoteId)
        {
            $this->_quoteId = (int)$this->_quoteId;
            $this->_quote   = QuoteMapper::getInstance()->find($this->_quoteId);
            if (!$this->_quote)
            {
                $this->_flashMessenger->addMessage(['danger' => 'Could not find the selected quote.']);
                $this->redirectToRoute('app.dashboard');
            }
        }
        else
        {
            // Create a new one
            $this->_quote = new QuoteModel();
        }

        if (isset($this->_mpsSession->selectedClientId))
        {
            $client = ClientMapper::getInstance()->find($this->_mpsSession->selectedClientId);
            if (!$client instanceof ClientModel || $client->dealerId != $this->_identity->dealerId)
            {
                $this->_flashMessenger->addMessage(["danger" => "A client is not selected."]);
                $this->redirectToRoute('app.dashboard');
            }
            else
            {
                $this->_navigation->clientName = $client->companyName;
            }
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
            QuoteMapper::getInstance()->save($this->_quote);
        }
        else
        {
            $this->_quoteId = QuoteMapper::getInstance()->insert($this->_quote);
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
                // Get all the ranges for the schema, and of course check to make sure there's at least 1
                $leasingSchemaRanges = $leasingSchema->getRanges();
                if (count($leasingSchemaRanges) > 0)
                {
                    // Selected range will be set to the very last schema range if the lease value is too high.
                    $selectedRange = false;
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
                        $leasingSchemaRate                       = new LeasingSchemaRateModel();
                        $leasingSchemaRate->leasingSchemaRangeId = $selectedRange->id;
                        $leasingSchemaRate->leasingSchemaTermId  = $leasingSchemaTerm->id;
                        $rateMapper                              = LeasingSchemaRateMapper::getInstance();
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
     * @param QuoteDeviceModel $quoteDevice
     */
    protected function recalculateQuoteDevice (QuoteDeviceModel &$quoteDevice)
    {
        // Recalculate the package cost
        $quoteDevice->packageCost = $quoteDevice->calculatePackageCost();
    }

    /**
     * Gets a quote device and validates to ensure it is part of the current quote.
     * If it is not, then we redirect the user.
     *
     * @param string $paramIdField The parameter that the quote device id resides in
     *
     * @return \MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceModel
     */
    public function getQuoteDevice ($paramIdField = 'id')
    {
        $quoteDeviceId = $this->_getParam($paramIdField, false);

        // Make sure they passed an id to us
        if (!$quoteDeviceId)
        {
            $this->_flashMessenger->addMessage(['warning' => 'Please select a device to edit first.']);
            $this->redirectToRoute('quotes', ['quoteId' => $this->_quoteId]);
        }

        $quoteDevice = QuoteDeviceMapper::getInstance()->find($quoteDeviceId);

        // Validate that we have a quote device that is associated with the quote
        if (!$quoteDevice || (int)$quoteDevice->quoteId !== (int)$this->_quoteId)
        {
            $this->_flashMessenger->addMessage(['warning' => 'You may only edit devices associated with this quote.']);
            $this->redirectToRoute('quotes', ['quoteId' => $this->_quoteId]);
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
            $this->_flashMessenger->addMessage(['danger' => 'There was an error getting the quote you previously selected. Please try selecting a quote again and contact the system administrator if the issue persists.']);
            $this->redirectToRoute('app.dashboard');
        }
    }

    /**
     *  Gets a fully qualified quote device server
     *
     * @return QuoteDeviceService
     */
    public function getQuoteDeviceService ()
    {
        if (!isset($this->_quoteDeviceService))
        {
            $this->_quoteDeviceService = new QuoteDeviceService($this->_userId, $this->_quoteId);
        }

        return $this->_quoteDeviceService;
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
            $this->_flashMessenger->addMessage(['danger' => 'Invalid quote group selected!']);
            $this->redirectToRoute('quotes', ['quoteId' => $this->_quoteId]);
        }
    }

    /**
     * Initializes the view to work with DOCX
     */
    public function initDocxContext ()
    {
        // Include php word and initialize a new instance
        $this->view->phpword = new \PhpOffice\PhpWord\PhpWord();
    }

    public function postDocxContext ()
    {
        // Nothing to do in POST yet
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
        // Nothing to do in POST yet
    }

    /**
     * Clones a favorite device into a new quote device
     *
     * @param int|DeviceConfigurationModel $favoriteDevice The device configuration to clone
     * @param int|float                    $defaultMargin  The margin to apply?
     *
     * @return int The quote device id, or false on error
     */
    public function cloneFavoriteDeviceToQuote ($favoriteDevice, $defaultMargin = 20)
    {
        try
        {
            // If it's not an object, it must be an id, so try and find it
            if (!($favoriteDevice instanceof DeviceConfigurationModel))
            {
                $favoriteDevice = DeviceConfigurationMapper::getInstance()->find($favoriteDevice);
            }

            // Get a new device and sync the device properties
            $quoteDeviceService         = new QuoteDeviceService($this->_userId, $this->_quote->id);
            $quoteDevice                = $quoteDeviceService->syncDevice($favoriteDevice->getDevice()->masterDeviceId);
            $quoteDevice->quoteId       = $this->_quote->id;
            $quoteDevice->margin        = $defaultMargin;
            $quoteDevice->buyoutValue   = 0;
            $quoteDevice->packageMarkup = 0;
            $quoteDevice->packageCost   = $quoteDevice->calculatePackageCost();
            $quoteDeviceId              = QuoteDeviceMapper::getInstance()->insert($quoteDevice);

            // Insert link to device
            $quoteDeviceConfiguration                 = new QuoteDeviceConfigurationModel();
            $quoteDeviceConfiguration->masterDeviceId = $favoriteDevice->masterDeviceId;
            $quoteDeviceConfiguration->quoteDeviceId  = $quoteDeviceId;
            QuoteDeviceConfigurationMapper::getInstance()->insert($quoteDeviceConfiguration);

            // Prepare option link
            $quoteDeviceConfigurationOption                 = new QuoteDeviceConfigurationOptionModel();
            $quoteDeviceConfigurationOption->masterDeviceId = $favoriteDevice->masterDeviceId;

            // Add to the default group
            QuoteDeviceGroupDeviceMapper::getInstance()->insertDeviceInDefaultGroup($this->_quote->id, (int)$quoteDeviceId);

            foreach ($favoriteDevice->getOptions() as $option)
            {
                // Get the device option
                $deviceOption = DeviceOptionMapper::getInstance()->find([
                    $favoriteDevice->masterDeviceId,
                    $option->optionId
                ]);

                // Insert quote device option
                $quoteDeviceOption = $quoteDeviceService->syncOption(new QuoteDeviceOptionModel(), $deviceOption);

                $quoteDeviceOption->quoteDeviceId = $quoteDeviceId;
                $quoteDeviceOption->quantity      = $option->quantity;
                $quoteDeviceOptionId              = QuoteDeviceOptionMapper::getInstance()->insert($quoteDeviceOption);

                // Insert link
                $quoteDeviceConfigurationOption->quoteDeviceOptionId = $quoteDeviceOptionId;
                $quoteDeviceConfigurationOption->optionId            = $option->optionId;
                QuoteDeviceConfigurationOptionMapper::getInstance()->insert($quoteDeviceConfigurationOption);
            }
        }
        catch (Exception $e)
        {
            \Tangent\Logger\Logger::logException($e);

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
        $quantities                             = [];
        $quantities ['monochromePagesQuantity'] = 0;
        $quantities ['colorPagesQuantity']      = 0;

        foreach ($this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {
            foreach ($quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice)
            {
                $quantities ['monochromePagesQuantity'] += $quoteDeviceGroupDevice->monochromePagesQuantity;
                $quantities ['colorPagesQuantity'] += $quoteDeviceGroupDevice->colorPagesQuantity;
            }
        }

        return $quantities;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::postDispatch()
     */
    public function postDispatch ()
    {
        $stage = ($this->_quote->stepName) ?: $this->_firstStepName;
        $this->_navigation->updateAccessibleSteps($stage);

        /**
         * FIXME lrobert: Sort of a bad fix to prevent NavigationMenu from being rendered on pages with no layout
         *
         * Added this in so that scripts that are being
         * called as part of ajax or otherwise not needing a layout won't
         * try to render a navigation menu. Sort of a bad fix as it isn't
         * controlling what pages should have a navigation menu.
         */
        if ($this->getLayout()->isEnabled())
        {
            $this->view->placeholder('ProgressionNav')->set($this->view->NavigationMenu($this->_navigation, ['quoteId' => $this->_quoteId]));
        }

        parent::postDispatch();
    }

    /**
     * Updates an assessment to be at the next available step
     *
     * @param bool $force Whether or not to force the update
     */
    public function updateQuoteStepName ($force = false)
    {
        // We can only do this when we have an active step
        if ($this->_navigation->activeStep instanceof My_Navigation_Step)
        {
            // That step also needs a next step for this to work
            if ($this->_navigation->activeStep->nextStep instanceof My_Navigation_Step)
            {
                $update = true;
                // We only want to update
                if ($force)
                {
                    $update = true;
                }
                else
                {
                    $newStepName = $this->_navigation->activeStep->nextStep->enumValue;

                    foreach ($this->_navigation->steps as $step)
                    {
                        // No need to update the step if we were going back in time.
                        if ($step->enumValue == $newStepName && $step->canAccess)
                        {
                            $update = false;
                            break;
                        }
                    }
                }

                if ($update)
                {
                    $this->_quote->stepName = $this->_navigation->activeStep->nextStep->enumValue;
                }
            }
        }
    }
}

