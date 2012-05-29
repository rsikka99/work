<?php

class Proposalgen_Model_Mapper_TicketViewed extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_TicketsViewed";
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
            $object = new Proposalgen_Model_TicketViewed();
            $object->setTicketId($row->ticket_id)
                ->setUserId($row->user_id)
                ->setDateViewed($row->date_viewed);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a ticket viewed row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     * @param unknown_type $object
     */
    public function save (Proposalgen_Model_TicketViewed $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["ticket_id"] = $object->getTicketId();
            $data ["user_id"] = $object->getUserId();
            $data ["date_viewed"] = $object->getDateViewed();
            
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