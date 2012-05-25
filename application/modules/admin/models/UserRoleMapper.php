<?php

class Admin_Model_UserRoleMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Admin_Model_DbTable_UserRole';

    /**
     * Saves an instance of Admin_Model_UserRole to the database.
     * If the id is null then it will insert a new row
     *
     * @param $userRole Admin_Model_UserRole
     *            The object to insert
     * @return mixed The primary key of the new row
     */
    public function insert (Admin_Model_UserRole &$userRole)
    {
        $data = $userRole->toArray();
        $id = $this->getDbTable()->insert($data);
        
        return $id;
    }

    /**
     * Saves (updates) an instance of Admin_Model_UserRole to the database.
     *
     * @param $userRole Admin_Model_UserRole
     *            The userRole model to save to the database
     * @param $primaryKey mixed
     *            Optional: The original primary key, in case we're changing it
     * @return int The number of rows affected
     */
    public function save (Admin_Model_UserRole $userRole, $primaryKey = null)
    {
        $data = $this->unsetNullValues($userRole->toArray());
        
        if ($primaryKey === null)
        {
            $primaryKey = array (
                    $data ['roleId'], 
                    $data ['userId'] 
            );
        }
        
        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array (
                'id = ?' => $primaryKey 
        ));
        
        return $rowsAffected;
    }

    /**
     * Saves an instance of Admin_Model_UserRole to the database.
     * If the id is null then it will insert a new row
     *
     * @param $userRole mixed
     *            This can either be an instance of Admin_Model_UserRole or the primary key to delete
     * @return mixed The primary key of the new row
     */
    public function delete ($userRole)
    {
        if ($userRole instanceof Admin_Model_UserRole)
        {
            $whereClause = array (
                    'roleId = ?' => $userRole->getRoleId(), 
                    'userId = ?' => $userRole->getUserId() 
            );
        }
        else
        {
            $whereClause = $userRole;
        }
        
        return $this->getDbTable()->delete($whereClause);
    }

    /**
     * Finds a userRole based on it's primaryKey
     *
     * @param $id int
     *            The id of the userRole to find
     * @return void Admin_Model_UserRole
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return;
        }
        $row = $result->current();
        return new Admin_Model_UserRole($row->toArray());
    }

    /**
     * Fetches a userRole
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL An SQL OFFSET value.
     * @return void Admin_Model_UserRole
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        return new Admin_Model_UserRole($row->toArray());
    }

    /**
     * Fetches all userRoles
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $count int
     *            OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL An SQL LIMIT offset.
     * @return multitype:Admin_Model_UserRole
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $entries [] = new Admin_Model_UserRole($row->toArray());
        }
        return $entries;
    }
}

