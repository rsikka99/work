<?php
class Proposalgen_Model_Mapper_ReplacementDevice extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_ReplacementDevices";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_ReplacementDevice
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
     * @return Proposalgen_Model_ReplacementDevice
     * @throws Exception
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object                      = new Proposalgen_Model_ReplacementDevice();
            $object->masterDeviceId      = $row->master_device_id;
            $object->replacementCategory = $row->replacement_category;
            $object->printSpeed          = $row->print_speed;
            $object->resolution          = $row->resolution;
            $object->monthlyRate         = $row->monthly_rate;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a replacement device row", 0, $e);
        }

        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param Proposalgen_Model_ReplacementDevice $object
     *
     * @return string
     * @throws Exception
     */
    public function save ($object)
    {
        try
        {
            $data ["master_device_id"]     = $object->masterDeviceId;
            $data ["replacement_category"] = $object->replacementCategory;
            $data ["print_speed"]          = $object->printSpeed;
            $data ["resolution"]           = $object->resolution;
            $data ["monthly_rate"]         = $object->monthlyRate;
            $primaryKey                    = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }

    /**
     * Fetches the cheapest replacement device for each category
     *
     * @return Proposalgen_Model_ReplacementDevice[][]
     */
    public function fetchCheapestForEachCategory ()
    {
        $replacementDevices                                                            = array();
        $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACMENT_BW]       = $this->fetchRow(array(
                                                                                                              'replacement_category = ?' => Proposalgen_Model_ReplacementDevice::REPLACMENT_BW
                                                                                                         ), array(
                                                                                                                 'monthly_rate ASC'
                                                                                                            ));
        $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACMENT_BWMFP]    = $this->fetchRow(array(
                                                                                                              'replacement_category = ?' => Proposalgen_Model_ReplacementDevice::REPLACMENT_BWMFP
                                                                                                         ), array(
                                                                                                                 'monthly_rate ASC'
                                                                                                            ));
        $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACMENT_COLOR]    = $this->fetchRow(array(
                                                                                                              'replacement_category = ?' => Proposalgen_Model_ReplacementDevice::REPLACMENT_COLOR
                                                                                                         ), array(
                                                                                                                 'monthly_rate ASC'
                                                                                                            ));
        $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACMENT_COLORMFP] = $this->fetchRow(array(
                                                                                                              'replacement_category = ?' => Proposalgen_Model_ReplacementDevice::REPLACMENT_COLORMFP
                                                                                                         ), array(
                                                                                                                 'monthly_rate ASC'
                                                                                                            ));

        return $replacementDevices;
    }
}