<?php

/**
 * Class Proposalgen_Model_Mapper_User_Report_Setting
 *
 * This class is a data mapper for the User_Report_Setting model.
 */
class Proposalgen_Model_Mapper_User_Report_Setting extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_User_Report_Setting';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_User_Report_Setting
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_User_Report_Setting to the database.
     * If the id is null then it will insert a new row
     *
     * @param $user_report_setting Proposalgen_Model_User_Report_Setting
     *            The object to insert
     * @return mixed The primary key of the new row
     */
    public function insert (Proposalgen_Model_User_Report_Setting &$user_report_setting)
    {
        $data = $user_report_setting->toArray();
        $id = $this->getDbTable()->insert($data);
        return $id;
    }

    /**
     * Saves (updates) an instance of Proposalgen_Model_User_Report_Setting to the database.
     *
     * @param $user_report_setting Proposalgen_Model_User_Report_Setting
     *            The user_report_setting model to save to the database
     * @param $primaryKey mixed
     *            Optional: The original primary key, in case we're changing it
     * @return int The number of rows affected
     */
    public function save (Proposalgen_Model_User_Report_Setting $user_report_setting, $primaryKey = null)
    {
        $data = $this->unsetNullValues($user_report_setting->toArray());
        
        if ($primaryKey === null)
        {
            $primaryKey = $data;
        }
        
        // Update the row
        $where = array ();
        if (isset($primaryKey ['userId']))
        {
            $where ['userId = ?'] = $primaryKey ['userId'];
        }
        
        if (isset($primaryKey ['reportSettingId']))
        {
            $where ['reportSettingId = ?'] = $primaryKey ['reportSettingId'];
        }
        
        $rowsAffected = $this->getDbTable()->update($data, $where);
        
        return $rowsAffected;
    }

    /**
     * Saves an instance of Proposalgen_Model_User_Report_Setting to the database.
     * If the id is null then it will insert a new row
     *
     * @param $user_report_setting mixed
     *            This can either be an instance of Proposalgen_Model_User_Report_Setting or the primary key to delete
     * @return mixed The primary key of the new row
     */
    public function delete ($user_report_setting)
    {
        if ($user_report_setting instanceof Proposalgen_Model_User_Report_Setting)
        {
            $data = $user_report_setting->toArray();
        }
        
        $whereClause = array ();
        if (isset($user_report_setting ['userId']))
        {
            $whereClause ['userId = ?'] = $user_report_setting ['userId'];
        }
        
        if (isset($user_report_setting ['reportSettingId']))
        {
            $whereClause ['reportSettingId = ?'] = $user_report_setting ['reportSettingId'];
        }
        
        return $this->getDbTable()->delete($whereClause);
    }

    /**
     * Finds a user_report_setting based on it's primaryKey
     *
     * @param $id int
     *            The id of the user_report_setting to find
     * @return void Proposalgen_Model_User_Report_Setting
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return;
        }
        $row = $result->current();
        return new Proposalgen_Model_User_Report_Setting($row->toArray());
    }

    /**
     * Fetches a user_report_setting
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL An SQL OFFSET value.
     * @return void Proposalgen_Model_User_Report_Setting
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        return new Proposalgen_Model_User_Report_Setting($row->toArray());
    }

    /**
     * Fetches all user_report_settings
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $count int
     *            OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL An SQL LIMIT offset.
     * @return multitype:Proposalgen_Model_User_Report_Setting
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $entries [] = new Proposalgen_Model_User_Report_Setting($row->toArray());
        }
        return $entries;
    }

    /**
     * (non-PHPdoc) @see My_Model_Mapper_Abstract::getPrimaryKeyValueForObject()
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array (
                $object->getUserId(), 
                $object->getReportSettingId() 
        );
    }
}