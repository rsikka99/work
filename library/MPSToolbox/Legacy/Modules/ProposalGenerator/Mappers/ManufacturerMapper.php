<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\ManufacturerModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class ManufacturerMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class ManufacturerMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_id          = 'id';
    public $col_displayName = 'displayname';
    public $col_fullName    = 'fullname';
    public $col_isDeleted   = 'isDeleted';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\ManufacturerDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return ManufacturerMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\ManufacturerModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object ManufacturerModel
     *                The object to insert
     * @return int The primary key of the new row
     */
    public function insert ($object)
    {
        // Get an array of data to save
        $data = $object->toArray();

        // Remove the id
        unset($data ["{$this->col_id}"]);

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        $object->id = $id;

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\ManufacturerModel to the database.
     *
     * @param $object     ManufacturerModel
     *                    The manufacturer model to save to the database
     * @param $primaryKey mixed
     *                    Optional: The original primary key, in case we're changing it
     *
     * @return string The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());

        if ($primaryKey === null)
        {
            $primaryKey = $data ["{$this->col_id}"];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_id} = ?" => $primaryKey,
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\ManufacturerModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof ManufacturerModel)
        {
            $whereClause = [
                "{$this->col_id} = ?" => $object->id,
            ];
        }
        else
        {
            $whereClause = [
                'id = ?' => $object,
            ];
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a manufacturer based on it's primaryKey
     *
     * @param $id int
     *            The id of the manufacturer to find
     *
     * @return ManufacturerModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof ManufacturerModel)
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
        $object = new ManufacturerModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Checks to see if a manufacturer id exists
     *
     * @param int $id The id of the manufacturer
     *
     * @return bool
     */
    public function exists ($id)
    {
        return ($this->find($id) instanceof ManufacturerModel);
    }

    /**
     * Fetches a manufacturer
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return ManufacturerModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new ManufacturerModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all manufacturers
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $count  int
     *                OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *                OPTIONAL An SQL LIMIT offset.
     *
     * @return ManufacturerModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = null, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new ManufacturerModel($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param int $id
     *
     * @return array
     */
    public function getWhereId ($id)
    {
        return [
            "{$this->col_id} = ?" => $id
        ];
    }

    /**
     * Gets all the manufacturers that are not "deleted"
     *
     * @return ManufacturerModel[]
     */
    public function fetchAllAvailableManufacturers ()
    {
        return $this->fetchAll([
            "{$this->col_isDeleted} = 0",
        ], [
            "{$this->col_fullName} ASC",
        ]);
    }

    /**
     * @param ManufacturerModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Searches the manufacturer by full name (uses LIKE %NAME% in where clause)
     *
     * @param string $manufacturerName
     *
     * @return ManufacturerModel[]
     */
    public function searchByName ($manufacturerName)
    {
        $results = $this->fetchAll([
            "{$this->col_fullName} LIKE ?" => "%{$manufacturerName}%",
        ]);

        if (count($results) < 1)
        {
            $results = $this->fetchAll([
                "{$this->col_displayName} LIKE ?" => "%{$manufacturerName}%",
            ]);
        }

        return $results;
    }
}