<?php

namespace MPSToolbox\Legacy\Modules\Assessment\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class AssessmentSurveyModel
 *
 * @package MPSToolbox\Legacy\Modules\Assessment\Models
 */
class AssessmentSurveyModel extends My_Model_Abstract
{
    const SUPPLY_ORDERS_DAILY         = 22;
    const SUPPLY_ORDERS_WEEKLY        = 4;
    const DEFAULT_SUPPLIES_ORDER_COST = 50.00;
    const DEFAULT_IT_HOURLY_RATE      = 40.00;

    /**
     * @var int
     */
    public $reportId;

    /**
     * @var int
     */
    public $costOfInkAndToner;

    /**
     * @var int
     */
    public $costOfLabor;

    /**
     * @var int
     */
    public $costToExecuteSuppliesOrder;

    /**
     * @var int
     */
    public $averageItHourlyRate;

    /**
     * @var int
     */
    public $numberOfSupplyOrdersPerMonth;

    /**
     * @var int
     */
    public $hoursSpentOnIt;

    /**
     * @var int
     */
    public $averageMonthlyBreakdowns;

    /**
     * @var int
     */
    public $pageCoverageMonochrome;

    /**
     * @var int
     */
    public $pageCoverageColor;

    /**
     * @var int
     */
    public $percentageOfInkjetPrintVolume;

    /**
     * @var int
     */
    public $averageRepairTime;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->reportId) && !is_null($params->reportId))
        {
            $this->reportId = $params->reportId;
        }

        if (isset($params->costOfInkAndToner) && !is_null($params->costOfInkAndToner))
        {
            $this->costOfInkAndToner = $params->costOfInkAndToner;
        }

        if (isset($params->costOfLabor) && !is_null($params->costOfLabor))
        {
            $this->costOfLabor = $params->costOfLabor;
        }

        if (isset($params->costToExecuteSuppliesOrder) && !is_null($params->costToExecuteSuppliesOrder))
        {
            $this->costToExecuteSuppliesOrder = $params->costToExecuteSuppliesOrder;
        }

        if (isset($params->averageItHourlyRate) && !is_null($params->averageItHourlyRate))
        {
            $this->averageItHourlyRate = $params->averageItHourlyRate;
        }

        if (isset($params->numberOfSupplyOrdersPerMonth) && !is_null($params->numberOfSupplyOrdersPerMonth))
        {
            $this->numberOfSupplyOrdersPerMonth = $params->numberOfSupplyOrdersPerMonth;
        }

        if (isset($params->hoursSpentOnIt) && !is_null($params->hoursSpentOnIt))
        {
            $this->hoursSpentOnIt = $params->hoursSpentOnIt;
        }

        if (isset($params->averageMonthlyBreakdowns) && !is_null($params->averageMonthlyBreakdowns))
        {
            $this->averageMonthlyBreakdowns = $params->averageMonthlyBreakdowns;
        }

        if (isset($params->pageCoverageMonochrome) && !is_null($params->pageCoverageMonochrome))
        {
            $this->pageCoverageMonochrome = $params->pageCoverageMonochrome;
        }

        if (isset($params->pageCoverageColor) && !is_null($params->pageCoverageColor))
        {
            $this->pageCoverageColor = $params->pageCoverageColor;
        }

        if (isset($params->percentageOfInkjetPrintVolume) && !is_null($params->percentageOfInkjetPrintVolume))
        {
            $this->percentageOfInkjetPrintVolume = $params->percentageOfInkjetPrintVolume;
        }

        if (isset($params->averageRepairTime) && !is_null($params->averageRepairTime))
        {
            $this->averageRepairTime = $params->averageRepairTime;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "reportId"                      => $this->reportId,
            "costOfInkAndToner"             => $this->costOfInkAndToner,
            "costOfLabor"                   => $this->costOfLabor,
            "costToExecuteSuppliesOrder"    => $this->costToExecuteSuppliesOrder,
            "averageItHourlyRate"           => $this->averageItHourlyRate,
            "numberOfSupplyOrdersPerMonth"  => $this->numberOfSupplyOrdersPerMonth,
            "hoursSpentOnIt"                => $this->hoursSpentOnIt,
            "averageMonthlyBreakdowns"      => $this->averageMonthlyBreakdowns,
            "pageCoverageMonochrome"        => $this->pageCoverageMonochrome,
            "pageCoverageColor"             => $this->pageCoverageColor,
            "percentageOfInkjetPrintVolume" => $this->percentageOfInkjetPrintVolume,
            "averageRepairTime"             => $this->averageRepairTime,
        ];
    }
}