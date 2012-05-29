<?php

/*
 * Author: Kevin Jervis
 */

class Emailqueue_Model_DbTable_EmailQueues extends Zend_Db_Table_Abstract
{
    protected $_name = 'email_queues';
    protected $_primary = 'id';
}

?>