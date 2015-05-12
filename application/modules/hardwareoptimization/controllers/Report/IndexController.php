<?php
use MPSToolbox\Legacy\Modules\HardwareOptimization\Forms\HardwareOptimizationQuoteForm;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\HardwareOptimizationQuoteMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\HardwareOptimizationDeviceInstanceMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationStepsModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationQuoteModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Services\QuoteDeviceService;

/**
 * Class Hardwareoptimization_Report_IndexController
 */
class Hardwareoptimization_Report_IndexController extends Hardwareoptimization_Library_Controller_Action
{
    public function indexAction ()
    {
        $this->_pageTitle = ['Hardware Optimization', 'Report'];
        $this->_navigation->setActiveStep(HardwareOptimizationStepsModel::STEP_FINISHED);
        $this->initReportList();

        $form = new HardwareOptimizationQuoteForm();

        if ($this->getRequest()->isPost())
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            try
            {
                $postData = $this->getRequest()->getPost();
                if (!isset($postData['goBack']))
                {
                    $db->beginTransaction();

                    $userId  = Zend_Auth::getInstance()->getIdentity()->id;
                    $quote   = new QuoteModel();
                    $quoteId = 0;

                    if (isset($postData['purchasedQuote']))
                    {
                        $quoteId = $quote->createNewQuote(QuoteModel::QUOTE_TYPE_PURCHASED, $this->_hardwareOptimization->clientId, $userId)->id;
                    }
                    else if (isset($postData['leasedQuote']))
                    {
                        $quoteId = $quote->createNewQuote(QuoteModel::QUOTE_TYPE_LEASED, $this->_hardwareOptimization->clientId, $userId)->id;
                    }

                    /**
                     * Linking a hardware optimization to a quote lets us know what the quote was for.
                     */
                    $hardwareOptimizationQuote                         = new HardwareOptimizationQuoteModel();
                    $hardwareOptimizationQuote->hardwareOptimizationId = $this->_hardwareOptimization->id;
                    $hardwareOptimizationQuote->quoteId                = $quoteId;
                    HardwareOptimizationQuoteMapper::getInstance()->insert($hardwareOptimizationQuote);

                    /**
                     * Add devices to the quote
                     */
                    $quoteDeviceService = new  QuoteDeviceService($userId, $quoteId);
                    $masterDeviceIds    = HardwareOptimizationDeviceInstanceMapper::getInstance()->getMasterDeviceQuantitiesForHardwareOptimization($this->_hardwareOptimization->id);

                    /**
                     * TODO lrobert: Calling the rows this way ($row["somecolumn"]) isn't fantastic. Gotta find a better way.
                     */
                    foreach ($masterDeviceIds as $row)
                    {
                        $quoteDeviceService->addDeviceToQuote($row["masterDeviceId"], $row["quantity"]);
                    }

                    $db->commit();
                    $this->redirectToRoute('quotes', ['quoteId' => $quoteId]);
                }
                else
                {
                    $this->gotoPreviousNavigationStep($this->_navigation);
                }
            }
            catch (Exception $e)
            {
                $db->rollBack();

                \Tangent\Logger\Logger::logException($e);
                $this->_flashMessenger->addMessage(["danger" => "Error creating quote from device list.  If problem persists please contact system administrator"]);
            }
        }
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl("/js/app/legacy/HtmlReport.js?".date('Ymd')));
    }
}