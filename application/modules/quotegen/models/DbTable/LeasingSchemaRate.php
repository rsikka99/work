<?php

class Quotegen_Model_DbTable_LeasingSchemaRate extends Zend_Db_Table_Abstract
{
    protected $_name = 'leasing_schema_rates';
    protected $_primary = array(
        'leasingSchemaTermId',
        'leasingSchemaRangeId'
    );
}