<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use MPSToolbox\Legacy\Modules\Admin\Mappers\DealerTonerVendorMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorManufacturerModel;
use My_Model_Mapper_Abstract;
use Zend_Auth;
use Zend_Db_Table_Select;

/**
 * Class TonerVendorManufacturerMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class TonerVendorManufacturerMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_manufacturerId = 'manufacturerId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\TonerVendorManufacturerDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return TonerVendorManufacturerMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorManufacturerModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object TonerVendorManufacturerModel
     *                The object to insert
     *
     * @return int The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $this->unsetNullValues($object->toArray());

        $id = $this->getDbTable()->insert($data);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorManufacturerModel to the database.
     *
     * @param $object     TonerVendorManufacturerModel
     *                    The TonerVendorManufacturer model to save to the database
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
            $primaryKey = $data [$this->col_manufacturerId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
            "{$this->col_manufacturerId} = ?" => $primaryKey
        ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorManufacturerModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof TonerVendorManufacturerModel)
        {
            $whereClause = array(
                "{$this->col_manufacturerId} = ?" => $object->manufacturerId
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_manufacturerId} = ?" => $object
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a TonerVendorManufacturer based on it's primaryKey
     *
     * @param $id int
     *            The id of the TonerVendorManufacturer to find
     *
     * @return TonerVendorManufacturerModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof TonerVendorManufacturerModel)
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
        $object = new TonerVendorManufacturerModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a TonerVendorManufacturer
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return TonerVendorManufacturerModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new TonerVendorManufacturerModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all TonerVendorManufacturers
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
     * @return TonerVendorManufacturerModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new TonerVendorManufacturerModel($row->toArray());

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
            "{$this->col_manufacturerId} = ?" => $id
        );
    }

    /**
     * @param TonerVendorManufacturerModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->manufacturerId;
    }

    /**
     * Returns all the toner vendor manufacturer for use in dropdown
     *
     * @param null $dealerId
     *
     * @return array
     */
    public function fetchAllForDealerDropdown ($dealerId = null)
    {
        $dropDown = array();

        if (!$dealerId && Zend_Auth::getInstance()->hasIdentity())
        {
            $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
        }


        foreach (DealerTonerVendorMapper::getInstance()->fetchAllForDealer($dealerId) as $dealerTonerVendor)
        {
            $dropDown[$dealerTonerVendor->manufacturerId] = ManufacturerMapper::getInstance()->find($dealerTonerVendor->manufacturerId)->fullname;
        }

        return $dropDown;
    }

    /**
     * Returns all the toner vendor manufacturer for use in dropdown
     *
     * @return array
     */
    public function fetchAllForDropdown ()
    {
        $dropDown = array();

        foreach ($this->fetchAll() as $tonerVendorManufacturer)
        {
            $dropDown[$tonerVendorManufacturer->manufacturerId] = $tonerVendorManufacturer->getManufacturerName();
        }

        return $dropDown;
    }

    /**
     * Should be used to call the stored procedure of updateTonerVendorByManufacturerId to update
     * the toner manufacturer table after insert / deleting a toner object
     *
     * @param $manufacturerId
     */
    public function updateTonerVendorByManufacturerId ($manufacturerId)
    {
        $db    = $this->getDbTable()->getDefaultAdapter();
        $sql   = $db->quoteInto("CALL updateTonerVendorByManufacturerId(?)", $manufacturerId, 'INTEGER');
        $query = $db->query($sql);
        $query->execute();
    }
}