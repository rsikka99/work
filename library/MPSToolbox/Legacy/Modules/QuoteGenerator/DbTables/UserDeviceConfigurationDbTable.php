<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class UserDeviceConfigurationMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class UserDeviceConfigurationDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'user_device_configurations';
    protected $_primary = [
        'deviceConfigurationId',
        'userId',
    ];
}