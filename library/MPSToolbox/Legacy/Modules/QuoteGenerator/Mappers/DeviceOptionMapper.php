<?php
namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceOptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class DeviceOptionMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers
 */
class DeviceOptionMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\DeviceOptionDbTable';

    /*
     * Define the primary key of the model association
     */
    public $col_masterDeviceId = 'masterDeviceId';
    public $col_optionId       = 'optionId';
    public $col_dealerId       = 'dealerId';

    /**
     * Gets an instance of the mapper
     *
     * @return DeviceOptionMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceOptionModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object DeviceOptionModel
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceOptionModel to the database.
     *
     * @param $object     DeviceOptionModel
     *                    The deviceOption model to save to the database
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
            $primaryKey [] = $data [$this->col_masterDeviceId];
            $primaryKey [] = $data [$this->col_optionId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
            "{$this->col_masterDeviceId} = ?" => $primaryKey [0],
            "{$this->col_optionId} = ?"       => $primaryKey [1]
        ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceOptionModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof DeviceOptionModel)
        {
            $whereClause = array(
                "{$this->col_masterDeviceId} = ?" => $object->masterDeviceId,
                "{$this->col_optionId} = ?"       => $object->optionId
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_masterDeviceId} = ?" => $object [0],
                "{$this->col_optionId} = ?"       => $object [1]
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Deletes all device options by device id;
     *
     * @param int $deviceId
     *
     * @return int The amount of rows affected.
     */
    public function deleteOptionsByDeviceId ($deviceId)
    {
        return $this->getDbTable()->delete(array(
            "{$this->col_masterDeviceId} = ?" => $deviceId
        ));
    }

    /**
     * Finds a deviceOption based on it's primaryKey
     *
     * @param array|DeviceOptionModel $id The id of the deviceOption to find
     *
     * @return DeviceOptionModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof DeviceOptionModel)
        {
            return $result;
        }

        // Assuming we don't have a cached object, lets go get it.
        $result = $this->getDbTable()->find(array('masterDeviceId' => $id [0]), array('optionId' => $id [1]));
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new DeviceOptionModel($row->toArray());

        // Save the object into the cache
        $primaryKey [0] = $object->masterDeviceId;
        $primaryKey [1] = $object->optionId;
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a deviceOption
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return DeviceOptionModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new DeviceOptionModel($row->toArray());

        // Save the object into the cache
        $primaryKey [0] = $object->masterDeviceId;
        $primaryKey [1] = $object->optionId;
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all deviceOptions
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
     * @return DeviceOptionModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new DeviceOptionModel($row->toArray());

            // Save the object into the cache
            $primaryKey [0] = $object->masterDeviceId;
            $primaryKey [1] = $object->optionId;
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }


    /**
     * Fetches all options by device
     *
     * @param $id int
     *            The id of the deviceOption to find
     *
     * @return OptionModel[]
     */
    public function fetchAllOptionsByDeviceId ($id)
    {
        $resultSet = $this->getDbTable()->fetchAll(array('masterDeviceId = ?' => $id));
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object     = new DeviceOptionModel($row->toArray());
            $option     = OptionMapper::getInstance()->find($object->optionId);
            $entries [] = $option;
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
            "{$this->col_masterDeviceId} = ?" => $id [0],
            "{$this->col_optionId} = ?"       => $id [1]
        );
    }

    /**
     * @param DeviceOptionModel $object
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array(
            $object->masterDeviceId,
            $object->optionId
        );
    }

    /**
     * Fetches a list of options for the dealer
     *
     * @param int $dealerId
     *
     * @return DeviceOptionModel[]
     */
    public function fetchDeviceOptionListForDealer ($dealerId)
    {
        $options = $this->fetchAll(array("{$this->col_dealerId} = ?" => $dealerId));

        return $options;
    }

    /**
     * Fetches a list of device options for the dealer and masterDevice id
     *
     * @param int $masterDeviceId
     * @param int $dealerId
     *
     * @return DeviceOptionModel[]
     */
    public function fetchDeviceOptionListForDealerAndDevice ($masterDeviceId, $dealerId)
    {
        $options = $this->fetchAll(array(
                "{$this->col_masterDeviceId} = ?" => $masterDeviceId,
                "{$this->col_dealerId} = ?"       => $dealerId)
        );

        return $options;
    }
}

