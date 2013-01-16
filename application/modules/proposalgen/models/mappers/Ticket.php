<?php
class Proposalgen_Model_Mapper_Ticket extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_Ticket";
    static $_instance;

    /**
     *
     * @return Proposalgen_Model_Mapper_Ticket
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
     * @return \Proposalgen_Model_Ticket
     * @throws Exception
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object              = new Proposalgen_Model_Ticket();
            $object->ticketId    = $row->id;
            $object->userId      = $row->user_id;
            $object->categoryId  = $row->category_id;
            $object->statusId    = $row->status_id;
            $object->title       = $row->title;
            $object->description = $row->description;
            $object->dateCreated = $row->date_created;
            $object->dateUpdated = $row->date_updated;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a ticket row", 0, $e);
        }

        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param Proposalgen_Model_Ticket $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        try
        {
            $data ["id"]           = $object->ticketId;
            $data ["user_id"]      = $object->userId;
            $data ["user_id"]      = $object->userId;
            $data ["category_id"]  = $object->categoryId;
            $data ["status_id"]    = $object->statusId;
            $data ["title"]        = $object->title;
            $data ["description"]  = $object->description;
            $data ["date_created"] = $object->dateCreated;
            $data ["date_updated"] = $object->dateUpdated;

            $primaryKey = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}