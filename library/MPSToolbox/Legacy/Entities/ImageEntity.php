<?php

namespace MPSToolbox\Legacy\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class DealerEntity
 *
 * @package MPSToolbox\Legacy\Entities
 *
 * @property int         id
 * @property string      image
 * @property string      filename
 */
class ImageEntity extends EloquentModel
{
    /**
     * @var string
     */
    protected $table      = 'images';
    protected $timestamps = false;
}