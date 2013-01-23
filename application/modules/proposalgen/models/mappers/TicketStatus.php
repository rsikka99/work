<?php
class Proposalgen_Model_Mapper_TicketStatus extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_TicketStatus";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_TicketStatus
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
     * @return Proposalgen_Model_TicketStatus
     * @throws Exception
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object             = new Proposalgen_Model_TicketStatus();
            $object->statusId   = $row->id;
            $object->statusName = $row->name;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a ticket status row", 0, $e);
        }

        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param Proposalgen_Model_TicketStatus $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        try
        {
            $data ["id"]   = $object->statusId;
            $data ["name"] = $object->statusName;
            $primaryKey    = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}