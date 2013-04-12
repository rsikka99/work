<?php
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
            $deviceToners = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchAll(array(
                                                                                               "master_device_id = ?" => $masterDeviceId
                                                                                          ));
            if ($deviceToners)
            {
                /* @var $deviceToner Proposalgen_Model_DeviceToner */
                foreach ($deviceToners as $deviceToner)
                {
                    $toner                                                                                 = $this->find($deviceToner->tonerId);
                    $toners [$toner->getPartType()->partTypeId] [$toner->getTonerColor()->tonerColorId] [] = $toner;
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
        $db = Zend_Db_Table::getDefaultAdapter();
        $dealerId                = $db->quote($dealerId, 'INT');

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
            $tonerArray [$toner->getPartType()->partTypeId] [$toner->getTonerColor()->tonerColorId] [] = $toner;
            $this->saveItemToCache($toner);
        }

        return $tonerArray;
    }


    /**
     * Fetches a list of toners for a device. (Used by Proposalgen_AdminController::devicetonersAction()
     *
     * @param $masterDeviceId
     *
     * @return array
     */
    public function fetchTonersAssignedToDevice ($masterDeviceId)
    {
        $db = $this->getDbTable()->getAdapter();

        $sql = 'SELECT
    `toners`.*,
    `part_types`.`name`                AS `type_name`,
    `toner_colors`.`name`              AS `toner_color_name`,
    `manufacturers`.`fullname`              AS `manufacturer_name`,
    `device_toners`.`master_device_id` AS `master_device_id`
FROM `toners`
    LEFT JOIN `device_toners` ON `device_toners`.`toner_id` = `toners`.`id`
    LEFT JOIN `part_types` ON `part_types`.`id` = `toners`.`partTypeId`
    LEFT JOIN `toner_colors` ON `toner_colors`.`id` = `toners`.`tonerColorId`
    LEFT JOIN `manufacturers` ON `manufacturers`.`id` = `toners`.`manufacturerId`
WHERE `device_toners`.`master_device_id` = ?';
        $sql = $db->quoteInto($sql, $masterDeviceId);

        $query = $db->query($sql);

        return $query->fetchAll();
    }

    /**
     * Fetches a list of toners. (Used by Proposalgen_AdminController::devicetonersAction()
     *
     * @param $tonerIdList A string of id's (comma separated)
     *
     * @return array
     */
    public function fetchListOfToners ($tonerIdList)
    {
        $db = $this->getDbTable()->getAdapter();

        $sql = "SELECT
    `toners`.*,
    `part_types`.`name`   AS `type_name`,
    `toner_colors`.`name` AS `toner_color_name`,
    `manufacturers`.`fullname` AS `manufacturer_name`,
    NULL                       AS `master_device_id`
FROM `toners`
    LEFT JOIN `part_types` ON `part_types`.`id` = `toners`.`partTypeId`
    LEFT JOIN `toner_colors` ON `toner_colors`.`id` = `toners`.`tonerColorId`
    LEFT JOIN `manufacturers` ON `manufacturers`.`id` = `toners`.`manufacturerId`
WHERE `toners`.`id` IN ({$tonerIdList});";

        $query = $db->query($sql);

        return $query->fetchAll();

    }

}