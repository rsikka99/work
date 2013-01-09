<?php

class Proposalgen_Model_Mapper_TicketComment extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_TicketComment";
    static $_instance;

    /**
     *
     * @return Proposalgen_Model_Mapper_TicketComment
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
     *
     * @param Zend_Db_Table_Row $row            
     * @return Proposalgen_Model_Mapper_TicketComment
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object = new Proposalgen_Model_TicketComment();
            $object->setCommentId($row->id)
                ->setTicketId($row->ticket_id)
                ->setUserId($row->user_id)
                ->setCommentText($row->content)
                ->setCommentDate($row->date_created);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a ticket comment row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_TicketComment $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["id"] = $object->getCommentId();
            $data ["ticket_id"] = $object->getTicketId();
            $data ["user_id"] = $object->getUserId();
            $data ["content"] = $object->getCommentText();
            $data ["date_created"] = $object->getCommentDate();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }
}
