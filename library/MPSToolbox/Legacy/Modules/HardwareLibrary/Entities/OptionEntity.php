<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Entities;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use MPSToolbox\Legacy\Entities\DealerEntity;

/**
 * Class OptionEntity
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Entities
 *
 * @property int          id
 * @property int          cost
 * @property string       dealerSku
 * @property string       description
 * @property string       name
 * @property string       oemSku
 *
 * @property int          dealerId
 *
 * @property DealerEntity dealer
 *
 */
class OptionEntity extends EloquentModel
{
    protected $table      = 'options';
    public    $timestamps = false;

    protected $fillable = ['id', 'cost', 'dealerId', 'dealerSku', 'description', 'name', 'oemSku'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dealer ()
    {
        return $this->belongsTo('\MPSToolbox\Legacy\Entities\DealerEntity', 'dealerId', 'id');
    }

    /**
     * @param Builder $query
     * @param int     $id
     * @param int     $dealerId
     *
     * @return Builder
     */
    public function scopeOptionForDealer ($query, $id, $dealerId)
    {
        return $query->whereId($id)->where('dealerId', '=', $dealerId);
    }
}