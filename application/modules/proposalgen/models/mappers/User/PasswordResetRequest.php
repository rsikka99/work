<?php

class Proposalgen_Model_Mapper_User_PasswordResetRequest extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_User_PasswordResetRequests";
    static $_instance;

    /**
     *
     * @return Proposalgen_Model_Mapper_User_PasswordResetRequest
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
     * @return Proposalgen_Model_Mapper_User_PasswordResetRequest
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object = new Proposalgen_Model_User_PasswordResetRequest();
            $dateRequested = new DateTime($row->date_requested);
            $object->setId($row->id)
                ->setDateRequested($dateRequested->getTimestamp())
                ->setResetToken($row->reset_token)
                ->setIpAddress($row->ip_address)
                ->setResetVerified($row->reset_verified)
                ->setUserId($row->user_id)
                ->setResetUsed($row->reset_used);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a user password reset request row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     * 
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_User_PasswordResetRequest $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["id"] = $object->getId();
            $data ["date_requested"] = date('Y-m-d H:i:s', $object->getDateRequested());
            $data ["reset_token"] = $object->getResetToken();
            $data ["ip_address"] = $object->getIpAddress();
            $data ["reset_verified"] = $object->getResetVerified();
            $data ["user_id"] = $object->getUserId();
            $data ["reset_used"] = $object->getResetUsed();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }
}