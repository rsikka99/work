<?php

class Proposalgen_Model_Mapper_DealerTonerOverride extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_DealerTonerOverride";
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
            $object = new Proposalgen_Model_DealerTonerOverride();
            $object->setDealerCompanyId($row->dealer_company_id)
                ->setTonerId($row->toner_id)
                ->setOverrideTonerPrice($row->override_toner_price);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a dealer toner override row", 0, $e);
        }
        return $object;
    }

    public function save (Proposalgen_Model_DealerTonerOverride $object)
    {
        $primaryKey = false;
        try
        {
            $data ["dealer_company_id"] = $object->getDealerCompanyId();
            $data ["toner_id"] = $object->getTonerId();
            $data ["override_toner_price"] = $object->getOverrideTonerPrice();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }
}
?>