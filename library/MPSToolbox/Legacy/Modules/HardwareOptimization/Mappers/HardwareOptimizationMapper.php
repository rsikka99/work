<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers;

use Assessment_ViewModel_Devices;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationDeviceInstanceModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMeterMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Expr;
use Zend_Db_Table;
use Zend_Db_Table_Select;

/**
 * Class HardwareOptimizationMapper
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers
 */
class HardwareOptimizationMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_id           = 'id';
    public $col_clientId     = 'clientId';
    public $col_rmsUploadId  = "rmsUploadId";
    public $col_dateCreated  = 'dateCreated';
    public $col_lastModified = 'lastModified';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables\HardwareOptimizationDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return HardwareOptimizationMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object HardwareOptimizationModel
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationModel to the database.
     *
     * @param $object     HardwareOptimizationModel
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
     *                This can either be an instance of MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof HardwareOptimizationModel)
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
     * @return HardwareOptimizationModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof HardwareOptimizationModel)
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
        $object = new HardwareOptimizationModel($row->toArray());

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
     * @return HardwareOptimizationModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new HardwareOptimizationModel($row->toArray());

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
     * @return HardwareOptimizationModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new HardwareOptimizationModel($row->toArray());

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
     * @return HardwareOptimizationModel
     */
    public function findHardwareOptimizationByClientId ($clientId)
    {
        return $this->fetch(array("{$this->col_clientId} = ? " => $clientId));
    }

    /**
     * @param HardwareOptimizationModel $object
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
     * @param int  $clientId
     * @param int  $rmsUploadId
     * @param null $order
     * @param int  $count
     * @param null $offset
     *
     * @return \MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationModel[]
     */
    public function fetchAllForClient ($clientId, $rmsUploadId, $order = null, $count = 50, $offset = null)
    {
        if ($order == null)
        {
            $order = $this->col_lastModified . " DESC";
        }

        return $this->fetchAll([
            "{$this->col_clientId} = ?"    => $clientId,
            "{$this->col_rmsUploadId} = ?" => $rmsUploadId,
        ], $order, $count, $offset);
    }

    /**
     * @param int                     $hardwareOptimizationId
     * @param CostPerPageSettingModel $costPerPageSetting
     * @param CostPerPageSettingModel $replacementCostPerPageSetting
     * @param int                     $limit
     * @param int                     $offset
     * @param bool                    $justCount
     *
     * @return array|int
     */
    public function fetchAllForHardwareOptimization ($hardwareOptimizationId, $costPerPageSetting, $replacementCostPerPageSetting, $limit, $offset = 0, $justCount = false)
    {
        $db                     = $this->getDbTable()->getAdapter();
        $hardwareOptimizationId = $db->quote($hardwareOptimizationId, 'INTEGER');

        $hardwareOptimization                      = HardwareOptimizationMapper::getInstance()->find($hardwareOptimizationId);
        MasterDeviceModel::$ReportLaborCostPerPage = $hardwareOptimization->getClient()->getClientSettings()->proposedFleetSettings->defaultLaborCostPerPage;
        MasterDeviceModel::$ReportPartsCostPerPage = $hardwareOptimization->getClient()->getClientSettings()->proposedFleetSettings->defaultPartsCostPerPage;

        $deviceInstanceMapper             = DeviceInstanceMapper::getInstance();
        $deviceInstanceMasterDeviceMapper = DeviceInstanceMasterDeviceMapper::getInstance();
        $masterDeviceMapper               = MasterDeviceMapper::getInstance();
        $manufacturerMapper               = ManufacturerMapper::getInstance();
        $deviceInstanceMeterMapper        = DeviceInstanceMeterMapper::getInstance();

        if ($justCount)
        {

            $selectStatement = new Zend_Db_Expr("COUNT(*)");
            $select          = $db->select();
            $select->from(array($this->getTableName()), $selectStatement)
                   ->join(array("di" => $deviceInstanceMapper->getTableName()), "{$this->getTableName()}.{$this->col_rmsUploadId} = di.{$deviceInstanceMapper->col_rmsUploadId}")
                   ->join(array("dimd" => $deviceInstanceMasterDeviceMapper->getTableName()), "dimd.{$deviceInstanceMasterDeviceMapper->col_deviceInstanceId} = di.{$deviceInstanceMapper->col_id}")
                   ->join(array("md" => $masterDeviceMapper->getTableName()), "md.{$masterDeviceMapper->col_id} = dimd.{$deviceInstanceMasterDeviceMapper->col_masterDeviceId}")
                   ->join(array("dim" => $deviceInstanceMeterMapper->getTableName()), "dim.{$deviceInstanceMeterMapper->col_deviceInstanceId} = di.{$deviceInstanceMapper->col_id}")
                   ->where("{$this->getTableName()}.$this->col_id = ?", $hardwareOptimizationId)
                   ->where("md.isLeased = ?", 0)
                   ->where("di.isExcluded = ?", 0)
                   ->where("DATEDIFF(dim.monitorEndDate, dim.monitorStartDate) >= ? ", Assessment_ViewModel_Devices::MINIMUM_MONITOR_INTERVAL_DAYS);

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
                   ->join(array("dim" => $deviceInstanceMeterMapper->getTableName()), "dim.{$deviceInstanceMeterMapper->col_deviceInstanceId} = di.{$deviceInstanceMapper->col_id}")
                   ->where("{$this->getTableName()}.$this->col_id = ?", $hardwareOptimizationId)
                   ->where("md.isLeased = ?", 0)
                   ->where("di.isExcluded = ?", 0)
                   ->where("DATEDIFF(dim.monitorEndDate, dim.monitorStartDate) >= ? ", Assessment_ViewModel_Devices::MINIMUM_MONITOR_INTERVAL_DAYS);

            $query = $db->query($select);

            $devices = array();
            foreach ($query->fetchAll() as $row)
            {
                $deviceInstance            = DeviceInstanceMapper::getInstance()->find($row['deviceInstanceId']);
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
                $replacementDevice = null;
                if ($i >= $maximum)
                {
                    break;
                }
                // We want the two arrays to be in parallel since we need to pass and array of devices to the form to generate the select elements
                $deviceInstance                     = DeviceInstanceMapper::getInstance()->find($devices['jsonData'][$i]['deviceInstanceId']);
                $hardwareOptimizationDeviceInstance = $deviceInstance->getHardwareOptimizationDeviceInstance($hardwareOptimizationId);
                if ($hardwareOptimizationDeviceInstance->action === HardwareOptimizationDeviceInstanceModel::ACTION_REPLACE || $hardwareOptimizationDeviceInstance->action === HardwareOptimizationDeviceInstanceModel::ACTION_UPGRADE)
                {
                    $replacementDevice = $hardwareOptimizationDeviceInstance->getMasterDevice();
                }

                $returnDevices['deviceInstances'][] = $deviceInstance;
                $jsonData                           = $devices['jsonData'][$i];

                $pageCount                 = $deviceInstance->getPageCounts();
                $deviceInstanceMonthlyCost = $deviceInstance->calculateMonthlyCost($costPerPageSetting);


                $costDelta                = ($replacementDevice instanceof MasterDeviceModel) ? $deviceInstanceMonthlyCost - $deviceInstance->calculateMonthlyCost($replacementCostPerPageSetting, $replacementDevice) : 0;
                $jsonData['monoAmpv']     = $pageCount->getBlackPageCount()->getMonthly();
                $jsonData['colorAmpv']    = $pageCount->getColorPageCount()->getMonthly();
                $jsonData['rawMonoCpp']   = $deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostPerPage()->monochromeCostPerPage;
                $jsonData['rawColorCpp']  = $deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostPerPage()->colorCostPerPage;
                $jsonData['rawCostDelta'] = $costDelta;

                $returnDevices['jsonData'][] = $jsonData;
            }

            return $returnDevices;
        }
    }
}