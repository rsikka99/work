<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceConfigurationOptionModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class QuoteDeviceConfigurationOptionMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers
 */
class QuoteDeviceConfigurationOptionMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column name definitions. Define all columns up here and use them down below.
     */
    public $col_quoteDeviceOptionId = 'quoteDeviceOptionId';
    public $col_optionId            = 'optionId';

    /*
     * Mapper Definitions
     */
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\QuoteDeviceConfigurationOptionDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return QuoteDeviceConfigurationOptionMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceConfigurationOptionModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object QuoteDeviceConfigurationOptionModel
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
     * @param QuoteDeviceConfigurationOptionModel $object
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return [
            $object->quoteDeviceOptionId,
            $object->optionId,
        ];
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceConfigurationOptionModel to the database.
     *
     * @param $object     QuoteDeviceConfigurationOptionModel
     *                    The quoteDeviceConfigurationOption model to save to the database
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
            $primaryKey = $this->col_quoteDeviceOptionId;
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_quoteDeviceOptionId} = ?" => $primaryKey,
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceConfigurationOptionModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof QuoteDeviceConfigurationOptionModel)
        {
            $whereClause = [
                "{$this->col_quoteDeviceOptionId} = ?" => $object->quoteDeviceOptionId,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_quoteDeviceOptionId} = ?" => $object,
            ];
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a quoteDeviceConfigurationOption based on it's primaryKey
     *
     * @param $id int
     *            The id of the quoteDeviceConfigurationOption to find
     *
     * @return QuoteDeviceConfigurationOptionModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof QuoteDeviceConfigurationOptionModel)
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
        $object = new QuoteDeviceConfigurationOptionModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a quoteDeviceConfigurationOption
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return QuoteDeviceConfigurationOptionModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new QuoteDeviceConfigurationOptionModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all quoteDeviceConfigurationOptions
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
     * @return QuoteDeviceConfigurationOptionModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new QuoteDeviceConfigurationOptionModel($row->toArray());

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
            "{$this->col_quoteDeviceOptionId} = ?" => $id,
        ];
    }

    /**
     * Finds a quote device configuration option by option id
     *
     * @param int $quoteDeviceOptionId
     *
     * @return QuoteDeviceConfigurationOptionModel
     */
    public function findByQuoteDeviceOptionId ($quoteDeviceOptionId)
    {
        return $this->fetch([
            "{$this->col_quoteDeviceOptionId} = ?" => $quoteDeviceOptionId,
        ]);
    }
}


