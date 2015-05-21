<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use MPSToolbox\Legacy\Modules\Admin\Mappers\DealerRmsProviderMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsProviderModel;
use My_Model_Mapper_Abstract;
use Zend_Auth;
use Zend_Db_Table_Select;

/**
 * Class RmsProviderMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class RmsProviderMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_id   = 'id';
    public $col_name = 'name';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\RmsProviderDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return RmsProviderMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsProviderModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object RmsProviderModel
     *                The object to insert
     * @return int The primary key of the new row
     */
    public function insert ($object)
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsProviderModel to the database.
     *
     * @param $object     RmsProviderModel
     *                    The Rms_Provider model to save to the database
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
     *                This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsProviderModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof RmsProviderModel)
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
     * Finds a Rms_Provider based on it's primaryKey
     *
     * @param $id int
     *            The id of the Rms_Provider to find
     *
     * @return RmsProviderModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof RmsProviderModel)
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
        $object = new RmsProviderModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a Rms_Provider
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return RmsProviderModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new RmsProviderModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all Rms_Providers
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
     * @return RmsProviderModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        if (!$order)
        {
            $order = "{$this->col_name} ASC";
        }

        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new RmsProviderModel($row->toArray());

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
     * @param RmsProviderModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }


    public function fetchAllForDealerDropdown ($dealerId = null)
    {
        $dropDown = [];

        if (!$dealerId && Zend_Auth::getInstance()->hasIdentity())
        {
            $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
        }


        foreach (DealerRmsProviderMapper::getInstance()->fetchAllForDealer($dealerId) as $dealerRmsProvider)
        {
            $dropDown[$dealerRmsProvider->rmsProviderId] = $this->find($dealerRmsProvider->rmsProviderId)->name;
        }

        return $dropDown;
    }

    /**
     * Returns all the toner vendor manufacturer for a dealer
     *
     * @param null $dealerId
     *
     * @return RmsProviderModel[]
     */
    public function fetchAllForDealer ($dealerId)
    {
        $rmsProviders = [];
        foreach (DealerRmsProviderMapper::getInstance()->fetchAllForDealer($dealerId) as $dealerRmsProvider)
        {
            $rmsProviders[] = $this->find($dealerRmsProvider->rmsProviderId);
        }

        return $rmsProviders;
    }


    /**
     * Returns all the toner vendor manufacturer for use in dropdown
     *
     * @return array
     */
    public function fetchAllForDropdown ()
    {
        $dropDown = [];

        foreach ($this->fetchAll() as $rmsProvider)
        {
            $dropDown[$rmsProvider->id] = $rmsProvider->name;
        }

        return $dropDown;
    }
}