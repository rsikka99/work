<?php

/**
 * Class Quotegen_Model_DbTable_OptionCategory
 */
class Quotegen_Model_DbTable_OptionCategory extends Zend_Db_Table_Abstract
{
    protected $_name = 'option_categories';
    protected $_primary = array(
        'categoryId',
        'optionId'
    );
}