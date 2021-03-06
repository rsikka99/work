<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DeviceDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class DeviceDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'devices';
    protected $_primary = [
        'masterDeviceId',
        'dealerId',
    ];

    /**
     * This function allows for searching by a device name, manufacturer, or SKU
     *
     * @param string $searchString
     *
     * @return array
     */
    public function searchByNameOrSku ($searchString)
    {
        $db           = $this->getAdapter();
        $searchString = $db->quoteInto("LIKE ?", "%$searchString%");
        $sql          = "
                SELECT
                    `devices`.`masterDeviceId`,
                    `modelName`,
                    `manufacturers`.`displayname`,
                    `device_configurations`.`name`,
                    `device_configurations`.`id`
                FROM `devices`

                JOIN `master_devices` ON `devices`.`masterDeviceId` = `master_devices`.`id`
                JOIN `manufacturers` ON `master_devices`.`manufacturerId` = `manufacturers`.`id`
                LEFT JOIN `device_configurations` ON `devices`.`masterDeviceId` = `device_configurations`.`masterDeviceId`
                WHERE `fullname` {$searchString}
                OR `displayname` {$searchString}
                OR `printer_model` {$searchString}
                OR `sku` {$searchString}
                ";
        $result       = $db->query($sql);

        if ($result)
        {
            $result = $result->fetchAll();
        }

        return $result;
    }
}