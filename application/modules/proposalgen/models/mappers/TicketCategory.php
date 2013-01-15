<?php
class Proposalgen_Model_Mapper_TicketCategory extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_TicketCategory";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_TicketCategory
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
     * @return Proposalgen_Model_TicketCategory
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object               = new Proposalgen_Model_TicketCategory();
            $object->categoryId   = $row->id;
            $object->categoryName = $row->name;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a ticket category row", 0, $e);
        }

        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param Proposalgen_Model_TicketCategory $object
     *
     * @throws Exception
     * @return string
     */
    public function save (Proposalgen_Model_TicketCategory $object)
    {
        try
        {
            $data ["id"]   = $object->categoryId;
            $data ["name"] = $object->categoryName;
            $primaryKey    = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}