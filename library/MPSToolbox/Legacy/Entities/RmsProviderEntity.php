<?php

namespace MPSToolbox\Legacy\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class RmsProviderEntity
 *
 * @package MPSToolbox\Legacy\Entities
 *
 * @property int    id
 * @property string name
 */
class RmsProviderEntity extends EloquentModel
{
    protected $table      = 'rms_providers';
    public    $timestamps = false;
}