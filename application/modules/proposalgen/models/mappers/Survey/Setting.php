<?php
class Proposalgen_Model_Mapper_Survey_Setting extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_Survey_Setting';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_Survey_Setting
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_Survey_Setting to the database.
     * If the id is null then it will insert a new row
     *
     * @param $survey_setting Proposalgen_Model_Survey_Setting
     *                        The object to insert
     *
     * @return mixed The primary key of the new row
     */
    public function insert (&$survey_setting)
    {
        $data = $survey_setting->toArray();
        unset($data ['id']);
        $id = $this->getDbTable()->insert($data);

        // Since the survey_setting is set properly, set the id in the appropriate places
        $survey_setting->id = $id;

        return $id;
    }

    /**
     * Saves (updates) an instance of Proposalgen_Model_Survey_Setting to the database.
     *
     * @param $object Proposalgen_Model_Survey_Setting
     *                        The survey_setting model to save to the database
     * @param $primaryKey     mixed
     *                        Optional: The original primary key, in case we're changing it
     *
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());

        if ($primaryKey === null)
        {
            $primaryKey = $data ['id'];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
                                                                'id = ?' => $primaryKey
                                                           ));

        return $rowsAffected;
    }

    /**
     * Saves an instance of Proposalgen_Model_Survey_Setting to the database.
     * If the id is null then it will insert a new row
     *
     * @param $survey_setting mixed
     *                        This can either be an instance of Proposalgen_Model_Survey_Setting or the primary key to delete
     *
     * @return mixed The primary key of the new row
     */
    public function delete ($survey_setting)
    {
        if ($survey_setting instanceof Proposalgen_Model_Survey_Setting)
        {
            $whereClause = array(
                'id = ?' => $survey_setting->id
            );
        }
        else
        {
            $whereClause = array(
                'id = ?' => $survey_setting
            );
        }

        return $this->getDbTable()->delete($whereClause);
    }

    /**
     * Finds a survey_setting based on it's primaryKey
     *
     * @param $id int
     *            The id of the survey_setting to find
     *
     * @return Proposalgen_Model_Survey_Setting
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return null;
        }
        $row = $result->current();

        return new Proposalgen_Model_Survey_Setting($row->toArray());
    }

    /**
     * Fetches a survey_setting
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return Proposalgen_Model_Survey_Setting
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        return new Proposalgen_Model_Survey_Setting($row->toArray());
    }

    /**
     * Fetches all survey_settings
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
     * @return Proposalgen_Model_Survey_Setting[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $entries [] = new Proposalgen_Model_Survey_Setting($row->toArray());
        }

        return $entries;
    }

    public function fetchUserSurveySetting ($userId)
    {
        $surveySetting     = false;
        $userSurveySetting = Proposalgen_Model_Mapper_User_Survey_Setting::getInstance()->find($userId);
        if ($userSurveySetting)
        {
            $surveySetting = $this->find($userSurveySetting->surveySettingId);
        }

        // If we don't have a setting yet, make a blank one
        if (!$surveySetting)
        {
            $surveySetting   = new Proposalgen_Model_Survey_Setting();
            $surveySettingId = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->insert($surveySetting);

            if ($userSurveySetting)
            {
                $userSurveySetting->surveySettingId = $surveySettingId;
                Proposalgen_Model_Mapper_User_Survey_Setting::getInstance()->save($userSurveySetting, null);
            }
            else
            {
                $userSurveySetting                  = new Proposalgen_Model_User_Survey_Setting();
                $userSurveySetting->userId          = $userId;
                $userSurveySetting->surveySettingId = $surveySettingId;
                Proposalgen_Model_Mapper_User_Survey_Setting::getInstance()->insert($userSurveySetting);
            }
        }

        return $surveySetting;

    }

    /**
     * @param Proposalgen_Model_Survey_Setting $object
     *
     * @return mixed
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }
}