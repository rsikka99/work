<?php
/**
 * Class Proposalgen_Model_Mapper_Toner_Vendor_Ranking
 */
class Proposalgen_Model_Mapper_Toner_Vendor_Ranking extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_tonerVendorRankingSetId = 'tonerVendorRankingSetId';
    public $col_manufacturerId = 'manufacturerId';
    public $col_rank = 'rank';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_Toner_Vendor_Ranking';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_Toner_Vendor_Ranking
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_Toner_Vendor_Ranking to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Proposalgen_Model_Toner_Vendor_Ranking
     *                The object to insert
     *
     * @return int The primary key of the new row
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
     * Saves (updates) an instance of Proposalgen_Model_Toner_Vendor_Ranking to the database.
     *
     * @param $object     Proposalgen_Model_Toner_Vendor_Ranking
     *                    The Toner Vendor Rankingmodel to save to the database
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

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
                                                                "{$this->col_tonerVendorRankingSetId} = ?" => $primaryKey[0],
                                                                "{$this->col_manufacturerId} = ?"          => $primaryKey[1]
                                                           ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Proposalgen_Model_Toner_Vendor_Ranking or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Proposalgen_Model_Toner_Vendor_Ranking)
        {
            $whereClause = array(
                "{$this->col_tonerVendorRankingSetId} = ?" => $object->tonerVendorRankingSetId,
                "{$this->col_manufacturerId} = ?"          => $object->manufacturerId
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_tonerVendorRankingSetId} = ?" => $object[0],
                "{$this->col_manufacturerId} = ?"          => $object[1]
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Deletes all the rankings with the id that has been passed
     *
     * @param $id int
     *
     * @return int the amount of rows delete
     */
    public function deleteByTonerVendorRankingId ($id)
    {
        $whereClause = array(
            "{$this->col_tonerVendorRankingSetId} = ?" => $id,
        );

        return $this->getDbTable()->delete($whereClause);
    }

    /**
     * Finds a Toner Vendor Rankingbased on it's primaryKey
     *
     * @param $id array
     *            The id of the Toner Vendor Rankingto find
     *
     * @return Proposalgen_Model_Toner_Vendor_Ranking
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Proposalgen_Model_Toner_Vendor_Ranking)
        {
            return $result;
        }

        // Assuming we don't have a cached object, lets go get it.
        $result = $this->getDbTable()->find((int)$id[0], (int)$id[1]);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new Proposalgen_Model_Toner_Vendor_Ranking($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a Template
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return Proposalgen_Model_Toner_Vendor_Ranking
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Proposalgen_Model_Toner_Vendor_Ranking($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all Templates
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
     * @return Proposalgen_Model_Toner_Vendor_Ranking[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Proposalgen_Model_Toner_Vendor_Ranking($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param array $id
     *
     * @return array
     */
    public function getWhereId ($id)
    {
        return array(
            "{$this->col_tonerVendorRankingSetId} = ?" => $id[0],
            "{$this->col_manufacturerId} = ?"          => $id[1]
        );
    }

    /**
     * @param Proposalgen_Model_Toner_Vendor_Ranking $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array(
            $object->tonerVendorRankingSetId,
            $object->manufacturerId
        );
    }

    /**
     * Gets all the ranks by for a rank id
     *
     * @param $rankId
     *
     * @return Proposalgen_Model_Toner_Vendor_Ranking[]
     */
    public function fetchAllByRankingSetIdAsArray ($rankId)
    {
        $selectedRanks = array();

        if ($rankId !== null)
        {
            foreach ($this->fetchAll(array("{$this->col_tonerVendorRankingSetId} = ?" => $rankId), "{$this->col_rank} ASC") as $tonerVendorRanking)
            {
                $selectedRanks[] = $tonerVendorRanking->manufacturerId;
            }
        }

        return $selectedRanks;
    }

    /**
     * @param $rankId
     *
     * @return Proposalgen_Model_Toner_Vendor_Ranking[]
     */
    public function fetchAllByRankingSetId ($rankId)
    {
        return $this->fetchAll(array("{$this->col_tonerVendorRankingSetId} = ?" => $rankId), "{$this->col_rank} ASC");
    }
}