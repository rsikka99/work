<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DeviceConfigurationDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class DeviceConfigurationDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'device_configurations';
    protected $_primary = 'id';
}