<?php

/**
 * Description of SurveyController:
 * This controller handles the survey/questionnaire.
 * User should be guided through a series of forms where they are asked to answer
 * questions about their existing fleet of printers.
 *
 * @author Lee robert
 */
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
                $this->_helper->redirector('company');
            }
            else
            {
                $reportSteps = $this->getReportSteps();
                $lastStep = null;
                /* @var $step Proposalgen_Model_Report_Step */
                foreach ( $reportSteps as $step )
                {
                    if (! $step->canAccess)
                    {
                        $lastStep = $step->previousStep;
                        break;
                    }
                }
                if ($lastStep !== null)
                {
                    // Send to latest page
                    $this->_helper->redirector($lastStep->action, $lastStep->controller);
                }
                else
                {
                    $this->_helper->redirector('company');
                }
            }
        }
        else
        {
            $this->_helper->redirector('index', 'index');
        }
    }

    /**
     * This is one of the pages in the survey
     */
    public function companyAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_COMPANY);

        $request = $this->getRequest();
        $form = new Proposalgen_Form_Survey_Company();

        // Get any saved answers
        $formDataFromAnswers = array (
                "company_name" => $this->getReport()->getCustomerCompanyName()
        );

        $formDataFromAnswers ["company_address"] = (Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(30, $this->getReport()
            ->getId())) ?  : "";

        $form->populate($formDataFromAnswers);

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ["goBack"]))
            {
                $this->gotoPreviousStep();
            }
            else
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        $this->getReport()->setCustomerCompanyName($form->getValue('company_name'));

                        // Everytime we save anything related to a report, we should save it (updates the modification date)
                        $this->saveReport();

                        $this->saveTextualQuestionAnswer(4, $form->getValue('company_name'));
                        $this->saveTextualQuestionAnswer(30, $form->getValue('company_address'));

                        if (isset($values ["saveAndContinue"]))
                        {
                            // Call the base controller to send us to the next logical step in the proposal.
                            $this->gotoNextStep();
                        }
                        else
                        {
                            $this->_helper->flashMessenger(array (
                                    'success' => "Your changes were saved sucessfully."
                            ));
                        }
                    }
                    else
                    {
                        throw new Zend_Validate_Exception("Form Validation Failed");
                    }
                }
                catch ( Zend_Validate_Exception $e )
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
    public function generalAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_GENERAL);

        $request = $this->getRequest();
        $form = new Proposalgen_Form_Survey_General();

        $mpsGoalRankings [1] = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(6, $this->getReport()
            ->getId())) ?  : false;
        $mpsGoalRankings [2] = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(7, $this->getReport()
            ->getId())) ?  : false;
        $mpsGoalRankings [3] = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(8, $this->getReport()
            ->getId())) ?  : false;
        $mpsGoalRankings [4] = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(9, $this->getReport()
            ->getId())) ?  : false;
        $mpsGoalRankings [5] = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(10, $this->getReport()
            ->getId())) ?  : false;

        /*
         * Get saved answers.
         */
        $formDataFromAnswers = array (
                "numb_employees" => (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(5, $this->getReport()
                    ->getId())) ?  : ""
        );

        /*
         * Note we use rank$rankNumber because the answer is the rank number. The questions 6-10 are translated to
         * values of 1-5. We must only set values for radio boxes that are set. $questionNumber is the way we map the
         * question to the values of each rank.
         */
        foreach ( $mpsGoalRankings as $questionNumber => $rankNumber )
        {
            // Only set it if it's a real number.
            if ($rankNumber !== FALSE)
            {
                $formDataFromAnswers ["rank{$rankNumber}"] = $questionNumber;
            }
        }

        $form->populate($formDataFromAnswers);

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ["goBack"]))
            {
                $this->gotoPreviousStep();
            }
            else
            {
                try
                {

                    if ($form->isValid($values))
                    {
                        $this->saveNumericQuestionAnswer(5, $form->getValue('numb_employees'));

                        // Map the rank numbers to the question numbers.
                        $rank [1] = $form->getValue('rank1');
                        $rank [2] = $form->getValue('rank2');
                        $rank [3] = $form->getValue('rank3');
                        $rank [4] = $form->getValue('rank4');
                        $rank [5] = $form->getValue('rank5');

                        foreach ( $rank as $rankNumber => $questionNumber )
                        {
                            // Right now it happens that the real question numbers are 5 above the 1-5.
                            $realQuestionNumber = $questionNumber + 5;
                            $this->saveNumericQuestionAnswer($realQuestionNumber, $rankNumber);
                        }

                        // Everytime we save anything related to a report, we should save it (updates the modification date)
                        $this->saveReport();

                        if (isset($values ["saveAndContinue"]))
                        {
                            // Call the base controller to send us to the next logical step in the proposal.
                            $this->gotoNextStep();
                        }
                        else
                        {
                            $this->_helper->flashMessenger(array (
                                    'success' => "Your changes were saved sucessfully."
                            ));
                        }
                    }
                    else
                    {
                        throw new Zend_Validate_Exception("Form Validation Failed");
                    }
                }
                catch ( Zend_Validate_Exception $e )
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
    public function financeAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_FINANCE);

        $request = $this->getRequest();
        $form = new Proposalgen_Form_Survey_Finance();

        // Populate the form with saved answers if we have them.


        $formDataFromAnswers = array ();

        $tonerCostRadio = (Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(11, $this->getReport()
            ->getId())) ?  : FALSE;

        if ($tonerCostRadio !== FALSE)
        {
            $formDataFromAnswers ["toner_cost_radio"] = $tonerCostRadio;
        }

        $tonerCost = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(11, $this->getReport()
            ->getId())) ?  : FALSE;

        if ($tonerCost !== FALSE)
        {
            $formDataFromAnswers ["toner_cost"] = $tonerCost;
        }

        $laborCostRadio = (Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(12, $this->getReport()
            ->getId())) ?  : FALSE;

        if ($laborCostRadio !== FALSE)
        {
            $formDataFromAnswers ["labor_cost_radio"] = $laborCostRadio;
        }

        $laborCost = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(12, $this->getReport()
            ->getId())) ?  : FALSE;

        if ($laborCost !== FALSE)
        {
            $formDataFromAnswers ["labor_cost"] = $laborCost;
        }

        $avgPurchase = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(14, $this->getReport()
            ->getId())) ?  : FALSE;

        if ($avgPurchase !== FALSE)
        {
            $formDataFromAnswers ["avg_purchase"] = $avgPurchase;
        }

        $itHourlyRate = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(15, $this->getReport()
            ->getId())) ?  : FALSE;

        if ($itHourlyRate !== FALSE)
        {
            $formDataFromAnswers ["it_hourlyRate"] = $itHourlyRate;
        }

        $form->populate($formDataFromAnswers);

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ["goBack"]))
            {
                $this->gotoPreviousStep();
            }
            else
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Question 11 (Ink and Toner costs
                        $this->saveTextualQuestionAnswer(11, $form->getValue('toner_cost_radio'));
                        if ($form->getValue('toner_cost'))
                        {
                            $this->saveNumericQuestionAnswer(11, $form->getValue('toner_cost'));
                        }

                        // Quesiton 12 (Service costs)
                        $this->saveTextualQuestionAnswer(12, $form->getValue('labor_cost_radio'));
                        if ($form->getValue('labor_cost'))
                        {
                            $this->saveNumericQuestionAnswer(12, $form->getValue('labor_cost'));
                        }

                        // Question 14
                        $this->saveNumericQuestionAnswer(14, $form->getValue('avg_purchase'));

                        // Question 15
                        $this->saveNumericQuestionAnswer(15, $form->getValue('it_hourlyRate'));

                        // Everytime we save anything related to a report, we should save it (updates the modification date)
                        $this->saveReport();

                        if (isset($values ["saveAndContinue"]))
                        {
                            // Call the base controller to send us to the next logical step in the proposal.
                            $this->gotoNextStep();
                        }
                        else
                        {
                            $this->_helper->flashMessenger(array (
                                    'success' => "Your changes were saved sucessfully."
                            ));
                        }
                    }
                    else
                    {
                        throw new Zend_Validate_Exception("Form Validation Failed");
                    }
                }
                catch ( Zend_Validate_Exception $e )
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
    public function purchasingAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_PURCHASING);

        $request = $this->getRequest();
        $form = new Proposalgen_Form_Survey_Purchasing();

        // Defaults
        $dailyValue = 22;
        $weeklyValue = 4;

        // Get any saved answers
        $formDataFromAnswers = array (
                "numb_vendors" => (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(16, $this->getReport()
                    ->getId())) ?  : ""
        );

        $numberOfMonthlyOrders = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(17, $this->getReport()
            ->getId())) ?  : FALSE;
        if ($numberOfMonthlyOrders !== FALSE)
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

        $form->populate($formDataFromAnswers);

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ["goBack"]))
            {
                $this->gotoPreviousStep();
            }
            else
            {
                try
                {

                    if ($form->isValid($values))
                    {
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

                        // Everytime we save anything related to a report, we should save it (updates the modification date)
                        $this->saveReport();

                        if (isset($values ["saveAndContinue"]))
                        {
                            // Call the base controller to send us to the next logical step in the proposal.
                            $this->gotoNextStep();
                        }
                        else
                        {
                            $this->_helper->flashMessenger(array (
                                    'success' => "Your changes were saved sucessfully."
                            ));
                        }
                    }
                    else
                    {
                        throw new Zend_Validate_Exception("Form Validation Failed");
                    }
                }
                catch ( Zend_Validate_Exception $e )
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
    public function itAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_IT);

        $request = $this->getRequest();
        $form = new Proposalgen_Form_Survey_It();

        // Populate the form with saved answers if we have them.


        $formDataFromAnswers = array ();

        $itHoursRadio = (Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(18, $this->getReport()
            ->getId())) ?  : FALSE;

        if ($itHoursRadio !== FALSE)
        {
            $formDataFromAnswers ["itHoursRadio"] = $itHoursRadio;
        }

        $itHours = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(18, $this->getReport()
            ->getId())) ?  : FALSE;

        if ($itHours !== FALSE)
        {
            $formDataFromAnswers ["itHours"] = $itHours;
        }

        $monthlyBreakdownsRadio = (Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(20, $this->getReport()
            ->getId())) ?  : FALSE;

        if ($monthlyBreakdownsRadio !== FALSE)
        {
            $formDataFromAnswers ["monthlyBreakdownRadio"] = $monthlyBreakdownsRadio;
        }

        $monthlyBreakdowns = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(20, $this->getReport()
            ->getId())) ?  : FALSE;

        if ($monthlyBreakdowns !== FALSE)
        {
            $formDataFromAnswers ["monthlyBreakdown"] = $monthlyBreakdowns;
        }

        $locationTracking = (Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(19, $this->getReport()
            ->getId())) ?  : FALSE;

        if ($locationTracking !== FALSE)
        {
            $formDataFromAnswers ["location_tracking"] = $locationTracking;
        }

        $form->populate($formDataFromAnswers);

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ["goBack"]))
            {
                $this->gotoPreviousStep();
            }
            else
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Question 18 (It Hours per month)
                        $this->saveTextualQuestionAnswer(18, $form->getValue('itHoursRadio'));
                        if ($form->getValue('itHours'))
                        {
                            $this->saveNumericQuestionAnswer(18, $form->getValue('itHours'));
                        }

                        // Quesiton 20 (Monthly Breakdowns)
                        $this->saveTextualQuestionAnswer(20, $form->getValue('monthlyBreakdownRadio'));
                        if ($form->getValue('monthlyBreakdown'))
                        {
                            $this->saveNumericQuestionAnswer(20, $form->getValue('monthlyBreakdown'));
                        }

                        // Question 19 (IP Based Location Tracking)
                        $this->saveTextualQuestionAnswer(19, $form->getValue('location_tracking'));

                        // Everytime we save anything related to a report, we should save it (updates the modification date)
                        $this->saveReport();

                        if (isset($values ["saveAndContinue"]))
                        {
                            // Call the base controller to send us to the next logical step in the proposal.
                            $this->gotoNextStep();
                        }
                        else
                        {
                            $this->_helper->flashMessenger(array (
                                    'success' => "Your changes were saved sucessfully."
                            ));
                        }
                    }
                    else
                    {
                        throw new Zend_Validate_Exception("Form Validation Failed");
                    }
                }
                catch ( Zend_Validate_Exception $e )
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
    public function usersAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_USERS);

        $request = $this->getRequest();
        $form = new Proposalgen_Form_Survey_Users();

        // Get any saved answers
        $formDataFromAnswers = array (
                "pageCoverage_BW" => (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(21, $this->getReport()
                    ->getId())) ?  : "",
                "pageCoverage_Color" => (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(22, $this->getReport()
                    ->getId())) ?  : ""
        );

        $percentPrintVolume = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(23, $this->getReport()
            ->getId())) ?  : FALSE;
        if ($percentPrintVolume !== FALSE)
        {
            $formDataFromAnswers ["printVolume"] = $percentPrintVolume;
        }

        $averagePrinterDowntime = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(24, $this->getReport()
            ->getId())) ?  : FALSE;
        if ($averagePrinterDowntime !== FALSE)
        {
            $formDataFromAnswers ["repairTime"] = $averagePrinterDowntime;
        }

        $form->populate($formDataFromAnswers);

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ["goBack"]))
            {
                $this->gotoPreviousStep();
            }
            else
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Question 21 (Page Coverage BW)
                        $this->saveNumericQuestionAnswer(21, $form->getValue('pageCoverage_BW'));

                        // Question 22 (Page Coverage Color)
                        $this->saveNumericQuestionAnswer(22, $form->getValue('pageCoverage_Color'));

                        // Question 23 (Percent Inkjet Printing)
                        $this->saveNumericQuestionAnswer(23, $form->getValue('printVolume'));

                        // Question 24 (Average Printer downtime)
                        $this->saveNumericQuestionAnswer(24, $form->getValue('repairTime'));

                        // Everytime we save anything related to a report, we should save it (updates the modification date)
                        $this->saveReport();

                        if (isset($values ["saveAndContinue"]))
                        {
                            // Call the base controller to send us to the next logical step in the proposal.
                            $this->gotoNextStep();
                        }
                        else
                        {
                            $this->_helper->flashMessenger(array (
                                    'success' => "Your changes were saved successfully."
                            ));
                        }
                    }
                    else
                    {
                        throw new Zend_Validate_Exception("Form Validation Failed");
                    }
                }
                catch ( Zend_Validate_Exception $e )
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

        $reportId = $this->getReport()->getId();
        // Populate all our view variables
        $currency = new Zend_Currency();

        // COMPANY
        $this->view->companyName = $this->getReport()->getCustomerCompanyName();
        $this->view->companyAddress = Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(30, $reportId);

        // GENERAL
        $this->view->numberOfEmployees = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(5, $reportId) . ' employees';

        $goalArray = array (
                1 => 'Ensure hardware matches print volume needs',
                2 => 'Increasing uptime and productivity',
                3 => 'Streamline logistics for supplies, service and hardware acquisition',
                4 => 'Reduce environmental impact',
                5 => 'Reduce costs'
        );

        $mpsGoalRankings [1] = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(6, $reportId);
        $mpsGoalRankings [2] = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(7, $reportId);
        $mpsGoalRankings [3] = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(8, $reportId);
        $mpsGoalRankings [4] = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(9, $reportId);
        $mpsGoalRankings [5] = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(10, $reportId);

        // Sort the goal rankings
        asort($mpsGoalRankings);

        $mpsGoalPriority = array ();
        for($i = 1; $i <= 5; $i ++)
        {

            $mpsGoalPriority [] = "{$i}. " . $goalArray [$mpsGoalRankings [$i]];
        }

        $this->view->mpsGoalPriority = implode("\n", $mpsGoalPriority);

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
        $this->view->averageItWage = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(15, $reportId) . ' per hour';

        // PURCHASING
        $this->view->numberOfSupplyVendors = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(16, $reportId) . ' vendor(s)';
        $this->view->numberOfSupplyOrders = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(17, $reportId) . ' order(s) per month';

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
        $this->view->pageCoverageColor = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(22, $reportId) . '%';
        $this->view->inkjentPrintPercentage = Proposalgen_Form_Survey_Users::$volumeOptions [Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(23, $reportId)];
        $this->view->averageRepairTime = Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(24, $reportId) . ' Days';
    }

    /**
     * Saves a textual answer for a report
     *
     * @param int $questionId
     * @param String $answer
     */
    protected function saveTextualQuestionAnswer ($questionId, $answer)
    {
        $mapper = Proposalgen_Model_Mapper_TextualAnswer::getInstance();
        $textualAnswer = new Proposalgen_Model_TextualAnswer();
        $textualAnswer->questionId = $questionId;
        $textualAnswer->answer = $answer;
        $textualAnswer->reportId = $this->getReport()->getId();
        $mapper->save($textualAnswer);
    }

    /**
     * Saves a numeric answer for a report
     *
     * @param int $questionId
     * @param number $answer
     */
    protected function saveNumericQuestionAnswer ($questionId, $answer)
    {
        $mapper = Proposalgen_Model_Mapper_NumericAnswer::getInstance();
        $numericAnswer = new Proposalgen_Model_NumericAnswer();
        $numericAnswer->questionId =$questionId;
        $numericAnswer->answer = $answer;
        $numericAnswer->reportId = $this->getReport()->getId();
        $mapper->save($numericAnswer);
    }

    /**
     * Saves a date answer for a report
     *
     * @param int $questionId
     * @param String $answer
     */
    protected function saveDateQuestionAnswer ($questionId, $answer)
    {
        $mapper = Proposalgen_Model_Mapper_DateAnswer::getInstance();
        $dateAnswer = new Proposalgen_Model_DateAnswer();
        $dateAnswer->questionId = $questionId;
        $dateAnswer->answer = $answer;
        $dateAnswer->reportId = $this->getReport()->getId();
        $mapper->save($dateAnswer);
    }
}
