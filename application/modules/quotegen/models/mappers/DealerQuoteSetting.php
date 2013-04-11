<?php

class Quotegen_Model_Mapper_DealerQuoteSetting extends My_Model_Mapper_Abstract
{
    /*
     * Column name definitions. Define all columns up here and use them down below. 
     */
    public $col_dealerId = 'dealerId';

    /*
     * Mapper Definitions
     */
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_DealerQuoteSetting';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_DealerQuoteSetting
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Quotegen_Model_DealerQuoteSetting to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Quotegen_Model_DealerQuoteSetting
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

        $object->id = $id;

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of Quotegen_Model_DealerQuoteSetting to the database.
     *
     * @param $object     Quotegen_Model_DealerQuoteSetting
     *                    The DealerQuoteSetting model to save to the database
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
            $primaryKey = $data [$this->col_dealerId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
                                                                "{$this->col_dealerId} = ?" => $primaryKey
                                                           ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Quotegen_Model_DealerQuoteSetting or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Quotegen_Model_DealerQuoteSetting)
        {
            $whereClause = array(
                "{$this->col_dealerId} = ?" => $object->dealerId
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_dealerId} = ?" => $object
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a DealerQuoteSetting based on it's primaryKey
     *
     * @param $id int
     *            The id of the DealerQuoteSetting to find
     *
     * @return Quotegen_Model_DealerQuoteSetting
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Quotegen_Model_DealerQuoteSetting)
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
        $object = new Quotegen_Model_DealerQuoteSetting($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a DealerQuoteSetting
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: A SQL OFFSET value.
     *
     * @return Quotegen_Model_DealerQuoteSetting
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Quotegen_Model_DealerQuoteSetting($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all DealerQuoteSettings
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $count  int
     *                OPTIONAL: A SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *                OPTIONAL: A SQL LIMIT offset.
     *
     * @return Quotegen_Model_DealerQuoteSetting[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Quotegen_Model_DealerQuoteSetting($row->toArray());

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
            "{$this->col_dealerId} = ?" => $id
        );
    }

    public function fetchDealerQuoteSetting ($dealerId)
    {
        $userDealerSetting = $this->fetch(array("{$this->col_dealerId} = ?" => $dealerId));

        // If we dont have setting create them
        if (!$userDealerSetting)
        {
            // Create a new QuoteSetting
            $quoteSetting                  = new Quotegen_Model_QuoteSetting();
            $quoteSetting->pricingConfigId = Proposalgen_Model_PricingConfig::NONE;
            $quoteSettingId                = Quotegen_Model_Mapper_QuoteSetting::getInstance()->insert($quoteSetting);

            // Create a new DealerQuoteSetting
            $userDealerSetting                 = new Quotegen_Model_DealerQuoteSetting();
            $userDealerSetting->dealerId      = $dealerId;
            $userDealerSetting->quoteSettingId = $quoteSettingId;
            $this->insert($userDealerSetting);
        }
        else
        {
            $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->find($userDealerSetting->quoteSettingId);
        }

        return $quoteSetting;

    }

    /**
     *
     * @param Quotegen_Model_DealerQuoteSetting $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->dealerId;
    }
}

