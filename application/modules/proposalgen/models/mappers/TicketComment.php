<?php
class Proposalgen_Model_Mapper_TicketComment extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_TicketComment";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_TicketComment
     */
    public static function getInstance ()
    {
        if (!isset(self::$_instance))
        {
            $className       = get_class();
            self::$_instance = new $className();
        }

        return self::$_instance;
    }

    /**
     * Maps a database row object to an Proposalgen_Model
     *
     * @param Zend_Db_Table_Row $row
     *
     * @throws Exception
     * @return Proposalgen_Model_Mapper_TicketComment
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object              = new Proposalgen_Model_TicketComment();
            $object->commentId   = $row->id;
            $object->ticketId    = $row->ticket_id;
            $object->userId      = $row->user_id;
            $object->commentText = $row->content;
            $object->commentDate = $row->date_created;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a ticket comment row", 0, $e);
        }

        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param Proposalgen_Model_TicketComment $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        try
        {
            $data ["id"]           = $object->commentId;
            $data ["ticket_id"]    = $object->ticketId;
            $data ["user_id"]      = $object->userId;
            $data ["content"]      = $object->commentText;
            $data ["date_created"] = $object->commentDate;
            $primaryKey            = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}