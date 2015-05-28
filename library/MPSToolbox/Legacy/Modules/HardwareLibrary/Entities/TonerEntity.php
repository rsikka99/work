<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Entities;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use MPSToolbox\Legacy\Entities\UserEntity;

/**
 * Class TonerEntity
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Entities
 *
 * @property int                id
 * @property string             sku
 * @property string             name
 * @property float              cost
 * @property int                yield
 * @property bool               isSystemDevice
 * @property string             imageFile
 * @property string             imageUrl
 *
 * @property int                manufacturerId
 * @property int                tonerColorId
 * @property int                userId
 *
 * @property ManufacturerEntity manufacturer
 * @property TonerColorEntity   tonerColor
 * @property UserEntity         user
 *
 */
class TonerEntity extends EloquentModel
{
    protected $table      = 'toners';
    public    $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manufacturer ()
    {
        return $this->belongsTo('\MPSToolbox\Legacy\Modules\HardwareLibrary\Entities\ManufacturerEntity', 'manufacturerId', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tonerColor ()
    {
        return $this->belongsTo('\MPSToolbox\Legacy\Modules\HardwareLibrary\Entities\TonerColorEntity', 'tonerColorId', 'id');
    }
}