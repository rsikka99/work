<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CountryModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class CountryMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers
 */
class CountryMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\CountryDbTable';

    /**
     * Define the primary key of the model association
     */
    public $col_country_id = 'country_id';
    public $col_name       = 'name';

    /**
     * Gets an instance of the mapper
     *
     * @return CountryMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CountryModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object CountryModel
     *                The object to insert
     * @return int The primary key of the new row
     */
    public function insert ($object)
    {
        // Get an array of data to save
        $data = $object->toArray();

        // Remove the id
        unset($data [$this->col_country_id]);

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        $object->country_id = $id;

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CountryModel to the database.
     *
     * @param $object     CountryModel
     *                    The client model to save to the database
     * @param $primaryKey mixed
     *                    Optional: The original primary key, in case we're changing it
     *
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());

        if ($primaryKey === null)
        {
            $primaryKey = $data [$this->col_country_id];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_country_id}  = ?" => $primaryKey,
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CountryModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof CountryModel)
        {
            $whereClause = [
                "{$this->col_country_id}  = ?" => $object->country_id,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_country_id}  = ?" => $object,
            ];
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a client based on it's primaryKey
     *
     * @param $id int
     *            The id of the client to find
     *
     * @return CountryModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof CountryModel)
        {
            return $result;
        }

        // Assuming we don't have a cached object, lets go get it.
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new CountryModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a client
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return CountryModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new CountryModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all clients
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $count  int
     *                OPTIONAL: An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *                OPTIONAL: An SQL LIMIT offset.
     *
     * @return CountryModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        if ($order == null)
        {
            $order = "{$this->col_name} ASC";
        }

        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new CountryModel($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Searches for a dealer by the dealers name
     *
     * @param string $searchTerm
     *
     * @return \MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CountryModel[]
     */
    public function searchForCountryName ($searchTerm)
    {
        return $this->fetchAll(["{$this->col_name} LIKE ?" => "%{$searchTerm}%"], "{$this->col_name} ASC");
    }

    public function nameToId($name) {
        $row = $this->fetch(["{$this->col_name} = ?" => $name]);
        if ($row) return $row->country_id;
        return null;
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param int $id
     *            the id of the country to find
     *
     * @return array
     */
    public function getWhereId ($id)
    {
        return [
            "{$this->col_country_id}  = ?" => $id,
        ];
    }

    /**
     * @param CountryModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->country_id;
    }
}