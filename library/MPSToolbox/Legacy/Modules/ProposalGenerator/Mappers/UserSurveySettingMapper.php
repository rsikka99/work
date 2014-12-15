<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use Exception;
use MPSToolbox\Legacy\Models\UserModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\UserSurveySettingModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class UserSurveySettingMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class UserSurveySettingMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\UserSurveySettingDbTable';

    protected $col_userId = 'userId';

    /**
     * Gets an instance of the mapper
     *
     * @return UserSurveySettingMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\UserSurveySettingModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $user_survey_setting UserSurveySettingModel
     *                             The object to insert
     *
     * @return int The primary key of the new row
     */
    public function insert (&$user_survey_setting)
    {
        $data = $user_survey_setting->toArray();
        $id   = $this->getDbTable()->insert($data);

        return $id;
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\UserSurveySettingModel to the database.
     *
     * @param UserSurveySettingModel $object     The   user_survey_setting model to save to the database
     * @param mixed                  $primaryKey Optional: The original primary key, in case we're changing it
     *
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());

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
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\UserSurveySettingModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $user_survey_setting mixed
     *                             This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\UserSurveySettingModel or the primary key to delete
     *
     * @return int The primary key of the new row
     */
    public function delete ($user_survey_setting)
    {
        if ($user_survey_setting instanceof UserSurveySettingModel)
        {
            $user_survey_setting = $user_survey_setting->toArray();
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
     * @return UserSurveySettingModel
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return false;
        }
        $row = $result->current();

        return new UserSurveySettingModel($row->toArray());
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
     * @return UserSurveySettingModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        return new UserSurveySettingModel($row->toArray());
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
     * @return UserSurveySettingModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $entries [] = new UserSurveySettingModel($row->toArray());
        }

        return $entries;
    }

    /**
     * @param $userId UserModel
     *
     * @return UserSurveySettingModel
     */
    public function findUserSurveySettingByUserId ($userId)
    {
        $surveySetting = array();

        try
        {
            // Get all rows that with this user id
            $userSurveySettings = $this->fetchAll(array("{$this->col_userId} = {$userId}"));
            foreach ($userSurveySettings as $userSurveySetting)
            {
                $surveySetting [] = $userSurveySetting;
            }
        }
        catch (Exception $e)
        {
            $surveySetting = null;
        }

        return $surveySetting;
    }

    /**
     * @param UserSurveySettingModel $object
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array(
            $object->userId,
            $object->surveySettingId
        );
    }
}