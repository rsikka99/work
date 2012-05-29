<?php

class Proposalgen_Model_Mapper_TicketCategory extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_TicketCategories";
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
            $object = new Proposalgen_Model_TicketCategory();
            $object->setCategoryId($row->category_id)
            	->setCategoryName($row->category_name);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a ticket category row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     * @param unknown_type $object
     */
    public function save (Proposalgen_Model_TicketCategory $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["category_id"] = $object->getCategoryId();
            $data ["category_name"] = $object->getCategoryName();
            
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