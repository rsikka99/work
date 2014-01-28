<?php
/**
 * Class Proposalgen_Model_DbTable_Client_Toner_Attribute
 */
class Proposalgen_Model_DbTable_Client_Toner_Attribute extends Zend_Db_Table_Abstract
{
    protected $_name = 'client_toner_attributes';
    protected $_primary = array(
        'tonerId',
        'clientId'
    );

}