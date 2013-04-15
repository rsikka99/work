<?php
class Proposalgen_Model_Mapper_HealthCheck_Setting extends My_Model_Mapper_Abstract
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
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_HealthCheck_Setting';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_HealthCheck_Setting
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_HealthCheck_Setting to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Proposalgen_Model_HealthCheck_Setting
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
     * Saves (updates) an instance of Proposalgen_Model_HealthCheck_Setting to the database.
     *
     * @param $object     Proposalgen_Model_HealthCheck_Setting
     *                    The HealthCheck_setting model to save to the database
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
     *                This can either be an instance of Proposalgen_Model_HealthCheck_Setting or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Proposalgen_Model_HealthCheck_Setting)
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
     * Finds a HealthCheck_setting based on it's primaryKey
     *
     * @param $id int
     *            The id of the HealthCheck_setting to find
     *
     * @return Proposalgen_Model_HealthCheck_Setting
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Proposalgen_Model_HealthCheck_Setting)
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
        $object = new Proposalgen_Model_HealthCheck_Setting($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a HealthCheck_setting
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: A SQL OFFSET value.
     *
     * @return Proposalgen_Model_HealthCheck_Setting
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Proposalgen_Model_HealthCheck_Setting($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all HealthCheck_settings
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
     * @return Proposalgen_Model_HealthCheck_Setting[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Proposalgen_Model_HealthCheck_Setting($row->toArray());

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
     * @param Proposalgen_Model_HealthCheck_Setting $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Gets the systems HealthCheck setting object
     *
     * @return Proposalgen_Model_HealthCheck_Setting
     */
    public function fetchSystemHealthCheckSetting ()
    {
        return $this->find(1);
    }

    /**
     * Gets the systems HealthCheck setting object
     *
     * @param $HealthCheckId
     *
     * @return Proposalgen_Model_HealthCheck_Setting
     */
    public function fetchSetting ($HealthCheckId)
    {
        $HealthCheckSetting = $this->find($HealthCheckId);
        // If we don't have a setting yet, make a blank one
        if (!$HealthCheckSetting)
        {
            $HealthCheckSetting   = new Proposalgen_Model_HealthCheck_Setting();
            $HealthCheckSetting->id = Proposalgen_Model_Mapper_HealthCheck_Setting::getInstance()->insert($HealthCheckSetting);
        }
        return $HealthCheckSetting;
    }
    /**
     * Gets a users HealthCheck setting object
     *
     * @param int $userId
     *            The user's id
     *
     * @return Proposalgen_Model_HealthCheck_Setting Returns false if it could not find one.
     */
    public function fetchUserHealthCheckSetting ($userId)
    {
        $HealthCheckSetting     = false;
        $userHealthCheckSetting = Proposalgen_Model_Mapper_User_HealthCheck_Setting::getInstance()->find($userId);
        if ($userHealthCheckSetting)
        {
            $HealthCheckSetting = $this->find($userHealthCheckSetting->HealthCheckSettingId);
        }

        // If we don't have a setting yet, make a blank one
        if (!$HealthCheckSetting)
        {
            $HealthCheckSetting   = new Proposalgen_Model_HealthCheck_Setting();
            $HealthCheckSettingId = Proposalgen_Model_Mapper_HealthCheck_Setting::getInstance()->insert($HealthCheckSetting);

            if ($userHealthCheckSetting)
            {
                $userHealthCheckSetting->HealthCheckSettingId = $HealthCheckSettingId;
                Proposalgen_Model_Mapper_User_HealthCheck_Setting::getInstance()->save($userHealthCheckSetting);
            }
            else
            {
                $userHealthCheckSetting                  = new Proposalgen_Model_User_HealthCheck_Setting();
                $userHealthCheckSetting->userId          = $userId;
                $userHealthCheckSetting->HealthCheckSettingId = $HealthCheckSettingId;
                Proposalgen_Model_Mapper_User_HealthCheck_Setting::getInstance()->insert($userHealthCheckSetting);
            }
        }

        return $HealthCheckSetting;
    }

    /**
     * Gets a dealer HealthCheck setting, if none exist it creates one.
     *
     * @param $dealerId
     *
     * @return bool|\Proposalgen_Model_HealthCheck_Setting
     */
    public function fetchDealerSetting ($dealerId)
    {
        $HealthCheckSetting       = false;
        $HealthCheckDealerSetting = Proposalgen_Model_Mapper_HealthCheck_Setting::getInstance()->fetchDealerSetting($dealerId);

        if ($HealthCheckDealerSetting)
        {
            $HealthCheckSetting = $this->find($HealthCheckDealerSetting->HealthCheckSettingId);
        }

        if (!$HealthCheckSetting)
        {
            // Take a copy
            $HealthCheckSetting   = $this->fetchSystemHealthCheckSetting();
            $HealthCheckSettingId = Proposalgen_Model_Mapper_HealthCheck_Setting::getInstance()->insert($HealthCheckSetting);

            if ($HealthCheckDealerSetting)
            {
                $HealthCheckDealerSetting->HealthCheckSettingId = $HealthCheckSettingId;
                Proposalgen_Model_Mapper_Dealer_HealthCheck_Setting::getInstance()->save($HealthCheckDealerSetting);
            }
            else
            {
                $HealthCheckDealerSetting                  = new Proposalgen_Model_Dealer_HealthCheck_Setting();
                $HealthCheckDealerSetting->HealthCheckSettingId = $HealthCheckSettingId;
                $HealthCheckDealerSetting->dealerId        = $dealerId;
                Proposalgen_Model_Mapper_Dealer_HealthCheck_Setting::getInstance()->insert($HealthCheckDealerSetting);
            }
        }

        return $HealthCheckSetting;
    }
}