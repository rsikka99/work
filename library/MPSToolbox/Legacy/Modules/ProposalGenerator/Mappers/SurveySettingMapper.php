<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use MPSToolbox\Legacy\Modules\Preferences\Mappers\DealerSettingMapper;
use MPSToolbox\Legacy\Modules\Preferences\Models\DealerSettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\SurveySettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\UserSurveySettingModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class SurveySettingMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class SurveySettingMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\SurveySettingDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return SurveySettingMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\SurveySettingModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object SurveySettingModel
     *                        The object to insert
     * @return int The primary key of the new row
     */
    public function insert ($object)
    {
        $data = $object->toArray();
        unset($data ['id']);
        $id = $this->getDbTable()->insert($data);

        // Since the survey_setting is set properly, set the id in the appropriate places
        $object->id = $id;

        return $id;
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\SurveySettingModel to the database.
     *
     * @param $object         SurveySettingModel
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
        $rowsAffected = $this->getDbTable()->update($data, [
            'id = ?' => $primaryKey,
        ]);

        return $rowsAffected;
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\SurveySettingModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $survey_setting mixed
     *                        This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\SurveySettingModel or the primary key to delete
     *
     * @return int The primary key of the new row
     */
    public function delete ($survey_setting)
    {
        if ($survey_setting instanceof SurveySettingModel)
        {
            $whereClause = [
                'id = ?' => $survey_setting->id,
            ];
        }
        else
        {
            $whereClause = [
                'id = ?' => $survey_setting,
            ];
        }

        return $this->getDbTable()->delete($whereClause);
    }

    /**
     * Finds a survey_setting based on it's primaryKey
     *
     * @param $id int
     *            The id of the survey_setting to find
     *
     * @return SurveySettingModel
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return null;
        }
        $row = $result->current();

        return new SurveySettingModel($row->toArray());
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
     * @return SurveySettingModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        return new SurveySettingModel($row->toArray());
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
     * @return SurveySettingModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $entries [] = new SurveySettingModel($row->toArray());
        }

        return $entries;
    }

    /**
     * @param $userId int The id of the user to find
     *
     * @return UserSurveySettingModel
     */
    public function fetchUserSurveySetting ($userId)
    {
        $surveySetting     = false;
        $userSurveySetting = UserSurveySettingMapper::getInstance()->find($userId);
        if ($userSurveySetting)
        {
            $surveySetting = $this->find($userSurveySetting->surveySettingId);
        }

        // If we don't have a setting yet, make a blank one
        if (!$surveySetting)
        {
            $surveySetting   = new SurveySettingModel();
            $surveySettingId = SurveySettingMapper::getInstance()->insert($surveySetting);

            if ($userSurveySetting)
            {
                $userSurveySetting->surveySettingId = $surveySettingId;
                UserSurveySettingMapper::getInstance()->save($userSurveySetting, null);
            }
            else
            {
                $userSurveySetting                  = new UserSurveySettingModel();
                $userSurveySetting->userId          = $userId;
                $userSurveySetting->surveySettingId = $surveySettingId;
                UserSurveySettingMapper::getInstance()->insert($userSurveySetting);
            }
        }

        return $surveySetting;

    }

    /**
     * @param $dealerId
     *
     * @return bool|SurveySettingModel
     */
    public function fetchDealerSurveySetting ($dealerId)
    {
        $surveySetting = false;
        $dealerSetting = DealerSettingMapper::getInstance()->find($dealerId);
        if ($dealerSetting)
        {
            $surveySetting = $this->find($dealerSetting->surveySettingId);
        }

        if (!$surveySetting)
        {
            $surveySetting     = new SurveySettingModel();
            $surveySetting->id = SurveySettingMapper::getInstance()->insert($surveySetting);

            if ($dealerSetting)
            {
                $dealerSetting->surveySettingId = $surveySetting->id;
                DealerSettingMapper::getInstance()->save($dealerSetting, null);
            }
            else
            {
                $dealerSetting                  = new DealerSettingModel();
                $dealerSetting->dealerId        = $dealerId;
                $dealerSetting->surveySettingId = $surveySetting->id;
                DealerSettingMapper::getInstance()->insert($dealerSetting);
            }
        }

        return $surveySetting;
    }

    /**
     * Fetches system default report survey_setting
     *
     * @return SurveySettingModel
     */
    public function fetchSystemSurveySettings ()
    {
        return $this->find(1);
    }

    /**
     * @param SurveySettingModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }
}