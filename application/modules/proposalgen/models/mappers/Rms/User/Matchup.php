<?php
class Proposalgen_Model_Mapper_Rms_User_Matchup extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_rmsProviderId = 'rmsProviderId';
    public $col_rmsModelId = 'rmsModelId';
    public $col_masterDeviceId = 'masterDeviceId';
    public $col_userId = 'userId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_Rms_User_Matchup';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_Rms_User_Matchup
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_Rms_User_Matchup to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Proposalgen_Model_Rms_User_Matchup
     *                The object to insert
     *
     * @return mixed The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $this->unsetNullValues($object->toArray());

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of Proposalgen_Model_Rms_User_Matchup to the database.
     *
     * @param $object     Proposalgen_Model_Rms_User_Matchup
     *                    The Rms_User_Matchup model to save to the database
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
            $primaryKey = $this->getPrimaryKeyValueForObject($object);
        }

        $whereClause = $this->getWhereId($primaryKey);

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, $whereClause);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Proposalgen_Model_Rms_User_Matchup or the
     *                primary key to delete
     *
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Proposalgen_Model_Rms_User_Matchup)
        {
            $object = $this->getPrimaryKeyValueForObject($object);
        }

        $whereClause = $this->getWhereId($object);

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a Rms_User_Matchup based on it's primaryKey
     *
     * @param $id int
     *            The id of the Rms_User_Matchup to find
     *
     * @return Proposalgen_Model_Rms_User_Matchup
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Proposalgen_Model_Rms_User_Matchup)
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
        $object = new Proposalgen_Model_Rms_User_Matchup($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a Rms_User_Matchup
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return Proposalgen_Model_Rms_User_Matchup
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Proposalgen_Model_Rms_User_Matchup($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all Rms_User_Matchups
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
     * @return Proposalgen_Model_Rms_User_Matchup[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Proposalgen_Model_Rms_User_Matchup($row->toArray());

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
            "{$this->col_rmsProviderId} = ?"  => $id[0],
            "{$this->col_rmsModelId} = ?"     => $id[1],
            "{$this->col_masterDeviceId} = ?" => $id[2],
            "{$this->col_masterDeviceId} = ?" => $id[3]
        );
    }

    /**
     * @param Proposalgen_Model_Rms_User_Matchup $object
     *
     * @return mixed
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array($object->rmsProviderId, $object->rmsModelId, $object->masterDeviceId, $object->userId);
    }
}