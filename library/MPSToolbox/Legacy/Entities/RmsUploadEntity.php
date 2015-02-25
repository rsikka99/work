<?php

namespace MPSToolbox\Legacy\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class RmsUploadEntity
 *
 * @package MPSToolbox\Legacy\Entities
 *
 * @property int               id
 * @property string            fileName
 * @property int               validRowCount
 * @property int               invalidRowCount
 * @property Carbon            uploadDate
 * @property int               clientId
 * @property int               rmsProviderId
 *
 * @property RmsProviderEntity rmsProvider
 */
class RmsUploadEntity extends EloquentModel
{
    /**
     * @var string
     */
    protected $table      = 'rms_uploads';
    public    $timestamps = false;

    public function getDates ()
    {
        return ['uploadDate'];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rmsProvider ()
    {
        return $this->belongsTo('\MPSToolbox\Legacy\Entities\RmsProviderEntity', 'rmsProviderId', 'id');
    }
}