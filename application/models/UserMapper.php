<?php

class Application_Model_UserMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Application_Model_DbTable_User';

    /**
     * Saves an instance of Application_Model_User to the database.
     * If the id is null then it will insert a new row
     *
     * @param $user Application_Model_User
     *            The object to insert
     * @return mixed The primary key of the new row
     */
    public function insert (Application_Model_User &$user)
    {
        $data = $user->toArray();
        unset($data ['id']);
        $id = $this->getDbTable()->insert($data);
        
        // Since the user is set properly, set the id in the appropriate places
        $user->setId($id);
        
        return $id;
    }

    /**
     * Saves (updates) an instance of Application_Model_User to the database.
     *
     * @param $user Application_Model_User
     *            The user model to save to the database
     * @param $primaryKey mixed
     *            Optional: The original primary key, in case we're changing it
     * @return int The number of rows affected
     */
    public function save (Application_Model_User $user, $primaryKey = null)
    {
        $data = $this->unsetNullValues($user->toArray());
        
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
     * Saves an instance of Application_Model_User to the database.
     * If the id is null then it will insert a new row
     *
     * @param $user mixed
     *            This can either be an instance of Application_Model_User or the primary key to delete
     * @return mixed The primary key of the new row
     */
    public function delete ($user)
    {
        if ($user instanceof Application_Model_User)
        {
            $whereClause = array (
                    'id = ?' => $user->getId() 
            );
        }
        else
        {
            $whereClause = array (
                    'id = ?' => $user 
            );
        }
        
        return $this->getDbTable()->delete($whereClause);
    }

    /**
     * Finds a user based on it's primaryKey
     *
     * @param $id int
     *            The id of the user to find
     * @return void Application_Model_User
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return;
        }
        $row = $result->current();
        return new Application_Model_User($row->toArray());
    }

    /**
     * Fetches a user
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL An SQL OFFSET value.
     * @return void Application_Model_User
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        return new Application_Model_User($row->toArray());
    }

    /**
     * Fetches all users
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $count int
     *            OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL An SQL LIMIT offset.
     * @return multitype:Application_Model_User
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $entries [] = new Application_Model_User($row->toArray());
        }
        return $entries;
    }
}

