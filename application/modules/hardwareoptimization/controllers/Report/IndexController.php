<?php
/**
 * Class Hardwareoptimization_Report_IndexController
 */
class Hardwareoptimization_Report_IndexController extends Hardwareoptimization_Library_Controller_Action
{
    public function indexAction ()
    {
        $this->view->headTitle('Hardware Optimization');
        $this->view->headTitle('Report');
        $this->_navigation->setActiveStep(Hardwareoptimization_Model_Hardware_Optimization_Steps::STEP_FINISHED);
        $this->initReportList();

        $form = new Hardwareoptimization_Form_Hardware_Optimization_Quote();

        if ($this->getRequest()->isPost())
        {
            try
            {
                $postData = $this->getRequest()->getPost();
                if (!isset($postData['goBack']))
                {
                    $userId  = Zend_Auth::getInstance()->getIdentity()->id;
                    $quote   = new Quotegen_Model_Quote();
                    $quoteId = 0;

                    if (isset($postData['purchasedQuote']))
                    {
                        $quoteId = $quote->createNewQuote(Quotegen_Model_Quote::QUOTE_TYPE_PURCHASED, $this->_hardwareOptimization->clientId, $userId)->id;
                    }
                    else if (isset($postData['leasedQuote']))
                    {
                        $quoteId = $quote->createNewQuote(Quotegen_Model_Quote::QUOTE_TYPE_LEASED, $this->_hardwareOptimization->clientId, $userId)->id;
                    }

                    // Add the record into hardware_optimization_quotes
                    $hardwareOptimizationQuote                         = new Hardwareoptimization_Model_Hardware_Optimization_Quote();
                    $hardwareOptimizationQuote->hardwareOptimizationId = $this->_hardwareOptimization->id;
                    $hardwareOptimizationQuote->quoteId                = $quoteId;
                    Hardwareoptimization_Model_Mapper_Hardware_Optimization_Quote::getInstance()->insert($hardwareOptimizationQuote);
                    // Get the replacement master devices
                    $masterDeviceIds    = Proposalgen_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance()->fetchUniqueReplacementDeviceInstancesForHardwareOptimization($this->_hardwareOptimization->id);
                    $quoteDeviceService = new  Quotegen_Service_QuoteDevice($userId, $quoteId);
                    foreach ($masterDeviceIds as $masterDeviceId)
                    {
                        $quoteDeviceService->addDeviceToQuote($masterDeviceId);
                    }

                    $this->redirector('index', 'quote_devices', 'quotegen', array('quoteId' => $quoteId));
                }
                else
                {
                    $this->gotoPreviousNavigationStep($this->_navigation);
                }
            }
            catch (Exception $e)
            {
                $this->_flashMessenger->addMessage(array("danger" => "Error creating quote from device list.  If problem persists please contact system administrator"));
            }
        }
        $this->view->form = $form;
        $this->view->headScript()->prependFile($this->view->baseUrl("/js/htmlReport.js"));
    }
}