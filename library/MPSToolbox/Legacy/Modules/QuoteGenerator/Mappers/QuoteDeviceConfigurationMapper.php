<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceConfigurationModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class QuoteDeviceConfigurationMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers
 */
class QuoteDeviceConfigurationMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column name definitions. Define all columns up here and use them down below.
     */
    public $col_quoteDeviceId  = 'quoteDeviceId';
    public $col_masterDeviceId = 'masterDeviceId';

    /*
     * Mapper Definitions
     */

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\QuoteDeviceConfigurationDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return QuoteDeviceConfigurationMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Counts and returns the amount of rows by deviceConfigurationId
     *
     * @param int $deviceConfigurationId
     *
     * @return number The amount of rows in the database.
     */
    public function countByDeviceId ($deviceConfigurationId)
    {
        return $this->count([
            'deviceConfigurationId = ?' => $deviceConfigurationId,
        ]);
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceConfigurationModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object QuoteDeviceConfigurationModel
     *                The object to insert
     *
     * @return int The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $object->toArray();

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceConfigurationModel to the database.
     *
     * @param $object     QuoteDeviceConfigurationModel
     *                    The quoteDeviceConfiguration model to save to the database
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
            $primaryKey [] = $data [$this->col_quoteDeviceId];
            $primaryKey [] = $data [$this->col_masterDeviceId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_quoteDeviceId} = ?"  => $primaryKey [0],
            "{$this->col_masterDeviceId} = ?" => $primaryKey [1],
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceConfigurationModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof QuoteDeviceConfigurationModel)
        {
            $whereClause = [
                "{$this->col_quoteDeviceId} = ?"  => $object->quoteDeviceId,
                "{$this->col_masterDeviceId} = ?" => $object->masterDeviceId,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_quoteDeviceId} = ?"  => $object [0],
                "{$this->col_masterDeviceId} = ?" => $object [1],
            ];
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Delete a quoteDeviceConfiguration by deviceConfigurationId
     *
     * @param int $deviceConfigurationId
     *
     * @return int The number of rows affected
     */
    public function deleteQuoteDeviceConfigurationById ($deviceConfigurationId)
    {
        return $this->getDbTable()->delete([
            'deviceConfigurationId = ?' => $deviceConfigurationId,
        ]);
    }

    /**
     * Finds a quoteDeviceConfiguration based on it's primaryKey
     *
     * @param $id int
     *            The id of the quoteDeviceConfiguration to find
     *
     * @return QuoteDeviceConfigurationModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof QuoteDeviceConfigurationModel)
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
        $object = new QuoteDeviceConfigurationModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Finds a quoteDeviceConfiguration by device id
     *
     * @param $id int
     *            The id of the device configuration to find
     *
     * @return QuoteDeviceConfigurationModel
     */
    public function findByDeviceId ($id)
    {
        return $this->fetch([
            "{$this->col_masterDeviceId} = ?" => $id,
        ]);
    }

    /**
     * Finds a quoteDeviceConfiguration by quote device id
     *
     * @param $id int
     *            The id of the quote device to find
     *
     * @return QuoteDeviceConfigurationModel
     */
    public function findByQuoteDeviceId ($id)
    {
        return $this->fetch([
            "{$this->col_quoteDeviceId} = ?" => $id,
        ]);
    }

    /**
     * Fetches a quoteDeviceConfiguration
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return QuoteDeviceConfigurationModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new QuoteDeviceConfigurationModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all quoteDeviceConfigurations
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
     * @return QuoteDeviceConfigurationModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new QuoteDeviceConfigurationModel($row->toArray());

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
            "{$this->col_quoteDeviceId} = ?"  => $id [0],
            "{$this->col_masterDeviceId} = ?" => $id [1],
        ];
    }

    /**
     * @param QuoteDeviceConfigurationModel $object
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return [
            $object->quoteDeviceId,
            $object->masterDeviceId,
        ];
    }
}

