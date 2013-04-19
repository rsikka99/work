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
    protected $_assessmentSurveySetting;

    /**
     * @var Assessment_Form_Assessment_Survey
     */
    protected $_form;

    public function __construct ($assessmentSurvey, $surveySetting)
    {
        $this->_assessmentSurvey        = $assessmentSurvey;
        $this->_assessmentSurveySetting = $surveySetting;
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
                Assessment_Model_Mapper_Assessment_Survey::getInstance()->insert($this->_assessmentSurvey);
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
            $this->_form->populate($this->createPopulateArray($this->_assessmentSurvey, $this->_assessmentSurveySetting));
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
     * @param $assessmentSurvey
     *
     * @param $assessmentSurveySetting
     *
     * @return array
     */
    protected function createPopulateArray ($assessmentSurvey, $assessmentSurveySetting)
    {
        $formDataFromAnswers                           = array();
        $formDataFromAnswers ["toner_cost_radio"]      = ($assessmentSurvey->costOfInkAndToner > 0) ? 'exact' : 'guess';
        $formDataFromAnswers ["toner_cost"]            = ($assessmentSurvey->costOfInkAndToner > 0) ? $assessmentSurvey->costOfInkAndToner : null;
        $formDataFromAnswers ["labor_cost_radio"]      = ($assessmentSurvey->costOfLabor !== null) ? 'exact' : 'guess';
        $formDataFromAnswers ["labor_cost"]            = ($assessmentSurvey->costOfLabor !== null) ? $assessmentSurvey->costOfLabor : null;
        $formDataFromAnswers ["avg_purchase"]          = ($assessmentSurvey->costToExecuteSuppliesOrder > 0) ? $assessmentSurvey->costToExecuteSuppliesOrder : Assessment_Model_Assessment_Survey::DEFAULT_SUPPLIES_ORDER_COST;
        $formDataFromAnswers ["it_hourlyRate"]         = ($assessmentSurvey->averageItHourlyRate > 0) ? $assessmentSurvey->averageItHourlyRate : Assessment_Model_Assessment_Survey::DEFAULT_IT_HOURLY_RATE;
        $formDataFromAnswers ["itHoursRadio"]          = ($assessmentSurvey->hoursSpentOnIt > 0) ? 'exact' : 'guess';
        $formDataFromAnswers ["itHours"]               = ($assessmentSurvey->hoursSpentOnIt > 0) ? $assessmentSurvey->hoursSpentOnIt : null;
        $formDataFromAnswers ["monthlyBreakdownRadio"] = ($assessmentSurvey->averageMonthlyBreakdowns > 0) ? 'exact' : 'guess';
        $formDataFromAnswers ["monthlyBreakdown"]      = ($assessmentSurvey->averageMonthlyBreakdowns > 0) ? $assessmentSurvey->averageMonthlyBreakdowns : null;
        $formDataFromAnswers ["pageCoverage_BW"]       = ($assessmentSurvey->pageCoverageMonochrome > 0) ? $assessmentSurvey->pageCoverageMonochrome : $assessmentSurveySetting->pageCoverageMono;
        $formDataFromAnswers ["pageCoverage_Color"]    = ($assessmentSurvey->pageCoverageColor > 0) ? $assessmentSurvey->pageCoverageColor : $assessmentSurveySetting->pageCoverageColor;
        $formDataFromAnswers ["printVolume"]           = ($assessmentSurvey->percentageOfInkjetPrintVolume > 0) ? $assessmentSurvey->percentageOfInkjetPrintVolume : 5;
        $formDataFromAnswers ["repairTime"]            = ($assessmentSurvey->averageRepairTime > 0.0) ? $assessmentSurvey->averageRepairTime : 0.5;

        /**
         * Number of monthly supply orders
         */
        if ($assessmentSurvey->numberOfSupplyOrdersPerMonth > 0)
        {
            switch ($assessmentSurvey->numberOfSupplyOrdersPerMonth)
            {
                case Assessment_Model_Assessment_Survey::SUPPLY_ORDERS_DAILY :
                    $formDataFromAnswers ["inkTonerOrderRadio"] = "Daily";
                    break;
                case Assessment_Model_Assessment_Survey::SUPPLY_ORDERS_WEEKLY :
                    $formDataFromAnswers ["inkTonerOrderRadio"] = "Weekly";
                    break;
                default :
                    $formDataFromAnswers ["inkTonerOrderRadio"] = "Times per month";
                    $formDataFromAnswers ["numb_monthlyOrders"] = $assessmentSurvey->numberOfSupplyOrdersPerMonth;
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