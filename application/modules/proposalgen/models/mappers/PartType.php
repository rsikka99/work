<?php

class Proposalgen_Model_Mapper_PartType extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_PartType";
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
    static $_partTypes = array ();

    public function find ($id)
    {
        if (! array_key_exists($id, self::$_partTypes))
        {
            self::$_partTypes [$id] = parent::find($id);
        }
        return self::$_partTypes [$id];
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
            $object = new Proposalgen_Model_PartType();
            $object->setPartTypeId($row->part_type_id)->setTypeName($row->type_name);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a part type row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     * 
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_PartType $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["part_type_id"] = $object->getPartTypeId();
            $data ["type_name"] = $object->getTypeName();
            
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