<?php

/**
 * Class Quotegen_Model_DbTable_ContractSection
 */
class Quotegen_Model_DbTable_ContractTemplateSection extends Zend_Db_Table_Abstract
{
    protected $_name = 'contract_template_sections';
    protected $_primary = array(
        'contractTemplateId',
        'contractSectionId'
    );
}