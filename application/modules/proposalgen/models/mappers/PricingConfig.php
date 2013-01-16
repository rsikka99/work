<?php
class Proposalgen_Model_Mapper_PricingConfig extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_PricingConfig";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_PricingConfig
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
     * @return \Proposalgen_Model_PricingConfig
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object                       = new Proposalgen_Model_PricingConfig();
            $object->pricingConfigId      = $row->id;
            $object->configName           = $row->name;
            $object->colorTonerPartTypeId = $row->color_toner_part_type_id;
            $object->monoTonerPartTypeId  = $row->mono_toner_part_type_id;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a pricing config row", 0, $e);
        }

        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param \Proposalgen_Model_PricingConfig $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        try
        {
            $data ["id"]   = $object->pricingConfigId;
            $data ["name"] = $object->configName;
            $primaryKey    = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}