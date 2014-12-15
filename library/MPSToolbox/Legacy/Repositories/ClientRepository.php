<?php
namespace MPSToolbox\Legacy\Repositories;

use MPSToolbox\Legacy\Entities\ClientEntity;

/**
 * Class ClientRepository
 *
 * @package MPSToolbox\Legacy\Repositories
 */
class ClientRepository
{

    /**
     * Counts the number of clients that belong to a dealer
     *
     * @param int $dealerId
     *
     * @return int
     */
    public static function countForDealer ($dealerId)
    {
        return ClientEntity::where('dealerId', '=', $dealerId)->count();
    }

    /**
     * Finds a client by id
     *
     * @param int $clientId
     *
     * @return ClientEntity
     */
    public static function find ($clientId)
    {
        return ClientEntity::find($clientId);
    }

    /**
     * Returns a query that is ordered by the last time a user saw the client
     *
     * @param int $dealerId
     * @param int $userId
     *
     * @return ClientEntity
     */
    public static function getQueryByLastSeen ($dealerId, $userId)
    {
        return ClientEntity::leftJoin('user_viewed_clients AS uvc', function ($join) use ($userId)
        {
            $join->on('uvc.clientId', '=', 'clients.id')
                 ->where('uvc.userId', '=', $userId);
        })
                           ->select(['clients.*', 'uvc.dateViewed'])
                           ->where('clients.dealerId', '=', $dealerId)
                           ->orderBy('uvc.dateViewed', 'DESC')
                           ->orderBy('clients.companyName');
    }
}