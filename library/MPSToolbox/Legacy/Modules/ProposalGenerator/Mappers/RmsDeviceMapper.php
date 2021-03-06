<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsDeviceModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Expr;
use Zend_Db_Table;
use Zend_Db_Table_Select;

/**
 * Class RmsDeviceMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class RmsDeviceMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_rmsProviderId = 'rmsProviderId';
    public $col_rmsModelId    = 'rmsModelId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\RmsDeviceDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return RmsDeviceMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsDeviceModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object RmsDeviceModel
     *                The object to insert
     * @return int The primary key of the new row
     */
    public function insert ($object)
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsDeviceModel to the database.
     *
     * @param $object     RmsDeviceModel
     *                    The Rms_Device model to save to the database
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

        $whereClause = $this->getWhereId($primaryKey);

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, $whereClause);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsDeviceModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof RmsDeviceModel)
        {
            $object = $this->getPrimaryKeyValueForObject($object);
        }

        $whereClause = $this->getWhereId($object);

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a Rms_Device based on it's primaryKey
     *
     * @param array $id The id of the Rms_Device to find
     *
     * @return RmsDeviceModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof RmsDeviceModel)
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
        $object = new RmsDeviceModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a Rms_Device
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return RmsDeviceModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new RmsDeviceModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all Rms_Devices
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
     * @return RmsDeviceModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new RmsDeviceModel($row->toArray());

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
            "{$this->col_rmsProviderId} = ?" => $id[0],
            "{$this->col_rmsModelId} = ?"    => $id[1],
        ];
    }

    /**
     * @param RmsDeviceModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return [$object->rmsProviderId, $object->rmsModelId];
    }

    /**
     * This function fetches match up devices
     *
     * @param string  $sortColumn
     *            The column to sort by
     * @param string  $sortDirection
     *            The direction to sort
     * @param string  $filterByColumn
     *            The column to filter by
     * @param string  $filterValue
     *            The value to filter with
     * @param number  $limit
     *            The number of records to retrieve
     * @param number  $offset
     *            The record to start at
     * @param boolean $justCount
     *            If set to true this function will return an integer of the row count of all available rows
     *
     * @return number|array Returns an array, or if justCount is true then it will count how many rows are
     *         available
     */
    public function getMatchupDevices ($sortColumn, $sortDirection, $filterByColumn = null, $filterValue = null, $limit = null, $offset = null, $justCount = false)
    {
        $db                         = Zend_Db_Table::getDefaultAdapter();
        $rmsDevicesTableName        = $this->getTableName();
        $rmsMasterMatchupsTableName = RmsMasterMatchupMapper::getInstance()->getTableName();
        $rmsProvidersTableName      = RmsProviderMapper::getInstance()->getTableName();
        $masterDevicesTableName     = MasterDeviceMapper::getInstance()->getTableName();
        $manufacturersTableName     = ManufacturerMapper::getInstance()->getTableName();

        $whereClause = [];
        if (strcasecmp($filterByColumn, 'printer') === 0 && $filterValue !== null)
        {
            $whereClause ["CONCAT({$rmsDevicesTableName}.manufacturer, \" \", {$rmsDevicesTableName}.modelName) LIKE ?"] = "%{$filterValue}%";
        }
        else if (strcasecmp($filterByColumn, 'model') === 0 && $filterValue !== null)
        {
            $whereClause ["{$rmsDevicesTableName}.rmsModelId = ?"] = $filterValue;
        }
        else if (strcasecmp($filterByColumn, 'onlyUnmapped') === 0 && $filterValue !== null)
        {
            $whereClause ["{$rmsMasterMatchupsTableName}.masterDeviceId IS ?"] = new Zend_Db_Expr('NULL');
        }

        /*
         * Based on what we want to do, we may only want the count
         */
        if ($justCount)
        {
            $rmsDeviceColumns = [
                'count' => 'COUNT(*)',
            ];

            // Make sure we don't select any other columns
            $rmsProviderColumns      = [];
            $rmsMasterMatchupColumns = [];
            $masterDeviceColumns     = [];
            $manufacturerColumns     = [];
        }
        else
        {
            // These are all the columns we want to be selecting
            $rmsDeviceColumns        = [
                'rmsProviderId',
                'rmsModelId',
                'rmsProviderDeviceName' => "CONCAT({$rmsDevicesTableName}.manufacturer, \" \", {$rmsDevicesTableName}.modelName)",
                'is_mapped'             => "IF({$rmsMasterMatchupsTableName}.masterDeviceId IS NOT NULL, 1, 0)",
            ];
            $rmsProviderColumns      = [
                'rmsProviderName' => 'name',
            ];
            $rmsMasterMatchupColumns = [];
            $masterDeviceColumns     = [
                'modelName',
                'masterDeviceId' => 'id',
            ];
            $manufacturerColumns     = [
                'displayname',
            ];
        }

        /*
         * Here we create our select statement
         */
        $zendDbSelect = $db->select()
                           ->from($rmsDevicesTableName, $rmsDeviceColumns)
                           ->join($rmsProvidersTableName, "{$rmsDevicesTableName}.`rmsProviderId` = {$rmsProvidersTableName}.`id`", $rmsProviderColumns)
                           ->joinLeft($rmsMasterMatchupsTableName, "{$rmsDevicesTableName}.`rmsProviderId` = {$rmsMasterMatchupsTableName}.`rmsProviderId` AND {$rmsDevicesTableName}.`rmsModelId` = {$rmsMasterMatchupsTableName}.`rmsModelId`", $rmsMasterMatchupColumns)
                           ->joinLeft($masterDevicesTableName, "{$rmsMasterMatchupsTableName}.`masterDeviceId` = {$masterDevicesTableName}.`id`", $masterDeviceColumns)
                           ->joinLeft($manufacturersTableName, "{$masterDevicesTableName}.`manufacturerId` = {$manufacturersTableName}.`id`", $manufacturerColumns);

        // Apply our where clause
        foreach ($whereClause as $cond => $value)
        {
            $zendDbSelect->where($cond, $value);
        }

        if ($limit > 0)
        {
            $offset = ($offset > 0) ? $offset : null;
            $zendDbSelect->limit($limit, $offset);
        }
        // TODO: Process a where clause here


        // If we're just counting we only need to return the count
        if ($justCount)
        {
            $zendDbStatement = $db->query($zendDbSelect);

            return $zendDbStatement->fetchColumn();
        }
        else
        {
            $zendDbSelect->order("{$rmsProvidersTableName}.name ASC")->order("{$sortColumn} {$sortDirection}");
            $zendDbStatement = $db->query($zendDbSelect);

            return $zendDbStatement->fetchAll();
        }
    }
}