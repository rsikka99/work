<?php

/**
 * Class Quotegen_Model_Mapper_QuoteDeviceGroup
 */
class Quotegen_Model_Mapper_QuoteDeviceGroup extends My_Model_Mapper_Abstract
{
    /*
     * Column name definitions. Define all columns up here and use them down below.
     */
    public $col_id = 'id';
    public $col_quoteId = 'quoteId';
    public $col_default = 'isDefault';

    /*
     * Mapper Definitions
     */
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_QuoteDeviceGroup';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_QuoteDeviceGroup
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Quotegen_Model_QuoteDeviceGroup to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Quotegen_Model_QuoteDeviceGroup
     *                The object to insert
     *
     * @return int The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $object->toArray();

        // Remove the id
        unset($data [$this->col_id]);

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        $object->id = $id;

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Inserts the default row for devices when a quote is created
     *
     * @param int $quoteId
     *
     * @return int of the id that has been created
     */
    public function insertDefaultGroupByQuoteId ($quoteId)
    {
        $deviceGroup            = new Quotegen_Model_QuoteDeviceGroup();
        $deviceGroup->quoteId   = $quoteId;
        $deviceGroup->isDefault = 1;
        $deviceGroup->name      = 'Default Group (Ungrouped)';

        $data = $deviceGroup->toArray();

        $id = $this->getDbTable()->insert($data);

        $deviceGroup->id = $id;

        // Save the object into the cache
        $this->saveItemToCache($deviceGroup);

        return $id;
    }

    /**
     * Gets the default row / quote Id
     *
     * @param int $quoteId
     *
     * @return Quotegen_Model_QuoteDeviceGroup
     */
    public function findDefaultGroupId ($quoteId)
    {
        return $this->fetch(array(
                                 "{$this->col_quoteId} = ?" => $quoteId,
                                 "{$this->col_default} = ?" => 1
                            ));
    }

    /**
     * Saves (updates) an instance of Quotegen_Model_QuoteDeviceGroup to the database.
     *
     * @param $object     Quotegen_Model_QuoteDeviceGroup
     *                    The QuoteDeviceGroup model to save to the database
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
            $primaryKey = $data [$this->col_id];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
                                                                "{$this->col_id} = ?" => $primaryKey
                                                           ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Quotegen_Model_QuoteDeviceGroup or the
     *                primary key to delete
     *
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Quotegen_Model_QuoteDeviceGroup)
        {
            $whereClause = array(
                "{$this->col_id} = ?" => $object->id
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_id} = ?" => $object
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a QuoteDeviceGroup based on it's primaryKey
     *
     * @param $id int
     *            The id of the QuoteDeviceGroup to find
     *
     * @return Quotegen_Model_QuoteDeviceGroup
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Quotegen_Model_QuoteDeviceGroup)
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
        $object = new Quotegen_Model_QuoteDeviceGroup($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a QuoteDeviceGroup
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: A SQL OFFSET value.
     *
     * @return Quotegen_Model_QuoteDeviceGroup
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Quotegen_Model_QuoteDeviceGroup($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all QuoteDeviceGroups
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $count  int
     *                OPTIONAL: A SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *                OPTIONAL: A SQL LIMIT offset.
     *
     * @return Quotegen_Model_QuoteDeviceGroup[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Quotegen_Model_QuoteDeviceGroup($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param $id int
     *
     * @return array
     */
    public function getWhereId ($id)
    {
        return array(
            "{$this->col_id} = ?" => $id
        );
    }

    /**
     * @param Quotegen_Model_QuoteDeviceGroup $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Gets all the device groups for a quote
     *
     * @param $quoteId int
     *                 The quote id
     *
     * @return Quotegen_Model_QuoteDeviceGroup
     */
    public function fetchDeviceGroupsForQuote ($quoteId)
    {
        return $this->fetchAll(array(
                                    "{$this->col_quoteId} = ?" => $quoteId
                               ));
    }
}

