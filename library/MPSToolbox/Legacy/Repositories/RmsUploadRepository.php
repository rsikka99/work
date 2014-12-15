<?php
namespace MPSToolbox\Legacy\Repositories;

use MPSToolbox\Legacy\Entities\RmsUploadEntity;

/**
 * Class RmsUploadRepository
 *
 * @package MPSToolbox\Legacy\Repositories
 */
class RmsUploadRepository
{

    /**
     * Counts the number of clients that belong to a dealer
     *
     * @param int $clientId
     *
     * @return int
     */
    public static function countForClient ($clientId)
    {
        return RmsUploadEntity::where('clientId', '=', $clientId)->count();
    }

    /**
     * Finds an RMS Upload by id
     *
     * @param int $rmsUploadId
     *
     * @return RmsUploadEntity
     */
    public static function find ($rmsUploadId)
    {
        return RmsUploadEntity::find($rmsUploadId);
    }
}