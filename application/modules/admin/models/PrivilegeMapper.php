<?php

class Admin_Model_PrivilegeMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Admin_Model_DbTable_Privilege';

    /**
     * Saves an instance of Admin_Model_Privilege to the database.
     * If the id is null then it will insert a new row
     *
     * @param $privilege Admin_Model_Privilege
     *            The object to insert
     * @return mixed The primary key of the new row
     */
    public function insert (Admin_Model_Privilege &$privilege)
    {
        $data = $privilege->toArray();
        unset($data ['id']);
        $id = $this->getDbTable()->insert($data);
        
        // Since the privilege is set properly, set the id in the appropriate places
        $privilege->setId($id);
        
        return $id;
    }

    /**
     * Saves (updates) an instance of Admin_Model_Privilege to the database.
     *
     * @param $privilege Admin_Model_Privilege
     *            The privilege model to save to the database
     * @param $primaryKey mixed
     *            Optional: The original primary key, in case we're changing it
     * @return int The number of rows affected
     */
    public function save (Admin_Model_Privilege $privilege, $primaryKey = null)
    {
        $data = $this->unsetNullValues($privilege->toArray());
        
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
     * Saves an instance of Admin_Model_Privilege to the database.
     * If the id is null then it will insert a new row
     *
     * @param $privilege mixed
     *            This can either be an instance of Admin_Model_Privilege or the primary key to delete
     * @return mixed The primary key of the new row
     */
    public function delete ($privilege)
    {
        if ($privilege instanceof Admin_Model_Privilege)
        {
            $whereClause = array (
                    'id = ?' => $privilege->getId() 
            );
        }
        else
        {
            $whereClause = array (
                    'id = ?' => $privilege 
            );
        }
        
        return $this->getDbTable()->delete($whereClause);
    }

    /**
     * Finds a privilege based on it's primaryKey
     *
     * @param $id int
     *            The id of the privilege to find
     * @return void Admin_Model_Privilege
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return;
        }
        $row = $result->current();
        return new Admin_Model_Privilege($row->toArray());
    }

    /**
     * Fetches a privilege
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL An SQL OFFSET value.
     * @return void Admin_Model_Privilege
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        return new Admin_Model_Privilege($row->toArray());
    }

    /**
     * Fetches all privileges
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $count int
     *            OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL An SQL LIMIT offset.
     * @return multitype:Admin_Model_Privilege
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $entries [] = new Admin_Model_Privilege($row->toArray());
        }
        return $entries;
    }
    
    /**
     * @see Zend_Db_Table
     * @return array Returns a count of objects
     */
    public function count ($whereClause = null)
    {
        $count = 0;
        try
        {
            $sql = $this->getDbTable()->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)->from($this->getDbTable()->info("name"), "count(*)");
            if ($whereClause)
            {
                foreach ($whereClause as $column => $value)
                {
                    $sql->where($this->getDbTable()->getAdapter()->quoteInto($column, $value));
                }
            }
            $result = $this->getDbTable()->getAdapter()->fetchRow($sql);
            if ($result)
            {
                $count = $result["count(*)"];
    
            }
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to count rows", 0, $e);
        }
        return $count;
    }
}

