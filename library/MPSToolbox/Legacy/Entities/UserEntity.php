<?php

namespace MPSToolbox\Legacy\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class UserEntity
 *
 * @package MPSToolbox\Legacy\Entities
 *
 * @property int            id
 * @property int            dealerId
 * @property string         email
 * @property string         password
 * @property string         firstname
 * @property string         lastname
 * @property string         loginAttempts
 * @property string         locked
 * @property string         resetPasswordOnNextLogin
 *
 * @property Carbon         eulaAccepted
 * @property Carbon         frozenUntil
 * @property Carbon         lastSeen
 * @property Carbon         passwordResetRequest
 *
 * @property DealerEntity   dealer
 * @property ClientEntity[] recentlyViewedClients
 */
class UserEntity extends EloquentModel
{
    /**
     * @var string
     */
    protected $table      = 'users';
    public    $timestamps = false;

    /**
     * @var array
     */
    protected $hidden = array('password');

    /**
     * Additional date fields that should be mutated
     *
     * @var array
     */
    protected $dates = array(
        'eulaAccepted',
        'frozenUntil',
        'lastSeen',
        'passwordResetRequest',
    );

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dealer ()
    {
        return $this->belongsTo('\MPSToolbox\Legacy\Entities\DealerEntity', 'dealerId', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function recentlyViewedClients ()
    {
        return $this->belongsToMany('\MPSToolbox\Legacy\Entities\ClientEntity', 'user_viewed_clients', 'userId', 'clientId')->withPivot('dateViewed')->orderBy('user_viewed_clients.dateViewed', 'desc')->take(5);
    }

    public function getDates ()
    {
        return array('dateViewed');
    }

}