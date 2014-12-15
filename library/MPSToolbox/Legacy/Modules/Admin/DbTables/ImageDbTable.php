<?php

namespace MPSToolbox\Legacy\Modules\Admin\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class ImageDbTable
 *
 * @package MPSToolbox\Legacy\Modules\Admin\DbTables
 */
class ImageDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'images';
    protected $_primary = 'id';
}