<?php

namespace MPSToolbox\Settings\Entities;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class ShopSettingsEntity
 *
 * @package MPSToolbox\Settings\Entities
 *
 * @property int   id
 * @property string shopifyName
 * @property float hardwareMargin
 * @property float oemTonerMargin
 * @property float compatibleTonerMargin
 * @property string rmsUri
 * @property string emailFromName
 * @property string emailFromAddress
 * @property string supplyNotifySubject
 * @property string supplyNotifyMessage
 */
class ShopSettingsEntity extends EloquentModel
{
    protected $table      = 'shop_settings';
    public    $timestamps = false;

}