<?php

class Proposalgen_Model_Mapper_UserPrivilege extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_UserPrivileges";
    static $_instance;

    /**
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
     * Maps a database row object to an Proposalgen_Model
     * @param Zend_Db_Table_Row $row
     * @return The appropriate Proposalgen_Model
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object = new Proposalgen_Model_UserPrivilege();
            $object->setPrivId($row->priv_id)->setUserId($row->user_id);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a user privilege row", 0, $e);
        }
        return $object;
    }

    public function save (Proposalgen_Model_UserPrivilege $object)
    {
        $primaryKey = false;
        try
        {
            $data ["priv_id"] = $object->getPrivId();
            $data ["user_id"] = $object->getUserId();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }

}
?>