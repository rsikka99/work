<?php
/**
 * Class Proposalgen_Model_Mapper_MasterDevice
 */
class Proposalgen_Model_Mapper_MasterDevice extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_id = 'id';
    public $col_manufacturerId = 'manufacturerId';
    public $col_modelName = 'modelName';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_MasterDevice';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_MasterDevice
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_MasterDevice to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Proposalgen_Model_MasterDevice
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
     * Saves (updates) an instance of Proposalgen_Model_MasterDevice to the database.
     *
     * @param $object     Proposalgen_Model_MasterDevice
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
     *                This can either be an instance of Proposalgen_Model_MasterDevice or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Proposalgen_Model_MasterDevice)
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
     * Finds a masterDevice based on it's primaryKey
     *
     * @param $id int
     *            The id of the masterDevice to find
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Proposalgen_Model_MasterDevice)
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
        $object = new Proposalgen_Model_MasterDevice($row->toArray());

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
     * @return Proposalgen_Model_MasterDevice
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Proposalgen_Model_MasterDevice($row->toArray());

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
     * @return Proposalgen_Model_MasterDevice[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Proposalgen_Model_MasterDevice($row->toArray());

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
     * Get all the printer model with the wild cards %<modelName>%
     *
     * @param string $criteria
     * @param null   $order
     * @param int    $count
     * @param null   $offset
     *
     * @return Proposalgen_Model_MasterDevice[]
     */
    public function fetchAllLikePrinterModel ($criteria, $order = null, $count = 25, $offset = null)
    {
        return $this->fetchAll(array("{$this->col_modelName} LIKE ?" => "%{$criteria}%"), $order, $count, $offset);
    }

    /**
     * Get all master devices that match the manufacturer id that has been passed.
     *
     * @param string $criteria
     * @param null   $order
     * @param int    $count
     * @param null   $offset
     *
     * @return Proposalgen_Model_MasterDevice[]
     */
    public function fetchAllByManufacturerId ($criteria, $order = null, $count = 25, $offset = null)
    {
        return $this->fetchAll(array("{$this->col_manufacturerId} = ?" => $criteria), $order, $count, $offset);
    }

    /**
     * Get all master devices that match the manufacturer full name that has been passed.
     *
     * @param string $criteria
     * @param null   $order
     * @param int    $count
     * @param null   $offset
     *
     * @return Proposalgen_Model_MasterDevice[]
     */
    public function fetchAllByManufacturerFullName ($criteria, $order = null, $count = 25, $offset = null)
    {
        $manufacturerMapper = Proposalgen_Model_Mapper_Manufacturer::getInstance();
        $manufacturer       = $manufacturerMapper->fetch(array("{$manufacturerMapper->col_fullName} = ?" => $criteria));
        if ($manufacturer)
        {
            return $this->fetchAll(array("{$this->col_manufacturerId} = ?" => $manufacturer->id), $order, $count, $offset);
        }

        return false;
    }

    /**
     * Fetches all master devices that are available to be used in the quote generator.
     *
     * @return Proposalgen_Model_MasterDevice[]
     */
    public function fetchAllAvailableMasterDevices ()
    {
        $qdTableName = Quotegen_Model_Mapper_Device::getInstance()->getTableName();

        $sql = "SELECT * FROM {$this->getTableName()} AS md
        LEFT JOIN {$qdTableName} AS qd ON qd.masterDeviceId = md.id
        WHERE qd.masterDeviceId IS null
        ORDER BY  md.manufacturer_id ASC, md.printer_model ASC
        ";

        $resultSet = $this->getDbTable()
                          ->getAdapter()
                          ->fetchAll($sql);

        $entries = array();
        foreach ($resultSet as $row)
        {
            $object = new Proposalgen_Model_MasterDevice($row);

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * This function fetches match up devices
     *
     * @param string  $sortColumn
     *              The column to sort by
     * @param string  $sortDirection
     *              The direction to sort
     * @param string  $filterByColumn
     *              The column to filter by
     * @param string  $filterValue
     *              The value to filter with
     * @param number  $limit
     *              The number of records to retrieve
     * @param number  $offset
     *              The record to start at
     * @param boolean $canSell
     *              If the we are searching for sellable devices
     * @param boolean $justCount
     *              If set to true this function will return an integer of the row count of all available rows
     *
     * @return number|array Returns an array, or if justCount is true then it will count how many rows are
     *         available
     */
    public function getCanSellMasterDevices ($sortColumn, $sortDirection, $filterByColumn = null, $filterValue = null, $limit = null, $offset = null, $canSell = false, $approved = false, $justCount = false)
    {
        $db                     = Zend_Db_Table::getDefaultAdapter();
        $masterDevicesTableName = Proposalgen_Model_Mapper_MasterDevice::getInstance()->getTableName();
        $manufacturerTableName  = Proposalgen_Model_Mapper_Manufacturer::getInstance()->getTableName();
        $deviceTableName        = Quotegen_Model_Mapper_Device::getInstance()->getTableName();
        $manufacturerColumns    = array();

        $whereClause = array();
        if (strcasecmp($filterByColumn, 'deviceName') === 0 && $filterValue !== '')
        {
            $whereClause ["CONCAT({$manufacturerTableName}.displayname, \" \", {$masterDevicesTableName}.modelName) LIKE ?"] = "%{$filterValue}%";
        }
        elseif (strcasecmp($filterByColumn, 'oemSku') === 0 && $filterValue !== '')
        {
            $whereClause ["oemSku LIKE ?"] = "%{$filterValue}%";
        }
        else if (strcasecmp($filterByColumn, 'dealerSku') === 0 && $filterValue !== '')
        {
            $whereClause ["dealerSku LIKE ?"] = "%{$filterValue}%";
        }
        if ($approved)
        {
            $whereClause ["isSystemDevice = ?"] = "0";
        }

        if ($justCount)
        {

            // Make sure we don't select any other columns
            if ($canSell)
            {
                $deviceColumns       = array('count' => 'COUNT(*)');
                $masterDeviceColumns = null;
            }
            else
            {
                $masterDeviceColumns = array('count' => 'COUNT(*)');
                $deviceColumns       = null;
            }

        }
        else
        {
            $masterDeviceColumns = array(
                'id',
                'modelName',
                'isSystemDevice'
            );
            $deviceColumns       = array(
                'oemSku',
                'dealerSku'
            );
            $manufacturerColumns = array(
                'displayname'
            );
        }

        /*
         * Here we create our select statement
         */
        if ($canSell)
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

        // If we're just counting we only need to return the count
        if ($justCount)
        {
            $zendDbStatement = $db->query($zendDbSelect);

            return $zendDbStatement->fetchColumn();
        }
        else
        {
            if ($sortColumn == "modelName")
            {
                $sortColumn = "CONCAT({$manufacturerTableName}.fullname, \" \", {$masterDevicesTableName}.modelName)";
            }

            $zendDbSelect->order("{$sortColumn} {$sortDirection}");
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
     * @return Proposalgen_Model_MasterDevice[]
     */
    public function fetchAllMasterDevices ($sortColumn, $sortDirection, $dealerId, $filterByColumn = null, $filterValue = null, $limit = null, $offset = null, $justCount = false)
    {
        $db                          = Zend_Db_Table::getDefaultAdapter();
        $masterDevicesTableName      = Proposalgen_Model_Mapper_MasterDevice::getInstance()->getTableName();
        $manufacturerTableName       = Proposalgen_Model_Mapper_Manufacturer::getInstance()->getTableName();
        $dealerMasterDeviceTableName = Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->getTableName();

        $whereClause = array();
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
            $masterDeviceColumns       = array('count' => 'COUNT(*)');
            $manufacturerColumns       = null;
            $dealerMasterDeviceColumns = null;
        }
        else
        {
            $masterDeviceColumns       = array(
                'id',
                'manufacturerId',
                'modelName'
            );
            $manufacturerColumns       = array(
                'fullname',
                'displayname'
            );
            $dealerMasterDeviceColumns = array(
                'laborCostPerPage',
                'partsCostPerPage'
            );
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
            $masterDevices       = array();
            foreach ($masterDeviceResults as $masterDeviceResult)
            {
                $object = new Proposalgen_Model_MasterDevice($masterDeviceResult);
                $this->saveItemToCache($object);
                $masterDevices[] = $object;
            }

            return $masterDevices;
        }
    }

    /**
     * @param Proposalgen_Model_MasterDevice $object
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
     * @return Proposalgen_Model_MasterDevice[]
     */
    public function searchByModelName ($modelName, $manufacturerId, $useWildCards = true)
    {
        if ($useWildCards)
        {
            $modelName = "%{$modelName}%";
        }

        $whereClause = array(
            "{$this->col_modelName} LIKE ?" => $modelName
        );

        if ($manufacturerId !== null)
        {
            $whereClause["{$this->col_manufacturerId} = ?"] = "{$manufacturerId}";
        }

        return $this->fetchAll($whereClause);
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
     * @return array
     */
    public function searchByName ($searchTerm, $manufacturerId = null)
    {
        $manufacturerMapper = Proposalgen_Model_Mapper_Manufacturer::getInstance();

        $returnLimit = 10;
        $sortOrder   = 'device_name ASC';

        $caseStatement = new Zend_Db_Expr("CASE WHEN md.isCopier AND md.tonerConfigId = 1 THEN 'Monochrome MFP'
            WHEN md.isCopier AND md.tonerConfigId > 1 THEN 'Color MFP'
            WHEN NOT md.isCopier AND md.tonerConfigId > 1 THEN 'Color '
            WHEN NOT md.isCopier AND md.tonerConfigId = 1 THEN 'Monochrome'
            END AS deviceType");

        $db     = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array("md" => $this->getTableName()), array('modelName', 'id', $caseStatement))
               ->joinLeft(array("m" => $manufacturerMapper->getTableName()), "m.{$manufacturerMapper->col_id} = md.{$this->col_manufacturerId}", array($manufacturerMapper->col_fullName, "device_name" => new Zend_Db_Expr("concat({$manufacturerMapper->col_fullName},' ', {$this->col_modelName})")))
               ->where("concat({$manufacturerMapper->col_fullName},' ', {$this->col_modelName}) LIKE ? AND m.isDeleted = 0")
               ->limit($returnLimit)
               ->order($sortOrder);

        /*
         * Filter by manufacturer id if provided
         */
        if ($manufacturerId)
        {
            $select->where("m.{$manufacturerMapper->col_id} = ?", $manufacturerId);
        }

        $stmt   = $db->query($select, $searchTerm);
        $result = $stmt->fetchAll();

        return $result;
    }

    /**
     * @param     $masterDeviceId
     * @param     $dealerId
     * @param int $defaultLaborCostPerPage
     * @param int $defaultPartsCostPerPage
     *
     * @return bool|Proposalgen_Model_MasterDevice
     */
    public function findForReports ($masterDeviceId, $dealerId, $defaultLaborCostPerPage = 0, $defaultPartsCostPerPage = 0)
    {

        $db                      = Zend_Db_Table::getDefaultAdapter();
        $defaultPartsCostPerPage = $db->quote($defaultPartsCostPerPage, 'FLOAT');
        $defaultLaborCostPerPage = $db->quote($defaultLaborCostPerPage, 'FLOAT');
        $dealerId                = $db->quote($dealerId, 'INT');
        $select                  = $db->select()
                                      ->from(array('pmd' => 'master_devices'), array(
                                                                                    'pmd.*',
                                                                               ))
                                      ->joinLeft(array('dmda' => 'dealer_master_device_attributes'), "pmd.id = dmda.masterDeviceId AND dmda.dealerId = {$dealerId}", array(
                                                                                                                                                                          "calculatedPartsCostPerPage"    => "COALESCE(dmda.partsCostPerPage, pmd.partsCostPerPage, {$defaultPartsCostPerPage})",
                                                                                                                                                                          "calculatedLaborCostPerPage"    => "COALESCE(dmda.laborCostPerPage, pmd.laborCostPerPage, {$defaultLaborCostPerPage})",
                                                                                                                                                                          "isUsingDealerPartsCostPerPage" => "(dmda.partsCostPerPage IS NOT NULL)",
                                                                                                                                                                          "isUsingDealerLaborCostPerPage" => "(dmda.laborCostPerPage IS NOT NULL)",
                                                                                                                                                                          "isUsingDeviceLaborCostPerPage" => "(pmd.laborCostPerPage IS NOT NULL AND dmda.laborCostPerPage IS NULL)",
                                                                                                                                                                          "isUsingDevicePartsCostPerPage" => "(pmd.partsCostPerPage IS NOT NULL AND dmda.partsCostPerPage IS NULL)",
                                                                                                                                                                          "isUsingReportLaborCostPerPage" => "(pmd.laborCostPerPage IS NULL AND dmda.laborCostPerPage IS NULL)",
                                                                                                                                                                          "isUsingReportPartsCostPerPage" => "(pmd.partsCostPerPage IS NULL AND dmda.partsCostPerPage IS NULL)"
                                                                                                                                                                     ))
                                      ->where("pmd.id = ? ", $masterDeviceId);

        $stmt = $db->query($select);

        $result = $stmt->fetch();
        $object = false;

        if ($result)
        {
            $object = new Proposalgen_Model_MasterDevice($result);
            $this->saveItemToCache($object);
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
                     ->from(array(
                                 'md' => 'master_devices'
                            ), array(
                                    'id AS master_id',
                                    'modelName',
                               ))
                     ->joinLeft(array(
                                     'm' => 'manufacturers'
                                ), 'm.id = md.manufacturerId', array(
                                                                    'fullname'
                                                               ))
                     ->joinLeft(array(
                                     'dmda' => 'dealer_master_device_attributes'
                                ), "dmda.masterDeviceId = md.id AND dmda.dealerId = {$dealerId}",
                array(
                     'laborCostPerPage',
                     'partsCostPerPage',
                ))
                     ->order(array(
                                  'm.fullname',
                                  'md.modelName',
                             ));

        if ($manufacturerId > 0)
        {
            $select->where("manufacturerId = ?", $manufacturerId);
        }

        $stmt      = $db->query($select);
        $result    = $stmt->fetchAll();
        $fieldList = array();

        foreach ($result as $value)
        {
            $fieldList [] = array(
                $value ['master_id'],
                $value ['fullname'],
                $value ['modelName'],
                $value ['laborCostPerPage'],
                $value ['partsCostPerPage'],
            );
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
                        ->from(array(
                                    'md' => 'master_devices'
                               ), array(
                                       'id AS master_id',
                                       'modelName',
                                       'isDuplex',
                                       'isCopier',
                                       'reportsTonerLevels',
                                       'ppmBlack',
                                       'ppmColor',
                                       'dutyCycle',
                                       'wattsPowerNormal',
                                       'wattsPowerIdle',
                                       'IF(jcmd.masterDeviceId IS NULL,0,1) AS jitCompatible',
                                  ))
                        ->joinLeft(array(
                                        'm' => 'manufacturers'
                                   ), 'm.id = md.manufacturerId', array(
                                                                       'fullname'
                                                                  ))
                        ->joinLeft(array(
                                        'jcmd' => 'jit_compatible_master_devices'
                                   ), "jcmd.dealerId = {$dealerId} AND jcmd.masterDeviceId = md.id", array())
                        ->order(array(
                                     'm.fullname',
                                     'md.modelName',
                                ));
        $stmt      = $db->query($select);
        $result    = $stmt->fetchAll();
        $fieldList = array();

        foreach ($result as $value)
        {
            $fieldList [] = array(
                $value ['master_id'],
                $value ['fullname'],
                $value ['modelName'],
                $value ['isDuplex'],
                $value ['isCopier'],
                $value ['reportsTonerLevels'],
                $value ['ppmBlack'],
                $value ['ppmColor'],
                $value ['dutyCycle'],
                $value ['wattsPowerNormal'],
                $value ['wattsPowerIdle'],
                $value ['jitCompatible']
            );
        }

        return $fieldList;
    }
}