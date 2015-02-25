<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingSetModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class TonerVendorRankingSetMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class TonerVendorRankingSetMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_id = 'id';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\TonerVendorRankingSetDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return TonerVendorRankingSetMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of  MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingSetModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object  TonerVendorRankingSetModel
     *                 The object to insert
     *
     * @return int The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $this->unsetNullValues($object->toArray());

        // Remove the id
        unset($data ["{$this->col_id}"]);

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        $object->id = $id;

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingSetModel to the database.
     *
     * @param $object      TonerVendorRankingSetModel
     *                     The Toner Vendor Ranking Set model to save to the database
     * @param $primaryKey  mixed
     *                     Optional: The original primary key, in case we're changing it
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
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_id} = ?" => $primaryKey,
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingSetModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof TonerVendorRankingSetModel)
        {
            $whereClause = [
                "{$this->col_id} = ?" => $object->id,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_id} = ?" => $object,
            ];
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a Toner Vendor Ranking Set based on it's primaryKey
     *
     * @param $id int
     *            The id of the Toner Vendor Ranking Set to find
     *
     * @return  TonerVendorRankingSetModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof TonerVendorRankingSetModel)
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
        $object = new  TonerVendorRankingSetModel($row->toArray());

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
     * @return  TonerVendorRankingSetModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new  TonerVendorRankingSetModel($row->toArray());

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
     * @return  TonerVendorRankingSetModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new  TonerVendorRankingSetModel($row->toArray());

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
        return [
            "{$this->col_id} = ?" => $id,
        ];
    }

    /**
     * @param  TonerVendorRankingSetModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Saves the rankings that are passed to it
     * If the id is null, it creates one for the quote setting
     *
     * @param $id          int
     * @param $rankingSets array
     *
     * @return int the toner_vendor_ranking_set id
     */
    public function saveRankingSets ($id, $rankingSets)
    {
        $tonerVendorRankingMapper = TonerVendorRankingMapper::getInstance();
        $hasChanged               = true;

        // If we have don't have a ranking set we need to get it's id and save the ranking
        if ($id === null)
        {
            $id = TonerVendorRankingSetMapper::getInstance()->insert(new TonerVendorRankingSetModel());
        }
        else
        {
            $rankArray = TonerVendorRankingSetMapper::getInstance()->find($id)->getRanksAsArray();

            foreach ($rankArray as $order => $manufacturerId)
            {
            }

            if (true)
            {
                $tonerVendorRankingMapper->deleteByTonerVendorRankingId($id);
            }
            else
            {
                $hasChanged = false;
            }
        }

        if ($hasChanged)
        {
            // Save the new set of rankings
            foreach ($rankingSets as $key => $value)
            {
                $tonerVendorRanking                          = new TonerVendorRankingModel();
                $tonerVendorRanking->manufacturerId          = $value;
                $tonerVendorRanking->tonerVendorRankingSetId = $id;
                $tonerVendorRanking->rank                    = $key;
                $tonerVendorRankingMapper->insert($tonerVendorRanking);
            }
        }

        return $id;
    }
}