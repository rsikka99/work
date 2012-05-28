<?php

class Proposalgen_Model_Mapper_TonerColor extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_TonerColor";
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
    static $_tonerColors = array ();

    public function find ($id)
    {
        if (! array_key_exists($id, self::$_tonerColors))
        {
            self::$_tonerColors [$id] = parent::find($id);
        }
        return self::$_tonerColors [$id];
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
            $object = new Proposalgen_Model_TonerColor();
            $object->setTonerColorId($row->toner_color_id)->setTonerColorName($row->toner_color_name);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a toner color row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     * 
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_TonerColor $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["toner_color_id"] = $object->getTonerColorId();
            $data ["toner_color_name"] = $object->getTonerColorName();
            
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