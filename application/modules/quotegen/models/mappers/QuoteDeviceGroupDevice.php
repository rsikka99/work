<?php

/**
 * Class Quotegen_Model_Mapper_QuoteDeviceGroupDevice
 */
class Quotegen_Model_Mapper_QuoteDeviceGroupDevice extends My_Model_Mapper_Abstract
{
    /*
     * Column name definitions. Define all columns up here and use them down below.
     */
    public $col_quoteDeviceId = 'quoteDeviceId';
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
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_QuoteDeviceGroupDevice';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_QuoteDeviceGroupDevice
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Quotegen_Model_QuoteDeviceGroupDevice to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Quotegen_Model_QuoteDeviceGroupDevice
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
     *
     * @param int $quoteId
     * @param int $deviceId
     * @param int $quantity
     *
     * @return mixed
     */
    public function insertDeviceInDefaultGroup ($quoteId, $deviceId, $quantity = 1)
    {
        $deviceGroup                          = new Quotegen_Model_QuoteDeviceGroupDevice();
        $deviceGroup->quoteDeviceId           = $deviceId;
        $deviceGroup->quantity                = $quantity;
        $deviceGroup->monochromePagesQuantity = 0;
        $deviceGroup->colorPagesQuantity      = 0;
        $deviceGroup->quoteDeviceGroupId      = 1;
        $quoteDeviceGroup                     = Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->findDefaultGroupId($quoteId);
        $deviceGroup->quoteDeviceGroupId      = $quoteDeviceGroup->id;

        $data = $deviceGroup->toArray();

        $id = $this->getDbTable()->insert($data);

        // Save the object into the cache
        $this->saveItemToCache($deviceGroup);

        return $id;
    }

    /**
     * Saves (updates) an instance of Quotegen_Model_QuoteDeviceGroupDevice to the database.
     *
     * @param $object     Quotegen_Model_QuoteDeviceGroupDevice
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
        $rowsAffected = $this->getDbTable()->update($data, array(
                                                                "{$this->col_quoteDeviceId} = ?"      => $primaryKey [0],
                                                                "{$this->col_quoteDeviceGroupId} = ?" => $primaryKey [1]
                                                           ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Quotegen_Model_QuoteDeviceGroupDevice or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Quotegen_Model_QuoteDeviceGroupDevice)
        {
            $whereClause = array(
                "{$this->col_quoteDeviceId} = ?"      => $object->quoteDeviceId,
                "{$this->col_quoteDeviceGroupId} = ?" => $object->quoteDeviceGroupId
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_quoteDeviceId} = ?"      => $object [0],
                "{$this->col_quoteDeviceGroupId} = ?" => $object [1]
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a template based on it's primaryKey
     *
     * @param array $id The id of the template to find
     *
     * @return Quotegen_Model_QuoteDeviceGroupDevice
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Quotegen_Model_QuoteDeviceGroupDevice)
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
        $object = new Quotegen_Model_QuoteDeviceGroupDevice($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a template
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: A SQL OFFSET value.
     *
     * @return Quotegen_Model_QuoteDeviceGroupDevice
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Quotegen_Model_QuoteDeviceGroupDevice($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all templates
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
     * @return Quotegen_Model_QuoteDeviceGroupDevice[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Quotegen_Model_QuoteDeviceGroupDevice($row->toArray());

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
        return array(
            "{$this->col_quoteDeviceId} = ?"      => $id [0],
            "{$this->col_quoteDeviceGroupId} = ?" => $id [1]
        );
    }

    /**
     * @param Quotegen_Model_QuoteDeviceGroupDevice $object
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array(
            $object->quoteDeviceId,
            $object->quoteDeviceGroupId
        );
    }

    /**
     * Gets all devices associated with a quote
     *
     * @param int $quoteDeviceGroupId
     *
     * @return \Quotegen_Model_QuoteDeviceGroupDevice[]
     */
    public function fetchDevicesForQuoteDeviceGroup ($quoteDeviceGroupId)
    {
        return $this->fetchAll(array(
                                    "{$this->col_quoteDeviceGroupId} = ?" => $quoteDeviceGroupId
                               ));
    }

    /**
     * Fetches all the quote device group devices associated with a quote device
     *
     * @param $quoteDeviceId int
     *                       The quote device id
     *
     * @return Quotegen_Model_QuoteDeviceGroupDevice[]
     *         The quote devices group devices
     */
    public function fetchDevicesForQuoteDevice ($quoteDeviceId)
    {
        return $this->fetchAll(array(
                                    "{$this->col_quoteDeviceId} = ?" => $quoteDeviceId
                               ));
    }
}

