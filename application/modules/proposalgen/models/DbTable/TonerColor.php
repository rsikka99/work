<?php

class Proposalgen_Model_DbTable_TonerColor extends Zend_Db_Table_Abstract
{
    protected $_name = 'proposalgenerator_toner_colors';
    protected $_primary = 'id';
    protected $_dependentTables = array (
            'Toner' 
    );
}

