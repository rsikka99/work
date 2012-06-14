<?php

class Quotegen_Model_Mapper_QuoteSettings extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_QuoteSettings';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_QuoteSettings
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Quotegen_Model_QuoteSettings to the database.
     * If the id is null then it will insert a new row
     *
     * @param $quoteSetting Quotegen_Model_QuoteSettings
     *            The object to insert
     * @return mixed The primary key of the new row
     */
    public function insert ($data)
    {
        if ($data instanceof Quotegen_Model_QuoteSettings)
        {
            $data = $data->toArray();
        }
        
        unset($data ['id']);
        
        // lower case the clientname
        $id = $this->getDbTable()->insert($data);
        
        return $id;
    }

    /**
     * Saves (updates) an instance of Quotegen_Model_QuoteSettings to the database.
     *
     * @param $quoteSettings Quotegen_Model_QuoteSettings
     *            The quote settings model to save to the database
     * @param $primaryKey mixed
     *            Optional: The original primary key, in case we're changing it
     * @return int The number of rows affected
     */
    public function save ($quoteSetting, $primaryKey = null)
    {
        $data = $this->unsetNullValues($quoteSetting->toArray());
        
        if ($primaryKey === null)
        {
            $primaryKey = $data ['id'];
        }
        
        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array (
                'id = ?' => $primaryKey 
        ));
        
        return $rowsAffected;
    }

    /**
     * Saves an instance of Quotegen_Model_QuoteSettings to the database.
     * If the id is null then it will insert a new row
     *
     * @param $quoteSetting mixed
     *            This can either be an instance of Quotegen_Model_QuoteSetting or the
     *            primary key to delete
     * @return mixed The primary key of the new row
     */
    public function delete ($quoteSetting)
    {
        if ($quoteSetting instanceof Quotegen_Model_QuoteSettings)
        {
            $whereClause = array (
                    'id = ?' => $quoteSetting->getId() 
            );
        }
        else
        {
            $whereClause = array (
                    'id = ?' => $quoteSetting 
            );
        }
        
        return $this->getDbTable()->delete($whereClause);
    }

    /**
     * Finds a quoteSetting based on it's primaryKey
     *
     * @param $id int
     *            The id of the quoteSetting to find
     * @return void Quotegen_Model_QuoteSettings
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return;
        }
        $row = $result->current();
        return new Quotegen_Model_QuoteSettings($row->toArray());
    }

    /**
     * Fetches a quote setting
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL An SQL OFFSET value.
     * @return void Quotegen_Model_QuoteSettings
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        return new Quotegen_Model_QuoteSettings($row->toArray());
    }

    /**
     * Fetches all quote settings
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $count int
     *            OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL An SQL LIMIT offset.
     * @return multitype:Quotegen_Model_QuoteSettings
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $entries [] = new Quotegen_Model_QuoteSettings($row->toArray());
        }
        return $entries;
    }
}

