<?php
class Assessment_IndexController extends Tangent_Controller_Action
{
    /**
     * The Zend_Auth identity
     *
     * @var stdClass
     */
    protected $_identity;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_mpsSession;

    /**
     * @var
     */
    protected $_assessment;

    /**
     * The navigation steps
     *
     * @var Assessment_Model_Assessment_Steps
     */
    protected $_navigation;

    public function init ()
    {
        $this->_identity   = Zend_Auth::getInstance()->getIdentity()->id;
        $this->_mpsSession = new Zend_Session_Namespace('mps-tools');
        $this->_navigation = Assessment_Model_Assessment_Steps::getInstance();
    }


    public function indexAction ()
    {

    }

    /**
     * This is our survey page. Everything we need to fill out is here.
     */
    public function surveyAction ()
    {
        $this->_navigation->setActiveStep(Assessment_Model_Assessment_Steps::STEP_SURVEY);

        /**
         * Fetch Survey Settings
         */
        $surveySetting = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->fetchSystemSurveySettings();

        $user = Application_Model_Mapper_User::getInstance()->find($this->_userId);

        $surveySetting->populate($user->getUserSettings()->getSurveySettings()->toArray());

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
        $formDataFromAnswers ["labor_cost_radio"]      = ($survey->costOfLabor !== null) ? 'exact' : 'guess';
        $formDataFromAnswers ["labor_cost"]            = ($survey->costOfLabor !== null) ? $survey->costOfLabor : null;
        $formDataFromAnswers ["avg_purchase"]          = ($survey->costToExecuteSuppliesOrder > 0) ? $survey->costToExecuteSuppliesOrder : Proposalgen_Model_Assessment_Survey::DEFAULT_SUPPLIES_ORDER_COST;
        $formDataFromAnswers ["it_hourlyRate"]         = ($survey->averageItHourlyRate > 0) ? $survey->averageItHourlyRate : Proposalgen_Model_Assessment_Survey::DEFAULT_IT_HOURLY_RATE;
        $formDataFromAnswers ["itHoursRadio"]          = ($survey->hoursSpentOnIt > 0) ? 'exact' : 'guess';
        $formDataFromAnswers ["itHours"]               = ($survey->hoursSpentOnIt > 0) ? $survey->hoursSpentOnIt : null;
        $formDataFromAnswers ["monthlyBreakdownRadio"] = ($survey->averageMonthlyBreakdowns > 0) ? 'exact' : 'guess';
        $formDataFromAnswers ["monthlyBreakdown"]      = ($survey->averageMonthlyBreakdowns > 0) ? $survey->averageMonthlyBreakdowns : null;
        $formDataFromAnswers ["pageCoverage_BW"]       = ($survey->pageCoverageMonochrome > 0) ? $survey->pageCoverageMonochrome : $surveySetting->pageCoverageMono;
        $formDataFromAnswers ["pageCoverage_Color"]    = ($survey->pageCoverageColor > 0) ? $survey->pageCoverageColor : $surveySetting->pageCoverageColor;
        $formDataFromAnswers ["printVolume"]           = ($survey->percentageOfInkjetPrintVolume > 0) ? $survey->percentageOfInkjetPrintVolume : 5;
        $formDataFromAnswers ["repairTime"]            = ($survey->averageRepairTime > 0.0) ? $survey->averageRepairTime : 0.5;

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
        else
        {
            $formDataFromAnswers ["inkTonerOrderRadio"] = "Daily";
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
                $this->gotoPreviousNavigationStep($this->_navigation);
            }
            else
            {
                if ($form->isValid($postData))
                {
                    $laborCost                             = $form->getValue('labor_cost');
                    $survey->costOfInkAndToner             = ($form->getValue('toner_cost')) ? : new Zend_Db_Expr('NULL');
                    $survey->costOfLabor                   = ($laborCost != null) ? $laborCost : new Zend_Db_Expr('NULL');
                    $survey->costToExecuteSuppliesOrder    = $form->getValue('avg_purchase');
                    $survey->averageItHourlyRate           = $form->getValue('it_hourlyRate');
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
                        $this->gotoNextNavigationStep($this->_navigation);
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array(
                                                                'success' => "Your changes were saved successfully."
                                                           ));
                    }
                }
                else
                {
                    $this->_flashMessenger->addMessage(array('danger' => 'Please correct the errors below before continuing.'));
                }

            }
        }


        $this->view->form = $form;
    }

    public function settingsAction ()
    {

    }

    public function reportsAction ()
    {

    }
}