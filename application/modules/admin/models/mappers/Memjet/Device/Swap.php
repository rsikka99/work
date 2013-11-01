<?php
/**
 * Class Admin_Model_Mapper_Memjet_Device_Swap
 */
class Admin_Model_Mapper_Memjet_Device_Swap extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_masterDeviceId = 'masterDeviceId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Admin_Model_DbTable_Memjet_Device_Swap';

    /**
     * @var Admin_Model_Memjet_Device_Swap[]
     */
    protected $_replacementDevices;

    /**
     * @var Admin_Model_Memjet_Device_Swap[]
     */
    protected $_blackReplacementDevices;

    /**
     * @var Admin_Model_Memjet_Device_Swap[]
     */
    protected $_blackMfpReplacementDevices;

    /**
     * @var Admin_Model_Memjet_Device_Swap[]
     */
    protected $_colorReplacementDevices;

    /**
     * @var Admin_Model_Memjet_Device_Swap[]
     */
    protected $_colorMfpReplacementDevices;

    /**
     * Gets an instance of the mapper
     *
     * @return Admin_Model_Mapper_Memjet_Device_Swap
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Admin_Model_Memjet_Device_Swap to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Admin_Model_Memjet_Device_Swap
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
     * Saves (updates) an instance of Admin_Model_Memjet_Device_Swap to the database.
     *
     * @param $object     Admin_Model_Memjet_Device_Swap
     *                    The Device Swap model to save to the database
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
            $primaryKey = $data [$this->col_masterDeviceId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
                                                                "{$this->col_masterDeviceId} = ?" => $primaryKey
                                                           ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Admin_Model_Memjet_Device_Swap or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Admin_Model_Memjet_Device_Swap)
        {
            $whereClause = array(
                "{$this->col_masterDeviceId} = ?" => $object->masterDeviceId
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_masterDeviceId} = ?" => $object
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a Device Swap based on it's primaryKey
     *
     * @param $id array
     *            The id of the Device Swap to find
     *
     * @return Admin_Model_Memjet_Device_Swap
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Admin_Model_Memjet_Device_Swap)
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
        $object = new Admin_Model_Memjet_Device_Swap($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a Template
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return Admin_Model_Memjet_Device_Swap
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Admin_Model_Memjet_Device_Swap($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all Templates
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
     * @return Admin_Model_Memjet_Device_Swap[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Admin_Model_Memjet_Device_Swap($row->toArray());

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
            "{$this->col_masterDeviceId} = ?" => $id[0]
        );
    }

    /**
     * @param Admin_Model_Memjet_Device_Swap $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array(
            $object->masterDeviceId
        );
    }

    /**
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     * @param                                      $sortOrder
     * @param null                                 $limit
     * @param null                                 $offset
     * @param bool                                 $justCount
     *
     * @return array|int
     */
    public function fetchAllDeviceSwaps ($costPerPageSetting, $sortOrder, $limit = null, $offset = null, $justCount = false)
    {
        $db = $this->getDbTable()->getAdapter();

        if ($justCount)
        {
            $select = $db->select()->from("{$this->getTableName()}", "COUNT(*)");

            return $db->query($select)->fetchColumn();
        }
        else
        {
            $manufacturerMapper = Proposalgen_Model_Mapper_Manufacturer::getInstance();
            $masterDeviceMapper = Proposalgen_Model_Mapper_MasterDevice::getInstance();
            $returnLimit        = 10;

            if (!$limit)
            {
                $offset = ($offset > 0) ? $offset : 0;
            }

            $caseStatement = new Zend_Db_Expr("*, CASE WHEN md.isCopier AND md.tonerConfigId = 1 THEN 'Monochrome MFP'
            WHEN md.isCopier AND md.tonerConfigId > 1 THEN 'Color MFP'
            WHEN NOT md.isCopier AND md.tonerConfigId > 1 THEN 'Color '
            WHEN NOT md.isCopier AND md.tonerConfigId = 1 THEN 'Monochrome'
            END AS deviceType");


            $db     = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from(array($this->getTableName()), $caseStatement)
                   ->joinLeft(array("md" => $masterDeviceMapper->getTableName()), "{$this->getTableName()}.{$this->col_masterDeviceId} = md.{$masterDeviceMapper->col_id}", array("{$masterDeviceMapper->col_id}"))
                   ->joinLeft(array("m" => $manufacturerMapper->getTableName()), "md.{$masterDeviceMapper->col_manufacturerId} = m.{$manufacturerMapper->col_id}", array($manufacturerMapper->col_fullName, "device_name" => new Zend_Db_Expr("concat({$manufacturerMapper->col_fullName},' ', {$masterDeviceMapper->col_modelName})")))
                   ->limit($returnLimit, $offset)
                   ->order($sortOrder);

            $query = $db->query($select);

            $deviceSwaps = array();
            foreach ($query->fetchAll() as $row)
            {
                $masterDevice         = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($row['id']);
                $row['deviceType']    = Proposalgen_Model_MasterDevice::$TonerConfigNames[$masterDevice->getDeviceType()];
                $row['monochromeCpp'] = $masterDevice->calculateCostPerPage($costPerPageSetting)->monochromeCostPerPage;
                $row['colorCpp']      = $row['monochromeCpp'] + $masterDevice->calculateCostPerPage($costPerPageSetting)->colorCostPerPage;

                $deviceSwaps[] = $row;
            }

            return $deviceSwaps;
        }
    }

    /**
     * Gets the replacement devices that are eligible for replacing Black Devices
     *
     * @param bool $allowUpgrades
     *
     * @return Admin_Model_Memjet_Device_Swap []
     */
    public function getBlackReplacementDevices ($allowUpgrades = true)
    {
        if (!isset($this->_blackReplacementDevices))
        {
            $replacementDevices = $this->fetchAllReplacements();
            foreach ($replacementDevices as $replacementDevice)
            {
                if ($replacementDevice->getReplacementCategory() === Admin_Model_Memjet_Device_Swap::$replacementTypes[Proposalgen_Model_ReplacementDevice::REPLACEMENT_BW])
                {
                    $this->_blackReplacementDevices [] = $replacementDevice;
                }
                else if ($allowUpgrades)
                {
                    $this->_blackReplacementDevices [] = $replacementDevice;
                }
            }
        }

        return $this->_blackReplacementDevices;
    }

    /**
     * Gets the replacement devices that are eligible for replacing Black MFP Devices
     *
     * @param bool $allowUpgrades
     *
     * @return Admin_Model_Memjet_Device_Swap []
     */
    public function getBlackMfpReplacementDevices ($allowUpgrades = true)
    {
        if (!isset($this->_blackMfpReplacementDevices))
        {
            $replacementDevices = $this->fetchAllReplacements();
            foreach ($replacementDevices as $replacementDevice)
            {
                if ($replacementDevice->getReplacementCategory() === Admin_Model_Memjet_Device_Swap::$replacementTypes[Proposalgen_Model_ReplacementDevice::REPLACEMENT_BW_MFP])
                {
                    $this->_blackMfpReplacementDevices [] = $replacementDevice;
                }
                else if ($allowUpgrades && $replacementDevice->getReplacementCategory() === Admin_Model_Memjet_Device_Swap::$replacementTypes[Proposalgen_Model_ReplacementDevice::REPLACEMENT_COLOR_MFP])
                {
                    $this->_blackMfpReplacementDevices [] = $replacementDevice;
                }
            }
        }

        return $this->_blackMfpReplacementDevices;
    }

    /**
     * Gets the replacement devices that are eligible for replacing Color Devices
     *
     * @param bool $allowUpgrades
     *
     * @return Admin_Model_Memjet_Device_Swap []
     */
    public function getColorReplacementDevices ($allowUpgrades = true)
    {
        if (!isset($this->_colorReplacementDevices))
        {
            $replacementDevices = $this->fetchAllReplacements();
            foreach ($replacementDevices as $replacementDevice)
            {
                if ($replacementDevice->getReplacementCategory() === Admin_Model_Memjet_Device_Swap::$replacementTypes[Proposalgen_Model_ReplacementDevice::REPLACEMENT_COLOR])
                {
                    $this->_colorReplacementDevices [] = $replacementDevice;
                }
                else if ($allowUpgrades && $replacementDevice->getReplacementCategory() === Admin_Model_Memjet_Device_Swap::$replacementTypes[Proposalgen_Model_ReplacementDevice::REPLACEMENT_COLOR_MFP])
                {
                    $this->_colorReplacementDevices [] = $replacementDevice;
                }
            }
        }

        return $this->_colorReplacementDevices;
    }

    /**
     * Gets the replacement devices that are eligible for replacing Color MFP Devices
     *
     * @return Admin_Model_Memjet_Device_Swap []
     */
    public function getColorMfpReplacementDevices ()
    {
        if (!isset($this->_colorMfpReplacementDevices))
        {
            $replacementDevices = $this->fetchAllReplacements();
            foreach ($replacementDevices as $replacementDevice)
            {
                if ($replacementDevice->getReplacementCategory() === Admin_Model_Memjet_Device_Swap::$replacementTypes[Proposalgen_Model_ReplacementDevice::REPLACEMENT_COLOR_MFP])
                {
                    $this->_colorMfpReplacementDevices [] = $replacementDevice;
                }
            }
        }

        return $this->_colorMfpReplacementDevices;
    }

    /**
     * @param $order
     *
     * @return Admin_Model_Memjet_Device_Swap[]
     */
    public function fetchAllReplacements ($order = null)
    {
        if (!isset($this->_replacementDevices))
        {
            $db = $this->getDbTable()->getAdapter();

            $masterDeviceMapper = Proposalgen_Model_Mapper_MasterDevice::getInstance();

            $caseStatement = new Zend_Db_Expr("device_swaps_memjet.*,
                        CASE WHEN md.isCopier AND md.tonerConfigId = 1 THEN 'monochromeMfp'
                        WHEN md.isCopier AND md.tonerConfigId > 1 THEN 'colorMfp'
                        WHEN NOT md.isCopier AND md.tonerConfigId > 1 THEN 'color'
                        WHEN NOT md.isCopier AND md.tonerConfigId = 1 THEN 'monochrome'
                        END AS replacementCategory");


            $db     = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from(array($this->getTableName()), $caseStatement)
                   ->joinLeft(array("md" => $masterDeviceMapper->getTableName()), "{$this->getTableName()}.{$this->col_masterDeviceId} = md.{$masterDeviceMapper->col_id}", array("{$masterDeviceMapper->col_id}"))
                   ->order($order);

            $query = $db->query($select);

            $deviceSwaps = array();
            foreach ($query->fetchAll() as $row)
            {
                $deviceSwap = new Admin_Model_Memjet_Device_Swap();
                $deviceSwap->populate($row);
                $deviceSwap->setReplacementCategory($row['replacementCategory']);
                $deviceSwaps[] = $deviceSwap;
            }

            $this->_replacementDevices = $deviceSwaps;
        }

        return $this->_replacementDevices;
    }
}