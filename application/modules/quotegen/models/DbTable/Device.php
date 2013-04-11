<?php

class Quotegen_Model_DbTable_Device extends Zend_Db_Table_Abstract
{
    protected $_name = 'qgen_devices';
    protected $_primary = array(
        'masterDeviceId',
        'dealerId'
    );

    /**
     * This function allows for searching by a device name, manufacturer, or sku
     *
     * @param unknown_type $searchString
     *
     * @return Ambigous <Zend_Db_Statement_Interface, Zend_Db_Statement, PDOStatement>
     */
    public function searchByNameOrSku ($searchString)
    {
        $db           = $this->getAdapter();
        $searchString = $db->quoteInto("LIKE ?", "%$searchString%");
        $sql          = "
                SELECT  
                    `qgen_devices`.`masterDeviceId`,
                    `printer_model`, 
                    `manufacturers`.`displayname`,
                    `qgen_device_configurations`.`name`,
                    `qgen_device_configurations`.`id`
                FROM `qgen_devices` 
                
                JOIN `pgen_master_devices` ON `qgen_devices`.`masterDeviceId` = `pgen_master_devices`.`id`
                JOIN `manufacturers` ON `pgen_master_devices`.`manufacturer_id` = `manufacturers`.`id`
                LEFT JOIN `qgen_device_configurations` ON `qgen_devices`.`masterDeviceId` = `qgen_device_configurations`.`masterDeviceId`
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