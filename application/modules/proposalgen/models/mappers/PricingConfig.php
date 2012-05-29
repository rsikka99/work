<?php

class Proposalgen_Model_Mapper_PricingConfig extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_PricingConfig";
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
            $object = new Proposalgen_Model_PricingConfig();
            $object->setPricingConfigId($row->pricing_config_id)->setConfigName($row->config_name)->setColorTonerPartTypeId($row->color_toner_part_type_id)->setMonoTonerPartTypeId($row->mono_toner_part_type_id);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a pricing config row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     * @param unknown_type $object
     */
    public function save (Proposalgen_Model_PricingConfig $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["pricing_config_id"] = $object->getPricingConfigId();
            $data ["config_name"] = $object->getConfigName();
            
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