<?php

class Proposalgen_Model_Mapper_UserSession extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_UserSessions";
    static $_instance;

    /**
     *
     * @return Tangent_Model_Mapper_Abstract
     */
    public static function getInstance ()
    {
        if (! isset(self::$_instance))
        {
            $className = get_class();
            self::$_instance = new $className();
        }
        return self::$_instance;
    }

    /**
     * Maps a database row object to an Application_Model
     * 
     * @param Zend_Db_Table_Row $row            
     * @return The appropriate Application_Model
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object = new Proposalgen_Model_UserSession();
            $object->setUserId($row->user_id)->setSessionId($row->session_id);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a user session row", 0, $e);
        }
        return $object;
    }

    public function save (Proposalgen_Model_UserSession $object)
    {
        $primaryKey = false;
        try
        {
            $data ["user_id"] = $object->getUserId();
            $data ["session_id"] = $object->getSessionId();
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }
}