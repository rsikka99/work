<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use MPSToolbox\Legacy\Entities\UserEntity;

/**
 * Class MasterDeviceEntity
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Entities
 *
 * @property int                                id
 * @property string                             modelName
 * @property bool                               isA3
 * @property bool                               isCapableOfReportingTonerLevels
 * @property bool                               isCopier
 * @property bool                               isDuplex
 * @property bool                               isFax
 * @property bool                               isLeased
 * @property bool                               isReplacementDevice
 * @property bool                               isSystemDevice
 * @property int                                leasedTonerYield
 * @property int                                maximumRecommendedMonthlyPageVolume
 * @property int                                ppmBlack
 * @property int                                ppmColor
 * @property int                                wattsPowerNormal
 * @property int                                wattsPowerIdle
 *
 * @property int                                manufacturerId
 * @property int                                tonerConfigId
 * @property int                                userId
 *
 * @property Carbon                             dateCreated
 * @property Carbon                             launchDate
 *
 * @property ManufacturerEntity                 manufacturer
 * @property DealerMasterDeviceAttributesEntity dealerMasterDeviceAttributes
 * @property TonerConfigurationEntity           tonerConfiguration
 * @property TonerEntity[]                      toners
 * @property UserEntity                         user
 *
 */
class MasterDeviceEntity extends EloquentModel
{
    protected $table      = 'master_devices';
    public    $timestamps = false;
    protected $dates      = [
        'dateCreated',
        'launchDate',
    ];

    protected $fillable = [
        'id',
        'modelName',
        'isA3',
        'isCapableOfReportingTonerLevels',
        'isCopier',
        'isDuplex',
        'isFax',
        'isLeased',
        'isReplacementDevice',
        'isSystemDevice',
        'leasedTonerYield',
        'maximumRecommendedMonthlyPageVolume',
        'ppmBlack',
        'ppmColor',
        'wattsPowerNormal',
        'wattsPowerIdle',
        'manufacturerId',
        'tonerConfigId',
        'userId',
        'dateCreated',
        'launchDate',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function dealerMasterDeviceAttributes ()
    {
        return $this->hasOne('\MPSToolbox\Legacy\Modules\HardwareLibrary\Entities\DealerMasterDeviceAttributesEntity', 'masterDeviceId', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function toners ()
    {
        return $this->belongsToMany('\MPSToolbox\Legacy\Modules\HardwareLibrary\Entities\TonerEntity', 'device_toners', 'master_device_id', 'toner_id')->withPivot(array('userId', 'isSystemDevice'));
    }

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
    public function tonerConfiguration ()
    {
        return $this->belongsTo('\MPSToolbox\Legacy\Modules\HardwareLibrary\Entities\TonerConfigurationEntity', 'tonerConfigId', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user ()
    {
        return $this->belongsTo('\MPSToolbox\Legacy\Entities\UserEntity', 'userId', 'id');
    }
}