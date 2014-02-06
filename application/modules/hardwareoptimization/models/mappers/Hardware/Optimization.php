<?php

/**
 * Class Hardwareoptimization_Model_Mapper_Hardware_Optimization
 */
class Hardwareoptimization_Model_Mapper_Hardware_Optimization extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_id = 'id';
    public $col_clientId = 'clientId';
    public $col_rmsUploadId = "rmsUploadId";
    public $col_dateCreated = 'dateCreated';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Hardwareoptimization_Model_DbTable_Hardware_Optimization';

    /**
     * Gets an instance of the mapper
     *
     * @return Hardwareoptimization_Model_Mapper_Hardware_Optimization
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Hardwareoptimization_Model_Hardware_Optimization to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Hardwareoptimization_Model_Hardware_Optimization
     *                The object to insert
     *
     * @return int The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $this->unsetNullValues($object->toArray());

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
     * Saves (updates) an instance of Hardwareoptimization_Model_Hardware_Optimization to the database.
     *
     * @param $object     Hardwareoptimization_Model_Hardware_Optimization
     *                    The Template model to save to the database
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
     *                This can either be an instance of Hardwareoptimization_Model_Hardware_Optimization or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Hardwareoptimization_Model_Hardware_Optimization)
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
     * Finds a Template based on it's primaryKey
     *
     * @param $id int
     *            The id of the Template to find
     *
     * @return Hardwareoptimization_Model_Hardware_Optimization
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Hardwareoptimization_Model_Hardware_Optimization)
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
        $object = new Hardwareoptimization_Model_Hardware_Optimization($row->toArray());

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
     * @return Hardwareoptimization_Model_Hardware_Optimization
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Hardwareoptimization_Model_Hardware_Optimization($row->toArray());

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
     * @return Hardwareoptimization_Model_Hardware_Optimization[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Hardwareoptimization_Model_Hardware_Optimization($row->toArray());

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
            "{$this->col_id} = ?" => $id
        );
    }

    /**
     * @param $clientId
     *
     * @return Hardwareoptimization_Model_Hardware_Optimization
     */
    public function findHardwareOptimizationByClientId ($clientId)
    {
        return $this->fetch(array("{$this->col_clientId} = ? " => $clientId));
    }

    /**
     * @param Hardwareoptimization_Model_Hardware_Optimization $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Fetches all the hardware optimizations for a client
     *
     * @param      $clientId
     * @param null $order
     * @param int  $count
     * @param null $offset
     *
     * @return Hardwareoptimization_Model_Hardware_Optimization[]
     */
    public function fetchAllForClient ($clientId, $order = null, $count = 50, $offset = null)
    {
        if ($order == null)
        {
            $order = $this->col_dateCreated . " DESC";
        }

        return $this->fetchAll(array("{$this->col_clientId} = ?" => $clientId), $order, $count, $offset);
    }

    /**
     * @param int                                  $hardwareOptimizationId
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     * @param int                                  $limit
     * @param int                                  $offset
     * @param bool                                 $justCount
     *
     * @return array|int
     */
    public function fetchAllForHardwareOptimization ($hardwareOptimizationId, $costPerPageSetting, $limit, $offset = 0, $justCount = false)
    {
        $db                     = $this->getDbTable()->getAdapter();
        $hardwareOptimizationId = $db->quote($hardwareOptimizationId, 'INTEGER');

        $hardwareOptimization                                   = Hardwareoptimization_Model_Mapper_Hardware_Optimization::getInstance()->find($hardwareOptimizationId);
        Proposalgen_Model_MasterDevice::$ReportLaborCostPerPage = $hardwareOptimization->getHardwareOptimizationSetting()->laborCostPerPage;
        Proposalgen_Model_MasterDevice::$ReportPartsCostPerPage = $hardwareOptimization->getHardwareOptimizationSetting()->partsCostPerPage;

        $deviceInstanceMapper             = Proposalgen_Model_Mapper_DeviceInstance::getInstance();
        $deviceInstanceMasterDeviceMapper = Proposalgen_Model_Mapper_Device_Instance_Master_Device::getInstance();
        $masterDeviceMapper               = Proposalgen_Model_Mapper_MasterDevice::getInstance();
        $manufacturerMapper               = Proposalgen_Model_Mapper_Manufacturer::getInstance();

        if ($justCount)
        {

            $selectStatement = new Zend_Db_Expr("COUNT(*)");
            $select          = $db->select();
            $select->from(array($this->getTableName()), $selectStatement)
                   ->join(array("di" => $deviceInstanceMapper->getTableName()), "{$this->getTableName()}.{$this->col_rmsUploadId} = di.{$deviceInstanceMapper->col_rmsUploadId}")
                   ->join(array("dimd" => $deviceInstanceMasterDeviceMapper->getTableName()), "dimd.{$deviceInstanceMasterDeviceMapper->col_deviceInstanceId} = di.{$deviceInstanceMapper->col_id}")
                   ->join(array("md" => $masterDeviceMapper->getTableName()), "md.{$masterDeviceMapper->col_id} = dimd.{$deviceInstanceMasterDeviceMapper->col_masterDeviceId}")
                   ->join(array("m" => $manufacturerMapper->getTableName()), "m.{$manufacturerMapper->col_id} = md.{$masterDeviceMapper->col_manufacturerId}")
                   ->where("{$this->getTableName()}.$this->col_id = ?", $hardwareOptimizationId)
                   ->where("md.isLeased = ?", 0);

            return $db->query($select)->fetchColumn();
        }
        else
        {
            if (!$limit)
            {
                $limit = 25;
            }

            $selectStatement = new Zend_Db_Expr("di.*, IF(md.tonerConfigId > 1, 1, 0) AS isColor, CONCAT(m.fullname, '</br>', md.modelName) AS device");

            $db     = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            // Get all the devices, we will limit and offset them once they are sorted by cost.
            $select->from(array($this->getTableName()), $selectStatement)
                   ->join(array("di" => $deviceInstanceMapper->getTableName()), "{$this->getTableName()}.{$this->col_rmsUploadId} = di.{$deviceInstanceMapper->col_rmsUploadId}")
                   ->join(array("dimd" => $deviceInstanceMasterDeviceMapper->getTableName()), "dimd.{$deviceInstanceMasterDeviceMapper->col_deviceInstanceId} = di.{$deviceInstanceMapper->col_id}")
                   ->join(array("md" => $masterDeviceMapper->getTableName()), "md.{$masterDeviceMapper->col_id} = dimd.{$deviceInstanceMasterDeviceMapper->col_masterDeviceId}")
                   ->join(array("m" => $manufacturerMapper->getTableName()), "m.{$manufacturerMapper->col_id} = md.{$masterDeviceMapper->col_manufacturerId}")
                   ->where("{$this->getTableName()}.$this->col_id = ?", $hardwareOptimizationId)
                   ->where("md.isLeased = ?", 0)
                   ->where("di.isExcluded = ?", 0);

            $query = $db->query($select);

            $devices = array();
            foreach ($query->fetchAll() as $row)
            {
                $deviceInstance            = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($row['deviceInstanceId']);
                $deviceInstanceMonthlyCost = $deviceInstance->calculateMonthlyCost($costPerPageSetting);

                $rowJson               = array(
                    'deviceInstanceId' => $row['deviceInstanceId'],
                    'isColor'          => $row['isColor'],
                    'device'           => $row['device'],
                    'rawMonthlyCost'   => $deviceInstanceMonthlyCost,
                );
                $devices['jsonData'][] = $rowJson;
            }

            // Sort the collection of devices by monthly cost
            // Sort by monthly Cost
            usort($devices['jsonData'], function ($a, $b)
            {
                return $b['rawMonthlyCost'] - $a['rawMonthlyCost'];
            });

            // Limit and offset devices
            $returnDevices = array();
            $maximum       = count($devices['jsonData']);
            for ($i = $offset; $i < $limit + $offset; $i++)
            {
                if ($i >= $maximum)
                {
                    break;
                }
                // We want the two arrays to be in parallel since we need to pass and array of devices to the form to generate the select elements
                $deviceInstance = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($devices['jsonData'][$i]['deviceInstanceId']);

                $returnDevices['deviceInstances'][] = $deviceInstance;
                $jsonData                           = $devices['jsonData'][$i];

                $pageCount                 = $deviceInstance->getPageCounts();
                $deviceInstanceMonthlyCost = $deviceInstance->calculateMonthlyCost($costPerPageSetting);
                $costDelta                 = $deviceInstanceMonthlyCost - $deviceInstance->calculateMonthlyCost($costPerPageSetting, $deviceInstance->getReplacementMasterDeviceForHardwareOptimization($hardwareOptimizationId));
                $jsonData['monoAmpv']      = number_format($pageCount->getBlackPageCount()->getMonthly());
                $jsonData['colorAmpv']     = number_format($pageCount->getColorPageCount()->getMonthly());
                $jsonData['rawMonoCpp']    = $deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostPerPage()->monochromeCostPerPage;
                $jsonData['rawColorCpp']   = $deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostPerPage()->colorCostPerPage;
                $jsonData['rawCostDelta']  = $costDelta;
//                $jsonData['reason']        = $deviceInstance->getReason();

                $returnDevices['jsonData'][] = $jsonData;
            }

            return $returnDevices;
        }
    }
}