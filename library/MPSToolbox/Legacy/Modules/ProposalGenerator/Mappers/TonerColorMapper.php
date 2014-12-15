<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class TonerColorMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class TonerColorMapper extends My_Model_Mapper_Abstract
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
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\TonerColorDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return TonerColorMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object TonerColorModel
     *                The object to insert
     *
     * @return mixed The primary key of the new row
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel to the database.
     *
     * @param $object     TonerColorModel
     *                    The TonerColor model to save to the database
     * @param $primaryKey mixed
     *                    Optional: The original primary key, in case we're changing it
     *
     * @return string The number of rows affected
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
     *                This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel or the
     *                primary key to delete
     *
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof TonerColorModel)
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
     * Finds a TonerColor based on it's primaryKey
     *
     * @param $id int
     *            The id of the TonerColor to find
     *
     * @return TonerColorModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof TonerColorModel)
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
        $object = new TonerColorModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a TonerColor
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return TonerColorModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new TonerColorModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all TonerColors
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
     * @return TonerColorModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new TonerColorModel($row->toArray());

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
     * @param TonerColorModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }


    /**
     * Gets an array of toner configurations with the id as the key and the name as the value
     *
     * @return array
     */
    public function fetchAllForMultiOptions ()
    {
        $tonerColors = array();
        foreach ($this->fetchAll(null, "{$this->col_id} ASC") as $tonerColor)
        {
            $tonerColors[$tonerColor->id] = $tonerColor->name;
        }

        return $tonerColors;
    }
}