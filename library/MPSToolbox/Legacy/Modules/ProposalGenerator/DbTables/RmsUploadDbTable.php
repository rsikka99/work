<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class RmsUploadDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class RmsUploadDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'rms_uploads';
    protected $_primary = 'id';
}