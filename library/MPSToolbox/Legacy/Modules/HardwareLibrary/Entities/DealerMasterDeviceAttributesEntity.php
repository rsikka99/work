<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Entities;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use MPSToolbox\Legacy\Entities\DealerEntity;

/**
 * Class DealerMasterDeviceAttributesEntity
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Entities
 *
 * @property int                masterDeviceId
 * @property int                dealerId
 * @property float              laborCostPerPage
 * @property float              partsCostPerPage
 * @property float              leaseBuyBackPrice
 *
 * @property MasterDeviceEntity masterDevice
 * @property DealerEntity       dealer
 */
class DealerMasterDeviceAttributesEntity extends EloquentModel
{
    protected $table      = 'dealer_master_device_attributes';
    public    $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function masterDevice ()
    {
        return $this->belongsTo('\MPSToolbox\Legacy\Modules\HardwareLibrary\Entities\MasterDeviceEntity', 'masterDeviceId', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dealer ()
    {
        return $this->belongsTo('\MPSToolbox\Legacy\Entities\DealerEntity', 'masterDeviceId', 'id');
    }
}