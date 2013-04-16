<?php
class Proposalgen_Model_Mapper_Healthcheck_Setting extends My_Model_Mapper_Abstract
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
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_Healthcheck_Setting';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_Healthcheck_Setting
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_Healthcheck_Setting to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Proposalgen_Model_Healthcheck_Setting
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
     * Saves (updates) an instance of Proposalgen_Model_Healthcheck_Setting to the database.
     *
     * @param $object     Proposalgen_Model_Healthcheck_Setting
     *                    The Healthcheck_setting model to save to the database
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
     *                This can either be an instance of Proposalgen_Model_Healthcheck_Setting or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Proposalgen_Model_Healthcheck_Setting)
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
     * Finds a Healthcheck_setting based on it's primaryKey
     *
     * @param $id int
     *            The id of the Healthcheck_setting to find
     *
     * @return Proposalgen_Model_Healthcheck_Setting
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Proposalgen_Model_Healthcheck_Setting)
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
        $object = new Proposalgen_Model_Healthcheck_Setting($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a Healthcheck setting
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: A SQL OFFSET value.
     *
     * @return Proposalgen_Model_Healthcheck_Setting
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Proposalgen_Model_Healthcheck_Setting($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all Healthcheck_settings
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
     * @return Proposalgen_Model_Healthcheck_Setting[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Proposalgen_Model_Healthcheck_Setting($row->toArray());

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
     * @param Proposalgen_Model_Healthcheck_Setting $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Gets the systems Healthcheck setting object
     *
     * @return Proposalgen_Model_Healthcheck_Setting
     */
    public function fetchSystemHealthcheckSetting ()
    {
        return $this->find(1);
    }

    /**
     * Gets the systems Healthcheck setting object
     *
     * @param $HealthcheckId
     *
     * @return Proposalgen_Model_Healthcheck_Setting
     */
    public function fetchSetting ($HealthcheckId)
    {
        $HealthcheckSetting = $this->find($HealthcheckId);
        // If we don't have a setting yet, make a blank one
        if (!$HealthcheckSetting)
        {
            $HealthcheckSetting   = new Proposalgen_Model_Healthcheck_Setting();
            $HealthcheckSetting->id = Proposalgen_Model_Mapper_Healthcheck_Setting::getInstance()->insert($HealthcheckSetting);
        }
        return $HealthcheckSetting;
    }
    /**
     * Gets a users Healthcheck setting object
     *
     * @param int $userId
     *            The user's id
     *
     * @return Proposalgen_Model_Healthcheck_Setting Returns false if it could not find one.
     */
    public function fetchUserSetting ($userId)
    {
        $HealthcheckSetting     = false;
        $userSetting = Preferences_Model_Mapper_User_Setting::getInstance()->find($userId);
        if ($userSetting)
        {
            $HealthcheckSetting = $this->find($userSetting->healthcheckSettingId);
        }

        // If we don't have a setting yet, make a blank one
        if (!$HealthcheckSetting)
        {
            $HealthcheckSetting   = new Proposalgen_Model_Healthcheck_Setting();
            $HealthcheckSetting->id = Proposalgen_Model_Mapper_Healthcheck_Setting::getInstance()->insert($HealthcheckSetting);

            if ($userSetting)
            {
                $userSetting->healthcheckSettingId = $HealthcheckSetting->id;
                Preferences_Model_Mapper_User_Setting::getInstance()->save($userSetting);
            }
            else
            {
                $userSetting                  = new Preferences_Model_User_Setting();
                $userSetting->userId          = $userId;
                $userSetting->healthcheckSettingId = $HealthcheckSetting->id;
                Preferences_Model_Mapper_User_Setting::getInstance()->insert($userSetting);
            }
        }

        return $HealthcheckSetting;
    }

    /**
     * Gets a dealer Healthcheck setting, if none exist it creates one.
     *
     * @param $dealerId
     *
     * @return Proposalgen_Model_Healthcheck_Setting
     */
    public function fetchDealerSetting ($dealerId)
    {
        $HealthcheckSetting       = false;
        $HealthcheckDealerSetting = Preferences_Model_Mapper_Dealer_Setting::getInstance()->find($dealerId);

        if ($HealthcheckDealerSetting)
        {
            $HealthcheckSetting = $this->find($HealthcheckDealerSetting->healthcheckSettingId);
        }

        if (!$HealthcheckSetting)
        {
            // Take a copy
            $HealthcheckSetting   = $this->fetchSystemHealthcheckSetting();
            $HealthcheckSetting->id = Proposalgen_Model_Mapper_Healthcheck_Setting::getInstance()->insert($HealthcheckSetting);

            if ($HealthcheckDealerSetting)
            {
                $HealthcheckDealerSetting->HealthcheckSettingId = $HealthcheckSetting->id;
                Preferences_Model_Mapper_Dealer_Setting::getInstance()->save($HealthcheckDealerSetting);
            }
            else
            {
                $HealthcheckDealerSetting                  = new Preferences_Model_Dealer_Setting();
                $HealthcheckDealerSetting->healthcheckSettingId = $HealthcheckSetting->id;
                $HealthcheckDealerSetting->dealerId        = $dealerId;
                Preferences_Model_Mapper_Dealer_Setting::getInstance()->insert($HealthcheckDealerSetting);
            }
        }

        return $HealthcheckSetting;
    }
}