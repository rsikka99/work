<?php

class Proposalgen_Model_DbTable_PartType extends Zend_Db_Table_Abstract
{
    protected $_name = 'proposalgenerator_part_types';
    protected $_primary = 'id';
    protected $_dependentTables = array (
            'PricingConfig' 
    );
}