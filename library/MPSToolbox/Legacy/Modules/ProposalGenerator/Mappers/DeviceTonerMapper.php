<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceTonerModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class DeviceTonerMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class DeviceTonerMapper extends My_Model_Mapper_Abstract
{
    public $col_tonerId        = 'toner_id';
    public $col_masterDeviceId = 'master_device_id';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\DeviceTonerDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return DeviceTonerMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of DeviceTonerModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object DeviceTonerModel
     *                The object to insert
     * @return int The primary key of the new row
     */
    public function insert ($object)
    {
        return $this->save($object);
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceTonerModel to the database.
     *
     * @param $object     DeviceTonerModel
     *                    The Device Toner model to save to the database
     * @param $primaryKey mixed
     *                    Optional: The original primary key, in case we're changing it
     *
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $st = \Zend_Db_Table::getDefaultAdapter()->query('replace into `oem_printing_device_consumable` set `printing_device`='.$object->master_device_id.', `printer_consumable`='.$object->toner_id.', `userId`='.$object->userId.', `isApproved`='.($object->isSystemDevice?1:0));
        $st->execute($object->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $st->rowCount();
    }


    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceTonerModel or the
     *                primary key to delete
     *
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare('delete from oem_printing_device_consumable where printer_consumable=? and printing_device=?');
        if ($object instanceof DeviceTonerModel)
        {
            $st->execute([$object->toner_id, $object->master_device_id]);
        }
        else
        {
            $st->execute([$object[0], $object[1]]);
        }

        return $st->rowCount();
    }

    /**
     * Finds a Device Toner based on it's primaryKey
     *
     * @param $id array
     *            The id of the Device Toner to find
     *
     * @return DeviceTonerModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof DeviceTonerModel)
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
        $object = new DeviceTonerModel($row->toArray());

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
     * @return DeviceTonerModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new DeviceTonerModel($row->toArray());

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
     * @return DeviceTonerModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new DeviceTonerModel($row->toArray());

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
            "{$this->col_tonerId} = ?"        => $id[0],
            "{$this->col_masterDeviceId} = ?" => $id[1],
        ];
    }

    /**
     * @param DeviceTonerModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return [$object->toner_id, $object->master_device_id];
    }

    /**
     * Gets all the toners for a master device
     *
     * @param $masterDeviceId
     *
     * @return DeviceTonerModel[]
     */
    public function getDeviceToners ($masterDeviceId)
    {
        return $this->fetchAll(['master_device_id = ?' => $masterDeviceId]);
    }

    /**
     * Fetches all device toner entries for a toner id
     *
     * @param int $tonerId The toner id to lookup
     *
     * @return DeviceTonerModel[]
     */
    public function fetchDeviceTonersByTonerId ($tonerId)
    {
        return $this->fetchAll(["{$this->col_tonerId} = ?" => $tonerId]);
    }

}