<?php

/**
 * Class Proposalgen_Model_Mapper_Client_Toner_Attribute
 */
class Proposalgen_Model_Mapper_Client_Toner_Attribute extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_tonerId = 'tonerId';
    public $col_clientId = 'clientId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_Client_Toner_Attribute';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_Client_Toner_Attribute
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_Client_Toner_Attribute to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Proposalgen_Model_Client_Toner_Attribute
     *                The object to insert
     *
     * @return int The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $this->unsetNullValues($object->toArray());

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of Proposalgen_Model_Client_Toner_Attribute to the database.
     *
     * @param $object     Proposalgen_Model_Client_Toner_Attribute
     *                    The ClientTonerAttribute model to save to the database
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
            $primaryKey [] = $data [$this->col_tonerId];
            $primaryKey [] = $data [$this->col_clientId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
            "{$this->col_tonerId} = ?"  => $primaryKey [0],
            "{$this->col_clientId} = ?" => $primaryKey [1],
        ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Proposalgen_Model_Client_Toner_Attribute or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Proposalgen_Model_Client_Toner_Attribute)
        {
            $whereClause = array(
                "{$this->col_tonerId} = ?"  => $object->tonerId,
                "{$this->col_clientId} = ?" => $object->clientId,

            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_tonerId} = ?"  => $object[0],
                "{$this->col_clientId} = ?" => $object[1],
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a ClientTonerAttribute based on it's primaryKey
     *
     * @param $id array
     *            The id of the ClientTonerAttribute to find
     *
     * @return Proposalgen_Model_Client_Toner_Attribute
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Proposalgen_Model_Client_Toner_Attribute)
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
        $object = new Proposalgen_Model_Client_Toner_Attribute($row->toArray());

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
     * @return Proposalgen_Model_Client_Toner_Attribute
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Proposalgen_Model_Client_Toner_Attribute($row->toArray());

        // Save the object into the cache
        $primaryKey [0] = $object->tonerId;
        $primaryKey [1] = $object->clientId;
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
     * @return Proposalgen_Model_Client_Toner_Attribute[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();

        foreach ($resultSet as $row)
        {
            $object = new Proposalgen_Model_Client_Toner_Attribute($row->toArray());

            // Save the object into the cache
            $primaryKey [0] = $object->tonerId;
            $primaryKey [1] = $object->clientId;
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
            "{$this->col_tonerId} = ?"  => $id [0],
            "{$this->col_clientId} = ?" => $id [1],
        );
    }

    /**
     * @param $tonerId  int
     * @param $clientId int
     *
     * @return Proposalgen_Model_Client_Toner_Attribute
     */
    public function findTonerAttributeByTonerId ($tonerId, $clientId)
    {
        return $this->fetch(array("{$this->col_tonerId} = ?" => $tonerId, "{$this->col_clientId} =  ?" => $clientId));
    }

    /**
     * @param Proposalgen_Model_Client_Toner_Attribute $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array(
            $object->tonerId,
            $object->clientId
        );
    }

    /**
     * Fetches all available uploads for a client
     *
     * @param          $clientId
     * @param          $dealerId
     * @param null     $order
     * @param int      $count
     * @param int|null $offset
     * @param null     $filter
     * @param null     $criteria
     * @param bool     $justCount
     *
     * @return Proposalgen_Model_Rms_Upload[]|int
     */
    public function fetchAllForClient ($clientId, $dealerId, $order = null, $count = 25, $offset = 0, $filter = null, $criteria = null, $justCount = false)
    {
        $db       = $this->getDbTable()->getAdapter();
        $sql      = "SELECT
    toners.id                           AS tonerId,
    toners.sku                          AS systemSku,
    dealer_toner_attributes.dealerSku   AS dealerSku,
    client_toner_attributes.clientSku   AS clientSku,
    client_toner_attributes.cost
    FROM client_toner_attributes
    LEFT JOIN toners ON toners.id = client_toner_attributes.tonerId
    LEFT JOIN dealer_toner_attributes ON dealer_toner_attributes.tonerId = client_toner_attributes.tonerId AND dealer_toner_attributes.dealerId = {$dealerId} ";

        $whereClause = "WHERE client_toner_attributes.clientId = {$clientId}";
        if ($filter == 'sku' && $criteria)
        {
            $whereClause .= " AND (toners.sku LIKE '%{$criteria}%' OR dealer_toner_attributes.dealerSku LIKE '%{$criteria}%' OR client_toner_attributes.clientSku LIKE '%{$criteria}%')";
        }
        else if ($filter == 'cost')
        {
            $whereClause .= " AND client_toner_attributes.cost LIKE '%{$criteria}%' ";
        }

        $sql .= $whereClause;

        if ($order)
        {
            $sql .= " ORDER BY " . $order[0] . " " . $order[1];
        }
        $sql .= " LIMIT " . $offset . ", " . $count . " ";
        $stmt   = $db->query($sql);
        $result = $stmt->fetchAll();

        if ($justCount)
        {
            return count($result);
        }

        return $result;
    }

    public function deleteAllForClient ($clientId)
    {
        return $this->getDbTable()->delete(array("{$this->col_clientId} = ?" => $clientId));
    }
}