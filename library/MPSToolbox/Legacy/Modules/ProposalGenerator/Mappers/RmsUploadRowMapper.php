<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadRowModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class RmsUploadRowMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class RmsUploadRowMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_id            = 'id';
    public $col_userId        = 'userId';
    public $col_rmsProviderId = 'rmsProviderId';
    public $col_rmsModelId    = 'rmsModelId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\RmsUploadRowDbTable';


    /**
     * An array of cached master devices that have been created from upload rows
     *
     * @var MasterDeviceModel[]
     */
    protected $_convertedMasterDeviceCache = [];

    /**
     * Gets an instance of the mapper
     *
     * @return RmsUploadRowMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadRowModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object RmsUploadRowModel
     *                The object to insert
     *
     * @return int The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $this->unsetNullValues($object->toArray());

        // Remove the id
        unset($data ["{$this->col_id}"]);

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        $object->id = $id;

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadRowModel to the database.
     *
     * @param $object     RmsUploadRowModel
     *                    The Rms_Upload_Row model to save to the database
     * @param $primaryKey mixed
     *                    Optional: The original primary key, in case we're changing it
     *
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());

        if ($primaryKey === null)
        {
            $primaryKey = $data [$this->col_id];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_id} = ?" => $primaryKey,
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadRowModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof RmsUploadRowModel)
        {
            $whereClause = [
                "{$this->col_id} = ?" => $object->id,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_id} = ?" => $object,
            ];
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a Rms_Upload_Row based on it's primaryKey
     *
     * @param $id int
     *            The id of the Rms_Upload_Row to find
     *
     * @return RmsUploadRowModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof RmsUploadRowModel)
        {
            return $result;
        }

        // Assuming we don't have a cached object, lets go get it.
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new RmsUploadRowModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a Rms_Upload_Row
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return RmsUploadRowModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new RmsUploadRowModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all Rms_Upload_Rows
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $count  int
     *                OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *                OPTIONAL An SQL LIMIT offset.
     *
     * @return RmsUploadRowModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new RmsUploadRowModel($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param int $id
     *
     * @return array
     */
    public function getWhereId ($id)
    {
        return [
            "{$this->col_id} = ?" => $id,
        ];
    }

    /**
     * @param RmsUploadRowModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }


    /**
     * Deletes all rows related to a report
     *
     * @param int $rmsUploadId The report id
     *
     * @return int The number of rows  deleted
     */
    public function deleteAllForRmsUpload ($rmsUploadId)
    {
        $deviceInstanceMapper    = DeviceInstanceMapper::getInstance();
        $rmsUploadRowTableName   = $this->getTableName();
        $deviceInstanceTableName = $deviceInstanceMapper->getTableName();

        $sql   = "
            DELETE {$rmsUploadRowTableName} FROM {$rmsUploadRowTableName}
            INNER JOIN {$deviceInstanceTableName} ON {$deviceInstanceTableName}.{$deviceInstanceMapper->col_rmsUploadRowId} = {$rmsUploadRowTableName}.{$this->col_id}
            WHERE {$deviceInstanceTableName}.{$deviceInstanceMapper->col_rmsUploadId} = ?;
        ";
        $query = $this->getDbTable()->getAdapter()->query($sql, $rmsUploadId);

        return $query->execute();
    }


    /**
     * Converts an upload row into a master device
     *
     * @param RmsUploadRowModel $rmsUploadRow
     *
     * @return bool|MasterDeviceModel Returns a master device, or false if the RMS upload row does not have complete information
     */
    public function convertUploadRowToMasterDevice ($rmsUploadRow)
    {
        if (!array_key_exists($rmsUploadRow->id, $this->_convertedMasterDeviceCache))
        {
            $masterDevice = false;
            if ($rmsUploadRow->hasCompleteInformation)
            {
                $masterDevice = new MasterDeviceModel();

                $masterDevice->populate($rmsUploadRow->toArray());

                // Unset the id for the master device to ensure that this device is a 'user mapped' master device
                $masterDevice->id                         = null;
                $toners                                   = [];

                $requiredTonerColorList = TonerConfigModel::getRequiredTonersForTonerConfig($rmsUploadRow->tonerConfigId);

                foreach ($requiredTonerColorList as $tonerColorName => $tonerColorId)
                {
                    $toner = $this->createTonerFromRmsUploadRow($rmsUploadRow->{"oem{$tonerColorName}TonerSku"}, $rmsUploadRow->{"oem{$tonerColorName}TonerYield"}, $rmsUploadRow->{"oem{$tonerColorName}TonerCost"}, $tonerColorId);
                    if ($toner !== false)
                    {
                        $toners[$masterDevice->manufacturerId][$tonerColorId][] = $toner;
                    }

                    $toner = $this->createTonerFromRmsUploadRow($rmsUploadRow->{"comp{$tonerColorName}TonerSku"}, $rmsUploadRow->{"comp{$tonerColorName}TonerYield"}, $rmsUploadRow->{"comp{$tonerColorName}TonerCost"}, $tonerColorId);
                    if ($toner !== false)
                    {
                        $toners[0][$tonerColorId][] = $toner;
                    }
                }
                $masterDevice->setToners($toners);
            }
            $this->_convertedMasterDeviceCache[$rmsUploadRow->id] = $masterDevice;
        }

        return $this->_convertedMasterDeviceCache[$rmsUploadRow->id];
    }

    /**
     * Creates a toner or returns false when invalid data is passed in.
     *
     * @param string $tonerSku
     * @param int    $tonerYield
     * @param float  $tonerCost
     * @param int    $tonerColor
     *
     * @return TonerModel|bool Returns false when the data is invalid for a toner
     */
    public function createTonerFromRmsUploadRow ($tonerSku, $tonerYield, $tonerCost, $tonerColor)
    {
        $toner = false;
        if (strlen($tonerSku) > 0 && $tonerYield > 0 && $tonerCost > 0)
        {
            $toner               = new TonerModel();
            $toner->sku          = $tonerSku;
            $toner->yield        = $tonerYield;
            $toner->cost         = $tonerCost;
            $toner->tonerColorId = $tonerColor;
        }


        return $toner;
    }


    /**
     * Fetches an array with the key as the full device name and the value as the id of the RMS upload rows.
     *
     * @return array
     */
    public function fetchRmsUploadRowArray ()
    {
        $results       = [];
        $rmsUploadRows = $this->fetchAll();
        foreach ($rmsUploadRows as $rmsUploadRow)
        {
            $results[$rmsUploadRow->fullDeviceName] = $rmsUploadRow->id;
        }

        return $results;

    }
}