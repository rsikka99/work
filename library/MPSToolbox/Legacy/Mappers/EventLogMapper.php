<?php
namespace MPSToolbox\Legacy\Mappers;

use Exception;
use MPSToolbox\Legacy\Models\EventLogModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table;
use Zend_Db_Table_Select;

/**
 * Class EventLogMapper
 *
 * @package MPSToolbox\Legacy\Mappers
 */
class EventLogMapper extends My_Model_Mapper_Abstract
{
    // Column Names
    /**
     * @var string
     */
    public $col_id = "id";

    /**
     * @var string
     */
    public $col_eventLogTypeId = "eventLogTypeId";

    /**
     * @var string
     */
    public $col_timestamp = "timestamp";

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\DbTables\EventLogDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return EventLogMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Models\EventLogModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object EventLogModel
     *                The object to insert
     *
     * @return mixed The primary key of the new row
     */
    public function insert (&$object)
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Models\EventLogModel to the database.
     *
     * @param $object     EventLogModel
     *                    The event_log model to save to the database
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
     *                This can either be an instance of MPSToolbox\Legacy\Models\EventLogModel or the
     *                primary key to delete
     *
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof EventLogModel)
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
     * Finds a event_log based on it's primaryKey
     *
     * @param $id int
     *            The id of the event_log to find
     *
     * @return EventLogModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof EventLogModel)
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
        $object = new EventLogModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a event_log
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return  EventLogModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new EventLogModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all event_logs
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $count  int
     *                OPTIONAL An SQL LIMIT count. (Defaults to 150)
     * @param $offset int
     *                OPTIONAL An SQL LIMIT offset.
     *
     * @return EventLogModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 150, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new EventLogModel($row->toArray());

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
     * @param EventLogModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Creates a MPSToolbox\Legacy\Models\EventLogModel for the given type, and optional message
     *
     * @param String $type
     * @param String $message
     *
     * @return bool|int
     */
    public function createEventLog ($type, $message = null)
    {
        $eventLogId = false;

        // Use a try here because if this fails the program should not crash, just log it and continue
        try
        {
            $eventLog                 = new EventLogModel();
            $eventLog->timestamp      = date('Y-m-d H:i:s');
            $eventLog->eventLogTypeId = $type;
            $eventLog->ipAddress      = $_SERVER['REMOTE_ADDR'];
            $eventLog->message        = $message;
            $eventLogId               = $this->insert($eventLog);
        }
        catch (Exception $e)
        {
            \Tangent\Logger\Logger::logException($e);
        }

        return $eventLogId;
    }

    /**
     * Gets a list of all event log data for the JqGrid on the admin/event_log page
     *
     * @param        $sorting
     * @param int    $limit
     * @param int    $offset
     * @param string $email
     * @param string $type
     *
     * @return array
     */
    public function fetchAllForJqGrid ($sorting, $limit = 10000, $offset = 0, $email = '', $type = 'All')
    {
        $db                    = Zend_Db_Table::getDefaultAdapter();
        $userEventLogTableName = UserEventLogMapper::getInstance()->getTableName();
        $eventLogTableName     = EventLogMapper::getInstance()->getTableName();
        $eventLogTypeTableName = EventLogTypeMapper::getInstance()->getTableName();
        $userTableName         = UserMapper::getInstance()->getTableName();

        $whereClause = array();

        $eventLogColumns = array(
            'id' => 'id',
            'eventLogTypeId',
            'timestamp',
            'ipAddress',
            'message'
        );

        $eventLogTypeColumns = array(
            'name',
            'description'
        );

        $userColumns = array(
            'email',
        );

        if ($email != '')
        {
            $whereClause["email like ?"] = $email;
        }

        if ($type != 'All')
        {
            $whereClause["name like ?"] = $type;
        }

        /*
         * Here we create our select statement
         */
        $zendDbSelect = $db->select()->from($eventLogTableName, $eventLogColumns);
        $zendDbSelect->join($eventLogTypeTableName, "{$eventLogTypeTableName}.`id` = {$eventLogTableName}.`eventLogTypeId`", $eventLogTypeColumns);
        $zendDbSelect->joinleft($userEventLogTableName, "{$userEventLogTableName}.`eventLogId` = {$eventLogTableName}.`id`");
        $zendDbSelect->joinLeft($userTableName, "{$userTableName}.`id` = {$userEventLogTableName}.`userId`", $userColumns);

        // Apply the limit/offset
        $zendDbSelect->limit($limit, $offset);

        // Apply our where clause
        foreach ($whereClause as $cond => $value)
        {
            $zendDbSelect->where($cond, $value);
        }

        $zendDbSelect->order($sorting);

        $zendDbStatement = $db->query($zendDbSelect);
        $results         = $zendDbStatement->fetchAll();

        return $results;
    }

    /**
     * Deletes all event logs
     *
     * @return int The number of rows deleted
     */
    public function deleteAllEventLogs ()
    {
        return $this->getDbTable()->delete('');
    }
}