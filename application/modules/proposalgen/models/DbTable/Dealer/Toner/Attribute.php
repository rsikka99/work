<?php
/**
 * Class Proposalgen_Model_DbTable_Dealer_Toner_Attribute
 */
class Proposalgen_Model_DbTable_Dealer_Toner_Attribute extends Zend_Db_Table_Abstract
{
    protected $_name = 'dealer_toner_attributes';
    protected $_primary = array(
        'tonerId',
        'dealerId'
    );

}