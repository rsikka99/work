<?php

use Phinx\Migration\AbstractMigration;

class MasterDevicesTech extends AbstractMigration
{
    public function up()
    {
        $this->query('drop view master_devices');
        $this->query('create view master_devices as select base_product.`id`,`sku`,`dateCreated`,`isCopier`,`isDuplex`,`isFax`,`isReplacementDevice`,`launchDate`,`manufacturerId`,`name` as `modelName`,`ppmBlack`,`ppmColor`,`tonerConfigId`,`wattsPowerNormal`,`wattsPowerIdle`,`isCapableOfReportingTonerLevels`,`userId`,`isSystemProduct` as `isSystemDevice`,`isA3`,`maximumRecommendedMonthlyPageVolume`,`imageFile`,`imageUrl`,`isSmartphone`,`additionalTrays`,`isPIN`,`isAccessCard`,`isWalkup`,`isStapling`,`isBinding`,`isFolding`,`isFloorStanding`,`isTouchscreen`,`isADF`,`isUSB`,`isWired`,`isWireless`,`weight`,`UPC`,`tech` from base_product join base_printing_device on base_printing_device.id=base_product.id join base_printer on base_printer.id=base_printing_device.id');
    }

    public function down()
    {

    }
}