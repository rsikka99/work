<?php

namespace MPSToolbox\Legacy\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class ClientEntity
 *
 * @package MPSToolbox\Legacy\Entities
 *
 * @property int               id
 * @property int               dealerId
 * @property string            accountNumber
 * @property string            companyName
 * @property string            legalName
 * @property int               employeeCount
 *
 * @property RmsUploadEntity[] rmsUploads
 */
class ClientEntity extends EloquentModel
{
    /**
     * @var string
     */
    protected $table      = 'clients';
    public    $timestamps = false;

    public function getDates ()
    {
        return array('dateViewed');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rmsUploads ()
    {
        return $this->hasMany('\MPSToolbox\Legacy\Entities\RmsUploadEntity', 'clientId', 'id')->orderBy('uploadDate', 'desc');
    }
}