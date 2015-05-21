<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsExcludedRowModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class RmsExcludedRowMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class RmsExcludedRowMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_id            = 'id';
    public $col_rmsUploadId   = 'rmsUploadId';
    public $col_modelName     = 'modelName';
    public $col_manufacturer  = 'manufacturerName';
    public $col_csvLineNumber = 'csvLineNumber';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\RmsExcludedRowDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return RmsExcludedRowMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsExcludedRowModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object RmsExcludedRowModel
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsExcludedRowModel to the database.
     *
     * @param $object     RmsExcludedRowModel
     *                    The Rms_Excluded_Row model to save to the database
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
     *                This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsExcludedRowModel or the
     *                primary key to delete
     *
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof RmsExcludedRowModel)
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
     * Finds a Rms_Excluded_Row based on it's primaryKey
     *
     * @param $id int
     *            The id of the Rms_Excluded_Row to find
     *
     * @return RmsExcludedRowModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof RmsExcludedRowModel)
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
        $object = new RmsExcludedRowModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a Rms_Excluded_Row
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return RmsExcludedRowModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new RmsExcludedRowModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all Rms_Excluded_Rows
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
     * @return RmsExcludedRowModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new RmsExcludedRowModel($row->toArray());

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
     * @param RmsExcludedRowModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Deletes all rows related to a report
     *
     * @param int $rmsUploadId The report id
     *
     * @return int The number of rows  deleted
     */
    public function deleteAllForRmsUpload ($rmsUploadId)
    {
        return $this->getDbTable()->delete(["{$this->col_rmsUploadId} = ?" => $rmsUploadId]);
    }


    /**
     * @param      $rmsUploadId
     * @param      $sortColumn
     * @param      $sortDirection
     * @param null $limit
     * @param null $offset
     * @param bool $justCount
     *
     * @return array|int
     */
    public function fetchAllForRmsUpload ($rmsUploadId, $sortColumn, $sortDirection, $limit = null, $offset = null, $justCount = false)
    {
        $db          = $this->getDbTable()->getAdapter();
        $rmsUploadId = $db->quote($rmsUploadId, 'INTEGER');

        if ($justCount)
        {
            $select = $db->select()->from('rms_excluded_rows', "COUNT(*)")->where('rmsUploadId = ?', $rmsUploadId);

            return $db->query($select)->fetchColumn();
        }
        else
        {
            /*
             * Parse our order
             */

            $order = [];
            if ($sortColumn != $this->col_modelName && $sortColumn != $this->col_manufacturer)
            {
                $order[] = "{$sortColumn} {$sortDirection}";
                $order[] = "{$this->col_manufacturer} ASC";
                $order[] = "{$this->col_modelName} ASC";
            }
            else if ($sortColumn == $this->col_manufacturer)
            {
                $order[] = "{$this->col_manufacturer} {$sortDirection}";
                $order[] = "{$this->col_modelName} {$sortDirection}";
            }
            else if ($sortColumn == $this->col_modelName)
            {
                $order[] = "{$this->col_manufacturer} {$sortDirection}";
                $order[] = "{$this->col_modelName} {$sortDirection}";
            }
            else
            {
                $order[] = "{$this->col_csvLineNumber} ASC";
            }
            /*
             * Parse our Limit
             */

            if (!$limit)
            {
                $limit  = "25";
                $offset = ($offset > 0) ? $offset : 0;
            }

            $select = $db->select()->from('rms_excluded_rows', ['*', 'model' => "CONCAT(manufacturerName, ' ' , modelName)"])->where('rmsUploadId = ?', $rmsUploadId)->order($order)->limit($limit, $offset);

            $query = $db->query($select);

            $excludedRows = [];

            foreach ($query->fetchAll() as $row)
            {

                $excludedRow        = new RmsExcludedRowModel($row);
                $excludedRow->model = $row ['model'];
                $excludedRows[]     = $excludedRow;
            }

            return $excludedRows;
        }
    }

    /**
     * Counts how many excluded rows we have for the report
     *
     * @param $rmsUploadId
     *
     * @return int
     */
    public function countRowsForRmsUpload ($rmsUploadId)
    {
        return $this->count(["{$this->col_rmsUploadId} = ?" => $rmsUploadId]);
    }
}