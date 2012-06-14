<?php

class Quotegen_Model_Mapper_LeasingSchemaRates extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_LeasingSchemaRates';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_LeasingSchemaRates
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Quotegen_Model_LeasingSchemaRates to the database.
     * If the id is null then it will insert a new row
     *
     * @param $leasingSchemaRate Quotegen_Model_LeasingSchemaRates
     *            The object to insert
     * @return mixed The primary key of the new row
     */
    public function insert ($data)
    {
        if ($data instanceof Quotegen_Model_LeasingSchemaRates)
        {
            $data = $data->toArray();
        }
        
        unset($data ['id']);
        
        $id = $this->getDbTable()->insert($data);
        
        return $id;
    }

    /**
     * Saves (updates) an instance of Quotegen_Model_LeasingSchemaRates to the database.
     *
     * @param $client Quotegen_Model_LeasingSchemaRates
     *            The leasingSchemaRate model to save to the database
     * @param $primaryKey mixed
     *            Optional: The original primary key, in case we're changing it
     * @return int The number of rows affected
     */
    public function save ($leasingSchemaRate, $primaryKey = null)
    {
        $data = $this->unsetNullValues($leasingSchemaRate->toArray());
        
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
     * Saves an instance of Quotegen_Model_LeasingSchemaRates to the database.
     * If the id is null then it will insert a new row
     *
     * @param $client mixed
     *            This can either be an instance of Quotegen_Model_LeasingSchemaRates or the
     *            primary key to delete
     * @return mixed The primary key of the new row
     */
    public function delete ($leasingSchemaRate)
    {
        if ($leasingSchemaRate instanceof Quotegen_Model_LeasingSchemaRates)
        {
            $whereClause = array (
                    'id = ?' => $leasingSchemaRate->getId() 
            );
        }
        else
        {
            $whereClause = array (
                    'id = ?' => $leasingSchemaRate 
            );
        }
        
        return $this->getDbTable()->delete($whereClause);
    }

    /**
     * Finds a leasingSchemaRate based on it's primaryKey
     *
     * @param $id int
     *            The id of the leasingSchemaRate to find
     * @return void Quotegen_Model_LeasingSchemaRates
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return;
        }
        $row = $result->current();
        return new Quotegen_Model_LeasingSchemaRates($row->toArray());
    }

    /**
     * Fetches a leasingSchemaRate
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL An SQL OFFSET value.
     * @return void Quotegen_Model_LeasingSchemaRates
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        return new Quotegen_Model_LeasingSchemaRates($row->toArray());
    }

    /**
     * Fetches all leasingSchemaRates
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $count int
     *            OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL An SQL LIMIT offset.
     * @return multitype:Quotegen_Model_LeasingSchemaRates
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $entries [] = new Quotegen_Model_LeasingSchemaRates($row->toArray());
        }
        return $entries;
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param unknown_type $id            
     * @return array
     */
    public function getWhereId ($id)
    {
        return array (
                'id = ?' => $id 
        );
    }
    
}

