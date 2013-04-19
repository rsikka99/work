<?php
class Assessment_Service_Assessment_Survey
{

    /**
     * @var Assessment_Model_Assessment_Survey
     */
    protected $_assessmentSurvey;

    /**
     * @var Proposalgen_Model_Survey_Setting
     */
    protected $_surveySetting;

    /**
     * @var Assessment_Form_Assessment_Survey
     */
    protected $_form;

    public function __construct ($assessmentSurvey, $surveySetting)
    {
        $this->_assessmentSurvey = $assessmentSurvey;
        $this->_surveySetting    = $surveySetting;
    }

    /**
     * Saves the survey.
     * FIXME: This is a new take on a service. It will auto detect whether or not to insert/update. Is this what we're going to want?
     *
     * @param $postData
     * @param $assessmentId
     *
     * @return bool|int
     */
    public function save ($postData, $assessmentId)
    {
        $assessmentSurveyId = false;

        $formData = $this->validateData($postData);
        if ($formData !== false)
        {
            $laborCost                                              = $formData['labor_cost'];
            $this->_assessmentSurvey->costOfInkAndToner             = ($formData['toner_cost']) ? : new Zend_Db_Expr('NULL');
            $this->_assessmentSurvey->costOfLabor                   = ($laborCost != null) ? $laborCost : new Zend_Db_Expr('NULL');
            $this->_assessmentSurvey->costToExecuteSuppliesOrder    = $formData['avg_purchase'];
            $this->_assessmentSurvey->averageItHourlyRate           = $formData['it_hourlyRate'];
            $this->_assessmentSurvey->hoursSpentOnIt                = ($formData['itHours']) ? : new Zend_Db_Expr('NULL');
            $this->_assessmentSurvey->averageMonthlyBreakdowns      = ($formData['monthlyBreakdown']) ? : new Zend_Db_Expr('NULL');
            $this->_assessmentSurvey->pageCoverageMonochrome        = $formData['pageCoverage_BW'];
            $this->_assessmentSurvey->pageCoverageColor             = $formData['pageCoverage_Color'];
            $this->_assessmentSurvey->percentageOfInkjetPrintVolume = $formData['printVolume'];
            $this->_assessmentSurvey->averageRepairTime             = $formData['repairTime'];

            /**
             * Number of monthly supply orders
             */
            switch ($formData['inkTonerOrderRadio'])
            {
                case "Daily" :
                    $this->_assessmentSurvey->numberOfSupplyOrdersPerMonth = Assessment_Model_Assessment_Survey::SUPPLY_ORDERS_DAILY;
                    break;
                case "Weekly" :
                    $this->_assessmentSurvey->numberOfSupplyOrdersPerMonth = Assessment_Model_Assessment_Survey::SUPPLY_ORDERS_WEEKLY;
                    break;
                default :
                    $this->_assessmentSurvey->numberOfSupplyOrdersPerMonth = $formData['numb_monthlyOrders'];
                    break;
            }

            /**
             * Save the survey
             */
            if ($this->_assessmentSurvey->reportId == $assessmentId)
            {
                Assessment_Model_Mapper_Assessment_Survey::getInstance()->save($this->_assessmentSurvey);
            }
            else
            {
                $this->_assessmentSurvey->reportId = $assessmentId;
                Assessment_Model_Mapper_Assessment_Survey::getInstance()->insert($survey);
            }
            $assessmentSurveyId = $this->_assessmentSurvey->reportId;
        }


        return $assessmentSurveyId;
    }


    /**
     * Gets the form
     *
     * @return Assessment_Form_Assessment_Survey
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new Assessment_Form_Assessment_Survey();
        }

        return $this->_form;
    }

    /**
     * Validates incoming data. Returns filtered data or false if there were validation issues
     *
     * @param $data
     *
     * @return array|bool
     */
    public function validateData ($data)
    {
        $formData = false;
        if ($this->getForm()->isValid($data))
        {
            $formData = $this->getForm()->getValues();
        }

        return $formData;
    }

    /**
     * @param $survey
     * @param $surveySetting
     *
     * @return array
     */
    protected function createPopulateArray ($survey, $surveySetting)
    {
        $formDataFromAnswers                           = array();
        $formDataFromAnswers ["toner_cost_radio"]      = ($survey->costOfInkAndToner > 0) ? 'exact' : 'guess';
        $formDataFromAnswers ["toner_cost"]            = ($survey->costOfInkAndToner > 0) ? $survey->costOfInkAndToner : null;
        $formDataFromAnswers ["labor_cost_radio"]      = ($survey->costOfLabor !== null) ? 'exact' : 'guess';
        $formDataFromAnswers ["labor_cost"]            = ($survey->costOfLabor !== null) ? $survey->costOfLabor : null;
        $formDataFromAnswers ["avg_purchase"]          = ($survey->costToExecuteSuppliesOrder > 0) ? $survey->costToExecuteSuppliesOrder : Assessment_Model_Assessment_Survey::DEFAULT_SUPPLIES_ORDER_COST;
        $formDataFromAnswers ["it_hourlyRate"]         = ($survey->averageItHourlyRate > 0) ? $survey->averageItHourlyRate : Assessment_Model_Assessment_Survey::DEFAULT_IT_HOURLY_RATE;
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
                case Assessment_Model_Assessment_Survey::SUPPLY_ORDERS_DAILY :
                    $formDataFromAnswers ["inkTonerOrderRadio"] = "Daily";
                    break;
                case Assessment_Model_Assessment_Survey::SUPPLY_ORDERS_WEEKLY :
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

        return $formDataFromAnswers;
    }
}