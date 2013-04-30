<?php

/**
 * Class Quotegen_Model_DbTable_UserViewedClient
 */
class Quotegen_Model_DbTable_UserViewedClient extends Zend_Db_Table_Abstract
{
    protected $_name = 'user_viewed_clients';
    protected $_primary = array(
        'userId',
        'clientId'
    );
}