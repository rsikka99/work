<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers;

use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Expr;
use Zend_Db_Table;
use Zend_Db_Table_Select;

/**
 * Class DeviceSwapMapper
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers
 */
class DeviceSwapMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_masterDeviceId = 'masterDeviceId';
    public $col_dealerId       = 'dealerId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables\DeviceSwapDbTable';

    /**
     * @var DeviceSwapModel[]
     */
    protected $_replacementDevices;

    /**
     * @var DeviceSwapModel[]
     */
    protected $_blackReplacementDevices;

    /**
     * @var DeviceSwapModel[]
     */
    protected $_blackMfpReplacementDevices;

    /**
     * @var DeviceSwapModel[]
     */
    protected $_colorReplacementDevices;

    /**
     * @var DeviceSwapModel[]
     */
    protected $_colorMfpReplacementDevices;

    /**
     * Gets an instance of the mapper
     *
     * @return DeviceSwapMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object DeviceSwapModel
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapModel to the database.
     *
     * @param $object     DeviceSwapModel
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
            $primaryKey = $this->getPrimaryKeyValueForObject($object);
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_masterDeviceId} = ?" => $primaryKey[0],
            "{$this->col_dealerId} = ?"       => $primaryKey[1]
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof DeviceSwapModel)
        {
            $whereClause = [
                "{$this->col_masterDeviceId} = ?" => $object->masterDeviceId,
                "{$this->col_dealerId} = ?"       => $object->dealerId,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_masterDeviceId} = ?" => $object[0],
                "{$this->col_dealerId} = ?"       => $object[1],
            ];
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
     * @return DeviceSwapModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof DeviceSwapModel)
        {
            return $result;
        }

        // Assuming we don't have a cached object, lets go get it.
        $result = $this->getDbTable()->find((int)$id[0], (int)$id[1]);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new DeviceSwapModel($row->toArray());

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
     * @return DeviceSwapModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new DeviceSwapModel($row->toArray());

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
     * @return DeviceSwapModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new DeviceSwapModel($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param array $id
     *
     * @return array
     */
    public function getWhereId ($id)
    {
        return [
            "{$this->col_masterDeviceId} = ?" => $id[0],
            "{$this->col_dealerId} = ?"       => $id[1],
        ];
    }

    /**
     * @param DeviceSwapModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return [
            $object->masterDeviceId,
            $object->dealerId,
        ];
    }

    /**
     * @param                                      $dealerId
     * @param CostPerPageSettingModel              $costPerPageSetting
     * @param                                      $sortOrder
     * @param null                                 $limit
     * @param null                                 $offset
     * @param bool                                 $justCount
     *
     * @return array|int
     */
    public function fetAllForDealer ($dealerId, $costPerPageSetting, $sortOrder, $limit = null, $offset = null, $justCount = false)
    {
        $db       = $this->getDbTable()->getAdapter();
        $dealerId = $db->quote($dealerId, 'INTEGER');

        if ($justCount)
        {
            $select = $db->select()->from("{$this->getTableName()}", "COUNT(*)")->where("{$this->col_dealerId} = ?", $dealerId);

            return $db->query($select)->fetchColumn();
        }
        else
        {
            $manufacturerMapper = ManufacturerMapper::getInstance();
            $masterDeviceMapper = MasterDeviceMapper::getInstance();

            /**
             * FIXME lrobert: WHY. WHYYY. Seriously why is this hardcoded in. Investigate why and document it or fix it so it's proper.
             */
            $returnLimit = 1000;

            if (!$limit)
            {
                $limit  = "1000";
                $offset = ($offset > 0) ? $offset : 0;
            }

            $caseStatement = new Zend_Db_Expr("*, CASE WHEN md.isCopier AND md.tonerConfigId = 1 THEN 'Monochrome MFP'
            WHEN md.isCopier AND md.tonerConfigId > 1 THEN 'Color MFP'
            WHEN NOT md.isCopier AND md.tonerConfigId > 1 THEN 'Color '
            WHEN NOT md.isCopier AND md.tonerConfigId = 1 THEN 'Monochrome'
            END AS deviceType");


            $db     = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from([$this->getTableName()], $caseStatement)
                   ->joinLeft(["md" => $masterDeviceMapper->getTableName()], "{$this->getTableName()}.{$this->col_masterDeviceId} = md.{$masterDeviceMapper->col_id}", ["{$masterDeviceMapper->col_id}"])
                   ->joinLeft(["m" => $manufacturerMapper->getTableName()], "md.{$masterDeviceMapper->col_manufacturerId} = m.{$manufacturerMapper->col_id}", [$manufacturerMapper->col_fullName, "device_name" => new Zend_Db_Expr("concat({$manufacturerMapper->col_fullName},' ', {$masterDeviceMapper->col_modelName})")])
                   ->where("{$this->getTableName()}.$this->col_dealerId = ?", $dealerId)
                   ->limit($returnLimit, $offset)
                   ->order($sortOrder);

            $query = $db->query($select);

            $deviceSwaps = [];
            foreach ($query->fetchAll() as $row)
            {
                $masterDevice         = MasterDeviceMapper::getInstance()->find($row['id']);
                $row['deviceType']    = MasterDeviceModel::$TonerConfigNames[$masterDevice->getDeviceType()];
                $row['monochromeCpp'] = $masterDevice->calculateCostPerPage($costPerPageSetting)->getCostOfInkAndTonerPerPage()->monochromeCostPerPage;
                $row['colorCpp']      = $row['monochromeCpp'] + $masterDevice->calculateCostPerPage($costPerPageSetting)->getCostOfInkAndTonerPerPage()->colorCostPerPage;

                $deviceSwaps[] = $row;
            }

            return $deviceSwaps;
        }
    }

    /**
     * Gets the replacement devices that are eligible for replacing Black Devices
     *
     * @param      $dealerId
     * @param bool $allowMfpUpgrades
     * @param bool $allowColorUpgrades
     *
     * @return DeviceSwapModel []
     */
    public function getBlackReplacementDevices ($dealerId, $allowMfpUpgrades = true, $allowColorUpgrades = true)
    {
        if (!isset($this->_blackReplacementDevices))
        {
            $replacementDevices = $this->fetchAllReplacementsForDealer($dealerId);
            foreach ($replacementDevices as $replacementDevice)
            {
                if ($replacementDevice->getReplacementCategory() === DeviceSwapModel::$replacementTypes[DeviceSwapModel::REPLACEMENT_BW])
                {
                    $this->_blackReplacementDevices [] = $replacementDevice;
                }
                else if ($allowColorUpgrades || $allowMfpUpgrades)
                {
                    if ($allowColorUpgrades && $allowMfpUpgrades)
                    {
                        $this->_blackReplacementDevices [] = $replacementDevice;
                    }
                    else if ($allowColorUpgrades && $replacementDevice->getReplacementCategory() === DeviceSwapModel::$replacementTypes[DeviceSwapModel::REPLACEMENT_COLOR])
                    {
                        $this->_blackReplacementDevices [] = $replacementDevice;
                    }
                    else if ($allowMfpUpgrades && $replacementDevice->getReplacementCategory() === DeviceSwapModel::$replacementTypes[DeviceSwapModel::REPLACEMENT_BW_MFP])
                    {
                        $this->_blackReplacementDevices [] = $replacementDevice;
                    }
                }
            }
        }

        return $this->_blackReplacementDevices;
    }

    /**
     * Gets the replacement devices that are eligible for replacing Black MFP Devices
     *
     * @param      $dealerId
     * @param bool $allowColorMfpUpgrades
     *
     * @return DeviceSwapModel []
     */
    public function getBlackMfpReplacementDevices ($dealerId, $allowColorMfpUpgrades = true)
    {
        if (!isset($this->_blackMfpReplacementDevices))
        {
            $replacementDevices = $this->fetchAllReplacementsForDealer($dealerId);
            foreach ($replacementDevices as $replacementDevice)
            {
                if ($replacementDevice->getReplacementCategory() === DeviceSwapModel::$replacementTypes[DeviceSwapModel::REPLACEMENT_BW_MFP])
                {
                    $this->_blackMfpReplacementDevices [] = $replacementDevice;
                }
                else if ($allowColorMfpUpgrades && $replacementDevice->getReplacementCategory() === DeviceSwapModel::$replacementTypes[DeviceSwapModel::REPLACEMENT_COLOR_MFP])
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
     * @param      $dealerId
     * @param bool $allowMfpUpgrades
     *
     * @return DeviceSwapModel []
     */
    public function getColorReplacementDevices ($dealerId, $allowMfpUpgrades = true)
    {
        if (!isset($this->_colorReplacementDevices))
        {
            $replacementDevices = $this->fetchAllReplacementsForDealer($dealerId);
            foreach ($replacementDevices as $replacementDevice)
            {
                if ($replacementDevice->getReplacementCategory() === DeviceSwapModel::$replacementTypes[DeviceSwapModel::REPLACEMENT_COLOR])
                {
                    $this->_colorReplacementDevices [] = $replacementDevice;
                }
                else if ($allowMfpUpgrades && $replacementDevice->getReplacementCategory() === DeviceSwapModel::$replacementTypes[DeviceSwapModel::REPLACEMENT_COLOR_MFP])
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
     * @param $dealerId
     *
     * @return DeviceSwapModel []
     */
    public function getColorMfpReplacementDevices ($dealerId)
    {
        if (!isset($this->_colorMfpReplacementDevices))
        {
            $replacementDevices = $this->fetchAllReplacementsForDealer($dealerId);
            foreach ($replacementDevices as $replacementDevice)
            {
                if ($replacementDevice->getReplacementCategory() === DeviceSwapModel::$replacementTypes[DeviceSwapModel::REPLACEMENT_COLOR_MFP])
                {
                    $this->_colorMfpReplacementDevices [] = $replacementDevice;
                }
            }
        }

        return $this->_colorMfpReplacementDevices;
    }

    /**
     * @param $dealerId
     * @param $order
     *
     * @return DeviceSwapModel[]
     */
    public function fetchAllReplacementsForDealer ($dealerId, $order = null)
    {
        if (!isset($this->_replacementDevices))
        {
            $db       = $this->getDbTable()->getAdapter();
            $dealerId = $db->quote($dealerId, 'INTEGER');

            $masterDeviceMapper = MasterDeviceMapper::getInstance();

            $caseStatement = new Zend_Db_Expr("device_swaps.*,
                        CASE WHEN md.isCopier AND md.tonerConfigId = 1 THEN 'monochromeMfp'
                        WHEN md.isCopier AND md.tonerConfigId > 1 THEN 'colorMfp'
                        WHEN NOT md.isCopier AND md.tonerConfigId > 1 THEN 'color'
                        WHEN NOT md.isCopier AND md.tonerConfigId = 1 THEN 'monochrome'
                        END AS replacementCategory");


            $db     = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from([$this->getTableName()], $caseStatement)
                   ->joinLeft(["md" => $masterDeviceMapper->getTableName()], "{$this->getTableName()}.{$this->col_masterDeviceId} = md.{$masterDeviceMapper->col_id}", ["{$masterDeviceMapper->col_id}"])
                   ->where("{$this->getTableName()}.$this->col_dealerId = ?", $dealerId)
                   ->order($order);

            $query = $db->query($select);

            $deviceSwaps = [];
            foreach ($query->fetchAll() as $row)
            {
                $deviceSwap = new DeviceSwapModel();
                $deviceSwap->populate($row);
                $deviceSwap->setReplacementCategory($row['replacementCategory']);
                $deviceSwaps[] = $deviceSwap;
            }

            $this->_replacementDevices = $deviceSwaps;
        }

        return $this->_replacementDevices;
    }
}