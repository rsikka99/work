<?php

/**
 * Class Proposalgen_Model_Mapper_User_Survey_Setting
 *
 * This class is a data mapper for the User_Survey_Setting model.
 */
class Proposalgen_Model_Mapper_User_Survey_Setting extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_User_Survey_Setting';

    protected $col_userId = 'userId';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_User_Survey_Setting
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_User_Survey_Setting to the database.
     * If the id is null then it will insert a new row
     *
     * @param $user_survey_setting Proposalgen_Model_User_Survey_Setting
     *                             The object to insert
     *
     * @return mixed The primary key of the new row
     */
    public function insert (&$user_survey_setting)
    {
        $data = $user_survey_setting->toArray();
        $id   = $this->getDbTable()->insert($data);

        return $id;
    }

    /**
     * Saves (updates) an instance of Proposalgen_Model_User_Survey_Setting to the database.
     *
     * @param $user_survey_setting Proposalgen_Model_User_Survey_Setting
     *                             The user_survey_setting model to save to the database
     * @param $primaryKey          mixed
     *                             Optional: The original primary key, in case we're changing it
     *
     * @return int The number of rows affected
     */
    public function save ($user_survey_setting, $primaryKey)
    {
        $data = $this->unsetNullValues($user_survey_setting->toArray());

        if ($primaryKey === null)
        {
            $primaryKey = $data;
        }

        // Update the row
        $where = array();
        if (isset($primaryKey ['userId']))
        {
            $where ['userId = ?'] = $primaryKey ['userId'];
        }

        if (isset($primaryKey ['surveySettingId']))
        {
            $where ['surveySettingId = ?'] = $primaryKey ['surveySettingId'];
        }

        $rowsAffected = $this->getDbTable()->update($data, $where);

        return $rowsAffected;
    }

    /**
     * Saves an instance of Proposalgen_Model_User_Survey_Setting to the database.
     * If the id is null then it will insert a new row
     *
     * @param $user_survey_setting mixed
     *                             This can either be an instance of Proposalgen_Model_User_Survey_Setting or the primary key to delete
     *
     * @return mixed The primary key of the new row
     */
    public function delete ($user_survey_setting)
    {
        if ($user_survey_setting instanceof Proposalgen_Model_User_Survey_Setting)
        {
            $data = $user_survey_setting->toArray();
        }

        $whereClause = array();
        if (isset($user_survey_setting ['userId']))
        {
            $whereClause ['userId = ?'] = $user_survey_setting ['userId'];
        }

        if (isset($user_survey_setting ['surveySettingId']))
        {
            $whereClause ['surveySettingId = ?'] = $user_survey_setting ['surveySettingId'];
        }

        return $this->getDbTable()->delete($whereClause);
    }

    /**
     * Finds a user_survey_setting based on it's primaryKey
     *
     * @param $id int
     *            The id of the user_survey_setting to find
     *
     * @return void Proposalgen_Model_User_Survey_Setting
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return;
        }
        $row = $result->current();

        return new Proposalgen_Model_User_Survey_Setting($row->toArray());
    }

    /**
     * @param $userId
     *
     * @return Proposalgen_Model_Survey_Setting
     */
    public function findUserSurveySettingByUserId ($userId)
    {
        try
        {
            // Get the joining table to get all possible results
            $userSurvey         = Proposalgen_Model_Mapper_User_Survey_Setting::getInstance()->fetchAll(array("{$this->col_userId} = ?" => $userId));
            $userSurveySettings = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->find($userSurvey[0]->getSurveySettingId());
        }
        catch (Exception $e)
        {
            $userSurveySettings = null;
        }

        return $userSurveySettings;
    }

    /**
     * Fetches a user_survey_setting
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return void Proposalgen_Model_User_Survey_Setting
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }

        return new Proposalgen_Model_User_Survey_Setting($row->toArray());
    }

    /**
     * Fetches all user_survey_settings
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
     * @return multitype:Proposalgen_Model_User_Survey_Setting
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $entries [] = new Proposalgen_Model_User_Survey_Setting($row->toArray());
        }

        return $entries;
    }

    /**
     * (non-PHPdoc)
     * @see My_Model_Mapper_Abstract::getPrimaryKeyValueForObject()
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array(
            $object->getUserId(),
            $object->getSurveySettingId()
        );
    }
}