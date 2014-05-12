<?php

/**
 * Class Proposalgen_Model_Mapper_Toner
 */
class Proposalgen_Model_Mapper_Toner extends My_Model_Mapper_Abstract
{

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_Toner';

    /*
     * Define the primary key of the model association
    */
    public $col_id = 'id';
    public $col_manufacturerId = 'manufacturerId';
    public $col_tonerColorId = 'tonerColorId';
    public $col_sku = 'sku';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_Toner
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_Toner to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Proposalgen_Model_Toner
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
     * Saves (updates) an instance of Proposalgen_Model_Toner to the database.
     *
     * @param $object     Proposalgen_Model_Toner
     *                    The toner to save to the database
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
     *                This can either be an instance of Proposalgen_Model_Toner or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Proposalgen_Model_Toner)
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
     * Finds a toner based on it's primaryKey
     *
     * @param $id int
     *            The id of the toner to find
     *
     * @return Proposalgen_Model_Toner
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Proposalgen_Model_Toner)
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
        $object = new Proposalgen_Model_Toner($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a toner
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: A SQL OFFSET value.
     *
     * @return Proposalgen_Model_Toner
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Proposalgen_Model_Toner($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all toners
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
     * @return Proposalgen_Model_Toner[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Proposalgen_Model_Toner($row->toArray());

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
     * @param Proposalgen_Model_Toner $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Gets all the toners for a device
     *
     * @param $masterDeviceId
     *
     * @return Proposalgen_Model_Toner[][][]
     * @throws Exception
     */
    public function getTonersForDevice ($masterDeviceId)
    {
        $toners = array();
        try
        {
            $deviceToners = Proposalgen_Model_Mapper_DeviceToner::getInstance()->getDeviceToners($masterDeviceId);
            if ($deviceToners)
            {
                /* @var $deviceToner Proposalgen_Model_DeviceToner */
                foreach ($deviceToners as $deviceToner)
                {
                    $toner                                                     = $this->find($deviceToner->toner_id);
                    $toners [$toner->manufacturerId] [$toner->tonerColorId] [] = $toner;
                }
            }
        }
        catch (Exception $e)
        {
            throw new Exception("Error fetching all toners for a master device", 0, $e);
        }

        return $toners;
    }


    /**
     * Gets a list of all the toner pricing for a master device by dealer
     * This list will have the cost of the toner resolved.
     *
     * @param int $masterDeviceId
     * @param int $dealerId
     * @param int $clientId
     *
     * @return Proposalgen_Model_Toner []
     */
    public function getReportToners ($masterDeviceId, $dealerId, $clientId = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $select = $db->select()
                     ->from('toners', array('*'))
                     ->joinLeft('device_toners', 'toner_id = toners.id', array(null))
                     ->joinLeft('dealer_toner_attributes', "dealer_toner_attributes.tonerId = toners.id AND dealer_toner_attributes.dealerId = ?", array(
                "dealerSku",
            ))
                     ->joinLeft('client_toner_orders', "client_toner_orders.tonerId = toners.id AND client_toner_orders.clientId = ?", array(
                "calculatedCost"         => "COALESCE(client_toner_orders.cost, dealer_toner_attributes.cost, toners.cost)",
                "isUsingCustomerPricing" => "IF(client_toner_orders.cost IS NOT NULL, TRUE, FALSE)",
                "isUsingDealerPricing"   => "IF(client_toner_orders.cost IS NULL AND dealer_toner_attributes.cost IS NOT NULL, TRUE, FALSE)",
            ))
                     ->where('master_device_id = ?', $masterDeviceId);


        $stmt = $db->query($select, array($dealerId, $clientId));

        $result     = $stmt->fetchAll();
        $tonerArray = false;

        foreach ($result as $row)
        {
            $toner = $this->getItemFromCache($row['id']);
            if (!$toner instanceof Proposalgen_Model_Toner)
            {
                $toner                                                                          = new Proposalgen_Model_Toner($row);
                $tonerArray [$toner->manufacturerId] [$toner->getTonerColor()->tonerColorId] [] = $toner;
                $this->saveItemToCache($toner);
            }
            else
            {
                $toner->calculatedCost         = $row['calculatedCost'];
                $toner->isUsingCustomerPricing = $row['isUsingCustomerPricing'];
                $toner->isUsingDealerPricing   = $row['isUsingDealerPricing'];
            }


        }

        return $tonerArray;
    }


    /**
     * Fetches a list of toners for a device. (Used by Proposalgen_AdminController::devicetonersAction()
     *
     * @param $masterDeviceId
     *
     * @return Proposalgen_Model_Toner []
     */
    public function fetchTonersAssignedToDevice ($masterDeviceId)
    {
        $db = $this->getDbTable()->getAdapter();

        $sql = 'SELECT
    `toners`.*,
    `toner_colors`.`name`              AS `toner_color_name`,
    `manufacturers`.`fullname`              AS `manufacturer_name`,
    `device_toners`.`master_device_id` AS `master_device_id`
FROM `toners`
    LEFT JOIN `device_toners` ON `device_toners`.`toner_id` = `toners`.`id`
    LEFT JOIN `toner_colors` ON `toner_colors`.`id` = `toners`.`tonerColorId`
    LEFT JOIN `manufacturers` ON `manufacturers`.`id` = `toners`.`manufacturerId`
WHERE `device_toners`.`master_device_id` = ?';
        $sql = $db->quoteInto($sql, $masterDeviceId);

        $query = $db->query($sql);

        $toners = array();
        foreach ($query->fetchAll() as $row)
        {
            $toner = $this->getItemFromCache($row[$this->col_id]);
            if (!$toner instanceof Proposalgen_Model_Toner)
            {
                $toner = new Proposalgen_Model_Toner($row);
                $this->saveItemToCache($toner);
            }
            $toners[] = $toner;
        }

        return $toners;
    }

    /**
     * Fetches a list of toners for a master device with machine compatibility
     *
     * @param      $masterDeviceId
     * @param null $order
     * @param bool $justCount
     *
     * @return Proposalgen_Model_Toner []
     */
    public function fetchTonersAssignedToDeviceWithMachineCompatibility ($masterDeviceId, $order = null, $justCount = false)
    {
        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
        $db       = $this->getDbTable()->getAdapter();
        $sql      = "SELECT
    DISTINCT
    toners.id                           AS id,
    toners.isSystemDevice               AS isSystemDevice,
    manufacturers.id                    AS manufacturerId,
    manufacturers.fullname              AS manufacturer,
    toners.sku                          AS systemSku,
    dealer_toner_attributes.dealerSku   AS dealerSku,
    toners.cost                         AS systemCost,
    dealer_toner_attributes.cost        AS dealerCost,
    toners.yield,
    toners.tonerColorId,
    dt3.isSystemDevice                  AS deviceTonersIsSystemDevice,
    IF(dt1.master_device_id = {$masterDeviceId},'1','0') AS is_added,
    (SELECT
    GROUP_CONCAT(CONCAT(manufacturers.fullname, ' ', master_devices.modelName) SEPARATOR ';,')
     FROM device_toners AS dt2
         LEFT JOIN master_devices ON master_devices.id = dt2.master_device_id
         LEFT JOIN manufacturers ON manufacturers.id = master_devices.manufacturerId
     WHERE dt2.toner_id = dt1.toner_id) AS device_list
FROM toners
    LEFT JOIN device_toners dt1 ON toners.id = dt1.toner_id
    LEFT JOIN device_toners dt3 ON toners.id = dt3.toner_id AND dt3.master_device_id = {$masterDeviceId}
    LEFT JOIN manufacturers ON manufacturers.id = toners.manufacturerId
    LEFT JOIN dealer_toner_attributes ON (dealer_toner_attributes.tonerId = toners.id AND dealer_toner_attributes.dealerId = {$dealerId})
        WHERE dt1.master_device_id = {$masterDeviceId}";

        if ($order)
        {
            $sql .= " ORDER BY " . implode(" ", $order);
        }

        $query = $db->query($sql);

        if ($justCount)
        {
            return count($query->fetchAll());
        }

        $stmt   = $db->query($sql);
        $result = $stmt->fetchAll();

        return $result;
    }

    /**
     * Fetches a list of toners with machine compatibility with a toner color id
     *
     * @param          $tonerColorConfigId
     * @param null     $order
     * @param int      $count
     * @param int|null $offset
     *
     * @param bool     $manufacturerId
     *
     * @param          $filter
     * @param          $criteria
     *
     * @param int      $masterDeviceId The id of the current master device, this is used to populate the is_added flag to determine if it is added or not
     *
     * @return Proposalgen_Model_Toner []
     */
    public function fetchTonersWithMachineCompatibilityUsingColorConfigId ($tonerColorConfigId, $order = null, $count = 25, $offset = 0, $manufacturerId = false, $filter, $criteria, $masterDeviceId = 0)
    {
        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
        $db       = $this->getDbTable()->getAdapter();
        $sql      = "SELECT
DISTINCTROW
    toners.id                           AS id,
    toners.isSystemDevice               AS isSystemDevice,
    manufacturers.id                    AS manufacturerId,
    manufacturers.fullname              AS manufacturer,
    toners.sku                          AS systemSku,
    dealer_toner_attributes.dealerSku   AS dealerSku,
    toners.cost                         AS systemCost,
    dealer_toner_attributes.cost        AS dealerCost,
    toners.yield,
    toners.tonerColorId,
    dt3.isSystemDevice                  AS deviceTonersIsSystemDevice,
    (SELECT
    GROUP_CONCAT(CONCAT(manufacturers.fullname, ' ', master_devices.modelName) SEPARATOR ';,')
     FROM device_toners AS dt2
         LEFT JOIN master_devices ON master_devices.id = dt2.master_device_id
         LEFT JOIN manufacturers ON manufacturers.id = master_devices.manufacturerId
     WHERE dt2.toner_id = dt1.toner_id) AS device_list
FROM toners
    LEFT JOIN device_toners dt1 ON toners.id = dt1.toner_id
    LEFT JOIN device_toners dt3 ON toners.id = dt3.toner_id AND dt3.master_device_id = {$masterDeviceId}
    LEFT JOIN manufacturers ON manufacturers.id = toners.manufacturerId
    LEFT JOIN dealer_toner_attributes ON (dealer_toner_attributes.tonerId = toners.id AND dealer_toner_attributes.dealerId = {$dealerId})
            ";
        if ($tonerColorConfigId)
        {
            // Sets up what colors we want to use
            $tonerColors = array();
            if ($tonerColorConfigId == 1)
            {
                $tonerColors[] = Proposalgen_Model_TonerColor::BLACK;
            }
            else if ($tonerColorConfigId == 2)
            {
                $tonerColors = array(Proposalgen_Model_TonerColor::BLACK, Proposalgen_Model_TonerColor::CYAN, Proposalgen_Model_TonerColor::MAGENTA, Proposalgen_Model_TonerColor::YELLOW);
            }
            else if ($tonerColorConfigId == 3)
            {
                $tonerColors[] = Proposalgen_Model_TonerColor::THREE_COLOR;
            }
            else if ($tonerColorConfigId == 4)
            {
                $tonerColors[] = Proposalgen_Model_TonerColor::FOUR_COLOR;
            }

            $colorWhere = " WHERE toners.{$this->col_tonerColorId} IN (";

            for ($colorCounter = 0; $colorCounter < count($tonerColors); $colorCounter++)
            {
                if ($colorCounter != 0)
                {
                    $colorWhere .= ",";
                }
                $colorWhere .= $tonerColors[$colorCounter];
            }

            $sql .= $colorWhere . ")";

            if ($manufacturerId)
            {
                $sql .= " AND manufacturers.id = {$manufacturerId}";
            }

            if ($filter == "tonerColorId")
            {
                if (strtolower($criteria) == "black")
                {
                    $criteria = "1";
                }
                else if (strtolower($criteria) == "cyan")
                {
                    $criteria = "2";
                }
                else if (strtolower($criteria) == "magenta")
                {
                    $criteria = "3";
                }
                else if (strtolower($criteria) == "yellow")
                {
                    $criteria = "4";
                }
                else if (strtolower($criteria) == "3 color")
                {
                    $criteria = "5";
                }
                else if (strtolower($criteria) == "4 color")
                {
                    $criteria = "6";
                }
            }

            if ($filter == "isSystemDevice")
            {
                $filter   = "toners.isSystemDevice";
                $criteria = "0";
                $sql .= " AND {$filter} = {$criteria}";
            }
            else if ($filter && $criteria)
            {
                $sql .= " AND {$filter} LIKE '%{$criteria}%'";
            }
        }

        if ($order)
        {

            $sql .= " ORDER BY " . $order[0] . " " . $order[1];
        }

        $sql .= " LIMIT " . $offset . ", " . $count . " ";
        $stmt   = $db->query($sql);
        $result = $stmt->fetchAll();

        return $result;
    }

    /**
     * Fetches a list of toners with machine compatibility for a certain color
     *
     * @param          $tonerColorId
     * @param null     $order
     * @param int      $count
     * @param int|null $offset
     *
     * @param bool     $manufacturerId
     *
     * @param          $filter
     * @param          $criteria
     *
     * @param int      $masterDeviceId
     *
     * @return Proposalgen_Model_Toner []
     */
    public function fetchTonersWithMachineCompatibilityUsingColorId ($tonerColorId, $order = null, $count = 25, $offset = 0, $manufacturerId = false, $filter, $criteria, $masterDeviceId = 0)
    {
        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
        $db       = $this->getDbTable()->getAdapter();
        $sql      = "SELECT
    DISTINCT
    toners.id                           AS id,
    toners.isSystemDevice               AS isSystemDevice,
    manufacturers.id                    AS manufacturerId,
    manufacturers.fullname              AS manufacturer,
    toners.sku                          AS systemSku,
    dealer_toner_attributes.dealerSku   AS dealerSku,
    toners.cost                         AS systemCost,
    dealer_toner_attributes.cost        AS dealerCost,
    toners.yield,
    toners.tonerColorId,
    dt3.isSystemDevice                  AS deviceTonersIsSystemDevice,
    (SELECT
    GROUP_CONCAT(CONCAT(manufacturers.fullname, ' ', master_devices.modelName) SEPARATOR ';,')
     FROM device_toners AS dt2
         LEFT JOIN master_devices ON master_devices.id = dt2.master_device_id
         LEFT JOIN manufacturers ON manufacturers.id = master_devices.manufacturerId
     WHERE dt2.toner_id = dt1.toner_id) AS device_list
FROM toners
    LEFT JOIN device_toners dt1 ON toners.id = dt1.toner_id
    LEFT JOIN device_toners dt3 ON toners.id = dt3.toner_id AND dt3.master_device_id = {$masterDeviceId}
    LEFT JOIN manufacturers ON manufacturers.id = toners.manufacturerId
    LEFT JOIN dealer_toner_attributes ON (dealer_toner_attributes.tonerId = toners.id AND dealer_toner_attributes.dealerId = {$dealerId})
            ";
        if ($tonerColorId)
        {
            $sql .= " WHERE toners.{$this->col_tonerColorId} = {$tonerColorId}";
        }
        if ($manufacturerId)
        {
            $sql .= " AND manufacturers.id = {$manufacturerId}";
        }
        if ($filter == "isSystemDevice")
        {
            $criteria = "0";
        }
        if ($filter && $criteria)
        {
            $sql .= " AND {$filter} LIKE '%{$criteria}%'";
        }
        if ($order)
        {

            $sql .= " ORDER BY " . $order[0] . " " . $order[1];
        }

        $sql .= " LIMIT " . $offset . ", " . $count . " ";
        $stmt   = $db->query($sql);
        $result = $stmt->fetchAll();

        return $result;
    }


    /**
     * Fetches a list of toners.
     *
     * @param array $tonerIdList A string of id's (comma separated)
     *
     * @param null  $masterDeviceId
     * @param null  $order
     * @param bool  $justCount
     *
     * @return array
     */
    public function fetchListOfToners ($tonerIdList, $masterDeviceId = null, $order = null, $justCount = false)
    {
        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
        $db       = $this->getDbTable()->getAdapter();

        $sql = "SELECT
            toners.id                          AS id,
            toners.isSystemDevice              AS isSystemDevice,
            manufacturers.fullname             AS manufacturer,
            toners.manufacturerId              AS manufacturerId,
            toners.sku                         AS systemSku,
            dealer_toner_attributes.dealerSku  AS dealerSku,
            toners.cost                        AS systemCost,
            dealer_toner_attributes.cost       AS dealerCost,
            toners.yield,
            toners.tonerColorId,
            dt3.isSystemDevice                  AS deviceTonersIsSystemDevice,
            IF(dt1.toner_id IN ({$tonerIdList}),'1','0') AS is_added,
    (SELECT
    GROUP_CONCAT(CONCAT(manufacturers.fullname, ' ', master_devices.modelName) SEPARATOR ';,')
     FROM device_toners AS dt2
         LEFT JOIN master_devices ON master_devices.id = dt2.master_device_id
         LEFT JOIN manufacturers ON manufacturers.id = master_devices.manufacturerId
     WHERE dt2.toner_id = dt1.toner_id) AS device_list
FROM `toners`
    LEFT JOIN device_toners dt1 ON toners.id = dt1.toner_id
    LEFT JOIN device_toners dt3 ON toners.id = dt3.toner_id AND dt3.master_device_id = {$masterDeviceId}
    LEFT JOIN `toner_colors` ON `toner_colors`.`id` = `toners`.`tonerColorId`
    LEFT JOIN `manufacturers` ON `manufacturers`.`id` = `toners`.`manufacturerId`
    LEFT JOIN dealer_toner_attributes ON dealer_toner_attributes.tonerId = toners.id AND dealer_toner_attributes.dealerId = {$dealerId}
WHERE `toners`.`id` IN ({$tonerIdList})
                GROUP BY id";

        if ($order)
        {
            $sql .= " ORDER BY " . implode(" ", $order);
        }

        $query = $db->query($sql);

        if ($justCount)
        {
            return count($query->fetchAll());
        }

        return $query->fetchAll();
    }

    /**
     * Fetches a list of toners.
     *
     * @param array $tonerIdList A string of id's (comma separated)
     *
     * @return array
     */
    public function fetchListOfTonersWithMachineCompatibility ($tonerIdList)
    {
        $db = $this->getDbTable()->getAdapter();

        $sql = " SELECT
            toner_id                          AS id,
            manufacturers.fullname            AS manufacturer,
            toners.sku                        AS systemSku,
            dealer_toner_attributes.dealerSku AS dealerSku,
            toners.cost                       AS systemCost,
            dealer_toner_attributes.cost      AS dealerCost,
            toners.yield,
            toners.tonerColorId,
            GROUP_CONCAT(CONCAT(manufacturers.fullname,\" \",master_devices.modelName) SEPARATOR \"; \") AS `device_list`
            FROM `toners`
                LEFT JOIN `toner_colors` ON `toner_colors`.`id` = `toners`.`tonerColorId`
                LEFT JOIN `manufacturers` ON `manufacturers`.`id` = `toners`.`manufacturerId`
                LEFT JOIN `device_toners` ON `device_toners`.`toner_id` = `toners`.`id`
                LEFT JOIN `master_devices` ON `master_devices`.`id` = `device_toners`.`master_device_id`
                LEFT JOIN dealer_toner_attributes ON dealer_toner_attributes.tonerId = toners.id
            WHERE `toners`.`id` IN ({$tonerIdList})";

        $query = $db->query($sql);

        return $query->fetchAll();
    }

    /**
     * @param      $tonerId
     * @param null $order
     * @param int  $count
     * @param int  $offset
     *
     * @return array|bool
     */
    public function fetchListOfAffectedToners ($tonerId, $order = null, $count = 25, $offset = 0)
    {
        $result = false;
        if ($tonerId)
        {
            $db           = $this->getDbTable()->getAdapter();
            $toner        = Proposalgen_Model_Mapper_Toner::getInstance()->find($tonerId);
            $tonerColorId = $toner->tonerColorId;
            $select       = $db->select()
                               ->from('toners', array(
                    'tonerId'    => 'id',
                    'systemSku'  => 'sku',
                    'systemCost' => 'cost',
                    'deviceName' => 'CONCAT(manufacturers.fullname," ",master_devices.modelName)',
                    'tonerColorId',
                    'yield'
                ))
                               ->joinLeft('device_toners', 'device_toners.toner_id = toners.id', array())
                               ->joinLeft('master_devices', 'master_devices.id = device_toners.master_device_id', array())
                               ->joinLeft('manufacturers', 'manufacturers.id = master_devices.manufacturerId', array())
                               ->joinLeft('dealer_toner_attributes', 'dealer_toner_attributes.tonerId = toners.id', array('dealerSku', 'dealerCost' => 'cost'))
//                      ->where("toners.{$this->col_id} = ?", $tonerId)
                               ->where("master_devices.id in (SELECT master_device_id from device_toners AS dt where dt.toner_id = {$tonerId})")
                               ->where("toners.{$this->col_tonerColorId} = ?", $tonerColorId)
                               ->limit($count, $offset)
                               ->order('deviceName asc');
            $stmt         = $db->query($select);

            $result = $stmt->fetchAll();


        }

        return $result;
    }

    /**
     * Gets the cheapest toners
     *
     * @param int   $masterDeviceId                   The master device id to get toners for
     * @param int   $dealerId                         The dealer id to get toner pricing for
     * @param array $monochromeManufacturerPreference CSV list of manufacturer ids
     * @param array $colorManufacturerPreference      CSV list of manufacturer ids
     * @param int   $clientId                         The client id to get toner pricing for
     *
     * @return array
     */
    public function getCheapestTonersForDevice ($masterDeviceId, $dealerId, $monochromeManufacturerPreference, $colorManufacturerPreference, $clientId = null)
    {
        $db                               = $this->getDbTable()->getDefaultAdapter();
        $masterDeviceId                   = $db->quoteInto($masterDeviceId, "INTEGER");
        $clientId                         = $db->quoteInto($clientId, "INTEGER");
        $dealerId                         = $db->quoteInto($dealerId, "INTEGER");
        $monochromeManufacturerPreference = $db->quoteInto($monochromeManufacturerPreference, "STRING");
        $colorManufacturerPreference      = $db->quoteInto($colorManufacturerPreference, "STRING");
        $sql                              = "CALL getCheapestTonersForDevice(?,?,?,?,?)";

        $query   = $db->query($sql, array($masterDeviceId, $dealerId, $monochromeManufacturerPreference, $colorManufacturerPreference, $clientId));
        $results = $query->fetchAll();
        $toners  = array();
        foreach ($results as $row)
        {
            $toners [$row[$this->col_tonerColorId]] = new Proposalgen_Model_Toner($row);
        }

        return $toners;
    }

    /**
     * Finds an instance of a Proposalgen_Model_Toner by it's SKU
     *
     * @param $sku
     *
     * @return \Proposalgen_Model_Toner
     */
    public function fetchBySku ($sku)
    {
        return $this->fetch(array("{$this->col_sku} = ?" => "{$sku}"));
    }

    /**
     * Finds an instance of a Proposalgen_Model_Toner by it's SKU and manufacturer
     *
     * @param string $sku
     * @param int    $manufacturerId
     *
     * @return \Proposalgen_Model_Toner
     */
    public function fetchBySkuAndManufacturer ($sku, $manufacturerId)
    {
        return $this->fetch(array(
            "{$this->col_sku} = ?"            => "{$sku}",
            "{$this->col_manufacturerId} = ?" => "{$manufacturerId}"
        ));
    }

    /**
     * Exports the toner pricing for the dealership
     *
     * @param $manufacturerId
     * @param $dealerId
     *
     * @return array
     */
    public function getTonerPricingForExport ($manufacturerId, $dealerId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        $select = $db->select()
                     ->from(array(
                    't' => 'toners'), array(
                    'id AS toners_id', 'sku', 'yield', "systemCost" => "cost"
                )
            )
                     ->joinLeft(array(
                'dt' => 'device_toners'
            ), 'dt.toner_id = t.id', array(
                'master_device_id'
            ))
                     ->joinLeft(array(
                'tm' => 'manufacturers'
            ), 'tm.id = t.manufacturerId', array(
                'fullname'
            ))
                     ->joinLeft(array(
                'tc' => 'toner_colors'
            ), 'tc.id = t.tonerColorId', array('name AS toner_color'))
                     ->joinLeft(array('dta' => 'dealer_toner_attributes'), "dta.tonerId = t.id AND dta.dealerId = {$dealerId}", array('cost', 'dealerSku'))
                     ->where("t.id > 0")
                     ->group('t.id')
                     ->order(array(
                'tm.fullname'
            ));

        if ($manufacturerId > 0)
        {
            $select->where("manufacturerId = ?", $manufacturerId);
        }

        $stmt   = $db->query($select);
        $result = $stmt->fetchAll();

        $fieldList = array();
        foreach ($result as $value)
        {
            $fieldList [] = array(
                $value ['toners_id'],
                $value ['fullname'],
                $value ['sku'],
                $value ['toner_color'],
                $value ['yield'],
                $value ['systemCost'],
                $value ['dealerSku'],
                $value ['cost'],
            );
        }

        return $fieldList;
    }

    /**
     * Gets a Zend_Validate_Db_NoRecordExists to be used in a form
     *
     * @param $id
     *
     * @return \Zend_Validate_Db_NoRecordExists
     */
    public function getDbNoRecordExistsValidator ($id = null)
    {
        $noRecordExistsArray          = array();
        $noRecordExistsArray['table'] = $this->getTableName();
        $noRecordExistsArray['field'] = $this->col_sku;

        if ($id)
        {
            $noRecordExistsArray['exclude'] = Zend_Db_Table::getDefaultAdapter()->quoteInto("{$this->col_id} != ?", $id);
        }

        return new Zend_Validate_Db_NoRecordExists($noRecordExistsArray);
    }

    /**
     * Fetches a list of toners with machine compatibility for a certain color
     *
     * @param null     $order
     * @param int      $count
     * @param int|null $offset
     * @param          $filter
     * @param          $criteria
     * @param int      $manufacturerId
     *
     * @return Proposalgen_Model_Toner []
     */
    public function fetchAllTonersWithMachineCompatibility ($order = null, $count = 25, $offset = 0, $filter, $criteria, $manufacturerId = 0)
    {
        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
        $db       = $this->getDbTable()->getAdapter();
        $sql      = "SELECT
    DISTINCT
    toners.id                           AS id,
    toners.isSystemDevice               AS isSystemDevice,
    manufacturers.id                    AS manufacturerId,
    manufacturers.fullname              AS manufacturer,
    toners.sku                          AS systemSku,
    dealer_toner_attributes.dealerSku   AS dealerSku,
    toners.cost                         AS systemCost,
    dealer_toner_attributes.cost        AS dealerCost,
    toners.yield,
    toners.tonerColorId,
    (SELECT
    GROUP_CONCAT(CONCAT(manufacturers.fullname, ' ', master_devices.modelName) SEPARATOR ';,')
     FROM device_toners AS dt2
         LEFT JOIN master_devices ON master_devices.id = dt2.master_device_id
         LEFT JOIN manufacturers ON manufacturers.id = master_devices.manufacturerId
     WHERE dt2.toner_id = dt1.toner_id) AS device_list
FROM toners
    LEFT JOIN device_toners dt1 ON toners.id = dt1.toner_id
    LEFT JOIN manufacturers ON manufacturers.id = toners.manufacturerId
    LEFT JOIN dealer_toner_attributes ON (dealer_toner_attributes.tonerId = toners.id AND dealer_toner_attributes.dealerId = {$dealerId})";

        $filterWhere = false;

        if ($filter && $criteria)
        {
            if (($filter == 'sku' || $filter == 'dealerSku') && $criteria)
            {
                if (!$filterWhere)
                {
                    $filterWhere = " WHERE ";
                }
                else
                {
                    $filterWhere .= " AND ";
                }

                $filterWhere .= " (toners.sku LIKE '%{$criteria}%' OR dealer_toner_attributes.dealerSku LIKE '%{$criteria}%')";
            }
        }

        if ($filter == 'isSystemDevice')
        {
            if (!$filterWhere)
            {
                $filterWhere = " WHERE ";
            }
            else
            {
                $filterWhere .= " AND ";
            }

            $filterWhere .= " toners.isSystemDevice = 0";
        }


        if ($manufacturerId > 0)
        {
            if (!$filterWhere)
            {
                $filterWhere .= " WHERE ";
            }
            else
            {
                $filterWhere .= " AND ";
            }

            $filterWhere .= " toners.manufacturerId = {$manufacturerId}";
        }

        $sql .= $filterWhere;

        if ($order)
        {
            $sql .= " ORDER BY " . $order[0] . " " . $order[1];
        }

        $sql .= " LIMIT " . $offset . ", " . $count . " ";
        $stmt   = $db->query($sql);
        $result = $stmt->fetchAll();

        return $result;
    }

    /**
     * Fetches a list of toners that are compatible with a certain toner
     *
     * @param int|Proposalgen_Model_Toner $tonerId
     * @param int                         $clientId
     *
     * @return Proposalgen_Model_Toner[]
     */
    public function getCompatibleToners ($tonerId, $clientId = null)
    {
        $toners = array();

        if ($tonerId instanceof Proposalgen_Model_Toner)
        {
            $tonerId = $tonerId->id;
        }

        if ($tonerId > 0)
        {
            $db       = $this->getDbTable()->getAdapter();
            $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
            $sql      = 'SELECT
    dt.toner_id,
    dt.master_device_id,
    COALESCE(client_toner_orders.cost, dealer_toner_attributes.cost, toners.cost)                  AS calculatedCost,
    IF(client_toner_orders.cost IS NOT NULL, TRUE, FALSE)                                          AS isUsingCustomerPricing,
    IF(client_toner_orders.cost IS NULL AND dealer_toner_attributes.cost IS NOT NULL, TRUE, FALSE) AS isUsingDealerPricing,
    COALESCE(client_toner_orders.cost, dealer_toner_attributes.cost, toners.cost) / toners.yield   AS cpp
FROM device_toners AS dt
    LEFT JOIN toners ON toners.id = dt.toner_id
    LEFT JOIN dealer_toner_attributes ON dealer_toner_attributes.tonerId = dt.toner_id AND dealer_toner_attributes.dealerId = ?
    LEFT JOIN client_toner_orders ON client_toner_orders.tonerId = dt.toner_id AND client_toner_orders.clientId = ?
WHERE dt.master_device_id IN (SELECT
                                  dt2.master_device_id
                              FROM device_toners AS dt2
                              WHERE dt2.toner_id = ?) AND dt.toner_id != ? AND toners.tonerColorId IN (SELECT
                                                                                                           tonerColorId
                                                                                                       FROM toners
                                                                                                       WHERE toners.id = ?)
GROUP BY dt.toner_id
ORDER BY cpp ASC';
            $stmt     = $db->query($sql, array($dealerId, $clientId, $tonerId, $tonerId, $tonerId));
            $result   = $stmt->fetchAll();

            foreach ($result as $tonerData)
            {
                $newTonerId = $tonerData['toner_id'];
                $toner      = $this->getItemFromCache($newTonerId);
                if (!$toner instanceof Proposalgen_Model_Toner)
                {
                    $toner                 = $this->find($newTonerId);
                    $toner->calculatedCost = $tonerData['calculatedCost'];
                    $this->saveItemToCache($toner);
                }

                $toners[] = $toner;
            }
        }

        return $toners;
    }

    /**
     * Gets compatible toners
     *
     * @param int|Proposalgen_Model_Toner $tonerId
     * @param null|int                    $clientId
     * @param null|int                    $dealerId
     *
     * @return Proposalgen_Model_Toner[]
     */
    public function findCompatibleToners ($tonerId, $clientId = null, $dealerId = null)
    {
        $db     = $this->getDbTable()->getDefaultAdapter();
        $toners = array();

        if ($tonerId instanceof Proposalgen_Model_Toner)
        {
            $tonerId = $tonerId->id;
        }


        $sql = 'SELECT
    t.*,
    COALESCE(cta.cost, dta.cost, t.cost)                                                           AS calculatedCost,
    IF(cta.cost IS NOT NULL, TRUE, FALSE)                                          AS isUsingCustomerPricing,
    IF(cta.cost IS NULL AND dta.cost IS NOT NULL, TRUE, FALSE) AS isUsingDealerPricing,
    COALESCE(cta.cost, dta.cost, t.cost) / t.yield                                                 AS cpp
FROM device_toners AS dt
-- Toners
    JOIN toners AS t
        ON t.id = dt.toner_id
-- Toners
    JOIN master_devices AS md
        ON md.id = dt.master_device_id
-- Dealer Toner attributes
    LEFT JOIN dealer_toner_attributes AS dta
        ON dta.tonerId = dt.toner_id AND dta.dealerId = ?
-- Client Toner Orders
    LEFT JOIN client_toner_orders AS cta
        ON cta.tonerId = dt.toner_id AND cta.clientId = ?
WHERE dt.master_device_id IN
      (
          SELECT
              md2.id
          FROM master_devices AS md2
-- Device toners
              JOIN device_toners AS dt2
                  ON dt2.master_device_id = md2.id
-- Toners
              JOIN toners AS t2
                  ON dt2.toner_id = t2.id
          WHERE dt2.toner_id = ?
      )
      AND dt.toner_id != ?
      AND t.tonerColorId IN (SELECT
                                 tonerColorId
                             FROM toners
                             WHERE toners.id = ?)
      AND t.manufacturerId != md.manufacturerId
GROUP BY dt.toner_id
ORDER BY cpp ASC';

        $result = $db->query($sql, array($dealerId, $clientId, $tonerId, $tonerId, $tonerId))->fetchAll();
        foreach ($result as $tonerData)
        {
            $newTonerId = $tonerData['id'];
            $toner      = $this->getItemFromCache($newTonerId);
            if (!$toner instanceof Proposalgen_Model_Toner)
            {
                $toner                         = $this->find($newTonerId);
                $toner->calculatedCost         = $tonerData['calculatedCost'];
                $toner->isUsingCustomerPricing = $tonerData['isUsingCustomerPricing'];
                $toner->isUsingDealerPricing   = $tonerData['isUsingDealerPricing'];
                $this->saveItemToCache($toner);
            }

            $toners[] = $toner;
        }

        return $toners;
    }

    /**
     * Gets other OEM toners
     *
     * @param int|Proposalgen_Model_Toner $tonerId
     * @param null|int                    $clientId
     * @param null|int                    $dealerId
     *
     * @return Proposalgen_Model_Toner[]
     */
    public function findOemToners ($tonerId, $clientId = null, $dealerId = null)
    {
        $toners = array();

        if ($tonerId instanceof Proposalgen_Model_Toner)
        {
            $tonerId = $tonerId->id;
        }

        $db  = $this->getDbTable()->getDefaultAdapter();
        $sql = 'SELECT
    t.*,
    COALESCE(cta.cost, dta.cost, t.cost)                                                           AS calculatedCost,
    IF(cta.cost IS NOT NULL, TRUE, FALSE)                                          AS isUsingCustomerPricing,
    IF(cta.cost IS NULL AND dta.cost IS NOT NULL, TRUE, FALSE) AS isUsingDealerPricing,
    COALESCE(cta.cost, dta.cost, t.cost) / t.yield                                                 AS cpp
FROM device_toners AS dt
-- Toners
    JOIN toners AS t
        ON t.id = dt.toner_id
-- Toners
    JOIN master_devices AS md
        ON md.id = dt.master_device_id
-- Dealer Toner attributes
    LEFT JOIN dealer_toner_attributes AS dta
        ON dta.tonerId = dt.toner_id AND dta.dealerId = ?
-- Client Toner Orders
    LEFT JOIN client_toner_orders AS cta
        ON cta.tonerId = dt.toner_id AND cta.clientId = ?
WHERE dt.master_device_id IN
      (
          SELECT
              md2.id
          FROM master_devices AS md2
-- Device toners
              JOIN device_toners AS dt2
                  ON dt2.master_device_id = md2.id
-- Toners
              JOIN toners AS t2
                  ON dt2.toner_id = t2.id
          WHERE dt2.toner_id = ?
      )
      AND dt.toner_id != ?
      AND t.tonerColorId IN (SELECT
                                 tonerColorId
                             FROM toners
                             WHERE toners.id = ?)
      AND t.manufacturerId = md.manufacturerId
GROUP BY dt.toner_id
ORDER BY cpp ASC';

        $result = $db->query($sql, array($dealerId, $clientId, $tonerId, $tonerId, $tonerId))->fetchAll();

        foreach ($result as $tonerData)
        {
            $newTonerId = $tonerData['id'];
            $toner      = $this->getItemFromCache($newTonerId);
            if (!$toner instanceof Proposalgen_Model_Toner)
            {
                $toner                         = $this->find($newTonerId);
                $toner->calculatedCost         = $tonerData['calculatedCost'];
                $toner->isUsingCustomerPricing = $tonerData['isUsingCustomerPricing'];
                $toner->isUsingDealerPricing   = $tonerData['isUsingDealerPricing'];
                $this->saveItemToCache($toner);
            }

            $toners[] = $toner;
        }

        return $toners;
    }
}