<?php
class Proposalgen_Model_Mapper_DealerMasterDeviceAttributes extends Tangent_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_id = 'id';
    public $col_dealerId = 'dealerId';
    public $col_cost = 'cost';
    public $col_partsCostPerPage = 'partsCostPerPage';
    public $col_laborCostPerPage = 'laborCostPerPage';
    public $col_dealerSku = 'dealerSku';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_DealerMasterDeviceAttributes';

    /**
     * @return Proposalgen_Model_Mapper_DealerMasterDeviceAttributes
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

    static $_DealerMasterDeviceAttributess = array();

    /**
     * @param int $id
     *
     * @return Proposalgen_Model_DealerMasterDeviceAttributes
     */
    public function find ($id)
    {
        if (!array_key_exists($id, self::$_DealerMasterDeviceAttributess))
        {
            self::$_DealerMasterDeviceAttributess [$id] = parent::find($id);
        }

        return self::$_DealerMasterDeviceAttributess [$id];
    }

    /**
     * Maps a database row object to an Proposalgen_Model
     *
     * @param Zend_Db_Table_Row $row
     *
     * @throws Exception
     * @return Proposalgen_Model_DealerMasterDeviceAttributes
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object                  = new Proposalgen_Model_DealerMasterDeviceAttributes();
            $object->masterDeviceId   = $row->masterDeviceId;
            $object->dealerId = $row->dealerId;
            $object->cost = $row->cost;
            $object->partsCostPerPage = $row->partsCostPerPage;
            $object->laborCostPerPage = $row->laborCostPerPage;
            $object->dealerSku = $row->dealerSku;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a dealer master device attributes row", 0, $e);
        }

        return $object;
    }

    /**
     * Saves an instance of Quotegen_Model_DealerMasterDeviceAttributes to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Quotegen_Model_DealerMasterDeviceAttributes
     *                The object to insert
     *
     * @return int The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $object->toArray();

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param Proposalgen_Model_DealerMasterDeviceAttributes $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        try
        {
            $data ["masterDeviceId"]   = $object->masterDeviceId;
            $data ["dealerId"] = $object->dealerId;
            $data ["cost"] = $object->cost;
            $data ["partsCostPerPage"] = $object->partsCostPerPage;
            $data ["laborCostPerPage"] = $object->laborCostPerPage;
            $data ["dealerSku"] = $object->dealerSku;
            $primaryKey    = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}