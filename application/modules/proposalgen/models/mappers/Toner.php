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
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $count  int
     *                OPTIONAL: A SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *                OPTIONAL: A SQL LIMIT offset.
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
     * @param $masterDeviceId
     * @param $dealerId
     *
     * @return Proposalgen_Model_Toner []
     */
    public function getReportToners ($masterDeviceId, $dealerId)
    {
        $db       = Zend_Db_Table::getDefaultAdapter();
        $dealerId = $db->quote($dealerId, 'INT');

        $select = $db->select()
                  ->from('toners', array('*'))
                  ->joinLeft('device_toners', 'toner_id = id', array(null))
                  ->joinLeft('dealer_toner_attributes', "tonerId = id AND dealerId = {$dealerId}", array(
                                                                                                        "calculatedCost" => "COALESCE(dealer_toner_attributes.cost, toners.cost)",
                                                                                                        "dealerSku",
                                                                                                   ))
                  ->where('master_device_id = ?', $masterDeviceId);


        $stmt = $db->query($select);

        $result     = $stmt->fetchAll();
        $tonerArray = false;

        foreach ($result as $row)
        {
            $toner = new Proposalgen_Model_Toner($row);
            // $tonerArray[1][2][] = Proposalgen_Model_Toner
            $tonerArray [$toner->manufacturerId] [$toner->getTonerColor()->tonerColorId] [] = $toner;
            $this->saveItemToCache($toner);
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

        return $query->fetchAll();
    }

    /**
     * Fetches a list of toners for a master device with machine compatibility
     *
     * @param $masterDeviceId
     *
     * @return Proposalgen_Model_Toner []
     */
    public function fetchTonersAssignedToDeviceWithMachineCompatibility ($masterDeviceId)
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
    IF(dt1.master_device_id = {$masterDeviceId},'1','0') AS is_added,
    (SELECT
    GROUP_CONCAT(CONCAT(manufacturers.fullname, ' ', master_devices.modelName) SEPARATOR ';,')
     FROM device_toners AS dt2
         LEFT JOIN master_devices ON master_devices.id = dt2.master_device_id
         LEFT JOIN manufacturers ON manufacturers.id = master_devices.manufacturerId
     WHERE dt2.toner_id = dt1.toner_id) AS device_list
FROM toners
    LEFT JOIN device_toners dt1 ON toners.id = dt1.toner_id
    LEFT JOIN manufacturers ON manufacturers.id = toners.manufacturerId
    LEFT JOIN dealer_toner_attributes ON (dealer_toner_attributes.tonerId = toners.id AND dealer_toner_attributes.dealerId = {$dealerId})
        WHERE master_device_id = {$masterDeviceId}";
        $stmt     = $db->query($sql);
        $result   = $stmt->fetchAll();

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
     * @param int      $masterDeviceId The id of the currrent master device, this is used to populate the is_added flag to determine if it is added or not
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
    (SELECT
    GROUP_CONCAT(CONCAT(manufacturers.fullname, ' ', master_devices.modelName) SEPARATOR ';,')
     FROM device_toners AS dt2
         LEFT JOIN master_devices ON master_devices.id = dt2.master_device_id
         LEFT JOIN manufacturers ON manufacturers.id = master_devices.manufacturerId
     WHERE dt2.toner_id = dt1.toner_id) AS device_list
FROM toners
    LEFT JOIN device_toners dt1 ON toners.id = dt1.toner_id
    LEFT JOIN manufacturers ON manufacturers.id = toners.manufacturerId
    LEFT JOIN dealer_toner_attributes ON (dealer_toner_attributes.tonerId = toners.id AND dealer_toner_attributes.dealerId = {$dealerId})
            ";
        if ($tonerColorConfigId)
        {
            // sets up what colors we want to use
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
    (SELECT
    GROUP_CONCAT(CONCAT(manufacturers.fullname, ' ', master_devices.modelName) SEPARATOR ';,')
     FROM device_toners AS dt2
         LEFT JOIN master_devices ON master_devices.id = dt2.master_device_id
         LEFT JOIN manufacturers ON manufacturers.id = master_devices.manufacturerId
     WHERE dt2.toner_id = dt1.toner_id) AS device_list
FROM toners
    LEFT JOIN device_toners dt1 ON toners.id = dt1.toner_id
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
     * @return array
     */
    public function fetchListOfToners ($tonerIdList)
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
            IF(dt1.master_device_id IN ({$tonerIdList}),'1','0') AS is_added,
    (SELECT
    GROUP_CONCAT(CONCAT(manufacturers.fullname, ' ', master_devices.modelName) SEPARATOR ';,')
     FROM device_toners AS dt2
         LEFT JOIN master_devices ON master_devices.id = dt2.master_device_id
         LEFT JOIN manufacturers ON manufacturers.id = master_devices.manufacturerId
     WHERE dt2.toner_id = dt1.toner_id) AS device_list
FROM `toners`
    LEFT JOIN device_toners dt1 ON toners.id = dt1.toner_id
    LEFT JOIN `toner_colors` ON `toner_colors`.`id` = `toners`.`tonerColorId`
    LEFT JOIN `manufacturers` ON `manufacturers`.`id` = `toners`.`manufacturerId`
    LEFT JOIN dealer_toner_attributes ON dealer_toner_attributes.tonerId = toners.id AND dealer_toner_attributes.dealerId = {$dealerId}
WHERE `toners`.`id` IN ({$tonerIdList})
                GROUP BY id;";

        $query = $db->query($sql);

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

    public function fetchListOfAffectedToners ($tonerId, $order = null, $count = 25, $offset = 0)
    {
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

            return $result;
        }
    }

    /**
     * Gets the cheapest toners
     *
     * @param int   $masterDeviceId                   The master device id to get toners for
     * @param int   $dealerId                         The dealer id to get toners for
     * @param array $monochromeManufacturerPreference CSV list of manufacturer ids
     * @param array $colorManufacturerPreference      CSV list of manufacturer ids
     *
     * @return array
     */
    public function getCheapestTonersForDevice ($masterDeviceId, $dealerId, $monochromeManufacturerPreference, $colorManufacturerPreference)
    {
        $db                               = $this->getDbTable()->getDefaultAdapter();
        $masterDeviceId                   = $db->quoteInto($masterDeviceId, "INTEGER");
        $dealerId                         = $db->quoteInto($dealerId, "INTEGER");
        $monochromeManufacturerPreference = $db->quoteInto($monochromeManufacturerPreference, "STRING");
        $colorManufacturerPreference      = $db->quoteInto($colorManufacturerPreference, "STRING");
        $sql                              = "CALL getCheapestTonersForDevice(?,?,?,?)";

        $query   = $db->query($sql, array($masterDeviceId, $dealerId, $monochromeManufacturerPreference, $colorManufacturerPreference));
        $results = $query->fetchAll();
        $toners  = array();
        foreach ($results as $row)
        {
            $toners [$row[$this->col_tonerColorId]] = new Proposalgen_Model_Toner($row);
//            $toners [$row["{$this->col_manufacturerId}"]] ['isOem']     = $row['isOem'];

        }

        return $toners;
    }

    /**
     * Finds an instance of a Proposalgen_Model_Toner by it's sku
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
}