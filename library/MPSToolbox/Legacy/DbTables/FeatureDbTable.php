<?php

namespace MPSToolbox\Legacy\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class FeatureDbTable
 *
 * @package MPSToolbox\Legacy\DbTables
 */
class FeatureDbTable extends Zend_Db_Table_Abstract
{
    protected $_primary = "id";
    protected $_name    = "features";
}