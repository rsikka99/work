<?php

class Application_Model_DbTable_Session extends Zend_Db_Table_Abstract
{
    protected $_name = 'sessions';
    protected $_primary = 'id';

    /**
     * Deletes a session by it's id.
     *
     * @param $sessionId
     *
     * @return int
     */
    public function deleteSession ($sessionId)
    {
        return $this->delete(array("{$this->_primary} = ?" => $sessionId));
    }
}