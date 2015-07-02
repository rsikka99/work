<?php
namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationOptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceOptionModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class OptionMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers
 */
class OptionMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\OptionDbTable';

    /*
     * Define the primary key of the model association
     */
    public $col_id       = 'id';
    public $col_dealerId = 'dealerId';

    /**
     * Gets an instance of the mapper
     *
     * @return OptionMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object OptionModel
     *                The object to insert
     * @return int The primary key of the new row
     */
    public function insert ($object)
    {
        // Get an array of data to save
        $data = $object->toArray();

        // Remove the id
        unset($data [$this->col_id]);

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        $object->id = $id;

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionModel to the database.
     *
     * @param $object     OptionModel
     *                    The option model to save to the database
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
     *                This can either be an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof OptionModel)
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
     * Finds a option based on it's primaryKey
     *
     * @param $id int
     *            The id of the option to find
     *
     * @return OptionModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof OptionModel)
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
        $object = new OptionModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a option
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return OptionModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new OptionModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all options
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
     * @return OptionModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new OptionModel($row->toArray());

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
     * Fetches all options that are available to be added to a device (Anything that is not already added).
     *
     * @param int $id
     *            The primary key of a device
     *
     * @return OptionModel[]
     *            The list of available options to add
     */
    public function fetchAllAvailableOptionsForDevice ($id)
    {
        $devOptTableName = DeviceOptionMapper::getInstance()->getTableName();

        $sql = "SELECT * FROM {$this->getTableName()} AS opt
                WHERE NOT EXISTS (
                    SELECT * FROM {$devOptTableName} AS do
                    WHERE do.masterDeviceId = ? AND do.optionId = opt.id
                )
                ORDER BY  opt.name ASC
                ";

        $resultSet = $this->getDbTable()
                          ->getAdapter()
                          ->fetchAll($sql, $id);

        $entries = [];
        foreach ($resultSet as $row)
        {
            $object = new OptionModel($row);

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Fetches all options for a device
     *
     * @param int $id
     *            The primary key of a device
     *
     * @return OptionModel[]
     *            The list of options
     */
    public function fetchAllDeviceOptionsForDevice ($id)
    {
        $deviceOptionMapper = DeviceOptionMapper::getInstance();
        $devOptTableName    = $deviceOptionMapper->getTableName();

        $sql = "SELECT * FROM {$this->getTableName()} AS opt
        		JOIN {$devOptTableName} AS do ON opt.{$this->col_id} = do.optionId
                    WHERE do.masterDeviceId = ? AND do.optionId = opt.id
                ORDER BY  opt.name ASC
        ";

        $resultSet = $this->getDbTable()
                          ->getAdapter()
                          ->fetchAll($sql, $id);
        $entries   = [];

        foreach ($resultSet as $row)
        {
            $deviceOption = new DeviceOptionModel($row);
            $deviceOptionMapper->saveItemToCache($deviceOption);
            $option = new OptionModel($row);
            $deviceOption->setOption($option);
            // Save the object into the cache
            $this->saveItemToCache($option);

            $entries [] = $deviceOption;
        }

        return $entries;
    }

    /**
     * @param OptionModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Fetches all options for a device
     *
     * @param int $id
     *            The primary key of a device
     *
     * @return DeviceConfigurationOptionModel[]
     *        The list of options
     */
    public function fetchAllOptionsForDeviceConfiguration ($id)
    {
        $devOptTableName = DeviceConfigurationOptionMapper::getInstance()->getTableName();

        $sql = "SELECT * FROM {$devOptTableName} AS dco
                JOIN {$this->getTableName()} AS opt ON dco.optionId = opt.id
                WHERE dco.deviceConfigurationId = ?
                ORDER BY opt.name ASC
        ";

        $resultSet = $this->getDbTable()
                          ->getAdapter()
                          ->fetchAll($sql, $id);

        $entries = [];
        foreach ($resultSet as $row)
        {
            $deviceConfigurationOption = new DeviceConfigurationOptionModel($row);

            $option = new OptionModel($row);

            $deviceConfigurationOption->option = $option;

            // Save the device configuration option to cache
            DeviceConfigurationOptionMapper::getInstance()->saveItemToCache($deviceConfigurationOption);

            // Save the option into the cache
            $this->saveItemToCache($option);

            $entries [] = $deviceConfigurationOption;
        }

        return $entries;
    }

    /**
     * Fetches all options that are available to be added to a device configuration (Anything that is not already
     * added).
     *
     * @param int $id
     *            The primary key of a device configuration
     *
     * @return OptionModel[]
     *            The list of available options to add
     */
    public function fetchAllAvailableOptionsForDeviceConfiguration ($id)
    {
        $devOptTableName       = DeviceConfigurationOptionMapper::getInstance()->getTableName();
        $deviceOptionTableName = DeviceOptionMapper::getInstance()->getTableName();

        $sql = "SELECT * FROM  {$deviceOptionTableName} AS do
                JOIN  {$this->getTableName()} AS opt ON do.optionId = opt.id
                WHERE NOT EXISTS (
                    SELECT * FROM {$devOptTableName} AS dco
                    WHERE dco.deviceConfigurationId = ? AND dco.optionId = opt.id
                )
                ORDER BY  opt.name ASC
                ";

        $resultSet = $this->getDbTable()
                          ->getAdapter()
                          ->fetchAll($sql, $id);

        $entries = [];
        foreach ($resultSet as $row)
        {
            $object = new OptionModel($row);

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Fetches all options that are available to be added to a quote device (Anything that is not already
     * added).
     *
     * @param int $quoteDeviceId
     *            The primary key of a quote device
     * @param int $masterDeviceId
     *            The primary key of the device associated with the quote device
     *
     * @return OptionModel[]
     *            The list of available options to add
     */
    public function fetchAllAvailableOptionsForQuoteDevice ($quoteDeviceId, $masterDeviceId)
    {
        $quoteDeviceConfigurationOptionTableName = QuoteDeviceConfigurationOptionMapper::getInstance()->getTableName();
        $quoteDeviceOptionTableName              = QuoteDeviceOptionMapper::getInstance()->getTableName();
        $deviceOptionTableName                   = DeviceOptionMapper::getInstance()->getTableName();

        $sql = "SELECT * FROM  {$deviceOptionTableName} AS do
                JOIN  {$this->getTableName()} AS opt ON do.optionId = opt.id
                WHERE NOT EXISTS (
                    SELECT * FROM {$quoteDeviceConfigurationOptionTableName} AS qdco
                    JOIN  {$quoteDeviceOptionTableName} AS qdo ON qdo.id = qdco.quoteDeviceOptionId
                    WHERE qdo.quoteDeviceId = ? AND qdco.optionId = opt.id
                )
                AND do.masterDeviceId = ?
                ORDER BY  opt.name ASC
        ";

        $resultSet = $this->getDbTable()
                          ->getAdapter()
                          ->fetchAll($sql, [
                              $quoteDeviceId,
                              $masterDeviceId,
                          ]);

        $entries = [];
        foreach ($resultSet as $row)
        {
            $object = new OptionModel($row);

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Fetches a list of options for the dealer
     *
     * @param int $dealerId
     *
     * @return OptionModel[]
     */
    public function fetchOptionListForDealer ($dealerId)
    {
        $options = $this->fetchAll(["{$this->col_dealerId} = ?" => $dealerId]);

        return $options;
    }

    /**
     * @param $masterDeviceId
     * @param $dealerId
     * @param null $sortColumn
     * @param null $filterByColumn
     * @param null $filterValue
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function fetchAllOptionsWithDeviceOptions ($masterDeviceId, $dealerId, $sortColumn = null, $filterByColumn = null, $filterValue = null, $limit = null, $offset = null, $filterAssigned=null)
    {
        $dbTable            = $this->getDbTable();
        $db = $dbTable->getAdapter();
        $masterDeviceId = $db->quote($masterDeviceId);
        $deviceOptionMapper = DeviceOptionMapper::getInstance();
        $columns            = [
            'id',
            'name',
            'description',
            'dealerSku',
            'oemSku', 'cost',
            'assigned' => new \Zend_Db_Expr('CASE WHEN NOT ISNULL(do.masterDeviceId) THEN 1 ELSE 0 END'),
        ];

        $select = $dbTable->select()
                          ->from(["o" => $this->getTableName()], $columns)
                          ->joinLeft(
                              ["do" => $deviceOptionMapper->getTableName()],
                              "do.{$deviceOptionMapper->col_optionId} = o.{$this->col_id} AND do.{$deviceOptionMapper->col_masterDeviceId} = {$masterDeviceId}",
                              []
                          )
                          ->where("o.{$this->col_dealerId} = ?", $dealerId);
        if ($limit > 0)
        {
            $offset = ($offset > 0) ? $offset : null;
            $select->limit($limit, $offset);
        }
        if ($filterByColumn && $filterValue)
        {

            if ($filterByColumn == "option")
            {
                $filterByColumn = "name";
            }
            $select->where("{$filterByColumn} LIKE '%{$filterValue}%'");
        }

        switch ($filterAssigned) {
            case '1': $select->where('do.masterDeviceId IS NOT NULL');
                break;
            case '2': $select->where('do.masterDeviceId IS NULL');
                break;
        }

        $select->order($sortColumn);

        $select->setIntegrityCheck(false);

        $query = $db->query($select);

        $results = $query->fetchAll();

        return $results;
    }
}

