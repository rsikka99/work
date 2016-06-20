<?php

use Phinx\Migration\AbstractMigration;

class BaseProductFix extends AbstractMigration
{
    public function up()
    {

        try {
            $this->execute("drop view if exists device_toners");
            $this->execute("RENAME TABLE __device_toners TO device_toners");
        } catch (Exception $ex) {
            //noop
        }

        $this->execute("drop view toners");
        $this->execute("create view toners as select base_product.`id`,`sku`,`cost`,`pageYield` as `yield`,`manufacturerId`,`colorId` as `tonerColorId`,`userId`,`isSystemProduct` as `isSystemDevice`,`imageFile`,`imageUrl`,`name`,`weight`,`UPC` from base_product join base_printer_consumable on base_printer_consumable.id=base_product.id join base_printer_cartridge on base_printer_cartridge.id=base_printer_consumable.id where quantity=1");

        $this->execute("drop table if exists oem_printing_device_consumable");
        $this->execute(
"
create table oem_printing_device_consumable (
  `printing_device` int not null,
  `printer_consumable` int not null,
  `userId` int not null,
  `isApproved` tinyint not null default 0,
  PRIMARY KEY (printing_device, printer_consumable),
  KEY ( `userId` ),
  CONSTRAINT FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
");

        $this->execute(
'
insert into oem_printing_device_consumable
select md.id, t.id, dt.userId, dt.isSystemDevice from device_toners dt join master_devices md on md.id=dt.master_device_id join toners t on t.id=dt.toner_id where t.manufacturerId=md.manufacturerId
');

        $this->execute("RENAME TABLE device_toners TO __device_toners");
        $this->execute("
create view device_toners as
    select
        printing_device master_device_id, printer_consumable toner_id, userId, isApproved isSystemDevice
    from oem_printing_device_consumable
union
    select
        printing_device master_device_id, compatible toner_id, userId, isApproved isSystemDevice
    from oem_printing_device_consumable o join compatible_printer_consumable c on o.printer_consumable=c.oem

");

    }

    public function down()
    {

    }
}