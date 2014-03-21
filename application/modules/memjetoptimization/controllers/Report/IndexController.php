<?php

/**
 * Class Memjetoptimization_Report_IndexController
 */
class Memjetoptimization_Report_IndexController extends Memjetoptimization_Library_Controller_Action
{
    public function indexAction ()
    {
        $this->view->headTitle('Memjet Optimization');
        $this->view->headTitle('Report');
        $this->_navigation->setActiveStep(Memjetoptimization_Model_Memjet_Optimization_Steps::STEP_FINISHED);
        $this->initReportList();

        $form = new Memjetoptimization_Form_Memjet_Optimization_Quote();

        if ($this->getRequest()->isPost())
        {
            try
            {
                $postData = $this->getRequest()->getPost();
                if (!isset($postData['goBack']))
                {
                    $userId          = Zend_Auth::getInstance()->getIdentity()->id;
                    $quote           = new Quotegen_Model_Quote();
                    $quoteId         = 0;
                    $masterDeviceIds = Memjetoptimization_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance()->fetchUniqueReplacementDeviceInstancesForMemjetOptimization($this->_memjetOptimization->id);
                    $validForQuote   = true;
                    foreach ($masterDeviceIds as $masterDeviceId)
                    {
                        if (!(Quotegen_Model_Mapper_Device::getInstance()->find(array($masterDeviceId, Zend_Auth::getInstance()->getIdentity()->dealerId)) instanceof Quotegen_Model_Device))
                        {
                            $validForQuote = false;
                            break;
                        }
                    }
                    if ($validForQuote)
                    {
                        if (isset($postData['purchasedQuote']))
                        {
                            $quoteId = $quote->createNewQuote(Quotegen_Model_Quote::QUOTE_TYPE_PURCHASED, $this->_memjetOptimization->clientId, $userId)->id;
                        }
                        else if (isset($postData['leasedQuote']))
                        {
                            $quoteId = $quote->createNewQuote(Quotegen_Model_Quote::QUOTE_TYPE_LEASED, $this->_memjetOptimization->clientId, $userId)->id;
                        }

                        // Add the record into memjet_optimization_quotes
                        $memjetOptimizationQuote                       = new Memjetoptimization_Model_Memjet_Optimization_Quote();
                        $memjetOptimizationQuote->memjetOptimizationId = $this->_memjetOptimization->id;
                        $memjetOptimizationQuote->quoteId              = $quoteId;
                        Memjetoptimization_Model_Mapper_Memjet_Optimization_Quote::getInstance()->insert($memjetOptimizationQuote);

                        // Get the replacement master devices
                        $quoteDeviceService = new  Quotegen_Service_QuoteDevice($userId, $quoteId);
                        foreach ($masterDeviceIds as $masterDeviceId)
                        {
                            $quantity = Memjetoptimization_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance()->countReplacementDevicesById($memjetOptimizationQuote->memjetOptimizationId, $masterDeviceId);
                            $quoteDeviceService->addDeviceToQuote($masterDeviceId, $quantity);
                        }

                        $this->redirector('index', 'quote_devices', 'quotegen', array('quoteId' => $quoteId));
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array("danger" => "Error creating quote from device list. All Memjet replacement devices must be set as a quote device."));
                    }
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