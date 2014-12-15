<?php

namespace MPSToolbox\Settings\Entities;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class GenericSettingsEntity
 *
 * @package MPSToolbox\Settings\Entities
 *
 * @property int   id
 * @property float defaultEnergyCost
 * @property float defaultMonthlyLeasePayment
 * @property float defaultPrinterCost
 *
 * @property float leasedMonochromeCostPerPage
 * @property float leasedColorCostPerPage
 * @property float mpsMonochromeCostPerPage
 * @property float mpsColorCostPerPage
 * @property float targetMonochromeCostPerPage
 * @property float targetColorCostPerPage
 * @property float tonerPricingMargin
 */
class GenericSettingsEntity extends EloquentModel
{
    protected $table      = 'generic_settings';
    public    $timestamps = false;

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getDefaultEnergyCostAttribute ($value)
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
    public function getDefaultMonthlyLeasePaymentAttribute ($value)
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
    public function getDefaultPrinterCostAttribute ($value)
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
    public function getLeasedMonochromeCostPerPageAttribute ($value)
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
    public function getLeasedColorCostPerPageAttribute ($value)
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
    public function getMpsMonochromeCostPerPageAttribute ($value)
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
    public function getMpsColorCostPerPageAttribute ($value)
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
    public function getTargetMonochromeCostPerPageAttribute ($value)
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
    public function getTargetColorCostPerPageAttribute ($value)
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
    public function getTonerPricingMarginAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }


}