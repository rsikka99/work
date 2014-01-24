<?php

/**
 * Class Assessment_Model_Mapper_Assessment
 */
class Assessment_Model_Mapper_Assessment extends My_Model_Mapper_Abstract
{
    /*
     * Column name definitions. Define all columns up here and use them down below.
     */
    public $col_id = 'id';
    public $col_clientId = 'clientId';
    public $col_rmsUploadId = 'rmsUploadId';
    public $col_dateCreated = 'dateCreated';
    public $col_stepName = 'stepName';

    /*
     * Mapper Definitions
     */
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Assessment_Model_DbTable_Assessment';

    /**
     * Gets an instance of the mapper
     *
     * @return Assessment_Model_Mapper_Assessment
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Assessment_Model_Report to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Assessment_Model_Assessment
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
     * Saves (updates) an instance of Assessment_Model_Report to the database.
     *
     * @param $object     Assessment_Model_Assessment
     *                    The report model to save to the database
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
     *                This can either be an instance of Assessment_Model_Report or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Assessment_Model_Assessment)
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
     * Finds a report based on it's primaryKey
     *
     * @param $id int
     *            The id of the report to find
     *
     * @return Assessment_Model_Assessment
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Assessment_Model_Assessment)
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
        $object = new Assessment_Model_Assessment($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a report
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: A SQL OFFSET value.
     *
     * @return Assessment_Model_Assessment
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Assessment_Model_Assessment($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all assessments
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
     * @return Assessment_Model_Assessment[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Assessment_Model_Assessment($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Fetches all unfinished assessments for a given client
     *
     * @param int $clientId
     *
     * @return Assessment_Model_Assessment[]
     */
    public function fetchAllUnfinishedAssessmentsForClient ($clientId)
    {
        return $this->fetchAll(array(
            "{$this->col_clientId} = ?"  => $clientId,
            "{$this->col_stepName} <> ?" => Assessment_Model_Assessment_Steps::STEP_FINISHED
        ));
    }

    /**
     * Fetches all assessments for a given client
     *
     * @param int $clientId
     *
     * @return Assessment_Model_Assessment[]
     */
    public function fetchAllFinishedAssessmentsForClient ($clientId)
    {
        return $this->fetchAll(array(
            "{$this->col_clientId} = ?" => $clientId,
            "{$this->col_stepName} = ?" => Assessment_Model_Assessment_Steps::STEP_FINISHED
        ));
    }

    /**
     * Fetches all assessments for a given client
     *
     * @param int $clientId
     *
     * @return Assessment_Model_Assessment[]
     */
    public function fetchAllAssessmentsForClient ($clientId)
    {
        return $this->fetchAll(array("{$this->col_clientId} = ?" => $clientId), "{$this->col_dateCreated} DESC", 100);
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
        return array("{$this->col_id} = ?" => $id);
    }

    /**
     * @param Assessment_Model_Assessment $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * This finds all assessments that have a master device using the $masterDeviceId and
     * sets the deviceModified field to 1
     *
     * @param int $masterDeviceId
     *
     * @return bool
     */
    public function setDevicesModifiedFlagOnAssessments ($masterDeviceId)
    {
        $sql    = "
    UPDATE assessments
	SET assessments.devicesModified=1
	WHERE assessments.id IN (
		SELECT di.rmsUploadId FROM device_instances AS di
        LEFT JOIN device_instance_master_devices AS dimd ON di.id = dimd.deviceInstanceId
        WHERE dimd.masterDeviceId = ?
		GROUP BY di.rmsUploadId
	);";
        $query  = $this->getDbTable()->getAdapter()->query($sql, $masterDeviceId);
        $result = $query->execute();

        return $result;
    }
}