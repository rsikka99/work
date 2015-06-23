<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceGroupDeviceModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class QuoteDeviceGroupDeviceMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers
 */
class QuoteDeviceGroupDeviceMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column name definitions. Define all columns up here and use them down below.
     */
    public $col_quoteDeviceId      = 'quoteDeviceId';
    public $col_quoteDeviceGroupId = 'quoteDeviceGroupId';

    /*
     * Mapper Definitions
     */
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\QuoteDeviceGroupDeviceDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return QuoteDeviceGroupDeviceMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceGroupDeviceModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object QuoteDeviceGroupDeviceModel
     *                The object to insert
     * @return int The primary key of the new row
     */
    public function insert ($object)
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
     *
     * @param int $quoteId
     * @param int $deviceId
     * @param int $quantity
     *
     * @return mixed
     */
    public function insertDeviceInDefaultGroup ($quoteId, $deviceId, $quantity = 1)
    {
        $deviceGroup                          = new QuoteDeviceGroupDeviceModel();
        $deviceGroup->quoteDeviceId           = $deviceId;
        $deviceGroup->quantity                = $quantity;
        $deviceGroup->monochromePagesQuantity = 0;
        $deviceGroup->colorPagesQuantity      = 0;
        $deviceGroup->quoteDeviceGroupId      = 1;
        $quoteDeviceGroup                     = QuoteDeviceGroupMapper::getInstance()->findDefaultGroupId($quoteId);
        $deviceGroup->quoteDeviceGroupId      = $quoteDeviceGroup->id;

        $data = $deviceGroup->toArray();

        $id = $this->getDbTable()->insert($data);

        // Save the object into the cache
        $this->saveItemToCache($deviceGroup);

        return $id;
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceGroupDeviceModel to the database.
     *
     * @param $object     QuoteDeviceGroupDeviceModel
     *                    The template model to save to the database
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
            $primaryKey [] = $data [$this->col_quoteDeviceGroupId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_quoteDeviceId} = ?"      => $primaryKey [0],
            "{$this->col_quoteDeviceGroupId} = ?" => $primaryKey [1],
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceGroupDeviceModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof QuoteDeviceGroupDeviceModel)
        {
            $whereClause = [
                "{$this->col_quoteDeviceId} = ?"      => $object->quoteDeviceId,
                "{$this->col_quoteDeviceGroupId} = ?" => $object->quoteDeviceGroupId,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_quoteDeviceId} = ?"      => $object [0],
                "{$this->col_quoteDeviceGroupId} = ?" => $object [1],
            ];
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a template based on it's primaryKey
     *
     * @param array $id The id of the template to find
     *
     * @return QuoteDeviceGroupDeviceModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof QuoteDeviceGroupDeviceModel)
        {
            return $result;
        }

        // Assuming we don't have a cached object, lets go get it.
        $result = $this->getDbTable()->find($id [0], $id [1]);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new QuoteDeviceGroupDeviceModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a template
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return QuoteDeviceGroupDeviceModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new QuoteDeviceGroupDeviceModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all templates
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
     * @return QuoteDeviceGroupDeviceModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new QuoteDeviceGroupDeviceModel($row->toArray());

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
            "{$this->col_quoteDeviceId} = ?"      => $id [0],
            "{$this->col_quoteDeviceGroupId} = ?" => $id [1],
        ];
    }

    /**
     * @param QuoteDeviceGroupDeviceModel $object
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return [
            $object->quoteDeviceId,
            $object->quoteDeviceGroupId,
        ];
    }

    /**
     * Gets all devices associated with a quote
     *
     * @param int $quoteDeviceGroupId
     *
     * @return QuoteDeviceGroupDeviceModel[]
     */
    public function fetchDevicesForQuoteDeviceGroup ($quoteDeviceGroupId)
    {
        return $this->fetchAll([
            "{$this->col_quoteDeviceGroupId} = ?" => $quoteDeviceGroupId,
        ]);
    }

    /**
     * Fetches all the quote device group devices associated with a quote device
     *
     * @param $quoteDeviceId int
     *                       The quote device id
     *
     * @return QuoteDeviceGroupDeviceModel[]
     *         The quote devices group devices
     */
    public function fetchDevicesForQuoteDevice ($quoteDeviceId)
    {
        return $this->fetchAll([
            "{$this->col_quoteDeviceId} = ?" => $quoteDeviceId,
        ]);
    }
}

