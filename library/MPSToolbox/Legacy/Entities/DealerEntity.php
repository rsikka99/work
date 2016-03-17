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
 * @property Carbon      dateCreated
 * @property ImageEntity dealerLogoImage
 * @property string      currency
 */
class DealerEntity extends EloquentModel
{
    /**
     * @var string
     */
    protected $table      = 'dealers';
    public $timestamps = false;

    public static function getDealerId() {
        return \Zend_Auth::getInstance()->getIdentity()->dealerId;
    }
    public static function getCurrency($dealerId = null) {
        if (!$dealerId) {
            $result = \Zend_Auth::getInstance()->getIdentity()->currency;
        } else {
            /** @var DealerEntity $dealer */
            $dealer = self::find($dealerId);
            if ($dealer) {
                $result = $dealer->currency;
            }
        }
        if (!empty($result)) return $result;
        return 'USD';
    }

    /**
     * Additional date fields that should be mutated
     *
     * @var array
     */
    protected $dates = [
        'dateCreated',
    ];

}