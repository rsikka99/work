<?php

namespace MPSToolbox\Legacy\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class CountryEntity
 *
 * @package MPSToolbox\Legacy\Entities
 *
 * @property int    country_id
 * @property string name
 * @property string iso_alpha2
 * @property string iso_alpha3
 * @property string iso_numeric
 * @property string currency_code
 * @property string currency_name
 * @property string currency_symbol
 * @property string flag
 */
class CountryEntity extends EloquentModel
{
    protected $primaryKey = 'country_id';
    /**
     * @var string
     */
    protected $table      = 'countries';
    public    $timestamps = false;
}