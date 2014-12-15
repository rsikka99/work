<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class RmsUploadRowDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class RmsUploadRowDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'rms_upload_rows';
    protected $_primary = 'id';
}