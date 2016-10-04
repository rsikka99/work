<?php

use Phinx\Migration\AbstractMigration;

class MasterDeviceOemSku extends AbstractMigration
{
    public function up()
    {
        $this->query('drop view master_devices');
        $this->query('create view master_devices as select base_product.`id`,`sku`,`dateCreated`,`isCopier`,`isDuplex`,`isFax`,`isReplacementDevice`,`launchDate`,`manufacturerId`,`name` as `modelName`,`ppmBlack`,`ppmColor`,`tonerConfigId`,`wattsPowerNormal`,`wattsPowerIdle`,`isCapableOfReportingTonerLevels`,`userId`,`isSystemProduct` as `isSystemDevice`,`isA3`,`maximumRecommendedMonthlyPageVolume`,`imageFile`,`imageUrl`,`isSmartphone`,`additionalTrays`,`isPIN`,`isAccessCard`,`isWalkup`,`isStapling`,`isBinding`,`isFolding`,`isFloorStanding`,`isTouchscreen`,`isADF`,`isUSB`,`isWired`,`isWireless`,`weight`,`UPC` from base_product join base_printing_device on base_printing_device.id=base_product.id join base_printer on base_printer.id=base_printing_device.id');

        $this->query("update base_product p set sku=null where sku=''");
        $this->query("update base_product p set sku=(select oemSku from devices where masterDeviceId=p.id limit 1) where sku is null and base_type='printer'");

        $this->query("ALTER TABLE `devices` CHANGE `oemSku` `oemSku` VARCHAR(255) null");
    }
    public function down()
    {

    }
}