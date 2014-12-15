<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ContractTemplateSectionModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class ContractTemplateSectionMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers
 */
class ContractTemplateSectionMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column name definitions. Define all columns up here and use them down below.
     */
    public $col_contractTemplateId = 'contractTemplateId';
    public $col_contractSectionId  = 'contractSectionId';

    /*
     * Mapper Definitions
     */
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\ContractTemplateSectionDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return ContractTemplateSectionMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ContractTemplateSectionModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object ContractTemplateSectionModel
     *                The object to insert
     *
     * @return int The primary key of the new row
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ContractTemplateSectionModel to the database.
     *
     * @param $object     ContractTemplateSectionModel
     *                    The template model to save to the database
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
            $primaryKey [] = $data [$this->col_contractTemplateId];
            $primaryKey [] = $data [$this->col_contractSectionId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
            "{$this->col_contractTemplateId} = ?" => $primaryKey [0],
            "{$this->col_contractSectionId} = ?"  => $primaryKey [1]
        ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ContractTemplateSectionModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof ContractTemplateSectionModel)
        {
            $whereClause = array(
                "{$this->col_contractTemplateId} = ?" => $object->contractTemplateId,
                "{$this->col_contractSectionId} = ?"  => $object->contractSectionId
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_contractTemplateId} = ?" => $object [0],
                "{$this->col_contractSectionId} = ?"  => $object [1]
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a template based on it's primaryKey
     *
     * @param array $id The id of the template to find
     *
     * @return ContractTemplateSectionModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof ContractTemplateSectionModel)
        {
            return $result;
        }

        // Assuming we don't have a cached object, lets go get it.
        $result = $this->getDbTable()->find($id [0], $id [1]);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new ContractTemplateSectionModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a template
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return ContractTemplateSectionModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new ContractTemplateSectionModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all templates
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $count  int
     *                OPTIONAL: An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *                OPTIONAL: An SQL LIMIT offset.
     *
     * @return ContractTemplateSectionModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new ContractTemplateSectionModel($row->toArray());

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
            "{$this->col_contractTemplateId} = ?" => $id [0],
            "{$this->col_contractSectionId} = ?"  => $id [1]
        );
    }

    /**
     * @param ContractTemplateSectionModel $object
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array(
            $object->contractTemplateId,
            $object->contractSectionId
        );
    }

    /**
     * Fetches all the sections for a contact template
     *
     * @param int $contractTemplateId
     *
     * @return ContractTemplateSectionModel[]
     */
    public function fetchAllForContractTemplate ($contractTemplateId)
    {
        return $this->fetchAll(array(
            "{$this->col_contractTemplateId} = ?" => $contractTemplateId,
        ));
    }
}

