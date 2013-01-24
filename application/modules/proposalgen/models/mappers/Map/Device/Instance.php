<?php
class Proposalgen_Model_Mapper_Map_Device_Instance extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_reportId = 'reportId';
    public $col_modelName = 'modelName';
    public $col_manufacturer = 'manufacturer';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_Map_Device_Instance';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_Map_Device_Instance
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_Map_Device_Instance to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Proposalgen_Model_Map_Device_Instance
     *                The object to insert
     *
     * @return int The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $this->unsetNullValues($object->toArray());

        // Remove the id
        unset($data ["{$this->col_reportId}"]);

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        $object->id = $id;

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of Proposalgen_Model_Map_Device_Instance to the database.
     *
     * @param $object     Proposalgen_Model_Map_Device_Instance
     *                    The Map_Device_Instance model to save to the database
     * @param $primaryKey mixed
     *                    Optional: The original primary key, in case we're changing it
     *
     * @throws BadMethodCallException
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        throw new BadMethodCallException("This method is not available for this mapper.");
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Proposalgen_Model_Map_Device_Instance or the
     *                primary key to delete
     *
     * @throws BadMethodCallException
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        throw new BadMethodCallException("This method is not available for this mapper.");
    }

    /**
     * Finds a Map_Device_Instance based on it's primaryKey
     *
     * @param $id int
     *            The id of the Map_Device_Instance to find
     *
     * @throws BadMethodCallException
     * @return Proposalgen_Model_Map_Device_Instance
     */
    public function find ($id)
    {
        throw new BadMethodCallException("This method is not available for this mapper.");
    }

    /**
     * Fetches a Map_Device_Instance
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @throws BadMethodCallException
     * @return Proposalgen_Model_Map_Device_Instance
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        throw new BadMethodCallException("This method is not available for this mapper.");
    }

    /**
     * Fetches all Proposalgen_Map_Device_Instances
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
     * @return Proposalgen_Model_Map_Device_Instance[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object     = new Proposalgen_Model_Map_Device_Instance($row->toArray());
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
     * @param Proposalgen_Model_Map_Device_Instance $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * This function fetches device instances to map
     *
     * @param int     $reportId
     * @param string  $sortColumn
     *            The column to sort by
     * @param string  $sortDirection
     *            The direction to sort
     * @param number  $limit
     *            The number of records to retrieve
     * @param number  $offset
     *            The record to start at
     * @param boolean $justCount
     *            If set to true this function will return an integer of the row count of all available rows
     *
     * @return number|Proposalgen_Model_DeviceInstance[] Returns an array, or if justCount is true then it will count how many rows are
     *           available
     */
    public function fetchAllForReport ($reportId, $sortColumn, $sortDirection, $limit = null, $offset = null, $justCount = false)
    {
        /*
         *Setup our where clause
         */
        $whereClause = array(
            "{$this->col_reportId} = ?" => $reportId
        );

        // If we're just counting we only need to return the count
        if ($justCount)
        {
            return $this->count($whereClause);
        }
        else
        {
            /*
             * Parse our order
             */

            $order = array();
            if ($sortColumn != $this->col_modelName && $sortColumn != $this->col_manufacturer)
            {
                $order[] = "{$sortColumn} {$sortDirection}";
                $order[] = "{$this->col_manufacturer} ASC";
                $order[] = "{$this->col_modelName} ASC";
            }
            else if ($sortColumn == $this->col_manufacturer)
            {
                $order[] = "{$this->col_manufacturer} {$sortDirection}";
                $order[] = "{$this->col_modelName} ASC";
            }
            else if ($sortColumn == $this->col_modelName)
            {
                $order[] = "{$this->col_manufacturer} ASC";
                $order[] = "{$this->col_modelName} {$sortDirection}";
            }

            /*
             * Parse our Limit
             */
            if ($limit > 0)
            {
                $offset = ($offset > 0) ? $offset : null;
            }

            return $this->fetchAll($whereClause, $order, $limit, $offset);
        }
    }
}