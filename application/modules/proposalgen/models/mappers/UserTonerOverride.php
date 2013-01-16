<?php
class Proposalgen_Model_Mapper_UserTonerOverride extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_UserTonerOverride";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_UserTonerOverride
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
     * @return Proposalgen_Model_UserTonerOverride
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object                        = new Proposalgen_Model_UserTonerOverride();
            $object->setUserId             = $row->user_id;
            $object->setTonerId            = $row->toner_id;
            $object->setOverrideTonerPrice = $row->override_toner_price;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a user toner override row", 0, $e);
        }

        return $object;
    }

    /**
     * @param Proposalgen_Model_UserTonerOverride $object
     *
     * @return string
     * @throws Exception
     */
    public function save ($object)
    {
        try
        {
            $data ["user_id"]              = $object->userId;
            $data ["toner_id"]             = $object->tonerId;
            $data ["override_toner_price"] = $object->overrideTonerPrice;
            $primaryKey                    = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}