<?php

namespace MPSToolbox\Settings\Entities;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class UserSettingsEntity
 *
 * @package MPSToolbox\Settings\Entities
 *
 * @property int                        userId
 * @property int                        currentFleetSettingsId
 * @property int                        proposedFleetSettingsId
 * @property int                        genericSettingsId
 * @property int                        quoteSettingsId
 * @property int                        optimizationSettingsId
 *
 * @property FleetSettingsEntity        currentFleetSettings
 * @property FleetSettingsEntity        proposedFleetSettings
 * @property GenericSettingsEntity      genericSettings
 * @property OptimizationSettingsEntity optimizationSettings
 * @property QuoteSettingsEntity        quoteSettings
 */
class UserSettingsEntity extends EloquentModel
{
    protected $primaryKey = 'userId';
    protected $table      = 'user_settings';
    public    $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentFleetSettings ()
    {
        return $this->belongsTo('\MPSToolbox\Settings\Entities\FleetSettingsEntity', 'currentFleetSettingsId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proposedFleetSettings ()
    {
        return $this->belongsTo('\MPSToolbox\Settings\Entities\FleetSettingsEntity', 'proposedFleetSettingsId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function genericSettings ()
    {
        return $this->belongsTo('\MPSToolbox\Settings\Entities\GenericSettingsEntity', 'genericSettingsId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quoteSettings ()
    {
        return $this->belongsTo('\MPSToolbox\Settings\Entities\QuoteSettingsEntity', 'quoteSettingsId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function optimizationSettings ()
    {
        return $this->belongsTo('\MPSToolbox\Settings\Entities\OptimizationSettingsEntity', 'optimizationSettingsId');
    }
}