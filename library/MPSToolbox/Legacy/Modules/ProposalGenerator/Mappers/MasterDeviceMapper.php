<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceSearchResultModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use My_Model_Mapper_Abstract;
use Tangent\Grid\Filter;
use Tangent\Grid\Order\Column;
use Zend_Auth;
use Zend_Db_Expr;
use Zend_Db_Table;
use Zend_Db_Table_Select;

/**
 * Class MasterDeviceMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class MasterDeviceMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_id             = 'id';
    public $col_manufacturerId = 'manufacturerId';
    public $col_modelName      = 'modelName';
    public $col_isCopier       = 'isCopier';
    public $col_tonerConfigId  = 'isCopier';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\MasterDeviceDbTable';

    protected $columnAliases = [];

    /**
     * Gets an instance of the mapper
     *
     * @return MasterDeviceMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    function __construct ()
    {
        $this->columnAliases = [
            'deviceName' => "CONCAT(manufacturers.displayname, \" \", master_devices.modelName)",
        ];
    }


    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object MasterDeviceModel
     *                The object to insert
     * @return int The primary key of the new row
     */
    public function insert ($object)
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

        #--
        $identity = \Zend_Auth::getInstance()->getIdentity();
        $db = \Zend_Db_Table::getDefaultAdapter();
        $sql = "insert into `history` set userId={$identity->id}, masterDeviceId={$object->id}, action='Created'";
        if (!$object->isSystemDevice) $sql.=", dealerId={$identity->dealerId}";
        $db->query($sql);
        #--

        return $id;
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel to the database.
     *
     * @param $object     MasterDeviceModel
     *                    The masterDevice model to save to the database
     * @param $primaryKey mixed
     *                    Optional: The original primary key, in case we're changing it
     *
     * @return string The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());

        if ($primaryKey === null)
        {
            $primaryKey = $data [$this->col_id];
        }

        $changed_fields = $this->changed_fields($this->getDbTable()->find($data [$this->col_id])->current(), $object);

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_id} = ?" => $primaryKey,
        ]);

        if ($rowsAffected) {
            $identity = \Zend_Auth::getInstance()->getIdentity();
            $db = \Zend_Db_Table::getDefaultAdapter();

            foreach ($changed_fields as $key=>$value) {
                if ($key=='imageUrl') continue;
                if (preg_match('#^is([A-Z].*)$#',$key,$match)) {
                    $key=$match[1];
                    $value = $value?'Yes':'No';
                }
                $sql = "insert into `history` set `userId`={$identity->id}, `masterDeviceId`={$object->id}, `action`='Changed `{$key}` to: ".addslashes($value)."'";
                if (!$object->isSystemDevice) $sql .= ", dealerId={$identity->dealerId}";
                $db->query($sql);
            }
        }

        // Save the object into the cache
        $this->saveItemToCache($object);
        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof MasterDeviceModel)
        {
            $whereClause = [
                "{$this->col_id} = ?" => $object->id,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_id} = ?" => $object,
            ];
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a masterDevice based on it's primaryKey
     *
     * @param $id int
     *            The id of the masterDevice to find
     *
     * @return MasterDeviceModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof MasterDeviceModel)
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
        $object = new MasterDeviceModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a masterDevice
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return MasterDeviceModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new MasterDeviceModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all masterDevices
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
     * @return MasterDeviceModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new MasterDeviceModel($row->toArray());

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
            "{$this->col_id} = ?" => $id,
        ];
    }

    /**
     * Get all the printer model with the wild cards %<modelName>%
     *
     * @param string $criteria
     * @param null   $order
     * @param int    $count
     * @param null   $offset
     *
     * @return MasterDeviceModel[]
     */
    public function fetchAllLikePrinterModel ($criteria, $order = null, $count = 25, $offset = null)
    {
        return $this->fetchAll(["{$this->col_modelName} LIKE ?" => "%{$criteria}%"], $order, $count, $offset);
    }

    /**
     * Get all master devices that match the manufacturer id that has been passed.
     *
     * @param string $criteria
     * @param null   $order
     * @param int    $count
     * @param null   $offset
     *
     * @return MasterDeviceModel[]
     */
    public function fetchAllByManufacturerId ($criteria, $order = null, $count = 25, $offset = null)
    {
        return $this->fetchAll(["{$this->col_manufacturerId} = ?" => $criteria], $order, $count, $offset);
    }

    /**
     * Get all master devices that match the manufacturer full name that has been passed.
     *
     * @param string $criteria
     * @param null   $order
     * @param int    $count
     * @param null   $offset
     *
     * @return MasterDeviceModel[]
     */
    public function fetchAllByManufacturerFullName ($criteria, $order = null, $count = 25, $offset = null)
    {
        $manufacturerMapper = ManufacturerMapper::getInstance();
        $manufacturer       = $manufacturerMapper->fetch(["{$manufacturerMapper->col_fullName} = ?" => $criteria]);
        if ($manufacturer)
        {
            return $this->fetchAll(["{$this->col_manufacturerId} = ?" => $manufacturer->id], $order, $count, $offset);
        }

        return false;
    }

    /**
     * Fetches all master devices that are available to be used in the quote generator.
     *
     * @return MasterDeviceModel[]
     */
    public function fetchAllAvailableMasterDevices ()
    {
        $qdTableName = DeviceMapper::getInstance()->getTableName();

        $sql = "SELECT * FROM {$this->getTableName()} AS md
        LEFT JOIN {$qdTableName} AS qd ON qd.masterDeviceId = md.id
        WHERE qd.masterDeviceId IS NULL
        ORDER BY  md.manufacturer_id ASC, md.printer_model ASC
        ";

        $resultSet = $this->getDbTable()
                          ->getAdapter()
                          ->fetchAll($sql);

        $entries = [];
        foreach ($resultSet as $row)
        {
            $object = new MasterDeviceModel($row);

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Gets a special column. Returns the same that is passed in if no special declaration exists for the column
     *
     * @param $filterIndex
     *
     * @return string
     */
    protected function getColumn ($filterIndex)
    {
        return (isset($this->columnAliases[$filterIndex])) ? $this->columnAliases[$filterIndex] : $filterIndex;
    }

    /**
     * This function fetches match up devices
     *
     * @param Column[]                $sortOrderBy      The column to sort by
     * @param Filter\AbstractFilter[] $filters          The column to filter by
     * @param number                  $limit            The number of records to retrieve
     * @param number                  $offset           The record to start at
     * @param bool                    $onlyQuoteDevices Whether to filter out only devices that are quotable/salable
     * @param bool                    $justCount        Whether or not to perform a count operation
     *
     * @return number|array Returns an array, or if justCount is true then it
     *                      will count how many rows are available
     */
    public function getCanSellMasterDevices ($sortOrderBy, $filters = null, $limit = null, $offset = null, $onlyQuoteDevices, $justCount = false)
    {
        $db                     = Zend_Db_Table::getDefaultAdapter();
        $masterDevicesTableName = MasterDeviceMapper::getInstance()->getTableName();
        $manufacturerTableName  = ManufacturerMapper::getInstance()->getTableName();
        $deviceTableName        = DeviceMapper::getInstance()->getTableName();
        $manufacturerColumns    = [];

        if ($justCount)
        {
            /**
             * Just counting here! We need to remove all the other columns
             * that we might be selecting to ensure we get a proper count.
             */
            // Make sure we don't select any other columns
            if ($onlyQuoteDevices)
            {
                $deviceColumns       = ['count' => 'COUNT(*)'];
                $masterDeviceColumns = [];
            }
            else
            {
                $masterDeviceColumns = ['count' => 'COUNT(*)'];
                $deviceColumns       = [];
            }

        }
        else
        {
            /**
             * Setup the columns we want to select
             */
            $masterDeviceColumns = [
                'id',
                'modelName',
                'isSystemDevice',
            ];
            $deviceColumns       = [
                'oemSku',
                'dealerSku',
                'online',
                'rent',
                'srp'
            ];
            $manufacturerColumns = [
                'displayname',
            ];
        }

        /**
         * Build our where array
         */
        $whereClause = [];
        foreach ($filters as $filter)
        {
            $whereColumn = $this->getColumn($filter->getFilterIndex());

            if ($filter instanceof Filter\Is)
            {
                if (is_bool($filter->getFilterValue()))
                {
                    $whereClause["{$whereColumn} = ?"] = $filter->getFilterValue();
                }
                else
                {
                    $whereClause["{$whereColumn} = ?"] = $filter->getFilterValue();
                }
            }
            elseif ($filter instanceof Filter\IsNot)
            {
                if (is_bool($filter->getFilterValue()))
                {
                    $whereClause["{$whereColumn} <> ?"] = $filter->getFilterValue();
                }
                else
                {
                    $whereClause["{$whereColumn} <> ?"] = $filter->getFilterValue();
                }
            }
            elseif ($filter instanceof Filter\Contains)
            {
                $whereClause["{$whereColumn} LIKE ?"] = "%" . $filter->getFilterValue() . "%";
            }
            elseif ($filter instanceof Filter\DoesNotContain)
            {
                $whereClause["{$whereColumn} NOT LIKE ?"] = "%" . $filter->getFilterValue() . "%";
            }
        }

        /*
         * Here we create our select statement
         */
        if ($onlyQuoteDevices)
        {
            $zendDbSelect = $db->select()->from($masterDevicesTableName, $masterDeviceColumns)
                               ->join($manufacturerTableName, "{$manufacturerTableName}.`id` = {$masterDevicesTableName}.`manufacturerId`", $manufacturerColumns)
                               ->join($deviceTableName, "{$masterDevicesTableName}.`id` = {$deviceTableName}.`masterDeviceId`" . ' && ' . $deviceTableName . '.dealerId = ' . Zend_Auth::getInstance()->getIdentity()->dealerId, $deviceColumns);
        }
        else
        {
            $zendDbSelect = $db->select()->from($masterDevicesTableName, $masterDeviceColumns)
                               ->join($manufacturerTableName, "{$manufacturerTableName}.`id` = {$masterDevicesTableName}.`manufacturerId`", $manufacturerColumns)
                               ->joinLeft($deviceTableName, "{$masterDevicesTableName}.`id` = {$deviceTableName}.`masterDeviceId`" . ' && ' . $deviceTableName . '.dealerId = ' . Zend_Auth::getInstance()->getIdentity()->dealerId, $deviceColumns);
        }

        /**
         * Apply the where clauses
         */
        foreach ($whereClause as $condition => $value)
        {
            $zendDbSelect->where($condition, $value);
        }

        /**
         * Apply our limit
         */
        if ($limit > 0)
        {
            $offset = ($offset > 0) ? $offset : null;
            $zendDbSelect->limit($limit, $offset);
        }

        /**
         * Only return the count if that's all we're doing
         */
        if ($justCount)
        {
            $zendDbStatement = $db->query($zendDbSelect);

            return $zendDbStatement->fetchColumn();
        }
        else
        {
            foreach ($sortOrderBy as $columnName => $column)
            {
                $sortColumn = $this->getColumn($columnName);

                $sortDirection = ($column->orderIsAscending()) ? 'ASC' : 'DESC';

                $zendDbSelect->order("{$sortColumn} {$sortDirection}");
            }

            $zendDbStatement = $db->query($zendDbSelect);

            return $zendDbStatement->fetchAll();
        }
    }

    /**
     * @param      $sortColumn
     * @param      $sortDirection
     * @param int  $dealerId
     * @param null $filterByColumn
     * @param null $filterValue
     * @param null $limit
     * @param null $offset
     * @param bool $justCount
     *
     * @return MasterDeviceModel[]
     */
    public function fetchAllMasterDevices ($sortColumn, $sortDirection, $dealerId, $filterByColumn = null, $filterValue = null, $limit = null, $offset = null, $justCount = false)
    {
        $db                          = Zend_Db_Table::getDefaultAdapter();
        $masterDevicesTableName      = MasterDeviceMapper::getInstance()->getTableName();
        $manufacturerTableName       = ManufacturerMapper::getInstance()->getTableName();
        $dealerMasterDeviceTableName = DealerMasterDeviceAttributeMapper::getInstance()->getTableName();

        $whereClause = [];
        if (strcasecmp($filterByColumn, 'modelName') === 0 && $filterValue !== '')
        {
            $whereClause ["CONCAT({$manufacturerTableName}.fullname, \" \", {$masterDevicesTableName}.modelName) LIKE ?"] = "%{$filterValue}%";
        }
        elseif (strcasecmp($filterByColumn, 'manufacturerId') === 0 && $filterValue > 0)
        {
            $whereClause ["{$manufacturerTableName}.id = ?"] = "$filterValue";
        }

        if ($justCount)
        {
            $masterDeviceColumns       = ['count' => 'COUNT(*)'];
            $manufacturerColumns       = null;
            $dealerMasterDeviceColumns = null;
        }
        else
        {
            $masterDeviceColumns       = [
                'id',
                'manufacturerId',
                'modelName',
            ];
            $manufacturerColumns       = [
                'fullname',
                'displayname',
            ];
            $dealerMasterDeviceColumns = [
                'laborCostPerPage',
                'partsCostPerPage',
            ];
        }

        /*
         * Here we create our select statement
         */
        $zendDbSelect = $db->select()->from($masterDevicesTableName, $masterDeviceColumns);
        if (!$justCount)
        {
            $zendDbSelect->join($manufacturerTableName, "{$manufacturerTableName}.`id` = {$masterDevicesTableName}.`manufacturerId`", $manufacturerColumns);
            $zendDbSelect->joinleft($dealerMasterDeviceTableName, "{$dealerMasterDeviceTableName}.`masterDeviceid` = {$masterDevicesTableName}.`id` AND {$dealerMasterDeviceTableName}.dealerId = ?", $dealerMasterDeviceColumns);
            $zendDbSelect->bind($dealerId);
        }
        else if ($filterByColumn)
        {
            $zendDbSelect->join($manufacturerTableName, "{$manufacturerTableName}.`id` = {$masterDevicesTableName}.`manufacturerId`", $manufacturerColumns);
            $zendDbSelect->joinleft($dealerMasterDeviceTableName, "{$dealerMasterDeviceTableName}.`masterDeviceid` = {$masterDevicesTableName}.`id` AND {$dealerMasterDeviceTableName}.dealerId = ?", $dealerMasterDeviceColumns);
            $zendDbSelect->bind($dealerId);
        }

        // Apply the limit/offset
        $zendDbSelect->limit($limit, $offset);

        // Apply our where clause
        foreach ($whereClause as $cond => $value)
        {
            $zendDbSelect->where($cond, $value);
        }

        // If we're just counting we only need to return the count
        if ($justCount)
        {
            $zendDbStatement = $db->query($zendDbSelect);

            return $zendDbStatement->fetchColumn();
        }
        else
        {
            if ($sortColumn == 'printer_model')
            {
                $sortColumn = 'modelName';
                $zendDbSelect->order("{$masterDevicesTableName}.{$sortColumn} {$sortDirection}");
            }
            if ($sortColumn == 'manufacturerId')
            {
                $sortColumn = 'fullname';
                $zendDbSelect->order("{$manufacturerTableName}.{$sortColumn} {$sortDirection}");
            }
            if ($sortColumn == 'labor_cost_per_page_dealer')
            {
                $sortColumn = 'laborCostPerPage';
                $zendDbSelect->order("{$dealerMasterDeviceTableName}.{$sortColumn} {$sortDirection}");
            }
            if ($sortColumn == 'parts_cost_per_page_dealer')
            {
                $sortColumn = 'partsCostPerPage';
                $zendDbSelect->order("{$dealerMasterDeviceTableName}.{$sortColumn} {$sortDirection}");
            }

            $zendDbStatement     = $db->query($zendDbSelect);
            $masterDeviceResults = $zendDbStatement->fetchAll();
            $masterDevices       = [];
            foreach ($masterDeviceResults as $masterDeviceResult)
            {
                $object = new MasterDeviceModel($masterDeviceResult);
                $this->saveItemToCache($object);
                $masterDevices[] = $object;
            }

            return $masterDevices;
        }
    }

    /**
     * @param MasterDeviceModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Searches the master devices by modelName (uses LIKE %NAME% in where clause)
     * If a manufacturer id is supplied it is used to refine the search
     *
     * @param string $modelName
     * @param int    $manufacturerId
     *
     * @param bool   $useWildCards Defaults to true. Will wrap the modelName in wildcards
     *
     * @return MasterDeviceModel[]
     */
    public function searchByModelName ($modelName, $manufacturerId, $useWildCards = true)
    {
        if ($useWildCards)
        {
            $modelName = "%{$modelName}%";
        }

        $whereClause = [
            "{$this->col_modelName} LIKE ?" => $modelName,
        ];

        if ($manufacturerId !== null)
        {
            $whereClause["{$this->col_manufacturerId} = ?"] = "{$manufacturerId}";
        }

        return $this->fetchAll($whereClause);
    }

    public function fetchByNameAndManufacturer($modelName, $manufacturerId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare('select id from master_devices where modelName=:modelName and manufacturerId=:manufacturerId');
        $st->execute(['modelName'=>$modelName, 'manufacturerId'=>$manufacturerId]);
        $arr = $st->fetchAll();
        if (empty($arr) || (count($arr)!=1)) return null;
        return $this->find($arr[0]['id']);
    }

    /**
     * Returns results from a custom query that combines manufacturer and master device tables.
     * It returns a list of devices that match (like statement) the search terms that have been passed.
     * You can search by manufacturer name (manufacturers table), model name (master_device table) an
     * or both.
     *
     * @param string   $searchTerm
     *
     * @param null|int $manufacturerId
     *
     * @return MasterDeviceSearchResultModel[]
     */
    public function searchByName ($searchTerm, $manufacturerId = null)
    {
        $masterDeviceSearchResults = [];
        $manufacturerMapper        = ManufacturerMapper::getInstance();

        $returnLimit = 10;
        $sortOrder   = 'masterDeviceFullDeviceName ASC';

        // TODO lrobert: 2014/07/07 - This is unused now but it might be needed.
        // Should be removed if no longer used once the refactor of searching for devices is finished
        $deviceType = new Zend_Db_Expr("CASE WHEN md.{$this->col_isCopier} AND md.tonerConfigId = 1 THEN 'Monochrome MFP'
            WHEN md.isCopier AND md.tonerConfigId > 1 THEN 'Color MFP'
            WHEN NOT md.isCopier AND md.tonerConfigId > 1 THEN 'Color '
            WHEN NOT md.isCopier AND md.tonerConfigId = 1 THEN 'Monochrome'
            END");

        $fullDeviceNameUsingDisplayManufacturerName = "CONCAT({$manufacturerMapper->col_displayName},' ', {$this->col_modelName})";
        $fullDeviceNameUsingFullManufacturerName    = "CONCAT({$manufacturerMapper->col_fullName},' ', {$this->col_modelName})";


        $db     = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select
            ->from(
                [
                    'md' => $this->getTableName(),
                ],
                [
                    'masterDeviceId'             => $this->col_id,
                    'masterDeviceModelName'      => $this->col_modelName,
                    'masterDeviceFullDeviceName' => $fullDeviceNameUsingDisplayManufacturerName,
                    'masterDeviceTonerConfigId'  => $this->col_tonerConfigId,
                    'masterDeviceIsMfp'          => $this->col_isCopier,
                    'masterDeviceIsColor'        => "IF({$this->col_tonerConfigId} = 1, true, false)",
                ]
            )
            ->joinLeft(
                [
                    'm' => $manufacturerMapper->getTableName(),
                ],
                "m.{$manufacturerMapper->col_id} = md.{$this->col_manufacturerId}",
                [
                    'manufacturerId'          => $manufacturerMapper->col_id,
                    'manufacturerDisplayName' => $manufacturerMapper->col_displayName,
                    'manufacturerFullName'    => $manufacturerMapper->col_fullName,
                ]
            )
            ->where("({$fullDeviceNameUsingDisplayManufacturerName} LIKE :searchTerm OR {$fullDeviceNameUsingFullManufacturerName} LIKE :searchTerm)")
            ->where("m.{$manufacturerMapper->col_isDeleted} = 0")
            ->limit($returnLimit)
            ->order($sortOrder);

        /*
         * Filter by manufacturer id if provided
         */
        if ($manufacturerId)
        {
            $select->where("m.{$manufacturerMapper->col_id} = ?", $manufacturerId);
        }

        $stmt = $db->query($select, [
            'searchTerm' => $searchTerm,
        ]);

        foreach ($stmt->fetchAll() as $result)
        {
            $masterDeviceSearchResult = new MasterDeviceSearchResultModel($result);

            $masterDeviceSearchResults[] = $masterDeviceSearchResult;
        }

        return $masterDeviceSearchResults;
    }

    /**
     * @param     $masterDeviceId
     * @param     $dealerId
     *
     * @return bool|MasterDeviceModel
     */
    public function findForReports ($masterDeviceId, $dealerId)
    {
        $cacheKey = "findForReports_{$dealerId}";

        $result = $this->getItemFromCache($masterDeviceId, $cacheKey);
        if ($result instanceof MasterDeviceModel)
        {
            return $result;
        }

        $db       = Zend_Db_Table::getDefaultAdapter();
        $dealerId = intval($dealerId);
        $select   = $db->select()
                       ->from(['pmd' => 'master_devices'], [
                           'pmd.*',
                       ])
                       ->joinLeft(['dmda' => 'dealer_master_device_attributes'], "pmd.id = dmda.masterDeviceId AND dmda.dealerId = {$dealerId}", [
                           "calculatedPartsCostPerPage"    => "COALESCE(dmda.partsCostPerPage, NULL)",
                           "calculatedLaborCostPerPage"    => "COALESCE(dmda.laborCostPerPage, NULL)",
                           "isUsingDealerPartsCostPerPage" => "(dmda.partsCostPerPage IS NOT NULL)",
                           "isUsingDealerLaborCostPerPage" => "(dmda.laborCostPerPage IS NOT NULL)",
                       ])
                       ->where("pmd.id = ? ", $masterDeviceId);

        $stmt = $db->query($select);

        $result = $stmt->fetch();
        $object = false;

        if ($result)
        {
            $object = new MasterDeviceModel($result);
            $this->saveItemToCache($object, $cacheKey);
        }

        return $object;
    }

    /**
     * Gets the required data required for exporting printer pricing
     *
     * @param $manufacturerId int
     * @param $dealerId       int
     *
     * @return array
     */
    public function getPrinterPricingForExport ($manufacturerId, $dealerId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        $select = $db->select()
                     ->from([
                         'md' => 'master_devices',
                     ], [
                         'id AS master_id',
                         'modelName',
                     ])
                     ->joinLeft([
                         'm' => 'manufacturers',
                     ], 'm.id = md.manufacturerId', [
                         'fullname',
                     ])
                     ->joinLeft(
                         ['dmda' => 'dealer_master_device_attributes'],
                         "dmda.masterDeviceId = md.id AND dmda.dealerId = {$dealerId}",
                         ['laborCostPerPage', 'partsCostPerPage'])
                     ->order([
                         'm.fullname',
                         'md.modelName',
                     ]);

        if ($manufacturerId > 0)
        {
            $select->where("manufacturerId = ?", $manufacturerId);
        }

        $stmt      = $db->query($select);
        $result    = $stmt->fetchAll();
        $fieldList = [];

        foreach ($result as $value)
        {
            $fieldList [] = [
                $value ['master_id'],
                $value ['fullname'],
                $value ['modelName'],
                $value ['laborCostPerPage'],
                $value ['partsCostPerPage'],
            ];
        }

        return $fieldList;
    }

    /**
     * Gets a list of all master devices inside the system and their features.
     *
     * @param $dealerId
     *
     * @return array
     */
    public function getPrinterFeaturesForExport ($dealerId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $dealerId  = $db->quote($dealerId, 'INT');
        $select    = $db->select()
                        ->from(
                            ['md' => 'master_devices',],
                            [
                                'id AS master_id',
                                'modelName',
                                'isDuplex',
                                'isCopier',
                                'isCapableOfReportingTonerLevels',
                                'ppmBlack',
                                'ppmColor',
                                'wattsPowerNormal',
                                'wattsPowerIdle',
                                'imageFile',
                                'IF(jcmd.masterDeviceId IS NULL,0,1) AS jitCompatible',
                            ])
                        ->joinLeft(
                            ['m' => 'manufacturers'],
                            'm.id = md.manufacturerId',
                            ['fullname']
                        )
                        ->joinLeft(
                            ['jcmd' => 'jit_compatible_master_devices'],
                            "jcmd.dealerId = {$dealerId} AND jcmd.masterDeviceId = md.id",
                            []
                        )
                        ->order([
                            'm.fullname',
                            'md.modelName',
                        ]);
        $stmt      = $db->query($select);
        $result    = $stmt->fetchAll();
        $fieldList = [];

        foreach ($result as $value)
        {
            $fieldList [] = [
                $value ['master_id'],
                $value ['fullname'],
                $value ['modelName'],
                $value ['isDuplex'],
                $value ['isCopier'],
                $value ['isCapableOfReportingTonerLevels'],
                $value ['ppmBlack'],
                $value ['ppmColor'],
                $value ['wattsPowerNormal'],
                $value ['wattsPowerIdle'],
                $value ['jitCompatible'],
                $value ['imageFile']?'Yes':'No',
            ];
        }

        return $fieldList;
    }
}