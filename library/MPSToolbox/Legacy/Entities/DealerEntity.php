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
 * @property string      dealerName
 * @property int         userLicenses
 * @property int         dealerLogoImageId
 *
 * @property Carbon      dateCreated
 *
 * @property ImageEntity dealerLogoImage
 */
class DealerEntity extends EloquentModel
{
    /**
     * @var string
     */
    protected $table      = 'dealers';
    public $timestamps = false;

    /**
     * Additional date fields that should be mutated
     *
     * @var array
     */
    protected $dates = array(
        'dateCreated',
    );

}