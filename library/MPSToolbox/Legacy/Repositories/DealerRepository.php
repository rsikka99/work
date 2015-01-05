<?php
namespace MPSToolbox\Legacy\Repositories;

use MPSToolbox\Legacy\Entities\DealerEntity;

/**
 * Class DealerRepository
 *
 * @package MPSToolbox\Legacy\Repositories
 */
class DealerRepository
{
    /**
     * Finds a dealer by id
     *
     * @param int $dealerId
     *
     * @return DealerEntity
     */
    public static function find ($dealerId)
    {
        return DealerEntity::find($dealerId);
    }
}