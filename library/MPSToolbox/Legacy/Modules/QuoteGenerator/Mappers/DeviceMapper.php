<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Expr;
use Zend_Db_Table;
use Zend_Db_Table_Select;

/**
 * Class DeviceMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers
 */
class DeviceMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\DeviceDbTable';

    /*
     * Define the primary key of the model association
    */
    public $col_masterDeviceId = 'masterDeviceId';
    public $col_dealerId       = 'dealerId';

    /**
     * Gets an instance of the mapper
     *
     * @return DeviceMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object DeviceModel
     *                The object to insert
     * @return int The primary key of the new row
     */
    public function insert ($object)
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceModel to the database.
     *
     * @param $object     DeviceModel
     *                    The device model to save to the database
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
            $primaryKey [] = $data [$this->col_dealerId];
        }

        $changed_fields = $this->changed_fields($this->getDbTable()->find($data[$this->col_masterDeviceId], $data[$this->col_dealerId])->current(), $object);

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_masterDeviceId} = ?" => $primaryKey[0],
            "{$this->col_dealerId} = ?"       => $primaryKey[1],
        ]);

        if ($rowsAffected) {
            $identity = \Zend_Auth::getInstance()->getIdentity();
            $db = \Zend_Db_Table::getDefaultAdapter();

            foreach ($changed_fields as $key=>$value) {
                $sql = "insert into `history` set `userId`={$identity->id}, `masterDeviceId`={$data[$this->col_masterDeviceId]}, `action`='Changed `{$key}` to: ".addslashes($value)."'";
                $sql .= ", dealerId={$data[$this->col_dealerId]}";
                $db->query($sql);
            }
        }

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof DeviceModel)
        {
            $whereClause = [
                "{$this->col_masterDeviceId} = ?" => $object->masterDeviceId,
                "{$this->col_dealerId} = ?"       => $object->dealerId,

            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_masterDeviceId} = ?" => $object[0],
                "{$this->col_dealerId} = ?"       => $object[1],
            ];
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a device based on it's primaryKey
     *
     * @param array $id The id of the device to find
     *
     * @return DeviceModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof DeviceModel)
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
        $object = new DeviceModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a device
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return DeviceModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new DeviceModel($row->toArray());

        // Save the object into the cache
        $primaryKey [0] = $object->masterDeviceId;
        $primaryKey [1] = $object->dealerId;
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all devices
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
     * @return DeviceModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new DeviceModel($row->toArray());

            // Save the object into the cache
            $primaryKey [0] = $object->masterDeviceId;
            $primaryKey [1] = $object->dealerId;
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param array $id
     *
     * @return array
     */
    public function getWhereId ($id)
    {
        return [
            "{$this->col_masterDeviceId} = ?" => $id [0],
            "{$this->col_dealerId} = ?"       => $id [1],
        ];
    }

    /**
     * @param DeviceModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return [
            (int)$object->masterDeviceId,
            (int)$object->dealerId,
        ];
    }

    /**
     * Fetches a list of devices for the dealer
     *
     * @param int $dealerId
     *
     * @return DeviceModel[]
     */
    public function fetchQuoteDeviceListForDealer ($dealerId)
    {
        $devices = $this->fetchAll(["{$this->col_dealerId} = ?" => $dealerId], null, 200);

        return $devices;
    }

    public function searchByName ($searchTerm, $dealerId = null, $manufacturerId = null)
    {
        $manufacturerMapper = ManufacturerMapper::getInstance();
        $masterDeviceMapper = MasterDeviceMapper::getInstance();

        $returnLimit = 10;
        $sortOrder   = 'device_name ASC';

        $caseStatement = new Zend_Db_Expr("*, CASE WHEN md.isCopier AND md.tonerConfigId = 1 THEN 'Monochrome MFP'
            WHEN md.isCopier AND md.tonerConfigId > 1 THEN 'Color MFP'
            WHEN NOT md.isCopier AND md.tonerConfigId > 1 THEN 'Color '
            WHEN NOT md.isCopier AND md.tonerConfigId = 1 THEN 'Monochrome'
            END AS deviceType");


        $db     = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from([$this->getTableName()], $caseStatement)
               ->joinLeft(
                   ["md" => $masterDeviceMapper->getTableName()],
                   "{$this->getTableName()}.{$this->col_masterDeviceId} = md.{$masterDeviceMapper->col_id}",
                   ["{$masterDeviceMapper->col_id}"]
               )
               ->joinLeft(
                   ["m" => $manufacturerMapper->getTableName()],
                   "md.{$masterDeviceMapper->col_manufacturerId} = m.{$manufacturerMapper->col_id}",
                   [
                       $manufacturerMapper->col_fullName,
                       "device_name" => new Zend_Db_Expr("CONCAT({$manufacturerMapper->col_fullName},' ', {$masterDeviceMapper->col_modelName})")
                   ]
               )
               ->where("CONCAT({$manufacturerMapper->col_fullName},' ', {$masterDeviceMapper->col_modelName}) LIKE ? AND m.isDeleted = 0")
               ->limit($returnLimit)
               ->order($sortOrder);

        /*
         * Filter by manufacturer id if provided
         */
        if ($manufacturerId)
        {
            $select->where("{$manufacturerMapper->col_fullName} = ?", $manufacturerId);
        }

        if ($dealerId)
        {
            $select->where("{$this->getTableName()}.$this->col_dealerId = ?", $dealerId);
        }

        $stmt   = $db->query($select, $searchTerm);
        $result = $stmt->fetchAll();

        return $result;
    }
}

