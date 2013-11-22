<?php
/**
 * Class Proposalgen_Model_Mapper_ReplacementDevice
 */
class Proposalgen_Model_Mapper_ReplacementDevice extends My_Model_Mapper_Abstract
{
    /**
     * Column Definitions
     */
    public $col_dealerId = 'dealerId';
    public $col_masterDeviceId = 'masterDeviceId';
    public $col_replacementCategory = 'replacementCategory';
    public $col_monthlyRate = 'monthlyRate';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_ReplacementDevice';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_ReplacementDevice
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_ReplacementDevice to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Proposalgen_Model_ReplacementDevice
     *                The object to insert
     *
     * @return int The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $this->unsetNullValues($object->toArray());

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of Proposalgen_Model_ReplacementDevice to the database.
     *
     * @param $object     Proposalgen_Model_ReplacementDevice
     *                    The Replacement Device model to save to the database
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
            $primaryKey = array($data [$this->col_dealerId], $data [$this->col_masterDeviceId]);
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
                                                                "{$this->col_dealerId} = ?"       => $primaryKey[0],
                                                                "{$this->col_masterDeviceId} = ?" => $primaryKey[1]
                                                           ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Proposalgen_Model_ReplacementDevice or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Proposalgen_Model_ReplacementDevice)
        {
            $whereClause = array(
                "{$this->col_dealerId} = ?"       => $object->dealerId,
                "{$this->col_masterDeviceId} = ?" => $object->masterDeviceId
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_dealerId} = ?"       => $object[0],
                "{$this->col_masterDeviceId} = ?" => $object[1]
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a Replacement Device based on it's primaryKey
     *
     * @param $id int
     *            The id of the Replacement Device to find
     *
     * @return Proposalgen_Model_ReplacementDevice
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Proposalgen_Model_ReplacementDevice)
        {
            return $result;
        }

        // Assuming we don't have a cached object, lets go get it.
        $result = $this->getDbTable()->find($id[0], $id[1]);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new Proposalgen_Model_ReplacementDevice($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a Replacement Device
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return Proposalgen_Model_ReplacementDevice
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Proposalgen_Model_ReplacementDevice($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all Replacement Devices
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
     * @return Proposalgen_Model_ReplacementDevice[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Proposalgen_Model_ReplacementDevice($row->toArray());

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
            "{$this->col_masterDeviceId} = ?" => $id
        );
    }

    /**
     * @param Proposalgen_Model_ReplacementDevice $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array($object->dealerId, $object->masterDeviceId);
    }

    /**
     * Fetches the cheapest replacement device for each category
     *
     * @param $dealerId
     *
     * @return Proposalgen_Model_ReplacementDevice[]
     */
    public function fetchCheapestForEachCategory ($dealerId)
    {
        $replacementDevices                                                              = array();
        $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACEMENT_BW]        = $this->fetch(array(
                                                                                                             "{$this->col_dealerId} = ?"            => $dealerId,
                                                                                                             "{$this->col_replacementCategory} = ?" => Proposalgen_Model_ReplacementDevice::REPLACEMENT_BW,
                                                                                                        ), array(
                                                                                                                "{$this->col_monthlyRate} ASC"
                                                                                                           ));
        $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACEMENT_BW_MFP]    = $this->fetch(array(
                                                                                                             "{$this->col_dealerId} = ?"            => $dealerId,
                                                                                                             "{$this->col_replacementCategory} = ?" => Proposalgen_Model_ReplacementDevice::REPLACEMENT_BW_MFP
                                                                                                        ), array(
                                                                                                                "{$this->col_monthlyRate} ASC"
                                                                                                           ));
        $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACEMENT_COLOR]     = $this->fetch(array(
                                                                                                             "{$this->col_dealerId} = ?"            => $dealerId,
                                                                                                             "{$this->col_replacementCategory} = ?" => Proposalgen_Model_ReplacementDevice::REPLACEMENT_COLOR
                                                                                                        ), array(
                                                                                                                "{$this->col_monthlyRate} ASC"
                                                                                                           ));
        $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACEMENT_COLOR_MFP] = $this->fetch(array(
                                                                                                             "{$this->col_dealerId} = ?"            => $dealerId,
                                                                                                             "{$this->col_replacementCategory} = ?" => Proposalgen_Model_ReplacementDevice::REPLACEMENT_COLOR_MFP
                                                                                                        ), array(
                                                                                                                "{$this->col_monthlyRate} ASC"
                                                                                                           ));

        return $replacementDevices;
    }

    /**
     * Gets the replacement devices that are eligible for replacing Black Devices
     *
     * @param      $dealerId
     * @param bool $allowUpgrades
     *
     * @return Proposalgen_Model_MasterDevice []
     */
    public function getBlackReplacementDevices ($dealerId, $allowUpgrades = true)
    {
        $deviceArray        = array();
        $replacementDevices = $this->fetchAllForDealer($dealerId);
        foreach ($replacementDevices as $replacementDevice)
        {
            if ($replacementDevice->replacementCategory === Proposalgen_Model_ReplacementDevice::$replacementTypes[Proposalgen_Model_ReplacementDevice::REPLACEMENT_BW])
            {
                $deviceArray [] = $replacementDevice->getMasterDevice();
            }
            else if ($allowUpgrades)
            {
                $deviceArray [] = $replacementDevice->getMasterDevice();
            }
        }

        return $deviceArray;
    }

    /**
     * Gets the replacement devices that are eligible for replacing Black MFP Devices
     *
     * @param      $dealerId
     * @param bool $allowUpgrades
     *
     * @return Proposalgen_Model_MasterDevice []
     */
    public function getBlackMfpReplacementDevices ($dealerId, $allowUpgrades = true)
    {
        $deviceArray        = array();
        $replacementDevices = $this->fetchAllForDealer($dealerId);
        foreach ($replacementDevices as $replacementDevice)
        {
            if ($replacementDevice->replacementCategory === Proposalgen_Model_ReplacementDevice::$replacementTypes[Proposalgen_Model_ReplacementDevice::REPLACEMENT_BW_MFP])
            {
                $deviceArray [] = $replacementDevice->getMasterDevice();
            }
            else if ($allowUpgrades && $replacementDevice->replacementCategory === Proposalgen_Model_ReplacementDevice::$replacementTypes[Proposalgen_Model_ReplacementDevice::REPLACEMENT_COLOR_MFP])
            {
                $deviceArray [] = $replacementDevice->getMasterDevice();
            }
        }

        return $deviceArray;
    }

    /**
     * Gets the replacement devices that are eligible for replacing Color Devices
     *
     * @param      $dealerId
     * @param bool $allowUpgrades
     *
     * @return Proposalgen_Model_MasterDevice []
     */
    public function getColorReplacementDevices ($dealerId, $allowUpgrades = true)
    {
        $deviceArray        = array();
        $replacementDevices = $this->fetchAllForDealer($dealerId);
        foreach ($replacementDevices as $replacementDevice)
        {
            if ($replacementDevice->replacementCategory === Proposalgen_Model_ReplacementDevice::$replacementTypes[Proposalgen_Model_ReplacementDevice::REPLACEMENT_COLOR])
            {
                $deviceArray [] = $replacementDevice->getMasterDevice();
            }
            else if ($allowUpgrades && $replacementDevice->replacementCategory === Proposalgen_Model_ReplacementDevice::$replacementTypes[Proposalgen_Model_ReplacementDevice::REPLACEMENT_COLOR_MFP])
            {
                $deviceArray [] = $replacementDevice->getMasterDevice();
            }
        }

        return $deviceArray;
    }

    /**
     * Gets the replacement devices that are eligible for replacing Color MFP Devices
     *
     * @param $dealerId
     *
     * @return Proposalgen_Model_MasterDevice []
     */
    public function getColorMfpReplacementDevices ($dealerId)
    {
        $deviceArray        = array();
        $replacementDevices = $this->fetchAllForDealer($dealerId);
        foreach ($replacementDevices as $replacementDevice)
        {
            $deviceArray [] = $replacementDevice->getMasterDevice();
        }

        return $deviceArray;
    }

    /**
     * @param $dealerId
     *
     * @param $order
     * @param $count
     * @param $offset
     *
     * @return Proposalgen_Model_ReplacementDevice[]
     */
    public function fetchAllForDealer ($dealerId, $order = null, $count = null, $offset = null)
    {
        return $this->fetchAll(array("{$this->col_dealerId} = ?" => $dealerId), $order, $count, $offset);
    }
}