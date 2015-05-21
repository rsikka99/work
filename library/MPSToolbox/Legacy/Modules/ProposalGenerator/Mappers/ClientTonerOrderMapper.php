<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\ClientTonerOrderModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class Proposalgen_Model_Mapper_Client_Toner_Attribute
 */
class ClientTonerOrderMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_id                 = 'id';
    public $col_tonerId            = 'tonerId';
    public $col_clientId           = 'clientId';
    public $col_oemSku             = 'oemSku';
    public $col_dealerSku          = 'dealerSku';
    public $col_clientSku          = 'clientSku';
    public $col_orderNumber        = 'orderNumber';
    public $col_replacementTonerId = 'replacementTonerId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\ClientTonerOrderDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return ClientTonerOrderMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_Client_Toner_Attribute to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object ClientTonerOrderModel
     *                The object to insert
     * @return int The primary key of the new row
     */
    public function insert ($object)
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
     * @param $object     ClientTonerOrderModel
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
     *                This can either be an instance of Proposalgen_Model_Client_Toner_Attribute or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof ClientTonerOrderModel)
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
     * Finds a ClientTonerAttribute based on it's primaryKey
     *
     * @param $id array
     *            The id of the ClientTonerAttribute to find
     *
     * @return ClientTonerOrderModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof ClientTonerOrderModel)
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
        $object = new ClientTonerOrderModel($row->toArray());

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
     * @return ClientTonerOrderModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new ClientTonerOrderModel($row->toArray());

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
     * @return ClientTonerOrderModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];

        foreach ($resultSet as $row)
        {
            $object = new ClientTonerOrderModel($row->toArray());

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
     * @param $tonerId  int
     * @param $clientId int
     *
     * @return ClientTonerOrderModel
     */
    public function findTonerAttributeByTonerId ($tonerId, $clientId)
    {
        return $this->fetch(["{$this->col_tonerId} = ?" => $tonerId, "{$this->col_clientId} =  ?" => $clientId]);
    }

    /**
     * @param ClientTonerOrderModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Fetches all available toner orders for a client
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
     * @return array|int
     */
    public function jqgridFetchAllForClient ($clientId, $dealerId, $order = null, $count = 25, $offset = 0, $filter = null, $criteria = null, $justCount = false)
    {
        $db       = $this->getDbTable()->getAdapter();
        $bindData = [];

        if ($justCount)
        {
            $sql = 'SELECT
   count(*),
       toners.id IS NOT NULL                                                                          AS hasToner,
    toners.id                                                                                      AS tonerId,
    toners.sku                                                                                     AS tonerSystemSku,
    replacementToners.sku                                                                          AS replacementOemSku,
    replacement_dta.dealerSku                                                                      AS replacementDealerSku,
    replacementToners.cost                                                                         AS replacementCost,
    IF(replacementToners.cost IS NOT NULL, client_toner_orders.cost - replacementToners.cost, \'-\') AS replacementSavings,
    dealer_toner_attributes.dealerSku                                                              AS tonerDealerSku,
    client_toner_orders.id                                                                         AS id,
    client_toner_orders.oemSku,
    client_toner_orders.dealerSku,
    client_toner_orders.clientSku,
    client_toner_orders.cost,
    client_toner_orders.orderNumber,
    client_toner_orders.quantity,
    client_toner_orders.dateOrdered,
    client_toner_orders.dateShipped,
    client_toner_orders.dateReconciled,
    client_toner_orders.replacementTonerId
FROM client_toner_orders
    LEFT JOIN toners
        ON toners.id = client_toner_orders.tonerId
    LEFT JOIN dealer_toner_attributes
        ON dealer_toner_attributes.tonerId = client_toner_orders.tonerId
           AND dealer_toner_attributes.dealerId = :dealerId
    LEFT JOIN toners AS replacementToners
        ON replacementToners.id = client_toner_orders.replacementTonerId
    LEFT JOIN dealer_toner_attributes AS replacement_dta
        ON replacement_dta.tonerId = client_toner_orders.replacementTonerId
           AND replacement_dta.dealerId = :dealerId ';
        }
        else
        {
            $sql = 'SELECT
    toners.id IS NOT NULL                                                                          AS hasToner,
    toners.id                                                                                      AS tonerId,
    toners.sku                                                                                     AS tonerSystemSku,
    replacementToners.sku                                                                          AS replacementOemSku,
    replacement_dta.dealerSku                                                                      AS replacementDealerSku,
    replacementToners.cost                                                                         AS replacementCost,
    IF(replacementToners.cost IS NOT NULL, client_toner_orders.cost - replacementToners.cost, \'-\') AS replacementSavings,
    dealer_toner_attributes.dealerSku                                                              AS tonerDealerSku,
    client_toner_orders.id                                                                         AS id,
    client_toner_orders.oemSku,
    client_toner_orders.dealerSku,
    client_toner_orders.clientSku,
    client_toner_orders.cost,
    client_toner_orders.orderNumber,
    client_toner_orders.quantity,
    client_toner_orders.dateOrdered,
    client_toner_orders.dateShipped,
    client_toner_orders.dateReconciled,
    client_toner_orders.replacementTonerId
FROM client_toner_orders
    LEFT JOIN toners
        ON toners.id = client_toner_orders.tonerId
    LEFT JOIN dealer_toner_attributes
        ON dealer_toner_attributes.tonerId = client_toner_orders.tonerId
           AND dealer_toner_attributes.dealerId = :dealerId
    LEFT JOIN toners AS replacementToners
        ON replacementToners.id = client_toner_orders.replacementTonerId
    LEFT JOIN dealer_toner_attributes AS replacement_dta
        ON replacement_dta.tonerId = client_toner_orders.replacementTonerId
           AND replacement_dta.dealerId = :dealerId ';
        }


        $bindData['dealerId'] = $dealerId;

        $whereClause = "WHERE client_toner_orders.clientId = :clientId";
        $bindData['clientId']  = $clientId;
        if ($filter == 'sku' && $criteria)
        {
            $whereClause .= " AND (toners.sku LIKE :searchSku OR dealer_toner_attributes.dealerSku LIKE :searchSku OR client_toner_orders.clientSku LIKE :searchSku)";
            $bindData['searchSku'] = "%$criteria%";
        }
        else if ($filter == 'cost')
        {
            $whereClause .= " AND client_toner_orders.cost LIKE :cost ";
            $bindData['cost'] = "%$criteria%";
        }

        $sql .= $whereClause;

        if ($order)
        {
            $sql .= " ORDER BY " . $order[0] . " " . $order[1];
        }

        $sql .= " LIMIT " . (int)$offset . "," . (int)$count;
//        $bindData['offset'] = $offset;
//        $bindData['count'] = $count;

        $stmt = $db->query($sql, $bindData);


        if ($justCount)
        {
            $result = (int)$stmt->fetchColumn();
        }
        else
        {
            $result = $stmt->fetchAll();
        }

        return $result;
    }

    /**
     * Fetches all available toner orders for a client
     *
     * @param      $clientId
     * @param      $dealerId
     * @param null $order
     * @param int  $count
     * @param int  $offset
     * @param null $filter
     * @param null $criteria
     * @param bool $justCount
     *
     * @return ClientTonerOrderModel[]|int
     */
    public function fetchAllForClient ($clientId, $dealerId, $order = null, $count = 25, $offset = 0, $filter = null, $criteria = null, $justCount = false)
    {
        $clientTonerOrderArray = $this->jqgridFetchAllForClient($clientId, $dealerId, $order, $count, $offset, $filter, $criteria, $justCount);
        $clientTonerOrders     = [];
        foreach ($clientTonerOrderArray as $clientTonerOrderData)
        {
            // Get the item from the cache and return it if we find it.
            $object = $this->getItemFromCache($clientTonerOrderData['id']);
            if (!$object instanceof ClientTonerOrderModel)
            {
                $object = new ClientTonerOrderModel($clientTonerOrderData);

                // Save the object into the cache
                $this->saveItemToCache($object);
            }

            $clientTonerOrders[] = $object;
        }

        return $clientTonerOrders;
    }

    /**
     * Deletes all toner orders for a given client
     *
     * @param int $clientId
     *
     * @return int The number of rows deleted
     */
    public function deleteAllForClient ($clientId)
    {
        return $this->getDbTable()->delete(["{$this->col_clientId} = ?" => $clientId]);
    }

    /**
     * Finds a toner order by OEM SKU and order number
     *
     * @param int    $clientId
     * @param string $oemSku The OEM SKU of the toner
     * @param string $orderNumber
     *
     * @return ClientTonerOrderModel
     */
    public function findTonerOrder ($clientId, $oemSku, $orderNumber)
    {
        return $this->fetch(["{$this->col_clientId} = ?" => $clientId, "{$this->col_oemSku} = ?" => $oemSku, "{$this->col_orderNumber} =  ?" => $orderNumber]);
    }
}