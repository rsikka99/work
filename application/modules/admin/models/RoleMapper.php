<?php

class Admin_Model_RoleMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Admin_Model_DbTable_Role';

    /**
     * Saves an instance of Admin_Model_Role to the database.
     * If the id is null then it will insert a new row
     *
     * @param $role Admin_Model_Role
     *            The object to insert
     * @return mixed The primary key of the new row
     */
    public function insert (Admin_Model_Role &$role)
    {
        $data = $role->toArray();
        unset($data ['id']);
        $id = $this->getDbTable()->insert($data);
        
        // Since the role is set properly, set the id in the appropriate places
        $role->setId($id);
        
        return $id;
    }

    /**
     * Saves (updates) an instance of Admin_Model_Role to the database.
     *
     * @param $role Admin_Model_Role
     *            The role model to save to the database
     * @param $primaryKey mixed
     *            Optional: The original primary key, in case we're changing it
     * @return int The number of rows affected
     */
    public function save (Admin_Model_Role $role, $primaryKey = null)
    {
        $data = $this->unsetNullValues($role->toArray());
        
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
     * Saves an instance of Admin_Model_Role to the database.
     * If the id is null then it will insert a new row
     *
     * @param $role mixed
     *            This can either be an instance of Admin_Model_Role or the primary key to delete
     * @return mixed The primary key of the new row
     */
    public function delete ($role)
    {
        if ($role instanceof Admin_Model_Role)
        {
            $whereClause = array (
                    'id = ?' => $role->getId() 
            );
        }
        else
        {
            $whereClause = array (
                    'id = ?' => $role 
            );
        }
        
        return $this->getDbTable()->delete($whereClause);
    }

    /**
     * Finds a role based on it's primaryKey
     *
     * @param $id int
     *            The id of the role to find
     * @return void Admin_Model_Role
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return;
        }
        $row = $result->current();
        return new Admin_Model_Role($row->toArray());
    }

    /**
     * Fetches a role
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL An SQL OFFSET value.
     * @return void Admin_Model_Role
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        return new Admin_Model_Role($row->toArray());
    }

    /**
     * Fetches all roles
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $count int
     *            OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL An SQL LIMIT offset.
     * @return multitype:Admin_Model_Role
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $entries [] = new Admin_Model_Role($row->toArray());
        }
        return $entries;
    }
}

