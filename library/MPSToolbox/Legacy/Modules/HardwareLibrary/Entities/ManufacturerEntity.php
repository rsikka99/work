<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Entities;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class ManufacturerEntity
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Entities
 *
 * @property int    id
 * @property string fullname
 * @property string displayname
 * @property bool   isDeleted
 */
class ManufacturerEntity extends EloquentModel
{
    protected $table      = 'manufacturers';
    public    $timestamps = false;
}