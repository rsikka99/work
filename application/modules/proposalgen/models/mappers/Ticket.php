<?php

class Proposalgen_Model_Mapper_Ticket extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_Ticket";
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
            $object = new Proposalgen_Model_Ticket();
            $object->setTicketId($row->ticket_id)
            	->setUserId($row->user_id)
            	->setCategoryId($row->category_id)
            	->setStatusId($row->status_id)
            	->setTitle($row->title)
            	->setDescription($row->description)
            	->setDateCreated($row->date_created)
            	->setDateUpdated($row->date_updated);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a ticket row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     * @param unknown_type $object
     */
    public function save (Proposalgen_Model_Ticket $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["ticket_id"] = $object->getTicketId();
            $data ["user_id"] = $object->getUserId();
            $data ["user_id"] = $object->getUserId();
            $data ["category_id"] = $object->getCategoryId();
            $data ["status_id"] = $object->getStatusId();
            $data ["title"] = $object->getTitle();
            $data ["description"] = $object->getDescription();
            $data ["date_created"] = $object->getDateCreated();
            $data ["date_updated"] = $object->getDateUpdated();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }
}
