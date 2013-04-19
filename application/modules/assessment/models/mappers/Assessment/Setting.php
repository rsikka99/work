<?php
class Assessment_Model_Mapper_Assessment_Setting extends My_Model_Mapper_Abstract
{
    /*
     * Column name definitions. Define all columns up here and use them down below.
     */
    public $col_id = 'id';

    /*
     * Mapper Definitions
     */
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Assessment_Model_DbTable_Assessment_Setting';

    /**
     * Gets an instance of the mapper
     *
     * @return Assessment_Model_Mapper_Assessment_Setting
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Assessment_Model_Report_Setting to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Assessment_Model_Assessment_Setting
     *                The object to insert
     *
     * @return int The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $object->toArray();

        // Remove the id
        unset($data [$this->col_id]);

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        $object->id = $id;

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of Assessment_Model_Report_Setting to the database.
     *
     * @param $object     Assessment_Model_Assessment_Setting
     *                    The assessment_setting model to save to the database
     * @param $primaryKey mixed
     *                    Optional: The original primary key, in case we're changing it
     *
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());

        if ($primaryKey === null)
        {
            $primaryKey = $data [$this->col_id];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
                                                                "{$this->col_id} = ?" => $primaryKey
                                                           ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Assessment_Model_Report_Setting or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Assessment_Model_Assessment_Setting)
        {
            $whereClause = array(
                "{$this->col_id} = ?" => $object->id
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_id} = ?" => $object
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a assessment_setting based on it's primaryKey
     *
     * @param $id int
     *            The id of the assessment_setting to find
     *
     * @return Assessment_Model_Assessment_Setting
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Assessment_Model_Assessment_Setting)
        {
            return $result;
        }

        // Assuming we don't have a cached object, lets go get it.
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new Assessment_Model_Assessment_Setting($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a assessment_setting
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: A SQL OFFSET value.
     *
     * @return Assessment_Model_Assessment_Setting
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Assessment_Model_Assessment_Setting($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all assessment_settings
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $count  int
     *                OPTIONAL: A SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *                OPTIONAL: A SQL LIMIT offset.
     *
     * @return Assessment_Model_Assessment_Setting[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Assessment_Model_Assessment_Setting($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param int $id
     *
     * @return array
     */
    public function getWhereId ($id)
    {
        return array(
            "{$this->col_id} = ?" => $id
        );
    }

    /**
     * @param Assessment_Model_Assessment_Setting $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Gets the systems assesssment setting object
     *
     * @return Assessment_Model_Assessment_Setting
     */
    public function fetchSystemAssessmentSetting ()
    {
        return $this->find(1);
    }

    /**
     *
     * @param int $userId
     *            The user's id
     *
     * @return Assessment_Model_Assessment_Setting Returns false if it could not find one.
     */
    public function fetchUserAssessmentSetting ($userId)
    {
        $assesssmentSetting = false;
        $userReportSetting  = Assessment_Model_Mapper_User_Report_Setting::getInstance()->find($userId);
        if ($userReportSetting)
        {
            $assesssmentSetting = $this->find($userReportSetting->reportSettingId);
        }

        // If we don't have a setting yet, make a blank one
        if (!$assesssmentSetting)
        {
            $assesssmentSetting   = new Assessment_Model_Assessment_Setting();
            $assesssmentSettingId = Assessment_Model_Mapper_Assessment_Setting::getInstance()->insert($assesssmentSetting);

            if ($userReportSetting)
            {
                $userReportSetting->reportSettingId = $assesssmentSettingId;
                Assessment_Model_Mapper_User_Report_Setting::getInstance()->save($userReportSetting);
            }
            else
            {
                $userReportSetting                  = new Assessment_Model_User_Report_Setting();
                $userReportSetting->userId          = $userId;
                $userReportSetting->reportSettingId = $assesssmentSettingId;
                Assessment_Model_Mapper_User_Report_Setting::getInstance()->insert($userReportSetting);
            }
        }

        return $assesssmentSetting;
    }

    /**
     * Gets a assesssments assesssment setting object
     *
     * @param int $assessmentId
     *            The assesssment's id
     *
     * @return Assessment_Model_Assessment_Setting Returns false if it could not find one.
     */
    public function fetchAssessmentAssessmentSetting ($assessmentId)
    {
        $assessmentSetting           = false;
        $assessmentAssessmentSetting = Assessment_Model_Mapper_Assessment_Assessment_Setting::getInstance()->find($assessmentId);
        if ($assessmentAssessmentSetting)
        {
            $assessmentSetting = $this->find($assessmentAssessmentSetting->assessmentSettingId);
        }

        // If we don't have a setting yet, make a blank one
        if (!$assessmentSetting)
        {
            $assessmentSetting   = new Assessment_Model_Assessment_Setting();
            $assessmentSettingId = Assessment_Model_Mapper_Assessment_Setting::getInstance()->insert($assessmentSetting);

            if ($assessmentAssessmentSetting)
            {
                $assessmentAssessmentSetting->assessmentSettingId = $assessmentSettingId;
                Assessment_Model_Mapper_Assessment_Assessment_Setting::getInstance()->save($assessmentAssessmentSetting);
            }
            else
            {
                $assessmentAssessmentSetting                      = new Assessment_Model_Assessment_Assessment_Setting();
                $assessmentAssessmentSetting->assessmentId        = $assessmentId;
                $assessmentAssessmentSetting->assessmentSettingId = $assessmentSettingId;
                Assessment_Model_Mapper_Assessment_Assessment_Setting::getInstance()->insert($assessmentAssessmentSetting);
            }
        }

        return $assessmentSetting;
    }

    /**
     * Gets a dealer assessment setting, if none exist it creates one.
     *
     * @param $dealerId
     *
     * @return bool|\Assessment_Model_Assessment_Setting
     */
    public function fetchDealerAssessmentSetting ($dealerId)
    {
        $assessmentSetting = false;
        $dealerSetting     = Preferences_Model_Mapper_Dealer_Setting::getInstance()->find($dealerId);

        if ($dealerSetting)
        {
            $assessmentSetting = $this->find($dealerSetting->assessmentSettingId);
        }

        if (!$assessmentSetting)
        {
            // Take a copy
            $assessmentSetting     = $this->fetchSystemAssessmentSetting();
            $assessmentSetting->id = Assessment_Model_Mapper_Assessment_Setting::getInstance()->insert($assessmentSetting);

            if ($dealerSetting)
            {
                $dealerSetting->assessmentSettingId = $assessmentSetting->id;
                Preferences_Model_Mapper_Dealer_Setting::getInstance()->save($dealerSetting);
            }
            else
            {
                $dealerSetting                      = new Preferences_Model_Dealer_Setting();
                $dealerSetting->assessmentSettingId = $assessmentSetting->id;
                $dealerSetting->dealerId            = $dealerId;
                Preferences_Model_Mapper_Dealer_Setting::getInstance()->insert($dealerSetting);
            }
        }

        return $assessmentSetting;
    }
}