<?php

class Proposalgen_Model_Mapper_Toner extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_Toner";
    static $_instance;

    /**
     *
     * @return Proposalgen_Model_Mapper_Toner
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
    static $_toners = array ();

    public function find ($id)
    {
        if (! array_key_exists($id, self::$_toners))
        {
            self::$_toners [$id] = parent::find($id);
        }
        return self::$_toners [$id];
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
            $object = new Proposalgen_Model_Toner();
            $object->setTonerId($row->id)
                ->setTonerSKU($row->sku)
                ->setTonerPrice($row->cost)
                ->setTonerYield($row->yield)
                ->setPartTypeId($row->part_type_id)
                ->setManufacturerId($row->manufacturer_id)
                ->setTonerColorId($row->toner_color_id);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a toner row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_Toner $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["id"] = $object->getTonerId();
            $data ["sku"] = $object->getTonerSKU();
            $data ["cost"] = $object->getTonerPrice();
            $data ["yield"] = $object->getTonerYield();
            $data ["manufacturer_id"] = $object->getManufacturerId();
            $data ["part_type_id"] = $object->getPartTypeId();
            $data ["toner_color_id"] = $object->getTonerColorId();
            
            $primaryKey = $this->saveRow($data);
            if ($primaryKey < 1)
                throw new Exception("LOL");
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.LOL - " . var_export($object->getTonerId(), true), 0, $e);
        }
        return $primaryKey;
    }

    public function getTonersForDevice ($masterDeviceId)
    {
        $toners = array ();
        try
        {
            $deviceToners = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchAll(array (
                    "master_device_id = ?" => $masterDeviceId 
            ));
            if ($deviceToners)
            {
                foreach ( $deviceToners as $deviceToner )
                {
                    $toner = $this->find($deviceToner->getTonerId());
                    $toners [$toner->getPartType()->getPartTypeId()] [$toner->getTonerColor()->getTonerColorId()] [] = $toner;
                }
            }
        }
        catch ( Exception $e )
        {
            throw new Exception("Error fetching all toners for a master device", 0, $e);
        }
        return $toners;
    }
}
