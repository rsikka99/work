<?php
namespace MPSToolbox\Legacy\Modules\Assessment\Services;

use MPSToolbox\Legacy\Entities\SurveyEntity;
use MPSToolbox\Legacy\Modules\Assessment\Forms\AssessmentSurveyForm;

/**
 * Class AssessmentSurveyService
 *
 * @package MPSToolbox\Legacy\Modules\Assessment\Services
 */
class AssessmentSurveyService
{

    /**
     * @var SurveyEntity
     */
    protected $survey;

    /**
     * @var AssessmentSurveyForm
     */
    protected $_form;

    /**
     * @param SurveyEntity $assessmentSurvey
     */
    public function __construct ($assessmentSurvey)
    {
        $this->survey = $assessmentSurvey;
    }

    /**
     * Saves the survey.
     *
     * @param $data
     * @param $clientId
     *
     * @return bool|int
     */
    public function save ($data, $clientId)
    {
        $success = false;

        try
        {
            $formData = $this->validateData($data);
            if ($formData !== false)
            {
                $laborCost                                   = $formData['labor_cost'];
                $this->survey->clientId                      = $clientId;
                $this->survey->costOfInkAndToner             = ($formData['toner_cost']) ?: null;
                $this->survey->costOfLabor                   = ($laborCost != null) ? $laborCost : null;
                $this->survey->costToExecuteSuppliesOrder    = $formData['avg_purchase'];
                $this->survey->averageItHourlyRate           = $formData['it_hourlyRate'];
                $this->survey->hoursSpentOnIt                = ($formData['itHours']) ?: null;
                $this->survey->averageMonthlyBreakdowns      = ($formData['monthlyBreakdown']) ?: null;
                $this->survey->pageCoverageMonochrome        = $formData['pageCoverage_BW'];
                $this->survey->pageCoverageColor             = $formData['pageCoverage_Color'];
                $this->survey->percentageOfInkjetPrintVolume = $formData['printVolume'];
                $this->survey->averageRepairTime             = $formData['repairTime'];

                /**
                 * Number of monthly supply orders
                 */
                switch ($formData['inkTonerOrderRadio'])
                {
                    case 'Daily' :
                        $this->survey->numberOfSupplyOrdersPerMonth = SurveyEntity::SUPPLY_ORDERS_DAILY;
                        break;
                    case 'Weekly' :
                        $this->survey->numberOfSupplyOrdersPerMonth = SurveyEntity::SUPPLY_ORDERS_WEEKLY;
                        break;
                    default :
                        $this->survey->numberOfSupplyOrdersPerMonth = $formData['numb_monthlyOrders'];
                        break;
                }

                if ($this->survey->isDirty())
                {
                    $success = $this->survey->save();
                }
                else
                {
                    $success = true;
                }

            }
        }
        catch (\Exception $e)
        {
        }

        return $success;
    }


    /**
     * Gets the form
     *
     * @return AssessmentSurveyForm
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new AssessmentSurveyForm();
            $this->_form->populate($this->createPopulateArray($this->survey));
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
     * @param SurveyEntity $assessmentSurvey
     *
     * @return array
     */
    protected function createPopulateArray ($assessmentSurvey)
    {
        $formDataFromAnswers                           = [];
        $formDataFromAnswers ['toner_cost_radio']      = ($assessmentSurvey->costOfInkAndToner > 0) ? 'exact' : 'guess';
        $formDataFromAnswers ['toner_cost']            = ($assessmentSurvey->costOfInkAndToner > 0) ? $assessmentSurvey->costOfInkAndToner : null;
        $formDataFromAnswers ['labor_cost_radio']      = ($assessmentSurvey->costOfLabor !== null) ? 'exact' : 'guess';
        $formDataFromAnswers ['labor_cost']            = ($assessmentSurvey->costOfLabor !== null) ? $assessmentSurvey->costOfLabor : null;
        $formDataFromAnswers ['avg_purchase']          = ($assessmentSurvey->costToExecuteSuppliesOrder > 0) ? $assessmentSurvey->costToExecuteSuppliesOrder : SurveyEntity::DEFAULT_SUPPLIES_ORDER_COST;
        $formDataFromAnswers ['it_hourlyRate']         = ($assessmentSurvey->averageItHourlyRate > 0) ? $assessmentSurvey->averageItHourlyRate : SurveyEntity::DEFAULT_IT_HOURLY_RATE;
        $formDataFromAnswers ['itHoursRadio']          = ($assessmentSurvey->hoursSpentOnIt > 0) ? 'exact' : 'guess';
        $formDataFromAnswers ['itHours']               = ($assessmentSurvey->hoursSpentOnIt > 0) ? $assessmentSurvey->hoursSpentOnIt : null;
        $formDataFromAnswers ['monthlyBreakdownRadio'] = ($assessmentSurvey->averageMonthlyBreakdowns > 0) ? 'exact' : 'guess';
        $formDataFromAnswers ['monthlyBreakdown']      = ($assessmentSurvey->averageMonthlyBreakdowns > 0) ? $assessmentSurvey->averageMonthlyBreakdowns : null;
        $formDataFromAnswers ['pageCoverage_BW']       = ($assessmentSurvey->pageCoverageMonochrome > 0) ? $assessmentSurvey->pageCoverageMonochrome : 6;
        $formDataFromAnswers ['pageCoverage_Color']    = ($assessmentSurvey->pageCoverageColor > 0) ? $assessmentSurvey->pageCoverageColor : 24;
        $formDataFromAnswers ['printVolume']           = ($assessmentSurvey->percentageOfInkjetPrintVolume > 0) ? $assessmentSurvey->percentageOfInkjetPrintVolume : 5;
        $formDataFromAnswers ['repairTime']            = ($assessmentSurvey->averageRepairTime > 0.0) ? $assessmentSurvey->averageRepairTime : 0.5;

        /**
         * Number of monthly supply orders
         */
        if ($assessmentSurvey->numberOfSupplyOrdersPerMonth > 0)
        {
            switch ($assessmentSurvey->numberOfSupplyOrdersPerMonth)
            {
                case SurveyEntity::SUPPLY_ORDERS_DAILY :
                    $formDataFromAnswers ['inkTonerOrderRadio'] = 'Daily';
                    break;
                case SurveyEntity::SUPPLY_ORDERS_WEEKLY :
                    $formDataFromAnswers ['inkTonerOrderRadio'] = 'Weekly';
                    break;
                default :
                    $formDataFromAnswers ['inkTonerOrderRadio'] = 'Times per month';
                    $formDataFromAnswers ['numb_monthlyOrders'] = $assessmentSurvey->numberOfSupplyOrdersPerMonth;
                    break;
            }
        }
        else
        {
            $formDataFromAnswers ['inkTonerOrderRadio'] = 'Daily';
        }

        return $formDataFromAnswers;
    }
}