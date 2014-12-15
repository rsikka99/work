<?php

namespace MPSToolbox\Legacy\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class SurveyEntity
 *
 * @package MPSToolbox\Legacy\Entities
 *
 * @property int    clientId
 * @property float  costOfInkAndToner
 * @property float  costOfLabor
 * @property float  costToExecuteSuppliesOrder
 * @property float  averageItHourlyRate
 * @property float  numberOfSupplyOrdersPerMonth
 * @property float  hoursSpentOnIt
 * @property float  averageMonthlyBreakdowns
 * @property float  pageCoverageMonochrome
 * @property float  pageCoverageColor
 * @property float  percentageOfInkjetPrintVolume
 * @property float  averageRepairTime
 * @property Carbon updated_at
 * @property Carbon created_at
 * @property Carbon surveyed_at
 */
class SurveyEntity extends EloquentModel
{
    const SUPPLY_ORDERS_DAILY         = 22;
    const SUPPLY_ORDERS_WEEKLY        = 4;
    const DEFAULT_SUPPLIES_ORDER_COST = 50.00;
    const DEFAULT_IT_HOURLY_RATE      = 40.00;

    /**
     * @var string
     */
    protected $primaryKey = 'clientId';

    /**
     * @var string
     */
    protected $table = 'surveys';

    /**
     * Additional date fields that should be mutated
     *
     * @var array
     */
    protected $dates = array('surveyed_at');

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getCostOfInkAndTonerAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getCostOfLaborAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getCostToExecuteSuppliesOrderAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getAverageItHourlyRateAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getNumberOfSupplyOrdersPerMonthAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getHoursSpentOnItAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getAverageMonthlyBreakdownsAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getPageCoverageMonochromeAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }
    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getPageCoverageColorAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getPercentageOfInkjetPrintVolumeAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getAverageRepairTimeAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }

}