<?php

class Proposalgen_Model_Mapper_DeviceInstance extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_DeviceInstance";
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
            $object = new Proposalgen_Model_DeviceInstance();
            $object->setDeviceInstanceId($row->device_instance_id)
                ->setReportId($row->report_id)
                ->setMasterDeviceId($row->master_device_id)
                ->setUploadDataCollectorId($row->upload_data_collector_id)
                ->setSerialNumber($row->serial_number)
                ->setMPSMonitorStartDate($row->mps_monitor_startdate)
                ->setMPSMonitorEndDate($row->mps_monitor_enddate)
                ->setMPSDiscoveryDate($row->mps_discovery_date)
                ->setIsExcluded($row->is_excluded)
                ->setIpAddress($row->ip_address)
                ->setJITSuppliesSupported($row->jit_supplies_supported);
        
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a device instance row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     * @param unknown_type $object
     */
    public function save (Proposalgen_Model_DeviceInstance $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["device_instance_id"] = $object->getTonerColorId();
            $data ["report_id"] = $object->getReportId();
            $data ["master_device_id"] = $object->getMasterDeviceId();
            $data ["upload_data_collector_id"] = $object->getUploadDataCollectorId();
            $data ["serial_number"] = $object->getSerialNumber();
            $data ["mps_monitor_startdate"] = $object->getMPSMonitorStartDate();
            $data ["mps_monitor_enddate"] = $object->getMPSMonitorEndDate();
            $data ["mps_discovery_date"] = $object->getMPSDiscoveryDate();
            $data ["is_excluded"] = $object->getIsExcluded();
            $data ["ip_address"] = $object->getIpAddress();
            $data ["jit_supplies_supported"] = $object->getJITSuppliesSupported();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }

    public function getDevicesForReport ($reportId, $isLeased = 0, $isExcluded = 0)
    {
        $reportId = 10;
        $devices = array ();
        try
        {
            $db = $this->getDbTable();
            $select = $db->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
            $select->joinLeft(array ( 'md' => 'master_device' ), 'device_instance.master_device_id = md.master_device_id', '*')
                ->where('device_instance.is_excluded = ?', $isExcluded)
                ->where('device_instance.report_id = ?', $reportId)
                ->setIntegrityCheck(false)
                ->limit(9000);
            $rows = $db->fetchAll($select);
            if (! $rows)
            {
                throw new Exception('No devices found');
            }
            else
            {
                echo count($rows);
                die();
                $masterDeviceMapper = Proposalgen_Model_Mapper_MasterDevice::getInstance();
                foreach ( $rows as $row )
                {
                    $devices [] = $this->mapRowToObject($row)->setMasterDevice($masterDeviceMapper->mapRowToObject($row));
                }
            }
        }
        catch ( Exception $e )
        {
            throw new Exception("Error fetching devices for report", 0, $e);
        }
        return $devices;
    }
}
?>