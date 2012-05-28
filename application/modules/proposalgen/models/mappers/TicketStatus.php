<?php

class Proposalgen_Model_Mapper_TicketStatus extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_TicketStatuses";
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
            $object = new Proposalgen_Model_TicketStatus();
            $object->setStatusId($row->status_id)->setStatusName($row->status_name);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a ticket status row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     * 
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_TicketStatus $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["status_id"] = $object->getStatusId();
            $data ["status_name"] = $object->getStatusName();
            
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