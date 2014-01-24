<?php

/**
 * Class Quotegen_Model_Mapper_DeviceConfiguration
 */
class Quotegen_Model_Mapper_DeviceConfiguration extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_DeviceConfiguration';

    /*
     * Define the primary key of the model association
     */
    public $col_id = 'id';
    public $col_dealerId = 'dealerId';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_DeviceConfiguration
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Counts and returns the amount of rows by masterDeviceId
     *
     * @param int $masterDeviceId
     *
     * @return int The amount of rows in the database.
     */
    public function countByDeviceId ($masterDeviceId)
    {
        return $this->count(array(
            'masterDeviceId = ?' => $masterDeviceId
        ));
    }

    /**
     * Saves an instance of Quotegen_Model_DeviceConfiguration to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Quotegen_Model_DeviceConfiguration
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
     * Saves (updates) an instance of Quotegen_Model_DeviceConfiguration to the database.
     *
     * @param $object     Quotegen_Model_DeviceConfiguration
     *                    The deviceConfiguration model to save to the database
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
     *                This can either be an instance of Quotegen_Model_DeviceConfiguration or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Quotegen_Model_DeviceConfiguration)
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
     * Deletes device configuration and all device configuration options by device id;
     *
     * @param int $deviceId
     *
     * @return int The amount of rows affected.
     */
    public function deleteConfigurationByDeviceId ($deviceId)
    {
        // Get all device configurations for this device
        $deviceConfigurations = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->fetchAllDeviceConfigurationByDeviceId($deviceId);

        // Loop through and delete all configuration options for each configuration
        foreach ($deviceConfigurations as $deviceConfiguration)
        {
            Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance()->deleteDeviceConfigurationOptionById($deviceConfiguration->id);
        }

        // Delete all device configurations
        return $this->getDbTable()->delete(array(
            "masterDeviceId = ?" => $deviceId
        ));
    }

    /**
     * Finds a deviceConfiguration based on it's primaryKey
     *
     * @param $id int
     *            The id of the deviceConfiguration to find
     *
     * @return Quotegen_Model_DeviceConfiguration
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result && $result instanceof Quotegen_Model_DeviceConfiguration)
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
        $object = new Quotegen_Model_DeviceConfiguration($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a deviceConfiguration
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return Quotegen_Model_DeviceConfiguration
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Quotegen_Model_DeviceConfiguration($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all deviceConfigurations
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
     * @return Quotegen_Model_DeviceConfiguration[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Quotegen_Model_DeviceConfiguration($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Fetches all deviceConfigurations by masterDeviceId
     *
     * @param int $masterDeviceId
     *
     * @return Quotegen_Model_DeviceConfiguration[]
     */
    public function fetchAllDeviceConfigurationByDeviceId ($masterDeviceId)
    {
        $deviceConfigurations = array();
        $resultSet            = $this->getDbTable()->fetchAll(array(
            'masterDeviceId = ?' => $masterDeviceId,
            'dealerId = ?'       => Zend_Auth::getInstance()->getIdentity()->dealerId
        ));

        foreach ($resultSet as $row)
        {
            $object = new Quotegen_Model_DeviceConfiguration($row->toArray());

            // Save the item into cahce
            $this->saveItemToCache($object);
            $deviceConfigurations [] = $object;
        }

        return $deviceConfigurations;
    }

    /**
     * Fetches all deviceConfigurations by masterDeviceId
     *
     * @param int $masterDeviceId
     *
     * @return Quotegen_Model_DeviceConfiguration[]
     */
    public function fetchAllDeviceConfigurationByDeviceIdAndDealerId ($masterDeviceId)
    {
        $deviceConfigurations = array();
        $resultSet            = $this->getDbTable()->fetchAll(array(
            'masterDeviceId = ?' => $masterDeviceId
        ));

        foreach ($resultSet as $row)
        {
            $object = new Quotegen_Model_DeviceConfiguration($row->toArray());

            // Save the item into cahce
            $this->saveItemToCache($object);
            $deviceConfigurations [] = $object;
        }

        return $deviceConfigurations;
    }

    /**
     * Fetches all the devices configurations available for a user
     *
     * @param int $userId
     *
     * @return Quotegen_Model_DeviceConfiguration[]
     */
    public function fetchAllDeviceConfigurationsAvailableToUser ($userId)
    {
        // FIXME: coding fetch all for now, needs to grab global + user configurations
        return $this->fetchAll();
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
            "{$this->col_id} = ?" => $id
        );
    }

    /**
     * @param Quotegen_Model_DeviceConfiguration $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->masterDeviceId;
    }

    /**
     * Fetches a list of device configurations for the dealer
     *
     * @param int $dealerId
     *
     * @return Quotegen_Model_DeviceConfiguration[]
     */
    public function fetchDeviceConfigurationListForDealer ($dealerId)
    {
        $devices = $this->fetchAll(array("{$this->col_dealerId} = ?" => $dealerId));

        return $devices;
    }
}

