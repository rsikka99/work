<?php
class Proposalgen_SurveyController extends Proposalgen_Library_Controller_Proposal
{

    /**
     * The index action.
     * Redirects the user to the latest report page. Will send the user back to the index controller if the session was
     * not set properly.
     */
    public function indexAction ()
    {
        if (isset($this->_reportSession->reportId))
        {
            if ($this->_reportSession->reportId === 0)
            {
                $this->_helper->redirector('finance');
            }
            else
            {
                $reportSteps = $this->getReportSteps();
                $lastStep    = null;
                /* @var $step Proposalgen_Model_Report_Step */
                foreach ($reportSteps as $step)
                {
                    if (!$step->canAccess)
                    {
                        break;
                    }
                    $lastStep = $step;
                }

                if ($lastStep !== null)
                {
                    // Send to latest page
                    $this->_helper->redirector($lastStep->action, $lastStep->controller);
                }
                else
                {
                    $this->_helper->redirector('finance');
                }
            }
        }
        else
        {
            $this->_helper->redirector('index', 'index');
        }
    }

    /**
     * This is our survey page. Everything we need to fill out is here.
     */
    public function surveyAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY);

        $form = new Proposalgen_Form_Assessment_Survey();

        /**
         * Get data to populate
         */

        $formDataFromAnswers = array();

        $tonerCostRadio = (Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(11, $this->getReport()->id)) ? : false;

        if ($tonerCostRadio !== false)
        {
            $formDataFromAnswers ["toner_cost_radio"] = $tonerCostRadio;
        }

        $tonerCost = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(11, $this->getReport()->id)) ? : false;

        if ($tonerCost !== false)
        {
            $formDataFromAnswers ["toner_cost"] = $tonerCost;
        }

        $laborCostRadio = (Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(12, $this->getReport()->id)) ? : false;

        if ($laborCostRadio !== false)
        {
            $formDataFromAnswers ["labor_cost_radio"] = $laborCostRadio;
        }

        $laborCost = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(12, $this->getReport()->id)) ? : false;

        if ($laborCost !== false)
        {
            $formDataFromAnswers ["labor_cost"] = $laborCost;
        }

        $avgPurchase = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(14, $this->getReport()->id)) ? : false;

        if ($avgPurchase !== false)
        {
            $formDataFromAnswers ["avg_purchase"] = $avgPurchase;
        }

        $itHourlyRate = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(15, $this->getReport()->id)) ? : false;

        if ($itHourlyRate !== false)
        {
            $formDataFromAnswers ["it_hourlyRate"] = $itHourlyRate;
        }

        // Defaults
        $dailyValue  = 22;
        $weeklyValue = 4;

        // Get any saved answers
        $formDataFromAnswers ["numb_vendors"] = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(16, $this->getReport()->id)) ? : "";

        $numberOfMonthlyOrders = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(17, $this->getReport()->id)) ? : false;
        if ($numberOfMonthlyOrders !== false)
        {
            switch ($numberOfMonthlyOrders)
            {
                case $dailyValue :
                    $formDataFromAnswers ["inkTonerOrderRadio"] = "Daily";
                    break;
                case $weeklyValue :
                    $formDataFromAnswers ["inkTonerOrderRadio"] = "Weekly";
                    break;
                default :
                    $formDataFromAnswers ["inkTonerOrderRadio"] = "Times per month";
                    $formDataFromAnswers ["numb_monthlyOrders"] = $numberOfMonthlyOrders;
                    break;
            }
        }

        $itHoursRadio = (Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(18, $this->getReport()->id)) ? : false;

        if ($itHoursRadio !== false)
        {
            $formDataFromAnswers ["itHoursRadio"] = $itHoursRadio;
        }

        $itHours = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(18, $this->getReport()->id)) ? : false;

        if ($itHours !== false)
        {
            $formDataFromAnswers ["itHours"] = $itHours;
        }

        $monthlyBreakdownsRadio = (Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(20, $this->getReport()->id)) ? : false;

        if ($monthlyBreakdownsRadio !== false)
        {
            $formDataFromAnswers ["monthlyBreakdownRadio"] = $monthlyBreakdownsRadio;
        }

        $monthlyBreakdowns = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(20, $this->getReport()->id)) ? : false;

        if ($monthlyBreakdowns !== false)
        {
            $formDataFromAnswers ["monthlyBreakdown"] = $monthlyBreakdowns;
        }

        $locationTracking = (Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(19, $this->getReport()->id)) ? : false;

        if ($locationTracking !== false)
        {
            $formDataFromAnswers ["location_tracking"] = $locationTracking;
        }

        // Get the user survey settings for overrides
        $surveySetting = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->fetchSystemDefaultSurveySettings();
        $surveySetting->populate(Proposalgen_Model_Mapper_Survey_Setting::getInstance()->fetchUserSurveySetting($this->_userId)->toArray());

        // Override with user settings
        $pageCoverageBW    = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(21, $this->getReport()->id);
        $pageCoverageColor = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(22, $this->getReport()->id);

        // Override the settings with user settings
        $pageCoverageBW    = (!$pageCoverageBW) ? $surveySetting->pageCoverageMono : $pageCoverageBW;
        $pageCoverageColor = (!$pageCoverageColor) ? $surveySetting->pageCoverageColor : $pageCoverageColor;

        $formDataFromAnswers["pageCoverage_BW"]    = $pageCoverageBW;
        $formDataFromAnswers["pageCoverage_Color"] = $pageCoverageColor;

        $percentPrintVolume = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(23, $this->getReport()->id)) ? : false;
        if ($percentPrintVolume !== false)
        {
            $formDataFromAnswers ["printVolume"] = $percentPrintVolume;
        }

        $averagePrinterDowntime = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(24, $this->getReport()->id)) ? : false;
        if ($averagePrinterDowntime !== false)
        {
            $formDataFromAnswers ["repairTime"] = $averagePrinterDowntime;
        }

        $form->populate($formDataFromAnswers);


        /**
         * Handle our post
         */
        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData ["goBack"]))
            {
                $this->gotoPreviousStep();
            }
            else
            {
                try
                {
                    if ($form->isValid($postData))
                    {
                        $this->saveReport(false);

                        // Question 11 (Ink and Toner costs
                        $this->saveTextualQuestionAnswer(11, $form->getValue('toner_cost_radio'));
                        if ($form->getValue('toner_cost'))
                        {
                            $this->saveNumericQuestionAnswer(11, $form->getValue('toner_cost'));
                        }

                        // Question 12 (Service costs)
                        $this->saveTextualQuestionAnswer(12, $form->getValue('labor_cost_radio'));
                        if ($form->getValue('labor_cost'))
                        {
                            $this->saveNumericQuestionAnswer(12, $form->getValue('labor_cost'));
                        }

                        // Question 14
                        $this->saveNumericQuestionAnswer(14, $form->getValue('avg_purchase'));

                        // Question 15
                        $this->saveNumericQuestionAnswer(15, $form->getValue('it_hourlyRate'));

                        // Question 16
                        $this->saveNumericQuestionAnswer(16, $form->getValue('numb_vendors'));

                        $numberOfOrdersRadio = $form->getValue('inkTonerOrderRadio');

                        switch ($numberOfOrdersRadio)
                        {
                            case "Daily" :
                                $ordersPerMonth = $dailyValue;
                                break;
                            case "Weekly" :
                                $ordersPerMonth = $weeklyValue;
                                break;
                            default :
                                $ordersPerMonth = $form->getValue('numb_monthlyOrders');
                                break;
                        }

                        // Question 16
                        $this->saveNumericQuestionAnswer(17, $ordersPerMonth);

                        // Question 18 (It Hours per month)
                        $this->saveTextualQuestionAnswer(18, $form->getValue('itHoursRadio'));
                        if ($form->getValue('itHours'))
                        {
                            $this->saveNumericQuestionAnswer(18, $form->getValue('itHours'));
                        }

                        // Question 20 (Monthly Breakdowns)
                        $this->saveTextualQuestionAnswer(20, $form->getValue('monthlyBreakdownRadio'));
                        if ($form->getValue('monthlyBreakdown'))
                        {
                            $this->saveNumericQuestionAnswer(20, $form->getValue('monthlyBreakdown'));
                        }

                        // Question 21 (Page Coverage BW)
                        $this->saveNumericQuestionAnswer(21, $form->getValue('pageCoverage_BW'));
                        // Question 22 (Page Coverage Color)
                        $this->saveNumericQuestionAnswer(22, $form->getValue('pageCoverage_Color'));
                        // Question 23 (Percent Inkjet Printing)
                        $this->saveNumericQuestionAnswer(23, $form->getValue('printVolume'));
                        // Question 24 (Average Printer downtime)
                        $this->saveNumericQuestionAnswer(24, $form->getValue('repairTime'));

                        // Every time we save anything related to a report, we should save it (updates the modification date)
                        $this->saveReport();

                        if (isset($postData ["saveAndContinue"]))
                        {
                            // Call the base controller to send us to the next logical step in the proposal.
                            $this->gotoNextStep();
                        }
                        else
                        {
                            $this->_helper->flashMessenger(array(
                                                                'success' => "Your changes were saved successfully."
                                                           ));
                        }
                    }
                    else
                    {
                        throw new Zend_Validate_Exception("Form Validation Failed");
                    }
                }
                catch (Zend_Validate_Exception $e)
                {
                    $form->buildBootstrapErrorDecorators();
                }
            }
        }


        $this->view->form = $form;
    }


    /**
     * This is one of the pages in the survey
     */
    public function verifyAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_VERIFY);
        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ["goBack"]))
            {
                $this->gotoPreviousStep();
            }
            else
            {
                $this->saveReport();
                $this->gotoNextStep();
            }
        }

        $reportId = $this->getReport()->id;
        // Populate all our view variables
        $currency = new Zend_Currency();

        // COMPANY
        $this->view->companyName    = $this->getReport()->getClient()->companyName;
        $this->view->companyAddress = $this->getReport()->getClient()->getAddress();

        // GENERAL
        $this->view->numberOfEmployees = number_format($this->getReport()->getClient()->employeeCount) . ' employees';

        // FINANCE
        $tonerCostsRadio = Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(11, $reportId);
        if ($tonerCostsRadio !== "I know the exact amount")
        {
            $this->view->tonerCosts = "I don't know";
        }
        else
        {
            $this->view->tonerCosts = $currency->toCurrency(Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(11, $reportId));
        }

        $laborCostsRadio = Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(12, $reportId);
        if ($laborCostsRadio !== "I know the exact amount")
        {
            $this->view->serviceCosts = "I don't know";
        }
        else
        {
            $this->view->serviceCosts = $currency->toCurrency(Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(12, $reportId));
        }

        $this->view->averagePurchaseOrderCost = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(14, $reportId) . ' per order';
        $this->view->averageItWage            = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(15, $reportId) . ' per hour';

        // PURCHASING
        $this->view->numberOfSupplyVendors = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(16, $reportId) . ' vendor(s)';
        $this->view->numberOfSupplyOrders  = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(17, $reportId) . ' order(s) per month';

        // IT
        $itServiceHoursRadio = Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(18, $reportId);
        if ($itServiceHoursRadio !== "I know the exact amount")
        {
            $this->view->itServiceHours = "I don't know";
        }
        else
        {
            $this->view->itServiceHours = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(18, $reportId) . ' hour(s)';
        }

        $averageMonthlyPrinterBreakdowns = Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(20, $reportId);
        if ($averageMonthlyPrinterBreakdowns !== "I know the exact amount")
        {
            $this->view->averageMonthlyPrinterBreakdowns = "I don't know";
        }
        else
        {
            $this->view->averageMonthlyPrinterBreakdowns = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(20, $reportId) . ' breakdown(s) per month';
        }

        $this->view->ipPrinterLocationTracking = (strcasecmp(Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(19, $reportId), "Y") === 0) ? 'Yes' : 'No';

        // USERS
        $this->view->pageCoverageMonochrome = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(21, $reportId) . '%';
        $this->view->pageCoverageColor      = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(22, $reportId) . '%';
        $this->view->inkjentPrintPercentage = Proposalgen_Form_Survey_Users::$volumeOptions [Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(23, $reportId)];
        $this->view->averageRepairTime      = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(24, $reportId) . ' Days';
    }

    /**
     * Saves a textual answer for a report
     *
     * @param int    $questionId
     * @param String $answer
     */
    protected function saveTextualQuestionAnswer ($questionId, $answer)
    {
        $mapper                    = Proposalgen_Model_Mapper_TextualAnswer::getInstance();
        $textualAnswer             = new Proposalgen_Model_TextualAnswer();
        $textualAnswer->questionId = $questionId;
        $textualAnswer->answer     = $answer;
        $textualAnswer->reportId   = $this->getReport()->id;
        $mapper->save($textualAnswer);
    }

    /**
     * Saves a numeric answer for a report
     *
     * @param int    $questionId
     * @param number $answer
     */
    protected function saveNumericQuestionAnswer ($questionId, $answer)
    {
        $mapper                    = Proposalgen_Model_Mapper_NumericAnswer::getInstance();
        $numericAnswer             = new Proposalgen_Model_NumericAnswer();
        $numericAnswer->questionId = $questionId;
        $numericAnswer->answer     = $answer;
        $numericAnswer->reportId   = $this->getReport()->id;
        $mapper->save($numericAnswer);
    }

    /**
     * Saves a date answer for a report
     *
     * @param int    $questionId
     * @param String $answer
     */
    protected function saveDateQuestionAnswer ($questionId, $answer)
    {
        $mapper                 = Proposalgen_Model_Mapper_DateAnswer::getInstance();
        $dateAnswer             = new Proposalgen_Model_DateAnswer();
        $dateAnswer->questionId = $questionId;
        $dateAnswer->answer     = $answer;
        $dateAnswer->reportId   = $this->getReport()->id;
        $mapper->save($dateAnswer);
    }
}