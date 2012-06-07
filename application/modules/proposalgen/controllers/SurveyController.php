<?php

/**
 * Description of SurveyController:
 * This controller handles the survey/questionaire.
 * User should
 * be quided through a series of forms where they are asked to answer
 * questions about their existing fleet of printers.
 *
 * @author Chris Garrah
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
                    if (! $step->getCanAccess())
                    {
                        $lastStep = $step->getPreviousStep();
                        break;
                    }
                }
                // Send to latest page
                $this->_helper->redirector($lastStep->getAction(), $lastStep->getController());
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
            ->getReportId())) ?  : "";
        
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
            ->getReportId())) ?  : false;
        $mpsGoalRankings [2] = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(7, $this->getReport()
            ->getReportId())) ?  : false;
        $mpsGoalRankings [3] = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(8, $this->getReport()
            ->getReportId())) ?  : false;
        $mpsGoalRankings [4] = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(9, $this->getReport()
            ->getReportId())) ?  : false;
        $mpsGoalRankings [5] = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(10, $this->getReport()
            ->getReportId())) ?  : false;
        
        /*
         * Get saved answers.
         */
        $formDataFromAnswers = array (
                "numb_employees" => (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(5, $this->getReport()
                    ->getReportId())) ?  : "" 
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
            ->getReportId())) ?  : FALSE;
        
        if ($tonerCostRadio !== FALSE)
        {
            $formDataFromAnswers ["toner_cost_radio"] = $tonerCostRadio;
        }
        
        $tonerCost = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(11, $this->getReport()
            ->getReportId())) ?  : FALSE;
        
        if ($tonerCost !== FALSE)
        {
            $formDataFromAnswers ["toner_cost"] = $tonerCost;
        }
        
        $laborCostRadio = (Proposalgen_Model_Mapper_TextualAnswer::getInstance()->getQuestionAnswer(12, $this->getReport()
            ->getReportId())) ?  : FALSE;
        
        if ($laborCostRadio !== FALSE)
        {
            $formDataFromAnswers ["labor_cost_radio"] = $laborCostRadio;
        }
        
        $laborCost = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(12, $this->getReport()
            ->getReportId())) ?  : FALSE;
        
        if ($laborCost !== FALSE)
        {
            $formDataFromAnswers ["labor_cost"] = $laborCost;
        }
        
        $avgPurchase = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(14, $this->getReport()
            ->getReportId())) ?  : FALSE;
        
        if ($avgPurchase !== FALSE)
        {
            $formDataFromAnswers ["avg_purchase"] = $avgPurchase;
        }
        
        $itHourlyRate = (Proposalgen_Model_Mapper_NumericAnswer::getInstance()->getQuestionAnswer(15, $this->getReport()
            ->getReportId())) ?  : FALSE;
        
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
    public function verifyAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_VERIFY);
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
        $textualAnswer->setQuestionId($questionId);
        $textualAnswer->setAnswer($answer);
        $textualAnswer->setReportId($this->getReport()
            ->getReportId());
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
        $numericAnswer->setQuestionId($questionId);
        $numericAnswer->setAnswer($answer);
        $numericAnswer->setReportId($this->getReport()
            ->getReportId());
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
        $dateAnswer->setQuestionId($questionId);
        $dateAnswer->setAnswer($answer);
        $dateAnswer->setReportId($this->getReport()
            ->getReportId());
        $mapper->save($dateAnswer);
    }
}
