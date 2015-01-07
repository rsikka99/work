<?php
namespace MPSToolbox\Legacy\Repositories;

use MPSToolbox\Legacy\Entities\CountryEntity;

/**
 * Class CountryRepository
 *
 * @package MPSToolbox\Legacy\Repositories
 */
class CountryRepository
{
    /**
     * Finds a country by id
     *
     * @param int $countryId
     *
     * @return CountryEntity
     */
    public static function find ($countryId)
    {
        return CountryEntity::find($countryId);
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public static function getQuery ()
    {
        return CountryEntity::orderBy('name');
    }
}