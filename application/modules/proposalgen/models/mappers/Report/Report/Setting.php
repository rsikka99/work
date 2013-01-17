<?php
class Proposalgen_Model_Mapper_Report_Report_Setting extends My_Model_Mapper_Abstract
{
    /*
     * Column name definitions. Define all columns up here and use them down below.
     */
    public $col_reportId = 'reportId';

    /*
     * Mapper Definitions
     */
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_Report_Report_Setting';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_Report_Report_Setting
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_Report_Report_Setting to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Proposalgen_Model_Report_Report_Setting
     *                The object to insert
     *
     * @return mixed The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $object->toArray();

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of Proposalgen_Model_Report_Report_Setting to the database.
     *
     * @param $object     Proposalgen_Model_Report_Report_Setting
     *                    The report_report_setting model to save to the database
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
            $primaryKey = $data [$this->col_reportId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
                                                                "{$this->col_reportId} = ?" => $primaryKey
                                                           ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Proposalgen_Model_Report_Report_Setting or the
     *                primary key to delete
     *
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Proposalgen_Model_Report_Report_Setting)
        {
            $whereClause = array(
                "{$this->col_reportId} = ?" => $object->reportId
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_reportId} = ?" => $object
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a report_report_setting based on it's primaryKey
     *
     * @param $id int
     *            The id of the report_report_setting to find
     *
     * @return Proposalgen_Model_Report_Report_Setting
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Proposalgen_Model_Report_Report_Setting)
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
        $object = new Proposalgen_Model_Report_Report_Setting($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a report_report_setting
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: A SQL OFFSET value.
     *
     * @return Proposalgen_Model_Report_Report_Setting
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Proposalgen_Model_Report_Report_Setting($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all report_report_settings
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
     * @return Proposalgen_Model_Report_Report_Setting[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Proposalgen_Model_Report_Report_Setting($row->toArray());

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
            "{$this->col_reportId} = ?" => $id
        );
    }

    /**
     * @param Proposalgen_Model_Report_Report_Setting $object
     *
     * @return mixed
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->reportId;
    }
}