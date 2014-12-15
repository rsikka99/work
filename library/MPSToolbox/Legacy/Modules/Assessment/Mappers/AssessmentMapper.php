<?php

namespace MPSToolbox\Legacy\Modules\Assessment\Mappers;

use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentModel;
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentStepsModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class AssessmentMapper
 *
 * @package MPSToolbox\Legacy\Modules\Assessment\Mappers
 */
class AssessmentMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column name definitions. Define all columns up here and use them down below.
     */
    public $col_id           = 'id';
    public $col_clientId     = 'clientId';
    public $col_rmsUploadId  = 'rmsUploadId';
    public $col_dateCreated  = 'dateCreated';
    public $col_lastModified = 'lastModified';
    public $col_stepName     = 'stepName';

    /*
     * Mapper Definitions
     */
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\Assessment\DbTables\AssessmentDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return AssessmentMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Assessment_Model_Report to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object AssessmentModel
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
     * @param $object     AssessmentModel
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
        if ($object instanceof AssessmentModel)
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
     * @return AssessmentModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof AssessmentModel)
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
        $object = new AssessmentModel($row->toArray());

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
     * @return AssessmentModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new AssessmentModel($row->toArray());

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
     * @return AssessmentModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new AssessmentModel($row->toArray());

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
     * @return AssessmentModel[]
     */
    public function fetchAllUnfinishedAssessmentsForClient ($clientId)
    {
        return $this->fetchAll(array(
            "{$this->col_clientId} = ?"  => $clientId,
            "{$this->col_stepName} <> ?" => AssessmentStepsModel::STEP_FINISHED
        ));
    }

    /**
     * Fetches all assessments for a given client
     *
     * @param int $clientId
     *
     * @return AssessmentModel[]
     */
    public function fetchAllFinishedAssessmentsForClient ($clientId)
    {
        return $this->fetchAll(array(
            "{$this->col_clientId} = ?" => $clientId,
            "{$this->col_stepName} = ?" => AssessmentStepsModel::STEP_FINISHED
        ));
    }

    /**
     * Fetches all assessments for a given client
     *
     * @param int $clientId
     * @param int $rmsUploadId
     *
     * @return \MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentModel[]
     */
    public function fetchAllAssessmentsForClient ($clientId, $rmsUploadId)
    {
        return $this->fetchAll([
            "{$this->col_clientId} = ?"    => $clientId,
            "{$this->col_rmsUploadId} = ?" => $rmsUploadId
        ], "{$this->col_lastModified} DESC", 100);
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
     * @param AssessmentModel $object
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