<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers;

use Exception;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CategoryModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionCategoryModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class OptionCategoryMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers
 */
class OptionCategoryMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\OptionCategoryDbTable';

    /*
     * Define the primary key of the model association
     */
    public $col_categoryId = 'categoryId';
    public $col_optionId   = 'optionId';

    /**
     * Gets an instance of the mapper
     *
     * @return OptionCategoryMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionCategoryModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object OptionCategoryModel
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionCategoryModel to the database.
     *
     * @param $object     OptionCategoryModel
     *                    The optionCategory model to save to the database
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
            $primaryKey [] = $data [$this->col_categoryId];
            $primaryKey [] = $data [$this->col_optionId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
            "{$this->col_categoryId} = ?" => $primaryKey [0],
            "{$this->col_optionId} = ?"   => $primaryKey [1]
        ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionCategoryModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof OptionCategoryModel)
        {
            $whereClause = array(
                "{$this->col_categoryId} = ?" => $object->categoryId,
                "{$this->col_optionId} = ?"   => $object->optionId
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_categoryId} = ?" => $object [0],
                "{$this->col_optionId} = ?"   => $object [1]
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Deletes rows by optionId
     *
     * @param $object mixed
     *                This can either be a instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionModel or the optionId
     *
     * @return int The number of rows deleted
     */
    public function deleteByOptionId ($object)
    {
        if ($object instanceof OptionModel)
        {
            $whereClause = array(
                "{$this->col_optionId} = ?" => $object->id
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_optionId} = ?" => $object
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Deletes rows by category id
     *
     * @param $object mixed
     *                This can either be a instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionModel or the optionId
     *
     * @return int The number of rows deleted
     */
    public function deleteByCategoryId ($object)
    {
        if ($object instanceof CategoryModel)
        {
            $whereClause = array(
                "{$this->col_categoryId} = ?" => $object->id
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_categoryId} = ?" => $object
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a optionCategory based on it's primaryKey
     *
     * @param $id int
     *            The id of the optionCategory to find
     *
     * @return OptionCategoryModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof OptionCategoryModel)
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
        $object = new OptionCategoryModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a optionCategory
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return OptionCategoryModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new OptionCategoryModel($row->toArray());

        // Save the object into the cache
        $primaryKey [0] = $object->categoryId;
        $primaryKey [1] = $object->optionId;
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all optionCategorys
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
     * @return OptionCategoryModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new OptionCategoryModel($row->toArray());

            // Save the object into the cache
            $primaryKey [0] = $object->categoryId;
            $primaryKey [1] = $object->optionId;
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
            "{$this->col_categoryId} = ?" => $id [0],
            "{$this->col_optionId} = ?"   => $id [1]
        );
    }

    /**
     * Gets all categories by id!
     *
     * @param int $optionId
     *
     * @return CategoryModel[]
     */
    public function fetchAllCategoriesForOption ($optionId)
    {
        $categories = array();
        try
        {

            $categoryMapper   = CategoryMapper::getInstance();
            $optionCategories = $this->fetchAll(array(
                "{$this->col_optionId} = ?" => $optionId
            ));

            foreach ($optionCategories as $optionCategory)
            {
                $categories [] = $categoryMapper->find($optionCategory->categoryId);
            }
        }
        catch (Exception $e)
        {
            $categories = false;
        }

        return $categories;
    }

    /**
     * @param $object OptionCategoryModel
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array(
            $object->categoryId,
            $object->optionId
        );
    }
}

