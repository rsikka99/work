<?php
class Proposalgen_Model_Mapper_Report extends My_Model_Mapper_Abstract
{
    /*
     * Column name definitions. Define all columns up here and use them down below.
     */
    public $col_id = 'id';
    public $col_userId = 'userId';
    public $col_reportStage = 'reportStage';

    /*
     * Mapper Definitions
     */
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_Report';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_Report
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_Report to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Proposalgen_Model_Report
     *                The object to insert
     *
     * @return mixed The primary key of the new row
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
     * Saves (updates) an instance of Proposalgen_Model_Report to the database.
     *
     * @param $object     Proposalgen_Model_Report
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
     *                This can either be an instance of Proposalgen_Model_Report or the
     *                primary key to delete
     *
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Proposalgen_Model_Report)
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
     * @return Proposalgen_Model_Report
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Proposalgen_Model_Report)
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
        $object = new Proposalgen_Model_Report($row->toArray());

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
     * @return Proposalgen_Model_Report
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Proposalgen_Model_Report($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all reports
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
     * @return Proposalgen_Model_Report[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Proposalgen_Model_Report($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Fetches all reports for a given user regardless of the report stage
     *
     * @param int $userId
     *
     * @return Proposalgen_Model_Report[]
     */
    public function fetchAllReportsForUser ($userId)
    {
        return $this->fetchAll(array(
                                    "{$this->col_userId} = ?" => $userId
                               ));
    }

    /**
     * Fetches all finished reports for a given user
     *
     * @param int $userId
     *
     * @return Proposalgen_Model_Report[]
     */
    public function fetchAllFinishedReportsForUser ($userId)
    {
        return $this->fetchAll(array(
                                    "{$this->col_userId} = ?"      => $userId,
                                    "{$this->col_reportStage} = ?" => Proposalgen_Model_Report_Step::STEP_FINISHED
                               ));
    }

    /**
     * Fetches all unfinished reports for a given user
     *
     * @param int $userId
     *
     * @return Proposalgen_Model_Report[]
     */
    public function fetchAllUnfinishedReportsForUser ($userId)
    {
        return $this->fetchAll(array(
                                    "{$this->col_userId} = ?"       => $userId,
                                    "{$this->col_reportStage} <> ?" => Proposalgen_Model_Report_Step::STEP_FINISHED
                               ));
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
     * @param Proposalgen_Model_Report $object
     *
     * @return mixed
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }
}