<?php
class Proposalgen_Model_Mapper_Toner extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_Toner";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_Toner
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

    static $_toners = array();

    /**
     * Gets a toner model
     *
     * @see Tangent_Model_Mapper_Abstract::find()
     *
     * @param int $id
     *
     * @return Proposalgen_Model_Toner
     */
    public function find ($id)
    {
        if (!array_key_exists($id, self::$_toners))
        {
            self::$_toners [$id] = parent::find($id);
        }

        return self::$_toners [$id];
    }

    /**
     * Maps a database row object to an Proposalgen_Model
     *
     * @param Zend_Db_Table_Row $row
     *
     * @throws Exception
     * @return \Proposalgen_Model_Toner
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object                 = new Proposalgen_Model_Toner();
            $object->id             = $row->id;
            $object->sku            = $row->sku;
            $object->cost           = $row->cost;
            $object->yield          = $row->yield;
            $object->partTypeId     = $row->part_type_id;
            $object->manufacturerId = $row->manufacturer_id;
            $object->tonerColorId   = $row->toner_color_id;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a toner row", 0, $e);
        }

        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param \Proposalgen_Model_Toner $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        try
        {
            $data ["id"]              = $object->id;
            $data ["sku"]             = $object->sku;
            $data ["cost"]            = $object->cost;
            $data ["yield"]           = $object->yield;
            $data ["manufacturer_id"] = $object->manufacturerId;
            $data ["part_type_id"]    = $object->partTypeId;
            $data ["toner_color_id"]  = $object->tonerColorId;
            $primaryKey               = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }

    /**
     * Gets all the toners for a device
     *
     * @param $masterDeviceId
     *
     * @return Proposalgen_Model_Toner[][][]
     * @throws Exception
     */
    public function getTonersForDevice ($masterDeviceId)
    {
        $toners = array();
        try
        {
            $deviceToners = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchAll(array(
                                                                                               "master_device_id = ?" => $masterDeviceId
                                                                                          ));
            if ($deviceToners)
            {
                /* @var $deviceToner Proposalgen_Model_DeviceToner */
                foreach ($deviceToners as $deviceToner)
                {
                    $toner                                                                                 = $this->find($deviceToner->tonerId);
                    $toners [$toner->getPartType()->partTypeId] [$toner->getTonerColor()->tonerColorId] [] = $toner;
                }
            }
        }
        catch (Exception $e)
        {
            throw new Exception("Error fetching all toners for a master device", 0, $e);
        }

        return $toners;
    }
}