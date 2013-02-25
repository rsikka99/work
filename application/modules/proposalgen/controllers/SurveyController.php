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

        /**
         * Fetch Survey Settings
         */
        $surveySetting = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->fetchSystemDefaultSurveySettings();
        $surveySetting->populate(Proposalgen_Model_Mapper_Survey_Setting::getInstance()->fetchUserSurveySetting($this->_userId)->toArray());

        $form = new Proposalgen_Form_Assessment_Survey();

        /**
         * Get data to populate
         */

        $survey = $this->getReport()->getSurvey();

        if (!$survey instanceof Proposalgen_Model_Assessment_Survey)
        {
            $survey = new Proposalgen_Model_Assessment_Survey();
        }


        $formDataFromAnswers = array();

        $formDataFromAnswers ["toner_cost_radio"]      = ($survey->costOfInkAndToner > 0) ? 'exact' : 'guess';
        $formDataFromAnswers ["toner_cost"]            = ($survey->costOfInkAndToner > 0) ? $survey->costOfInkAndToner : null;
        $formDataFromAnswers ["labor_cost_radio"]      = ($survey->costOfLabor > 0) ? 'exact' : 'guess';
        $formDataFromAnswers ["labor_cost"]            = ($survey->costOfLabor > 0) ? $survey->costOfLabor : null;
        $formDataFromAnswers ["avg_purchase"]          = ($survey->costToExecuteSuppliesOrder > 0) ? $survey->costToExecuteSuppliesOrder : Proposalgen_Model_Assessment_Survey::DEFAULT_SUPPLIES_ORDER_COST;
        $formDataFromAnswers ["it_hourlyRate"]         = ($survey->averageItHourlyRate > 0) ? $survey->averageItHourlyRate : Proposalgen_Model_Assessment_Survey::DEFAULT_IT_HOURLY_RATE;
        $formDataFromAnswers ["numb_vendors"]          = ($survey->numberOfSuppliesVendors > 0) ? $survey->numberOfSuppliesVendors : null;
        $formDataFromAnswers ["numb_vendors"]          = ($survey->numberOfSuppliesVendors > 0) ? $survey->numberOfSuppliesVendors : null;
        $formDataFromAnswers ["itHoursRadio"]          = ($survey->hoursSpentOnIt > 0) ? 'exact' : 'guess';
        $formDataFromAnswers ["itHours"]               = ($survey->hoursSpentOnIt > 0) ? $survey->hoursSpentOnIt : null;
        $formDataFromAnswers ["monthlyBreakdownRadio"] = ($survey->averageMonthlyBreakdowns > 0) ? 'exact' : 'guess';
        $formDataFromAnswers ["monthlyBreakdown"]      = ($survey->averageMonthlyBreakdowns > 0) ? $survey->averageMonthlyBreakdowns : null;
        $formDataFromAnswers ["pageCoverage_BW"]       = ($survey->pageCoverageMonochrome > 0) ? $survey->pageCoverageMonochrome : $surveySetting->pageCoverageMono;
        $formDataFromAnswers ["pageCoverage_Color"]    = ($survey->pageCoverageColor > 0) ? $survey->pageCoverageColor : $surveySetting->pageCoverageColor;
        $formDataFromAnswers ["printVolume"]           = $survey->percentageOfInkjetPrintVolume;
        $formDataFromAnswers ["repairTime"]            = $survey->averageRepairTime;

        /**
         * Number of monthly supply orders
         */
        if ($survey->numberOfSupplyOrdersPerMonth > 0)
        {
            switch ($survey->numberOfSupplyOrdersPerMonth)
            {
                case Proposalgen_Model_Assessment_Survey::SUPPLY_ORDERS_DAILY :
                    $formDataFromAnswers ["inkTonerOrderRadio"] = "Daily";
                    break;
                case Proposalgen_Model_Assessment_Survey::SUPPLY_ORDERS_WEEKLY :
                    $formDataFromAnswers ["inkTonerOrderRadio"] = "Weekly";
                    break;
                default :
                    $formDataFromAnswers ["inkTonerOrderRadio"] = "Times per month";
                    $formDataFromAnswers ["numb_monthlyOrders"] = $survey->numberOfSupplyOrdersPerMonth;
                    break;
            }
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
                if ($form->isValid($postData))
                {
                    $survey->costOfInkAndToner             = ($form->getValue('toner_cost')) ? : new Zend_Db_Expr('NULL');
                    $survey->costOfLabor                   = ($form->getValue('labor_cost')) ? : new Zend_Db_Expr('NULL');
                    $survey->costToExecuteSuppliesOrder    = $form->getValue('avg_purchase');
                    $survey->averageItHourlyRate           = $form->getValue('it_hourlyRate');
                    $survey->numberOfSuppliesVendors       = $form->getValue('numb_vendors');
                    $survey->hoursSpentOnIt                = ($form->getValue('itHours')) ? : new Zend_Db_Expr('NULL');
                    $survey->averageMonthlyBreakdowns      = ($form->getValue('monthlyBreakdown')) ? : new Zend_Db_Expr('NULL');
                    $survey->pageCoverageMonochrome        = $form->getValue('pageCoverage_BW');
                    $survey->pageCoverageColor             = $form->getValue('pageCoverage_Color');
                    $survey->percentageOfInkjetPrintVolume = $form->getValue('printVolume');
                    $survey->averageRepairTime             = $form->getValue('repairTime');

                    /**
                     * Number of monthly supply orders
                     */
                    switch ($form->getValue('inkTonerOrderRadio'))
                    {
                        case "Daily" :
                            $survey->numberOfSupplyOrdersPerMonth = Proposalgen_Model_Assessment_Survey::SUPPLY_ORDERS_DAILY;
                            break;
                        case "Weekly" :
                            $survey->numberOfSupplyOrdersPerMonth = Proposalgen_Model_Assessment_Survey::SUPPLY_ORDERS_WEEKLY;
                            break;
                        default :
                            $survey->numberOfSupplyOrdersPerMonth = $form->getValue('numb_monthlyOrders');
                            break;
                    }

                    // Every time we save anything related to a report, we should save it (updates the modification date)
                    $this->saveReport();

                    /**
                     * Save the survey
                     */
                    if ($survey->reportId > 0)
                    {
                        Proposalgen_Model_Mapper_Assessment_Survey::getInstance()->save($survey);
                    }
                    else
                    {
                        $survey->reportId = $this->getReport()->id;
                        Proposalgen_Model_Mapper_Assessment_Survey::getInstance()->insert($survey);
                    }

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
                    $this->_helper->flashMessenger(array('danger' => 'Please correct the errors below before continuing.'));
                }

            }
        }


        $this->view->form = $form;
    }
}