<?php

namespace MPSToolbox\Settings\Entities;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class QuoteSettingsEntity
 *
 * @package MPSToolbox\Settings\Entities
 *
 * @property int   id
 * @property float defaultDeviceMargin
 * @property float defaultPageMargin
 */
class QuoteSettingsEntity extends EloquentModel
{
    protected $table      = 'quote_settings';
    public    $timestamps = false;

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getDefaultDeviceMarginAttribute ($value)
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
    public function getDefaultPageMarginAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }
}