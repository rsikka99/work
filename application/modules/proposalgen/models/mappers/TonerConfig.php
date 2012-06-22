<?php

class Proposalgen_Model_Mapper_TonerConfig extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_TonerConfig";
    static $_instance;

    /**
     *
     * @return Proposalgen_Model_Mapper_TonerConfig
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
    static $_tonerConfigs = array ();

    public function find ($id)
    {
        if (! array_key_exists($id, self::$_tonerConfigs))
        {
            self::$_tonerConfigs [$id] = parent::find($id);
        }
        return self::$_tonerConfigs [$id];
    }

    /**
     * Maps a database row object to an Proposalgen_Model
     *
     * @param Zend_Db_Table_Row $row            
     * @return Proposalgen_Model_TonerConfig
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object = new Proposalgen_Model_TonerConfig();
            $object->setTonerConfigId($row->id)->setTonerConfigName($row->name);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a toner config row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_TonerConfig $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["id"] = $object->getTonerConfigId();
            $data ["name"] = $object->getTonerConfigName();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }
}
