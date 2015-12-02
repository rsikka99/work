<?php

namespace MPSToolbox\Settings\Entities;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use MPSToolbox\Entities\DealerEntity;
use MPSToolbox\Settings\Service\DealerSettingsService;

/**
 * Class DealerSettingsEntity
 *
 * @package MPSToolbox\Settings\Entities
 *
 * @property int                        dealerId
 * @property int                        currentFleetSettingsId
 * @property int                        proposedFleetSettingsId
 * @property int                        genericSettingsId
 * @property int                        quoteSettingsId
 * @property int                        optimizationSettingsId
 * @property int                        shopSettingsId
 * @property int                        quoteValid
 * @property string                     quoteCustom
 *
 * @property FleetSettingsEntity        currentFleetSettings
 * @property FleetSettingsEntity        proposedFleetSettings
 * @property GenericSettingsEntity      genericSettings
 * @property OptimizationSettingsEntity optimizationSettings
 * @property QuoteSettingsEntity        quoteSettings
 * @property ShopSettingsEntity         shopSettings
 */
class DealerSettingsEntity extends EloquentModel
{
    protected $primaryKey = 'dealerId';
    protected $table      = 'dealer_settings';
    public    $timestamps = false;

    /**
     * @param null $dealerId
     * @return DealerSettingsEntity
     */
    public static function getDealerSettings($dealerId=null) {
        $service = new DealerSettingsService();
        return $service->getDealerSettings($dealerId);
    }

    public static function hasShopify() {
        $dealerSettings = self::getDealerSettings();
        if (!$dealerSettings) return false;
        $shopSettings = $dealerSettings->shopSettings;
        return $shopSettings->shopifyName!='';
    }

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
    public function shopSettings ()
    {
        return $this->belongsTo('\MPSToolbox\Settings\Entities\ShopSettingsEntity', 'shopSettingsId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function optimizationSettings ()
    {
        return $this->belongsTo('\MPSToolbox\Settings\Entities\OptimizationSettingsEntity', 'optimizationSettingsId');
    }
}