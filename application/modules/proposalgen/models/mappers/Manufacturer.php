<?php

class Proposalgen_Model_Mapper_Manufacturer extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_Manufacturer";
    static $_instance;

    /**
     *
     * @return Proposalgen_Model_Mapper_Manufacturer
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
    static $_manufacturers = array ();

    public function find ($id)
    {
        if (! array_key_exists($id, self::$_manufacturers))
        {
            self::$_manufacturers [$id] = parent::find($id);
        }
        return self::$_manufacturers [$id];
    }

    /**
     * Maps a database row object to an Proposalgen_Model
     * 
     * @param Zend_Db_Table_Row $row            
     * @return The appropriate Proposalgen_Model
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object = new Proposalgen_Model_Manufacturer();
            $object->setManufacturerId($row->id)
                ->setManufacturerName($row->manufacturer_name)
                ->setIsDeleted($row->is_deleted);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a manufacturer row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     * 
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_Manufacturer $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["id"] = $object->getManufacturerId();
            $data ["manufacturer_name"] = $object->getManufacturerName();
            $data ["is_deleted"] = $object->getIsDeleted();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }
}
