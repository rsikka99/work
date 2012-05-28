<?php

class Proposalgen_Model_Mapper_ReplacementDevice extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_ReplacementDevices";
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
            $object = new Proposalgen_Model_ReplacementDevice();
            $object->setMasterDeviceId($row->master_device_id)
                ->setReplacementCategory($row->replacement_category)
                ->setPrintSpeed($row->print_speed)
                ->setResolution($row->resolution)
                ->setMonthlyRate($row->monthly_rate);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a replacement device row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     * 
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_ReplacementDevice $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["master_device_id"] = $object->getMasterDeviceId();
            $data ["replacement_category"] = $object->getReplacementCategory();
            $data ["print_speed"] = $object->getPrintSpeed();
            $data ["resolution"] = $object->getResolution();
            $data ["monthly_rate"] = $object->getMonthlyRate();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }

    /**
     * Fetches the cheapest replacement device for each category
     */
    public function fetchCheapestForEachCategory ()
    {
        $replacementDevices = array ();
        $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACMENT_BW] = $this->fetchRow(array (
                'replacement_category = ?' => Proposalgen_Model_ReplacementDevice::REPLACMENT_BW 
        ), array (
                'monthly_rate ASC' 
        ));
        $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACMENT_BWMFP] = $this->fetchRow(array (
                'replacement_category = ?' => Proposalgen_Model_ReplacementDevice::REPLACMENT_BWMFP 
        ), array (
                'monthly_rate ASC' 
        ));
        $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACMENT_COLOR] = $this->fetchRow(array (
                'replacement_category = ?' => Proposalgen_Model_ReplacementDevice::REPLACMENT_COLOR 
        ), array (
                'monthly_rate ASC' 
        ));
        $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACMENT_COLORMFP] = $this->fetchRow(array (
                'replacement_category = ?' => Proposalgen_Model_ReplacementDevice::REPLACMENT_COLORMFP 
        ), array (
                'monthly_rate ASC' 
        ));
        return $replacementDevices;
    }
}
?>