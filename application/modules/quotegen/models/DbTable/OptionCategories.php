<?php

class Quotegen_Model_DbTable_OptionCategories extends Zend_Db_Table_Abstract
{
    protected $_name = 'quotegen_option_categories';
    protected $_primary = array (
            'categoryId', 
            'optionId' 
    );  
}