<?php
class Proposalgen_Model_Mapper_DeviceInstance extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_DeviceInstance";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_DeviceInstance
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
     * @return \Proposalgen_Model_DeviceInstance
     * @throws Exception
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object                        = new Proposalgen_Model_DeviceInstance();
            $object->id      = $row->id;
            $object->reportId              = $row->report_id;
            $object->masterDeviceId        = $row->master_device_id;
            $object->uploadDataCollectorId = $row->upload_data_collector_id;
            $object->serialNumber          = $row->serial_number;
            $object->mpsMonitorStartDate   = $row->mps_monitor_startdate;
            $object->mpsMonitorEndDate     = $row->mps_monitor_enddate;
            $object->mpsDiscoveryDate      = $row->mps_discovery_date;
            $object->isExcluded            = $row->is_excluded;
            $object->ipAddress             = $row->ip_address;
            $object->jitSuppliesSupported  = $row->jit_supplies_supported;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a device instance row", 0, $e);
        }

        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param Proposalgen_Model_DeviceInstance $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        $primaryKey = 0;
        try
        {
            $data ["id"]                       = $object->id;
            $data ["report_id"]                = $object->reportId;
            $data ["master_device_id"]         = $object->masterDeviceId;
            $data ["upload_data_collector_id"] = $object->uploadDataCollectorId;
            $data ["serial_number"]            = $object->serialNumber;
            $data ["mps_monitor_startdate"]    = $object->mpsMonitorStartDate;
            $data ["mps_monitor_enddate"]      = $object->mpsMonitorEndDate;
            $data ["mps_discovery_date"]       = $object->mpsDiscoveryDate;
            $data ["is_excluded"]              = $object->isExcluded;
            $data ["ip_address"]               = $object->ipAddress;
            $data ["jit_supplies_supported"]   = $object->jitSuppliesSupported;

            $primaryKey = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }

    /**
     * @param     $reportId
     * @param int $isLeased
     * @param int $isExcluded
     *
     * @return Proposalgen_Model_DeviceInstance[]
     * @throws Exception
     */
    public function getDevicesForReport ($reportId, $isLeased = 0, $isExcluded = 0)
    {
        $reportId = 10;
        $devices  = array();
        try
        {
            $db     = $this->getDbTable();
            $select = $db->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
            $select->joinLeft(array(
                                   'md' => 'proposalgenerator_master_devices'
                              ), 'proposalgenerator_device_instances.master_device_id = md.id', '*')
                ->where('proposalgenerator_device_instances.is_excluded = ?', $isExcluded)
                ->where('proposalgenerator_device_instances.report_id = ?', $reportId)
                ->setIntegrityCheck(false)
                ->limit(9000);
            $rows = $db->fetchAll($select);
            if (!$rows)
            {
                throw new Exception('No devices found');
            }
            else
            {
                $masterDeviceMapper = Proposalgen_Model_Mapper_MasterDevice::getInstance();
                foreach ($rows as $row)
                {
                    $devices [] = $this->mapRowToObject($row)->setMasterDevice(new Proposalgen_Model_MasterDevice($row));
                }
            }
        }
        catch (Exception $e)
        {
            throw new Exception("Error fetching devices for report", 0, $e);
        }

        return $devices;
    }
}