<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class RmsUploadMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class RmsUploadMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_id            = 'id';
    public $col_clientId      = 'clientId';
    public $col_rmsProviderId = 'rmsProviderId';
    public $col_uploadDate    = 'uploadDate';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\RmsUploadDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return RmsUploadMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object RmsUploadModel
     *                The object to insert
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadModel to the database.
     *
     * @param $object     RmsUploadModel
     *                    The Rms_Upload model to save to the database
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
     *                This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof RmsUploadModel)
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
     * Finds a Rms_Upload based on it's primaryKey
     *
     * @param $id int
     *            The id of the Rms_Upload to find
     *
     * @return RmsUploadModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof RmsUploadModel)
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
        $object = new RmsUploadModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a Rms_Upload
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return RmsUploadModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new RmsUploadModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all Rms_Uploads
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
     * @return RmsUploadModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new RmsUploadModel($row->toArray());

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
     * @param RmsUploadModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Fetches all available uploads for a client
     *
     * @param      $clientId
     * @param null $order
     * @param int  $count
     * @param null $offset
     * @param bool $justCount
     *
     * @return RmsUploadModel[]|int
     */
    public function fetchAllForClient ($clientId, $order = null, $count = 25, $offset = null, $justCount = false)
    {
        /**
         * Send back the count
         */
        if ($justCount)
        {
            return $this->count(array("{$this->col_clientId} = ?" => $clientId));
        }

        if ($order === null)
        {
            $order = "{$this->col_uploadDate} DESC";
        }

        return $this->fetchAll(array("{$this->col_clientId} = ?" => $clientId), $order, $count, $offset);
    }
}