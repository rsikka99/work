<?php

use Phinx\Migration\AbstractMigration;

class BaseProduct extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {

        $this->execute('SET FOREIGN_KEY_CHECKS=0');

        $this->execute('drop table IF EXISTS base_printer_cartridge');
        $this->execute('drop table IF EXISTS base_printer_consumable');
        $this->execute('drop table IF EXISTS base_printer');
        $this->execute('drop table IF EXISTS base_printing_device');
        $this->execute('drop table IF EXISTS base_product');
        $this->execute('drop view IF EXISTS master_devices');
        $this->execute('drop view IF EXISTS toners');
        try {
            $this->execute("RENAME TABLE __master_devices TO master_devices");
            $this->execute("RENAME TABLE __toners TO toners");
        } catch (Exception $ex) {
            //noop
        }

        $this->execute(
"
create table IF NOT EXISTS base_product (
   id int not null AUTO_INCREMENT,
   base_type enum('printer','printer_consumable','printer_cartridge','sku') not null,
   userId int not null,
   manufacturerId int not null,
   dateCreated date null default null,
   sku varchar(255) null default null,
   name varchar(255) not null,
   isSystemProduct tinyint not null default 0,
   imageFile varchar(255) null default null,
   imageUrl varchar(255) null default null,
   weight decimal(10,2) null default null,
   UPC varchar(16) null default null,
   PRIMARY KEY (`id`),
   KEY ( `userId` ),
   KEY ( `manufacturerId` ),
   KEY ( `sku` ),
   KEY ( `name` ),
   CONSTRAINT FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
   CONSTRAINT FOREIGN KEY (`manufacturerId`) REFERENCES `manufacturers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
");
        $this->execute("
create table base_printing_device (
  id INT NOT NULL,
  productLine varchar(255) null default null,
  PRIMARY KEY (`id`),
  CONSTRAINT FOREIGN KEY (`id`) REFERENCES `base_product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
");
        $this->execute("
create table base_printer (
  id int not null,
  tech enum('Laser','Ink','LED','Other') null default null,
  tonerConfigId int not null default 0,
  launchDate date null default null,
  isCopier tinyint not null default 0,
  isDuplex tinyint not null default 0,
  isFax tinyint not null default 0,
  isReplacementDevice tinyint not null default 0,
  isCapableOfReportingTonerLevels tinyint not null default 0,
  isA3 tinyint not null default 0,
  isSmartphone tinyint not null default 0,
  additionalTrays tinyint not null default 0,
  isPIN tinyint not null default 0,
  isAccessCard tinyint not null default 0,
  isWalkup tinyint not null default 0,
  isStapling tinyint not null default 0,
  isBinding tinyint not null default 0,
  isTouchscreen tinyint not null default 0,
  isADF tinyint not null default 0,
  isUSB tinyint not null default 0,
  isWired tinyint not null default 0,
  isWireless tinyint not null default 0,
  maximumRecommendedMonthlyPageVolume int not null default 0,
  wattsPowerNormal int not null default 0,
  wattsPowerIdle int not null default 0,
  ppmBlack int not null default 0,
  ppmColor int not null default 0,

  PRIMARY KEY (`id`),
  CONSTRAINT FOREIGN KEY (`id`) REFERENCES `base_printing_device` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`tonerConfigId`) REFERENCES `toner_configs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB

");

        $this->execute("
create table base_printer_consumable (
  id int not null,
  cost decimal(10,2) not null default '0.0',
  pageYield int not null default 0,
  quantity int not null default 1,
  type enum('Inkjet Cartridge','Laser Cartridge','Other Cartridge','Drum Kit','Print Head','Monochrome Toner','Maintenance','Color Toner','Cleaning') null default null,
  compatiblePrinters text null,
  PRIMARY KEY (`id`),
  CONSTRAINT FOREIGN KEY (`id`) REFERENCES `base_product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
");

        $this->execute("
create table compatible_printer_consumable (
  oem int not null,
  compatible int not null,
  KEY (`oem`),
  KEY (`compatible`),
  CONSTRAINT FOREIGN KEY (`oem`) REFERENCES `base_printer_consumable` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`compatible`) REFERENCES `base_printer_consumable` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
");

        $this->execute("
create table base_printer_cartridge (
  id int not null,
  colorId int not null,
  mlYield int null default null,
  PRIMARY KEY (`id`),
  KEY (`colorId`),
  CONSTRAINT FOREIGN KEY (`id`) REFERENCES `base_printer_consumable` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`colorId`) REFERENCES `toner_colors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
");

#--
        $this->execute("
insert into base_product(base_type,id,userId,manufacturerId,dateCreated,sku,name,isSystemProduct,imageFile,imageUrl,weight,UPC)
select 'printer_cartridge',id, userId, manufacturerId, now(), sku, name, isSystemDevice, imageFile, imageUrl, weight, UPC from toners
");
        $this->execute("
insert into base_printer_consumable (id, cost, pageYield, `type`)
select id, cost, yield, 'Monochrome Toner' from toners where tonerColorId=1
");
        $this->execute("
insert into base_printer_consumable (id, cost, pageYield, `type`)
select id, cost, yield, 'Color Toner' from toners where tonerColorId>1
");
        $this->execute("
insert into base_printer_cartridge (id, colorId)
select id, tonerColorId from toners
");

        $this->execute("
insert into base_product(base_type,id,userId,manufacturerId,dateCreated,sku,name,isSystemProduct,imageFile,imageUrl,weight,UPC)
select 'printer',id+10000,userId,manufacturerId,dateCreated,'',modelName,isSystemDevice,imageFile,imageUrl,weight,UPC from master_devices
");
        $this->execute("
insert into base_printing_device(id)
select id+10000 from master_devices
");
        $this->execute("
insert into base_printer(id,tonerConfigId,launchDate,isCopier,isDuplex,isFax,isReplacementDevice,isCapableOfReportingTonerLevels,isA3,isSmartphone,additionalTrays,isPIN,isAccessCard,isWalkup,isStapling,isBinding,isTouchscreen,isADF,isUSB,isWired,isWireless,maximumRecommendedMonthlyPageVolume,wattsPowerNormal,wattsPowerIdle,ppmBlack,ppmColor)
select id+10000,tonerConfigId,launchDate,isCopier,isDuplex,isFax,isReplacementDevice,isCapableOfReportingTonerLevels,isA3,isSmartphone,additionalTrays,isPIN,isAccessCard,isWalkup,isStapling,isBinding,isTouchscreen,isADF,isUSB,isWired,isWireless,maximumRecommendedMonthlyPageVolume,wattsPowerNormal,wattsPowerIdle,ppmBlack,ppmColor from master_devices
");


#--
$this->execute("ALTER TABLE `client_toner_orders`
  drop FOREIGN KEY `client_toner_attributes_ibfk_1`,
  drop FOREIGN KEY `client_toner_attributes_ibfk_3`");
$this->execute("ALTER TABLE `client_toner_orders`
  ADD CONSTRAINT `client_toner_attributes_ibfk_1` FOREIGN KEY (`tonerId`) REFERENCES `base_printer_consumable` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `client_toner_attributes_ibfk_3` FOREIGN KEY (`replacementTonerId`) REFERENCES `base_printer_consumable` (`id`) ON DELETE SET NULL ON UPDATE CASCADE");

$this->execute("ALTER TABLE `dealer_toner_attributes`
  drop FOREIGN KEY `dealer_toner_attributes_ibfk_1`");
$this->execute("ALTER TABLE `dealer_toner_attributes`
  ADD CONSTRAINT `dealer_toner_attributes_ibfk_1` FOREIGN KEY (`tonerId`) REFERENCES `base_printer_consumable` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");

$this->execute("ALTER TABLE `device_needs_toner`
  drop FOREIGN KEY `device_needs_toner_ibfk_4`");
$this->execute("ALTER TABLE `device_needs_toner`
  ADD CONSTRAINT `device_needs_toner_ibfk_4` FOREIGN KEY (`toner`) REFERENCES `base_printer_consumable` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");

$this->execute("ALTER TABLE `device_toners`
  drop FOREIGN KEY `device_toners_ibfk_4`");
$this->execute("ALTER TABLE `device_toners`
  ADD CONSTRAINT `device_toners_ibfk_4` FOREIGN KEY (`toner_id`) REFERENCES `base_printer_consumable` (`id`)");

$this->execute("ALTER TABLE `history`
  drop FOREIGN KEY `history_ibfk_3`");
$this->execute("ALTER TABLE `history`
  ADD CONSTRAINT `history_ibfk_3` FOREIGN KEY (`tonerId`) REFERENCES `base_printer_consumable` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");

$this->execute("ALTER TABLE `dealer_master_device_attributes`
  DROP FOREIGN KEY `dealer_master_device_attributes_ibfk_2`");
$this->execute("update `dealer_master_device_attributes` set masterDeviceId = masterDeviceId+10000");
$this->execute("ALTER TABLE `dealer_master_device_attributes`
  ADD CONSTRAINT `dealer_master_device_attributes_ibfk_2` FOREIGN KEY (`masterDeviceId`) REFERENCES `base_printing_device` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");

$this->execute("ALTER TABLE `devices`
  DROP FOREIGN KEY `devices_ibfk_1`");
$this->execute("update `devices` set masterDeviceId = masterDeviceId+10000");
$this->execute("ALTER TABLE `devices`
  ADD CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`masterDeviceId`) REFERENCES `base_printing_device` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");

$this->execute("ALTER TABLE `device_instance_master_devices`
  DROP FOREIGN KEY `device_instance_master_devices_ibfk_2`");
$this->execute("update `device_instance_master_devices` set masterDeviceId = masterDeviceId+10000");
$this->execute("ALTER TABLE `device_instance_master_devices`
  ADD CONSTRAINT `device_instance_master_devices_ibfk_2` FOREIGN KEY (`masterDeviceId`) REFERENCES `base_printing_device` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");

$this->execute("ALTER TABLE `device_instance_replacement_master_devices`
  DROP FOREIGN KEY `device_instance_replacement_master_devices_ibfk_1`");
$this->execute("update `device_instance_replacement_master_devices` set masterDeviceId = masterDeviceId+10000");
$this->execute("ALTER TABLE `device_instance_replacement_master_devices`
  ADD CONSTRAINT `device_instance_replacement_master_devices_ibfk_1` FOREIGN KEY (`masterDeviceId`) REFERENCES `base_printing_device` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");

$this->execute("ALTER TABLE `device_needs_toner`
  DROP FOREIGN KEY `device_needs_toner_ibfk_3`");
$this->execute("update `device_needs_toner` set masterDeviceId = masterDeviceId+10000");
$this->execute("ALTER TABLE `device_needs_toner`
  ADD CONSTRAINT `device_needs_toner_ibfk_3` FOREIGN KEY (`masterDeviceId`) REFERENCES `base_printing_device` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");

$this->execute("ALTER TABLE `device_swaps`
  DROP FOREIGN KEY `device_swaps_ibkf1`");
$this->execute("update `device_swaps` set masterDeviceId = masterDeviceId+10000");
$this->execute("ALTER TABLE `device_swaps`
  ADD CONSTRAINT `device_swaps_ibkf1` FOREIGN KEY (`masterDeviceId`) REFERENCES `base_printing_device` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");

$this->execute("ALTER TABLE `device_toners`
  DROP FOREIGN KEY `device_toners_ibfk_5`");
$this->execute("update `device_toners` set master_device_id = master_device_id+10000");
$this->execute("ALTER TABLE `device_toners`
  ADD CONSTRAINT `device_toners_ibfk_5` FOREIGN KEY (`master_device_id`) REFERENCES `base_printing_device` (`id`)");

$this->execute("ALTER TABLE `hardware_optimization_device_instances`
  DROP FOREIGN KEY `hardware_optimization_device_instances_ibfk_3`");
$this->execute("update `hardware_optimization_device_instances` set masterDeviceId = masterDeviceId+10000");
$this->execute("ALTER TABLE `hardware_optimization_device_instances`
  ADD CONSTRAINT `hardware_optimization_device_instances_ibfk_3` FOREIGN KEY (`masterDeviceId`) REFERENCES `base_printing_device` (`id`) ON DELETE SET NULL ON UPDATE CASCADE");

$this->execute("ALTER TABLE `history`
  DROP FOREIGN KEY `history_ibfk_2`");
$this->execute("update `history` set masterDeviceId = masterDeviceId+10000");
$this->execute("ALTER TABLE `history`
  ADD CONSTRAINT `history_ibfk_2` FOREIGN KEY (`masterDeviceId`) REFERENCES `base_printing_device` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");

$this->execute("ALTER TABLE `jit_compatible_master_devices`
  DROP FOREIGN KEY `jit_compatible_master_devices_ibfk_2`");
$this->execute("update `jit_compatible_master_devices` set masterDeviceId = masterDeviceId+10000");
$this->execute("ALTER TABLE `jit_compatible_master_devices`
  ADD CONSTRAINT `jit_compatible_master_devices_ibfk_2` FOREIGN KEY (`masterDeviceId`) REFERENCES `base_printing_device` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");

$this->execute("ALTER TABLE `master_device_service`
  DROP FOREIGN KEY `ingram_service_ibfk_1`");
$this->execute("update `master_device_service` set masterDeviceId = masterDeviceId+10000");
$this->execute("ALTER TABLE `master_device_service`
  ADD CONSTRAINT `ingram_service_ibfk_1` FOREIGN KEY (`masterDeviceId`) REFERENCES `base_printing_device` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");

$this->execute("ALTER TABLE `rms_device_instances`
  DROP FOREIGN KEY `rms_device_instances_ibfk_2`");
$this->execute("update `rms_device_instances` set masterDeviceId = masterDeviceId+10000");
$this->execute("ALTER TABLE `rms_device_instances`
  ADD CONSTRAINT `rms_device_instances_ibfk_2` FOREIGN KEY (`masterDeviceId`) REFERENCES `base_printing_device` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");

$this->execute("ALTER TABLE `rms_device_matchups`
  DROP FOREIGN KEY `rms_device_matchups_ibfk2`");
$this->execute("update `rms_device_matchups` set masterDeviceId = masterDeviceId+10000");
$this->execute("ALTER TABLE `rms_device_matchups`
  ADD CONSTRAINT `rms_device_matchups_ibfk2` FOREIGN KEY (`masterDeviceId`) REFERENCES `base_printing_device` (`id`) ON DELETE SET NULL ON UPDATE CASCADE");

$this->execute("ALTER TABLE `rms_master_matchups`
  DROP FOREIGN KEY `rms_master_matchups_ibfk_1`");
$this->execute("update `rms_master_matchups` set masterDeviceId = masterDeviceId+10000");
$this->execute("ALTER TABLE `rms_master_matchups`
  ADD CONSTRAINT `rms_master_matchups_ibfk_1` FOREIGN KEY (`masterDeviceId`) REFERENCES `base_printing_device` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");

$this->execute("ALTER TABLE `rms_user_matchups`
  DROP FOREIGN KEY `rms_user_matchups_ibfk_1`");
$this->execute("update `rms_user_matchups` set masterDeviceId = masterDeviceId+10000");
$this->execute("ALTER TABLE `rms_user_matchups`
  ADD CONSTRAINT `rms_user_matchups_ibfk_1` FOREIGN KEY (`masterDeviceId`) REFERENCES `base_printing_device` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");


        $this->execute("RENAME TABLE master_devices TO __master_devices");
        $this->execute("create view master_devices as select base_product.`id`,`dateCreated`,`isCopier`,`isDuplex`,`isFax`,`isReplacementDevice`,`launchDate`,`manufacturerId`,`name` as `modelName`,`ppmBlack`,`ppmColor`,`tonerConfigId`,`wattsPowerNormal`,`wattsPowerIdle`,`isCapableOfReportingTonerLevels`,`userId`,`isSystemProduct` as `isSystemDevice`,`isA3`,`maximumRecommendedMonthlyPageVolume`,`imageFile`,`imageUrl`,`isSmartphone`,`additionalTrays`,`isPIN`,`isAccessCard`,`isWalkup`,`isStapling`,`isBinding`,`isTouchscreen`,`isADF`,`isUSB`,`isWired`,`isWireless`,`weight`,`UPC` from base_product join base_printing_device on base_printing_device.id=base_product.id join base_printer on base_printer.id=base_printing_device.id");

        $this->execute("RENAME TABLE toners TO __toners");
        $this->execute("create view toners as select base_product.`id`,`sku`,`cost`,`pageYield` as `yield`,`manufacturerId`,`colorId` as `tonerColorId`,`userId`,`isSystemProduct` as `isSystemDevice`,`imageFile`,`imageUrl`,`name`,`weight`,`UPC` from base_product join base_printer_consumable on base_printer_consumable.id=base_product.id join base_printer_cartridge on base_printer_cartridge.id=base_printer_consumable.id");

#--

        $this->execute('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $this->execute('drop table IF EXISTS base_printer_cartridge');
        $this->execute('drop table IF EXISTS base_printer_consumable');
        $this->execute('drop table IF EXISTS base_printer');
        $this->execute('drop table IF EXISTS base_printing_device');
        $this->execute('drop table IF EXISTS base_product');
        $this->execute('drop view master_devices');
        $this->execute('drop view toners');
        $this->execute('SET FOREIGN_KEY_CHECKS=1');
    }
}